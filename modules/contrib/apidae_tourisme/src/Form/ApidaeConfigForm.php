<?php

namespace Drupal\apidae_tourisme\Form;

use Drupal\apidae_tourisme\ApidaeSync;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\image\Entity\ImageStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ApidaeConfigForm.
 */
class ApidaeConfigForm extends ConfigFormBase {

  /**
   * Apidae sync service.
   *
   * @var \Drupal\apidae_tourisme\ApidaeSync
   */
  protected $apidaeSync;

  /**
   * Class constructor.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ApidaeSync $apidaeSync) {
    parent::__construct($config_factory);
    $this->apidaeSync = $apidaeSync;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('apidae_toursime.sync')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'apidae_tourisme.config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'apidae_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('apidae_tourisme.config');
    $form['auth'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Auth'),
    ];
    $form['auth']['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Api Key'),
      '#maxlength' => 128,
      '#size' => 64,
      '#default_value' => $config->get('api_key'),
      '#required' => TRUE,
    ];
    $selections = $config->get('selectionIds');
    if (!is_array($selections)) {
      $selections = explode(',', $selections);
    }
    $form['auth']['selectionIds'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Selection ids, commas separated'),
      '#default_value' => implode(', ', $selections),
      '#required' => TRUE,
    ];
    $form['auth']['default_langcode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default langcode for content'),
      '#description' => $this->t('Ex : fr'),
      '#maxlength' => 128,
      '#size' => 64,
      '#default_value' => $config->get('default_langcode'),
      '#required' => TRUE,
    ];
    $form['auth']['languages'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Lancodes, commas separated'),
      '#description' => $this->t('Please note that the module <strong>content_translation</strong> has to be enable.<br />Ex : fr,es,en'),
      '#maxlength' => 128,
      '#size' => 64,
      '#default_value' => $config->get('languages'),
      '#required' => TRUE,
    ];
    $form['auth']['project_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Project ID'),
      '#maxlength' => 128,
      '#size' => 64,
      '#default_value' => $config->get('project_id'),
      '#required' => TRUE,
    ];
    $form['data'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Data'),
    ];
    $form['data']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable sync'),
      '#default_value' => $config->get('enabled'),
    ];
    $form['data']['batch_size'] = [
      '#type' => 'number',
      '#title' => $this->t('Batch size'),
      '#default_value' => $config->get('batch_size') ?? 20,
    ];
    $form['data']['uid'] = [
      '#type' => 'number',
      '#title' => $this->t('User ID for node creator'),
      '#default_value' => $config->get('uid') ?? 1,
    ];
    $form['data']['sync_medias'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Synchronize medias'),
      '#default_value' => $config->get('sync_medias'),
    ];
    $form['data']['media_image_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Image style to use to resize medias before saving medias'),
      '#description' => $this->t('Leave « No Resize » to save original medias'),
      '#default_value' => $config->get('media_image_style'),
      '#empty_option' => $this->t('No Resize'),
      '#empty_value' => NULL,
      '#options' => $this->getImageStylesOptions(),
    ];
    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $override = [
      'apiKey' => $form_state->getValue('api_key'),
      'projetId' => $form_state->getValue('project_id'),
    ];
    if (!$response = $this->apidaeSync->selectionsQuery($override)) {
      $form_state->setError($form['auth'], $this->t('The auth settings (API Key and Project ID) are not working, see watchdog for more informations.'));
    }
    else {
      $selections = $form_state->getValue('selectionIds');
      $selections = str_replace(PHP_EOL, ',', $selections);
      $selections = explode(',', $selections);
      foreach ($selections as $key => $selection) {
        if (!empty(trim($selection))) {
          $selection = trim($selection);
          if (!$response = $this->apidaeSync->selectionsInformationQuery([$selection], $override)) {
            $form_state->setError($form['auth']['selectionIds'], $this->t('The selection @id does not exist, see watchdog for more informations.', ['@id' => $selection]));
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $selections = $form_state->getValue('selectionIds');
    $selections = str_replace(PHP_EOL, ',', $selections);
    $selections = explode(',', $selections);
    $selectionArray = [];
    foreach ($selections as $key => $selection) {
      if (!empty(trim($selection))) {
        $selectionArray[] = trim($selection);
      }
    }
    $selectionArray = array_unique($selectionArray);
    $this->config('apidae_tourisme.config')
      ->set('auth', $form_state->getValue('auth'))
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('enabled', $form_state->getValue('enabled'))
      ->set('selectionIds', $selectionArray)
      ->set('project_id', $form_state->getValue('project_id'))
      ->set('languages', $form_state->getValue('languages'))
      ->set('enabled', $form_state->getValue('enabled'))
      ->set('sync_medias', $form_state->getValue('sync_medias'))
      ->set('batch_size', (int) $form_state->getValue('batch_size'))
      ->set('uid', (int) $form_state->getValue('uid'))
      ->set('default_langcode', $form_state->getValue('default_langcode'))
      ->set('media_image_style', $form_state->getValue('media_image_style'))
      ->save();

    if (!$form_state->getValue('enabled')) {
      $this->messenger()->addMessage('Please note that sync is not enabled, therefore no content will be synced', 'warning');
    }

  }

  public function getImageStylesOptions() {
    $stylesArray = [];
    $styles = ImageStyle::loadMultiple();
    foreach ($styles as $key => $style) {
      $stylesArray[$key] = $style->label();
    }
    return $stylesArray;
  }

}
