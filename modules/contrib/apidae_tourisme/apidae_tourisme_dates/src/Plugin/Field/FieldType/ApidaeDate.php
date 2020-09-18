<?php

namespace Drupal\apidae_tourisme_dates\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'time' field type.
 *
 * @FieldType(
 *   category= @Translation("General"),
 *   id = "apidae_date",
 *   label = @Translation("Apidae Date"),
 *   description = @Translation("Attributs dates apidae"),
 *   default_widget = "apidae_date_widget",
 *   default_formatter = "apidae_date_formatter"
 * )
 */
class ApidaeDate extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['dateDebut'] = DataDefinition::create('string')
      ->setSetting('maxlength', 10)
      ->setLabel(new TranslatableMarkup('dateDebut'));

    $properties['dateFin'] = DataDefinition::create('string')
      ->setSetting('maxlength', 10)
      ->setLabel(new TranslatableMarkup('dateFin'));

    $properties['complementHoraire'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('complementHoraire'));

    $properties['type'] = DataDefinition::create('string')
      ->setSetting('maxlength', 50)
      ->setLabel(new TranslatableMarkup('Answer'));

    $properties['horaireOuverture'] = DataDefinition::create('string')
      ->setSetting('maxlength', 8)
      ->setLabel(new TranslatableMarkup('Answer'));

    $properties['horaireFermeture'] = DataDefinition::create('string')
      ->setSetting('maxlength', 8)
      ->setLabel(new TranslatableMarkup('Answer'));

    $properties['ouverturesJournalieres'] = DataDefinition::create('string')
      ->setSetting('maxlength', 100)
      ->setLabel(new TranslatableMarkup('Answer'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'dateDebut' => [
          'type' => 'varchar_ascii',
          'length' => 10,
          'not null' => FALSE,
        ],
        'dateFin' => [
          'type' => 'varchar_ascii',
          'length' => 10,
          'not null' => FALSE,
        ],
        'complementHoraire' => [
          'type' => 'text',
          'size' => 'big',
          'not null' => FALSE,
        ],
        'type' => [
          'type' => 'varchar_ascii',
          'length' => 50,
          'not null' => FALSE,
        ],
        'horaireOuverture' => [
          'type' => 'varchar_ascii',
          'length' => 8,
          'not null' => FALSE,
        ],
        'horaireFermeture' => [
          'type' => 'varchar_ascii',
          'length' => 8,
          'not null' => FALSE,
        ],
        'ouverturesJournalieres' => [
          'type' => 'varchar_ascii',
          'length' => 100,
          'not null' => FALSE,
        ],
      ],
      'indexes' => [
        'value' => ['dateDebut'],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values['dateDebut'] = date('Y-m-d');
    $values['dateFin'] = date('Y-m-d');
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $dateDebut = $this->get('dateDebut')->getValue();
    $dateFin = $this->get('dateFin')->getValue();
    return trim($dateDebut) === '' && trim($dateFin) === '';
  }

}
