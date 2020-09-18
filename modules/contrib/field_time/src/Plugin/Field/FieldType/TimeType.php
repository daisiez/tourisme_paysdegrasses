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
 *   id = "time",
 *   label = @Translation("Time"),
 *   description = @Translation("Time field"),
 *   default_widget = "time_widget",
 *   default_formatter = "time_formatter"
 * )
 */
class TimeType extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Time'))
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
        'value' => [
          'type' => 'varchar_ascii',
          'length' => 8,
          'not null' => FALSE,
        ],
      ],
      'indexes' => [
        'value' => ['value'],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values['value'] = '11:55:00';
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return trim($value) === '';
  }

}
