<?php

namespace Drupal\field_time\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a time field form element.
 *
 * Usage example:
 *
 * @code
 * $form['time'] = array(
 *   '#type' => 'time',
 *   '#title' => $this->t('Time'),
 * '#required' => TRUE,
 * );
 * @endcode
 *
 * @FormElement("time")
 */
class TimeElement extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#show_seconds' => FALSE,
      '#input' => TRUE,
      '#process' => [
        [$class, 'processAjaxForm'],
      ],
      '#pre_render' => [
        [$class, 'preRenderTime'],
      ],
      '#theme' => 'input__time',
      '#theme_wrappers' => ['form_element'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    if ($input === FALSE && !empty($element['#default_value'])) {
      $input = $element['#default_value'];
    }

    if (!empty($input)) {
      return $input;
    }

    return NULL;
  }

  /**
   * Prepares a #type 'time' render element for input.html.twig.
   *
   * @param array $element
   *   An associative array containing the properties of the element.
   *   Properties used: #title, #value, #description, #size, #maxlength,
   *   #placeholder, #required, #attributes.
   *
   * @return array
   *   The $element with prepared variables ready for input.html.twig.
   *
   * @see \Drupal\time_field\Plugin\Field\FieldWidget\TimeWidget::formElement()
   */
  public static function preRenderTime(array $element) {
    $element['#attributes']['type'] = 'time';
    $element['#attributes']['class'] = ['form-time'];
    Element::setAttributes($element, [
      'id',
      'name',
      'value',
      'size',
      'maxlength',
      'placeholder',
    ]);
    static::setAttributes($element, ['form-text']);

    return $element;
  }

}
