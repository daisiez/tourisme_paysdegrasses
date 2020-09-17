<?php
/**
 * @file
 * Contains \Drupal\foundry\Plugin\Setting\foundry\Components\Navbar\NavbarStyle.
 */

namespace Drupal\foundry\Plugin\Setting\foundry\Components\Navbar;

use Drupal\bootstrap\Annotation\BootstrapSetting;
use Drupal\bootstrap\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;

/**
 * The "navbar_style" theme setting.
 *
 * @ingroup plugins_setting
 *
 * @BootstrapSetting(
 *   id = "navbar_style",
 *   type = "select",
 *   title = @Translation("Navbar Styling"),
 *   description = @Translation("Determines where the navbar is positioned on the page."),
 *   defaultValue = "",
 *   groups = {
 *     "components" = @Translation("Components"),
 *     "navbar" = @Translation("Navbar"),
 *   },
 *   empty_option = @Translation("White"),
 *   options = {
 *     "black" = @Translation("Black"),
 *     "transparent" = @Translation("transparent"),
 *   },
 * )
 */
class NavbarStyle extends SettingBase {}
