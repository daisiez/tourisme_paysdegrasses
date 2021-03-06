<?php

namespace Drupal\colorbox\Commands;

use Drush\Commands\DrushCommands;
use Symfony\Component\Filesystem\Filesystem;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://git.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://git.drupalcode.org/devel/tree/drush.services.yml
 */
class ColorboxCommands extends DrushCommands {

  /**
   * Download and install the Colorbox plugin.
   *
   * @param mixed $path
   *   Optional. A path where to install the Colorbox plugin.
   *   If omitted Drush will use the default location.
   *
   * @command colorbox:plugin
   * @aliases colorboxplugin,colorbox-plugin
   */
  public function download($path = '') {

    $fs = new Filesystem();

    if (empty($path)) {
      $path = DRUPAL_ROOT . '/libraries/colorbox';
    }

    // Create path if it doesn't exist
    // Exit with a message otherwise.
    if (!$fs->exists($path)) {
      $fs->mkdir($path);
    }
    else {
      $this->logger()->notice(dt('Colorbox is already present at @path. No download required.', ['@path' => $path]));
      return;
    }

    // Load the colorbox defined library.
    if ($colorbox_library = \Drupal::service('library.discovery')->getLibraryByName('colorbox', 'colorbox')) {
      // Download the file.
      $client = new Client();
      $destination = tempnam(sys_get_temp_dir(), 'colorbox-tmp');
      try {
        $client->get($colorbox_library['remote'] . '/archive/master.zip', ['save_to' => $destination]);
      }
      catch (RequestException $e) {
        // Remove the directory.
        $fs->remove($path);
        $this->logger()->error(dt('Drush was unable to download the colorbox library from @remote. @exception', [
          '@remote' => $colorbox_library['remote'] . '/archive/master.zip',
          '@exception' => $e->getMessage(),
        ], 'error'));
        return;
      }

      // Move downloaded file.
      $fs->rename($destination, $path . '/colorbox.zip');

      // Unzip the file.
      $zip = new \ZipArchive();
      $res = $zip->open($path . '/colorbox.zip');
      if ($res === TRUE) {
        $zip->extractTo($path);
        $zip->close();
      }
      else {
        // Remove the directory if unzip fails and exit.
        $fs->remove($path);
        $this->logger()->error(dt('Error: unable to unzip colorbox file.', [], 'error'));
        return;
      }

      // Remove the downloaded zip file.
      $fs->remove($path . '/colorbox.zip');

      // Move the file.
      $fs->mirror($path . '/colorbox-master', $path, NULL, ['override' => TRUE]);
      $fs->remove($path . '/colorbox-master');

      // Success.
      $this->logger()->notice(dt('The colorbox library has been successfully downloaded to @path.', [
        '@path' => $path,
      ], 'success'));
    }
    else {
      $this->logger()->error(dt('Drush was unable to load the colorbox library'));
    }
  }

}
