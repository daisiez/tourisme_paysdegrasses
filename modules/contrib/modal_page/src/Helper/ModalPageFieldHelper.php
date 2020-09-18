<?php

namespace Drupal\modal_page\Helper;

use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Modal Page Field Helper.
 */
class ModalPageFieldHelper {

  /**
   * Get Field Role.
   */
  public function getFieldRole() {

    // @codingStandardsIgnoreStart
    $label = t('Who can access this Modal');
    $description = t('If no role is selected this Modal will be visible to everyone.');
    // @codingStandardsIgnoreEnd

    $fieldRoles = BaseFieldDefinition::create('list_string');
    $fieldRoles->setLabel($label);
    $fieldRoles->setSettings(['allowed_values' => user_role_names()]);
    $fieldRoles->setDescription($description);
    $fieldRoles->setRequired(FALSE);
    $fieldRoles->setCardinality(-1);
    $fieldRoles->setDisplayOptions('form', [
      'type' => 'options_buttons',
      'weight' => 6,
    ]);
    $fieldRoles->setDisplayConfigurable('form', TRUE);

    return $fieldRoles;
  }

}
