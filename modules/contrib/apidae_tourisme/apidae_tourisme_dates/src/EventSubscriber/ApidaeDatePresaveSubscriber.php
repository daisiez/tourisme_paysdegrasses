<?php

namespace Drupal\apidae_tourisme_dates\EventSubscriber;

use Drupal\apidae_tourisme\Event\ApidaeNodePresaveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * ApidaeNodePresaveEvent implementation.
 *
 * @see \Drupal\apidae_tourisme\Event\ApidaeNodePresaveEvent
 *
 * @package Drupal\apidae_tourisme_examples\EventSubscriber
 */
class ApidaeDatePresaveSubscriber implements EventSubscriberInterface {

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
    $node = $event->getNode();
    $objet = $event->getApidaeObject();
    $ouvertures = [];
    if (isset($objet['ouverture']['periodesOuvertures']) && is_array($objet['ouverture']['periodesOuvertures'])) {
      foreach ($objet['ouverture']['periodesOuvertures'] as $ouverture) {
        if (isset($ouverture['complementHoraire'])) {
          $ouverture['complementHoraire'] = array_pop($ouverture['complementHoraire']);
        }
        if (isset($ouverture['ouverturesJournalieres'])) {
          $ouverture['ouverturesJournalieres'] = implode('|', array_map(function ($a) {return $a['jour'];}, $ouverture['ouverturesJournalieres']));
        }
        $ouvertures[] = $ouverture;
      }
    }
    $node->set('field_apidae_dates', $ouvertures);
  }

}
