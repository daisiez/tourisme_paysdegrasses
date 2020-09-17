<?php
/**
 * @file
 * Contains \Drupal\foundry\Plugin\Setting\foundry\General\ThemeStyle\ThemeStyleColor.
 */

namespace Drupal\foundry\Plugin\Setting\foundry\General\ThemeStyle;

use Drupal\bootstrap\Annotation\BootstrapSetting;
use Drupal\bootstrap\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;

/**
 * The "theme_style_color" theme setting.
 *
 * @ingroup plugins_setting
 *
 * @BootstrapSetting(
 *   id = "theme_style_color",
 *   type = "select",
 *   title = @Translation("Theme Color"),
 *   description = @Translation("Determines where the navbar is positioned on the page."),
 *   defaultValue = "",
 *   groups = {
 *     "general" = @Translation("General"),
 *     "themestyle" = @Translation("ThemeStyle"),
 *   },
 *   empty_option = @Translation("Default"),
 *   options = {
 *     "red" = @Translation("Red"),
 *     "orange" = @Translation("Orange"),
 *   },
 * )
 */
class ThemeStyleColor extends SettingBase {}
