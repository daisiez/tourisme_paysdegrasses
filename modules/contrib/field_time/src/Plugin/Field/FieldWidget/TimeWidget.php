<?php

namespace Drupal\field_time\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'time_widget' widget.
 *
 * @FieldWidget(
 *   id = "time_widget",
 *   label = @Translation("Time widget"),
 *   field_types = {
 *     "time"
 *   }
 * )
 */
class TimeWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'enabled' => FALSE,
      'step' => 5,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      'enabled' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Add seconds parameter to input widget'),
        '#default_value' => $this->getSetting('enabled'),
      ],
      'step' => [
        '#type' => 'textfield',
        '#title' => $this->t('Step to change seconds'),
        '#open' => TRUE,
        '#default_value' => $this->getSetting('step'),
        '#states' => [
          'visible' => [
            ':input[name$="[enabled]"]' => ['checked' => TRUE],
          ],
        ],
      ],
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\field_time\Element\TimeElement::preRenderTime()
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    // Determine if we're showing seconds in the widget.
    $show_seconds = (bool) $this->getSetting('enabled');
    $value = $items[$delta]->value ?? NULL;
    if ($show_seconds && strlen($value) === 5) {
      $value .= ':00';
    }
    $additional = [
      '#type' => 'time',
      '#default_value' => $value,
    ];
    // Add the step attribute if we're showing seconds in the widget.
    if ($show_seconds) {
      $additional['#attributes']['step'] = $this->getSetting('step');
    }
    // Set a property to determine the format in TimeElement::preRenderTime().
    $additional['#show_seconds'] = $show_seconds;
    $element['value'] = $element + $additional;
    return $element;
  }

}
