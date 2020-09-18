<?php

namespace Drupal\field_time\Plugin\Field\FieldType;

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
 *   id = "time_range",
 *   label = @Translation("Time Range"),
 *   description = @Translation("Time range field"),
 *   default_widget = "time_range_widget",
 *   default_formatter = "time_range_formatter"
 * )
 */
class TimeRangeType extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['from'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Start time'))
      ->setDescription(new TranslatableMarkup('Format HH:MM:SS'))
      ->setSetting('maxlength', 8)
      ->setRequired(TRUE);

    $properties['to'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('End time'))
      ->setDescription(new TranslatableMarkup('Format HH:MM:SS'))
      ->setSetting('maxlength', 8)
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'from' => [
          'type' => 'varchar_ascii',
          'length' => 8,
          'not null' => FALSE,
        ],
        'to' => [
          'type' => 'varchar_ascii',
          'length' => 8,
          'not null' => FALSE,
        ],
      ],
      'indexes' => [
        'value' => ['from', 'to'],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values['from'] = '10:55:00';
    $values['to'] = '11:55:00';
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $from = $this->get('from')->getValue();
    $to = $this->get('to')->getValue();
    return trim($from) === '' || trim($to) === '';
  }

}
