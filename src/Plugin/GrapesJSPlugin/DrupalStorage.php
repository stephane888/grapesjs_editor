<?php

namespace Drupal\grapesjs_editor\Plugin\GrapesJSPlugin;

use Drupal\editor\Entity\Editor;
use Drupal\grapesjs_editor\GrapesJSPluginBase;
use Drupal\grapesjs_editor\GrapesJSPluginInterface;

/**
 * Defines the "drupal_storage" plugin.
 *
 * @GrapesJSPlugin(
 *   id = "drupal_storage",
 *   label = @Translation("Storage"),
 *   module = "grapesjs_editor"
 * )
 */
class DrupalStorage extends GrapesJSPluginBase implements GrapesJSPluginInterface {

  /**
   * {@inheritDoc}
   */
  public function getLibraries(Editor $editor) {
    return [
      'grapesjs_editor/drupal-storage',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [
      'grapesSettings' => [
        'storageManager' => ['type' => 'drupal'],
        'plugins' => [
          'drupal-storage',
        ],
      ],
    ];
  }

}
