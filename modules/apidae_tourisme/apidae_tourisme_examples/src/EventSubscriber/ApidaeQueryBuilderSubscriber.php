<?php

namespace Drupal\apidae_tourisme_examples\EventSubscriber;

use Drupal\apidae_tourisme\Event\ApidaeQueryBuilderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Example of ApidaeQueryBuilderEvent implementation.
 *
 * @see ApidaeQueryBuilderEvent
 *
 * @package Drupal\apidae_tourisme_examples\EventSubscriber
 */
class ApidaeQueryBuilderSubscriber implements EventSubscriberInterface {

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

    /* Add your custom query fields :
     * $query['lorem'] = 'ipsum';
     */

    $event->setQuery($query);
  }

}
