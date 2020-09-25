<?php

namespace Drupal\grapesjs_editor\Plugin\GrapesJSPlugin;

use Drupal\editor\Entity\Editor;
use Drupal\grapesjs_editor\GrapesJSPluginBase;
use Drupal\grapesjs_editor\GrapesJSPluginInterface;

/**
 * Defines the "drupal_basic_blocks" plugin.
 *
 * @GrapesJSPlugin(
 *   id = "drupal_basic_blocks",
 *   label = @Translation("Basic Blocks"),
 *   module = "grapesjs_editor"
 * )
 */
class DrupalBasicBlocks extends GrapesJSPluginBase implements GrapesJSPluginInterface {

  /**
   * {@inheritDoc}
   */
  public function getLibraries(Editor $editor) {
    return [
      'grapesjs_editor/drupal-basic-blocks',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [
      'grapesSettings' => [
        'plugins' => [
          'drupal-basic-blocks',
        ],
      ],
    ];
  }

}
