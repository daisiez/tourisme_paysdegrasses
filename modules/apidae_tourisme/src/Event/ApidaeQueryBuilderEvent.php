<?php

namespace Drupal\apidae_tourisme\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event dispatched when the query is built, before it's execution.
 *
 * @package Drupal\apidae_tourisme\Event
 */
class ApidaeQueryBuilderEvent extends Event {

  public const EVENT_NAME = 'apidae_tourisme_query_builder';

  /**
   * The Apidae query construction array.
   *
   * @var array
   */
  protected $query;

  /**
   * ApidaeQueryBuilderEvent constructor.
   *
   * @param array $query
   *   The Apidae query construction array.
   */
  public function __construct(array $query) {
    $this->query = $query;
  }

  /**
   * Return the query array.
   *
   * @return array
   *   The Apidae query construction array.
   */
  public function getQuery() : array {
    return $this->query;
  }

  /**
   * Update the query array.
   *
   * @param array $query
   *   The new Apidae query construction array.
   *
   * @return $this
   */
  public function setQuery(array $query) {
    $this->query = $query;
    return $this;
  }

}
