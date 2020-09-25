<?php

namespace Drupal\grapesjs_editor;

use Drupal\Core\Plugin\PluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines a class for GrapesJS plugins.
 */
abstract class GrapesJSPluginBase extends PluginBase implements GrapesJSPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [];
  }

}
