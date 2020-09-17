<?php

namespace Drupal\apidae_tourisme\Event;

use Drupal\node\Entity\Node;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event dispatched before the saving of a node.
 *
 * @package Drupal\apidae_tourisme\Event
 */
class ApidaeNodePresaveEvent extends Event {

  public const EVENT_NAME = 'apidae_tourisme_node_presave';

  /**
   * The Node object.
   *
   * @var \Drupal\node\Entity\Node
   */
  protected $node;

  /**
   * Array of locales to sync.
   *
   * @var array
   */
  protected $locales;

  /**
   * Default langcode.
   *
   * @var string
   */
  protected $langcode;

  /**
   * Either "CREATION" or "UPDATE".
   *
   * @var string
   */
  protected $mode;

  /**
   * The Apidae data about the object.
   *
   * @var array
   */
  protected $apidaeObject;

  /**
   * ApidaeNodePresaveEvent constructor.
   *
   * @param \Drupal\node\Entity\Node $node
   *   The node object.
   * @param array $apidaeObject
   *   The Apidae object.
   * @param string $mode
   *   Either "CREATION" or "UPDATE".
   * @param string $langcode
   *   Default langcode.
   * @param array $locales
   *   Array of locales to sync.
   */
  public function __construct(Node $node, array $apidaeObject, $mode, $langcode, array $locales) {
    $this->apidaeObject = $apidaeObject;
    $this->node = $node;
    $this->mode = $mode;
    $this->langcode = $langcode;
    $this->locales = $locales;
  }

  /**
   * Return the implicated node.
   *
   * @return \Drupal\node\Entity\Node
   *   The Node object.
   */
  public function getNode() : Node {
    return $this->node;
  }

  /**
   * Return the current mode ("CREATION" or "UPDATE").
   *
   * @return string
   *   Either "CREATION" or "UPDATE".
   */
  public function getMode() : string {
    return $this->mode;
  }

  /**
   * Return the Apidae object.
   *
   * @return array
   *   The Apidae object.
   */
  public function getApidaeObject() : array {
    return $this->apidaeObject;
  }

  /**
   * Return the locales to sync.
   *
   * @return array
   *   The locales to sync.
   */
  public function getLocales() : array {
    return $this->locales;
  }

  /**
   * Return the default langcode.
   *
   * @return string
   *   The default langcode.
   */
  public function getLangcode() : string {
    return $this->langcode;
  }

}
