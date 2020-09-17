<?php
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Theme\ThemeSettings;
use Drupal\system\Form\ThemeSettingsForm;
use Drupal\Core\Form;

function foundry_form_system_theme_settings_alter(&$form, Drupal\Core\Form\FormStateInterface $form_state) {
  $form['st_settings'] = array(
        '#type' => 'fieldset',
        '#title' => t('Foundy Theme Settings'),
        '#collapsible' => true,
        '#collapsed' => true,
    );

   
  
  // Theme Color
  $form['st_settings']['tabs']['theme_menu_config'] = array(
    '#type' => 'fieldset',
    '#title' => t('menu setting'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  
  $form['st_settings']['tabs']['theme_menu_config']['theme_menu'] = array(
    '#type' => 'select',
    '#title' => t('Menu Type'),
    '#default_value' => theme_get_setting('theme_menu','foundry'),
    '#options'  => array(
        'menuwhite'              => t('White - Default'),
        'menudark'           => t('Dark')
    ),
  );
  
  $form['st_settings']['tabs']['theme_menu_config']['menu_transparant'] = array(
    '#type' => 'textfield',
    '#title' => t('Menu transparent'),
    '#default_value' => theme_get_setting('menu_transparant','foundry'),
	'#description' => 'Enter the absoulute page URLs that you want to have the transparent menu, separating by commas. For example, "/node/56,/node/78,/node/99"',
  );
  
  // Color
  $form['st_settings']['tabs']['them_color_config'] = array(
    '#type' => 'fieldset',
    '#title' => t('Color setting'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  
  $form['st_settings']['tabs']['them_color_config']['theme_color'] = array(
    '#type' => 'select',
    '#title' => t('Color'),
    '#default_value' => theme_get_setting('theme_color'),
    '#options'  => array(
        'default'           => t('Default'),
        'red'              => t('Red'),
        'orange'             => t('Orange'),
        'purple'           => t('Purple'),
        'chipotle'       => t('Chipotle'),
        'fire'            => t('Fire'),
        'gunmetal'            => t('Gunmetal'),
        'hyperblue'             => t('Hyperblue'),
        'nature'           => t('Nature'),
        'navy'             => t('Navy'),
        'nearblack'            => t('Nearblack'),
        'offyellow'            => t('Offyellow'),
        'rose'            => t('Rose'),
        'starup'             => t('Starup')
    ),
  );
  
}

