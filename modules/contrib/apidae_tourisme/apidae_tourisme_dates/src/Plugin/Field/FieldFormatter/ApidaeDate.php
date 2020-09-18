<?php

namespace Drupal\apidae_tourisme_dates\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'apidae_date' formatter.
 *
 * @FieldFormatter(
 *   id = "apidae_date_formatter",
 *   label = @Translation("Apidae Date"),
 *   field_types = {
 *     "apidae_date"
 *   }
 * )
 */
class ApidaeDate extends FormatterBase {

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return array
   *   Drupal theme apidae_date
   */
  protected function viewValue(FieldItemInterface $item) {
    $data = $item->getValue();
    if (trim($data['ouverturesJournalieres']) !== '') {
      $data['ouverturesJournalieres'] = explode('|', $data['ouverturesJournalieres']);
    }
    else {
      $data['ouverturesJournalieres'] = NULL;
    }
    return [
      '#theme' => 'apidae_date',
      '#data' => $data,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = $this->viewValue($item);
    }

    return $elements;
  }

}
