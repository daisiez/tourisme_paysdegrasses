<?php

namespace Drupal\apidae_tourisme_dates\EventSubscriber;

use Drupal\apidae_tourisme\Event\ApidaeQueryBuilderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * ApidaeQueryBuilderEvent implementation to add date items to query.
 *
 * @see ApidaeQueryBuilderEvent
 *
 * @package Drupal\apidae_tourisme_dates\EventSubscriber
 */
class ApidaeDateQueryBuilderSubscriber implements EventSubscriberInterface {

  /**
   * Return the subscribed event(s).
   *
   * @return array|string[]
   *   Subscribed event(s).
   */
  public static function getSubscribedEvents() {
    return [ApidaeQueryBuilderEvent::EVENT_NAME => 'alterQuery'];
  }

  /**
   * The main function to alter the query.
   *
   * @param \Drupal\apidae_tourisme\Event\ApidaeQueryBuilderEvent $event
   *   The Event object.
   */
  public function alterQuery(ApidaeQueryBuilderEvent $event) {
    $query = $event->getQuery();

    $query['responseFields'][] = 'ouverture.periodesOuvertures';

    $event->setQuery($query);
  }

}
