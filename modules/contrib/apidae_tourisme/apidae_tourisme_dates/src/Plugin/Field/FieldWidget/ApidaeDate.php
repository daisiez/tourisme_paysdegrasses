<?php

namespace Drupal\apidae_tourisme_dates\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'apidae_date' widget.
 *
 * @FieldWidget(
 *   id = "apidae_date_widget",
 *   label = @Translation("Apidae Date"),
 *   field_types = {
 *     "apidae_date"
 *   }
 * )
 */
class ApidaeDate extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['dateDebut'] = [
      '#type' => 'date',
      '#title' => t('dateDebut'),
      '#default_value' => $items[$delta]->dateDebut ?? NULL,
    ];

    $element['dateFin'] = [
      '#type' => 'date',
      '#title' => t('dateFin'),
      '#default_value' => $items[$delta]->dateFin ?? NULL,
    ];

    $element['complementHoraire'] = [
      '#type' => 'textarea',
      '#default_value' => $items[$delta]->complementHoraire ?? '',
      '#title' => t('complementHoraire'),
      '#rows' => 5,
    ];
    $element['type'] = [
      '#type' => 'textfield',
      '#title' => t('Type'),
      '#default_value' => $items[$delta]->type ?? NULL,
      '#size' => 120,
      '#maxlength' => 255,
    ];
    $element['horaireOuverture'] = [
      '#type' => 'time',
      '#title' => t('dateDebut'),
      '#default_value' => $items[$delta]->horaireOuverture ?? NULL,
    ];
    $element['horaireFermeture'] = [
      '#type' => 'time',
      '#title' => t('dateFin'),
      '#default_value' => $items[$delta]->horaireFermeture ?? NULL,
    ];
    $element['ouverturesJournalieres'] = [
      '#type' => 'textfield',
      '#size' => 100,
      '#maxlength' => 100,
      '#not null' => FALSE,
    ];
    return $element;
  }

}
