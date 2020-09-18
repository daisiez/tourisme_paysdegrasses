<?php

namespace Drupal\field_time\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'time_widget' widget.
 *
 * @FieldWidget(
 *   id = "time_range_widget",
 *   label = @Translation("Time range widget"),
 *   field_types = {
 *     "time_range"
 *   }
 * )
 */
class TimeRangeWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $show_seconds = (bool) $this->getSetting('enabled');
    $element['from'] = [
      '#title' => $this->t('Start time'),
      '#type' => 'time',
    ];
    $element['to'] = [
      '#title' => $this->t('End time'),
      '#type' => 'time',
    ];
    $from = $items[$delta]->from ?? NULL;
    if ($show_seconds && strlen($from) === 5) {
      $from .= ':00';
    }
    $to = $items[$delta]->to ?? NULL;
    if ($show_seconds && strlen($to) === 5) {
      $to .= ':00';
    }
    $element['from']['#default_value'] = $from;
    $element['to']['#default_value'] = $to;

    if ((int) $this->fieldDefinition->getFieldStorageDefinition()->getCardinality() === 1) {
      $element += [
        '#type' => 'fieldset',
      ];
    }
    if ($show_seconds) {
      $element['from']['#attributes']['step'] = $this->getSetting('step');
      $element['to']['#attributes']['step'] = $this->getSetting('step');
    }

    $element['from']['#show_seconds'] = $show_seconds;
    $element['to']['#show_seconds'] = $show_seconds;

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return ['enabled' => FALSE, 'step' => 5] + parent::defaultSettings();
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

}
