<?php

namespace Drupal\apidae_tourisme;

use Drupal\apidae_tourisme\Event\ApidaeNodePresaveEvent;
use Drupal\apidae_tourisme\Event\ApidaeQueryBuilderEvent;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Random;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\node\Entity\Node;
use GuzzleHttp\ClientInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\Core\File\FileSystemInterface;

/**
 * Class ApidaeSync.
 */
class ApidaeSync {

  use StringTranslationTrait;

  /**
   * GuzzleHttp\ClientInterface definition.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * Module Handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Event Dispatcher service.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * State service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  private $state;

  /**
   * Logger chanel service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  private $logger;

  /**
   * Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  private $messenger;

  /**
   * Entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * File System Service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  private $fileSystem;

  /**
   * Apidae config project ID.
   *
   * @var string
   */
  protected $apidaeProjectId;

  /**
   * Apidae config project API key.
   *
   * @var string
   */
  protected $apidaeApiKey;

  /**
   * Languages to sync, comma separated.
   *
   * @var string
   */
  protected $languages;

  /**
   * Apidae selections to sync, comma separated.
   *
   * @var string
   */
  protected $selectionIds;

  /**
   * Last sync updated.
   *
   * @var int
   */
  protected $lastUpdate = 0;

  /**
   * Number of batch per sync.
   *
   * @var int
   */
  private static $count = 20;

  /**
   * Items to sync batch size.
   *
   * @var int
   */
  private $maxItemsPerBatch;

  /**
   * Content creator UID.
   *
   * @var int
   */
  private $creator;

  /**
   * Media image style to use before saving medias
   *
   * @var string|null
   */
  private $mediaImageStyle;

  /**
   * Main langcode.
   *
   * @var string
   */
  private $langcode;

  /**
   * Do we sync medias from apidae.
   *
   * @var bool
   */
  private $syncMedias;

  /**
   * Main langcode capitalized.
   *
   * @var string
   */
  private $langcodeCapitalized;

  /**
   * Apidae API url.
   *
   * @var string
   */
  private static $url = 'http://api.apidae-tourisme.com/api/v002/';

  /**
   * Constructs a new ApidaeSync object.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   Guzzle client.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory service.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   Cache backend.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   Module handler to check if i18n is enabled.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   Event dispatcher (@see ApidaeNodePresaveEvent, ApidaeQueryBuilderEvent).
   * @param \Drupal\Core\State\StateInterface $state
   *   State service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   Logger service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   Messenger service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity Type Manager service.
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   File system service.
   */
  public function __construct(ClientInterface $http_client, ConfigFactoryInterface $config_factory, CacheBackendInterface $cacheBackend, ModuleHandlerInterface $module_handler, EventDispatcherInterface $eventDispatcher, StateInterface $state, LoggerChannelFactoryInterface $loggerFactory, MessengerInterface $messenger, EntityTypeManagerInterface $entityTypeManager, FileSystemInterface $fileSystem) {
    $this->httpClient = $http_client;
    $this->configFactory = $config_factory;
    $this->cacheBackend = $cacheBackend;
    $this->moduleHandler = $module_handler;
    $this->eventDispatcher = $eventDispatcher;

    $config = $this->configFactory->get('apidae_tourisme.config')->get();

    $this->apidaeApiKey = $config['api_key'];
    $this->apidaeProjectId = $config['project_id'];
    $this->languages = explode(',', $config['languages']);
    $this->selectionIds = $config['selectionIds'];
    if (!is_array($this->selectionIds)) {
      $this->selectionIds = explode(',', $this->selectionIds);
    }
    $this->maxItemsPerBatch = $config['batch_size'];
    $this->creator = $config['uid'];
    $this->langcode = $config['default_langcode'];
    $this->syncMedias = $config['sync_medias'] ?? TRUE;
    $this->langcodeCapitalized = ucfirst($this->langcode);
    $this->mediaImageStyle = $config['media_image_style'] ?? NULL;
    $this->state = $state;
    $this->lastUpdate = $this->state->get('apidae.last_sync', 0);
    $this->logger = $loggerFactory->get('apidae');
    $this->messenger = $messenger;
    $this->entityTypeManager = $entityTypeManager;
    $this->fileSystem = $fileSystem;
  }

  /**
   * Create the sync process.
   *
   * @param bool $forceUpdate
   *   If true, the update is forced, even if the content update is after.
   * @param array $ids
   *   Ids of objet_touristique to sync.
   *
   * @return bool
   *   Sync result.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function sync($forceUpdate = FALSE, array $ids = []) {
    $this->state->set('apidae.last_sync', date('U'));
    $first = 0;
    $data['numFound'] = 1000;
    $results = [
      'created' => 0,
      'updated' => 0,
      'error' => 0,
      'not_updated' => 0,
    ];
    while ($data['numFound'] > $first && $first < $this->maxItemsPerBatch) {
      $data = $this->doQuery($first, self::$count, $ids);
      foreach ($data['objetsTouristiques'] as $objetTouristique) {
        $this->parseOject($objetTouristique, $results, $forceUpdate);
      }
      $first += $data['query']['count'];
    }
    $this->logger->info($this->t('Sync over, @created created, @updated updated, @not_updated unchanged, @error errors, @num_results results', [
      '@created' => $results['created'],
      '@updated' => $results['updated'],
      '@error' => $results['error'],
      '@not_updated' => $results['not_updated'],
      '@num_results' => $data['numFound'],
    ]));
    if (\count($ids) > 0) {
      $this->messenger->addStatus($this->t('Sync over, @created created, @updated updated, @not_updated unchanged, @error errors, @num_results results', [
        '@created' => $results['created'],
        '@updated' => $results['updated'],
        '@error' => $results['error'],
        '@not_updated' => $results['not_updated'],
        '@num_results' => $data['numFound'],
      ]));
    }
    return TRUE;
  }

  /**
   * Apidae query construction and execution.
   *
   * @param int $first
   *   Pager config.
   * @param int $count
   *   Pager config.
   * @param array $ids
   *   Ids of objet_touristique to sync.
   * @param array $selectionIds
   *   Array of selections, if NULL, configuration will be used.
   * @param string $order
   *   Sort parameter.
   *
   * @return bool|mixed
   *   Return the query results, false in case of error.
   */
  protected function doQuery($first, $count, array $ids = [], array $selectionIds = [], $order = 'RANDOM') {
    $random = new Random();
    $query = [
      'projetId' => $this->apidaeProjectId,
      'apiKey' => $this->apidaeApiKey,
      'selectionIds' => \count($selectionIds) > 0 ? $selectionIds : $this->selectionIds,
      'locales' => $this->languages,
      'first' => $first,
      'count' => $count,
      'order' => $order,
      'randomSeed' => $random->word('20'),
      'responseFields' => [
        'id',
        'nom',
        'localisation',
        'presentation',
        'descriptionTarif.tarifsEnClair',
        'informations.moyensCommunication',
        'illustrations',
        'gestion.dateModification',
      ],
    ];

    if (\count($ids) > 0) {
      $query['identifiants'] = $ids;
    }
    $event = new ApidaeQueryBuilderEvent($query);
    $this->eventDispatcher->dispatch($event::EVENT_NAME, $event);
    $query = $event->getQuery();

    $url = self::$url . 'recherche/list-objets-touristiques?query=' . Json::encode($query);
    try {
      $response = $this->httpClient->get($url);
      $data = $response->getBody();
      return Json::decode($data);
    }
    catch (\Exception $e) {
      $this->logger->error($this->t('error @message<br />query : <code>@query</code>', [
        '@message' => $e->getMessage(),
        '@query' => print_r($query, TRUE),
      ]));
      return FALSE;
    }
  }

  /**
   * Execute a custom selection search.
   *
   * @param int $first
   *   Pager config.
   * @param int $count
   *   Pager config.
   * @param array $selectionIds
   *   Array of selections ids.
   * @param string $order
   *   Sort parameter.
   *
   * @return bool|mixed
   *   Return the query results, false in case of error.
   *
   * @see http://dev.apidae-tourisme.com/fr/documentation-technique/v2/api-de-diffusion/liste-des-services-2/v002recherchelist-objets-touristiques
   */
  public function listObjetsTouristiquesQuery($first, $count, array $selectionIds, $order = 'RANDOM') {
    return $this->doQuery($first, $count, [], $selectionIds, $order);
  }

  /**
   * Apidae selections query.
   *
   * @param array $override
   *   Optional override parameters.
   *
   * @return bool|mixed
   *   Return the project selection, false in case of error.
   */
  public function selectionsQuery(array $override = []) {
    $query = [
      'apiKey' => $override['apiKey'] ?? $this->apidaeApiKey,
      'projetId' => $override['projetId'] ?? $this->apidaeProjectId,
    ];
    $url = self::$url . 'referentiel/selections/?query=' . Json::encode($query);
    try {
      $response = $this->httpClient->get($url);
      $data = $response->getBody();
      return Json::decode($data);
    }
    catch (\Exception $e) {
      $this->logger->error($this->t('@message<br />query : @query', [
        '@message' => $e->getMessage(),
        '@query' => print_r($query, TRUE),
      ]));
      return FALSE;
    }
  }

  /**
   * Apidae get information about selections.
   *
   * @param array $selection_id
   *   Array of selection ids.
   *
   * @param array $override
   *   Optional override parameters.
   *
   * @return bool|mixed
   *   Return the project selection, false in case of error.
   */
  public function selectionsInformationQuery(array $selection_id, array $override = []) {
    $query = [
      'apiKey' => $override['apiKey'] ?? $this->apidaeApiKey,
      'projetId' => $override['projetId'] ?? $this->apidaeProjectId,
      'selectionIds' => $selection_id,
    ];
    $url = self::$url . 'referentiel/selections/?query=' . Json::encode($query);
    try {
      $response = $this->httpClient->get($url);
      $data = $response->getBody();
      return Json::decode($data);
    }
    catch (\Exception $e) {
      $this->logger->error($this->t('error @message<br />query : @query', [
        '@message' => $e->getMessage(),
        '@query' => print_r($query, TRUE),
      ]));
      return FALSE;
    }
  }

  /**
   * Apidae query selectionsParObjetQuery construction and execution.
   *
   * @param int[] $object_ids
   *   Array of Apidae object ids.
   *
   * @return bool|mixed
   *   Return the query results, false in case of error.
   */
  protected function selectionsParObjetQuery(array $object_ids) {
    $query = [
      'apiKey' => $this->apidaeApiKey,
      'projetId' => $this->apidaeProjectId,
      'referenceIds' => $object_ids,
    ];
    $url = self::$url . 'referentiel/selections-par-objet?query=' . Json::encode($query);
    try {
      $response = $this->httpClient->get($url);
      $data = $response->getBody();
      return Json::decode($data);
    }
    catch (\Exception $e) {
      $this->logger->error($this->t('error @message<br />query : @query', [
        '@message' => $e->getMessage(),
        '@query' => print_r($query, TRUE),
      ]));
      return FALSE;
    }
  }

  /**
   * Parse a Result object and create / update a node.
   *
   * @param array $apidaeObject
   *   The apidae object.
   * @param array $results
   *   Current sync results.
   * @param bool $forceUpdate
   *   If true, the update is forced, even if the content update is after.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function parseOject(array $apidaeObject, array &$results, $forceUpdate = FALSE) {
    $modificationDate = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", $apidaeObject['gestion']['dateModification']);
    $locales = array_diff($this->languages, [$this->langcode]);
    if (!$this->moduleHandler->moduleExists('content_translation')) {
      $locales = [];
    }
    if (!$objet = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'objet_touristique', 'field_id_ws' => $apidaeObject['id']])) {
      /** @var \Drupal\node\Entity\Node $objet */
      $objet = Node::create([
        'field_id_ws' => $apidaeObject['id'],
        'langcode' => $this->langcode,
        'default_langcode' => TRUE,
        'type' => 'objet_touristique',
        'title' => $apidaeObject['nom']['libelle' . $this->langcodeCapitalized],
        'field_type' => $apidaeObject['type'],
        'promote' => 0,
        'uid' => $this->creator,
      ]);
      $objet->save();
      $is_new = TRUE;
    }
    else {
      $is_new = FALSE;
      $objet = array_pop($objet);
      /** @var \Drupal\node\Entity\Node $objet */
      if (!$forceUpdate && $objet->getChangedTime() > $modificationDate->format('U')) {
        $results['not_updated']++;
        return;
      }
    }
    $objet->set('title', $apidaeObject['nom']['libelle' . $this->langcodeCapitalized]);
    $objet->set('field_description_courte', $this->getDescriptionCourte($apidaeObject, $this->langcode));
    $objet->set('field_description', $this->getDescription($apidaeObject, $this->langcode));
    $objet->set('field_phone', $this->getPhoneFromObject($apidaeObject, $this->langcode));
    if ($this->syncMedias) {
      $objet->set('field_illustrations', $this->getMedias($apidaeObject));
    }
    $objet->set('field_geolocation', $this->getGeolocalisation($apidaeObject));
    $objet->set('field_email', $this->getMailFromObject($apidaeObject, $this->langcode));
    $objet->set('field_website', $this->getWebsiteFromObject($apidaeObject, $this->langcode));
    $objet->set('field_address', $this->getAddress($apidaeObject));
    $objet->set('promote', 0);

    foreach ($locales as $locale) {
      $key = 'libelle' . \ucwords($locale);
      $titleAvailable = isset($apidaeObject['nom'][$key]);
      $descriptionAvailable = isset($apidaeObject['presentation']['descriptifsThematises'][0]['description'][$key]);
      $descriptionCourteAvailable = isset($apidaeObject['presentation']['descriptifCourt'][$key]);
      if ($titleAvailable || $descriptionAvailable || $descriptionCourteAvailable) {
        if (!$objet->hasTranslation($locale)) {
          $translation = $objet->addTranslation($locale);
        }
        else {
          $translation = $objet->getTranslation($locale);
        }
        $translation->set('title', $apidaeObject['nom']['libelle' . $this->langcodeCapitalized]);
        $translation->set('field_description', $this->getDescription($apidaeObject, $locale));
        $translation->set('field_description_courte', $this->getDescriptionCourte($apidaeObject, $locale));
        $translation->save();
      }
      elseif ($objet->hasTranslation($locale)) {
        $objet->removeTranslation($locale);
      }

    }
    $event = new ApidaeNodePresaveEvent($objet, $apidaeObject, 'UPDATE', $this->langcode, $this->languages);
    $this->eventDispatcher->dispatch($event::EVENT_NAME, $event);
    if ($objet->save()) {
      if ($is_new) {
        $results['created']++;
      }
      else {
        $results['updated']++;
      }
    }
    else {
      $results['error']++;
    }
  }

  /**
   * Return field form Apidae object.
   *
   * @param array $object
   *   The Apidae object.
   * @param string $locale
   *   The langcode wanted.
   *
   * @return string|null
   *   The data if set, null otherwise.
   */
  private function getDescriptionCourte(array $object, $locale) {
    return $object['presentation']['descriptifCourt']['libelle' . \ucwords($locale)] ?? NULL;
  }

  /**
   * Return field form Apidae object.
   *
   * @param array $object
   *   The Apidae object.
   * @param string $locale
   *   The langcode wanted.
   *
   * @return string|null
   *   The data if set, null otherwise.
   */
  private function getDescription(array $object, $locale) {
    return $object['presentation']['descriptifsThematises'][0]['description']['libelle' . \ucwords($locale)] ?? NULL;
  }

  /**
   * Return field form Apidae object.
   *
   * @param array $object
   *   The Apidae object.
   * @param string $locale
   *   The langcode wanted.
   *
   * @return string|null
   *   The data if set, null otherwise.
   */
  private function getPhoneFromObject(array $object, $locale) {
    if (isset($object['informations']['moyensCommunication'])) {
      foreach ($object['informations']['moyensCommunication'] as $moyen) {
        if ($moyen['type']['id'] === 201 && isset($moyen['coordonnees'][$locale])) {
          return $moyen['coordonnees'][$locale];
        }
      }
    }
    return NULL;
  }

  /**
   * Return field form Apidae object.
   *
   * @param array $object
   *   The Apidae object.
   * @param string $locale
   *   The langcode wanted.
   *
   * @return string|null
   *   The data if set, null otherwise.
   */
  private function getMailFromObject(array $object, $locale) {
    if (isset($object['informations']['moyensCommunication'])) {
      foreach ($object['informations']['moyensCommunication'] as $moyen) {
        if ($moyen['type']['id'] === 204 && isset($moyen['coordonnees'][$locale])) {
          return $moyen['coordonnees'][$locale];
        }
      }
    }
    return NULL;
  }

  /**
   * Return field form Apidae object.
   *
   * @param array $object
   *   The Apidae object.
   * @param string $locale
   *   The langcode wanted.
   *
   * @return string|null
   *   The data if set, null otherwise.
   */
  private function getWebsiteFromObject(array $object, $locale) {
    if (isset($object['informations']['moyensCommunication'])) {
      foreach ($object['informations']['moyensCommunication'] as $moyen) {
        if ($moyen['type']['id'] === 205 && isset($moyen['coordonnees'][$locale])) {
          return $moyen['coordonnees'][$locale];
        }
      }
    }
    return NULL;
  }

  /**
   * Return field form Apidae object.
   *
   * @param array $object
   *   The Apidae object.
   *
   * @return array|null
   *   The data if set, null otherwise.
   */
  private function getGeolocalisation(array $object) {
    if (isset($object['localisation']['geolocalisation']['geoJson']['coordinates'])) {
      return [
        'lng' => $object['localisation']['geolocalisation']['geoJson']['coordinates'][0],
        'lat' => $object['localisation']['geolocalisation']['geoJson']['coordinates'][1],
      ];
    }
    return NULL;
  }

  /**
   * Return field form Apidae object.
   *
   * @param array $object
   *   The Apidae object.
   *
   * @return array|null
   *   The data if set, null otherwise.
   */
  private function getAddress(array $object) {
    if (isset($object['localisation']['adresse'])) {
      return [
        'country_code' => 'FR',
        'address_line1' => $object['localisation']['adresse']['adresse1'] ?? NULL,
        'address_line2' => $object['localisation']['adresse']['adresse2'] ?? NULL,
        'locality' => $object['localisation']['adresse']['commune']['nom'],
        'postal_code' => $object['localisation']['adresse']['commune']['codePostal'],
      ];
    }
    return NULL;

  }

  /**
   * Return field form Apidae object.
   *
   * @param array $object
   *   The Apidae object.
   *
   * @return array|null
   *   The data if set, null otherwise.
   */
  private function getMedias(array $object) {
    $files = [];
    if (!$style = ImageStyle::load($this->mediaImageStyle)) {
      $this->mediaImageStyle = NULL;
    }
    if (isset($object['illustrations']) && is_array($object['illustrations'])) {
      foreach ($object['illustrations'] as $illu) {
        $modificationDate = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", $illu['traductionFichiers'][0]['lastModifiedDate']);
        $url = $illu['traductionFichiers'][0]['url'];
        $filename = basename($url);
        $title = $illu['nom']['libelleFr'] ?? $filename;
        $temporary = 'temporary://objets_touristiques/' . $modificationDate->format('Y-m') . '/';
        $folder = 'public://objets_touristiques/' . $modificationDate->format('Y-m') . '/';
        $temporarydDestination = $temporary . $filename;
        $destination = $folder . $filename;
        if (file_exists($destination) && $existingFiles = $this->entityTypeManager->getStorage('file')->loadByProperties(['uri' => $destination])) {
          $files[] = array_pop($existingFiles);
          continue;
        }
        if (!is_dir($folder)) {
          $this->fileSystem->mkdir($folder, NULL, TRUE);
        }
        if (!is_dir($temporary)) {
          $this->fileSystem->mkdir($temporary, NULL, TRUE);
        }
        if ($data = file_get_contents($url)) {
          if ($this->mediaImageStyle !== NULL) {
            $file = file_save_data($data, $temporarydDestination, FileSystemInterface::EXISTS_REPLACE);
            $style->createDerivative($temporarydDestination, $destination);
            unlink($temporarydDestination);
            $file->setFileUri($destination);
          }
          else {
            $file = file_save_data($data, $destination, FileSystemInterface::EXISTS_REPLACE);
          }
          $file->save();
          $files[] = [
            'target_id' => $file->id(),
            'alt' => $title,
            'title' => $title,
          ];
        }
        else {
          $this->logger->error($this->t('Problem getting @url file', [
            '@url' => $url,
          ]));
        }

      }
    }
    return $files;
  }

}
