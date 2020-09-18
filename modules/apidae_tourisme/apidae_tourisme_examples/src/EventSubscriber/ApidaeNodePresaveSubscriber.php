<?php

namespace Drupal\apidae_tourisme_examples\EventSubscriber;

use Drupal\apidae_tourisme\Event\ApidaeNodePresaveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * ApidaeNodePresaveEvent implementation.
 *
 * @see \Drupal\apidae_tourisme\Event\ApidaeNodePresaveEvent
 *
 * @package Drupal\apidae_tourisme_examples\EventSubscriber
 */
class ApidaeNodePresaveSubscriber implements EventSubscriberInterface {

  /**
   * Return the subscribed event(s).
   *
   * @return array|string[]
   *   Subscribed event(s).
   */
  public static function getSubscribedEvents() {
    return [ApidaeNodePresaveEvent::EVENT_NAME => 'nodeUpdate'];
  }

  /**
   * The main function to alter the node.
   *
   * @param \Drupal\apidae_tourisme\Event\ApidaeNodePresaveEvent $event
   *   The Event object.
   */
  public function nodeUpdate(ApidaeNodePresaveEvent $event) {
    $langcode = $event->getLangcode();
    $locales = $event->getLocales();

    $node = $event->getNode();
    $objet = $event->getApidaeObject();

    // In this example, field_example is a formatted text field.
    $node->set('field_example', [
      'value' => nl2br($this->getFieldExample($objet, $langcode)),
      'format' => 'basic_html',
    ]);

    foreach ($locales as $locale) {
      if ($node->hasTranslation($locale) && $translation = $node->getTranslation($locale)) {
        $translation->set('field_example', [
          'value' => nl2br($this->getFieldExample($objet, $locale)),
          'format' => 'basic_html',
        ]);
      }
    }
  }

  /**
   * Parse Apidae Object tree and return data.
   *
   * @param array $object
   *   The apidae object.
   * @param string $locale
   *   The langcode wanted.
   *
   * @return mixed|null
   *   The data, null if not existing.
   */
  private function getFieldExample(array $object, $locale) {
    $key = 'libelle' . ucfirst($locale);
    return $object['example'][$key] ?? NULL;
  }

}
