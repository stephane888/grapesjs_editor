<?php

namespace Drupal\grapesjs_editor\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a GrapesJSPlugin annotation object.
 *
 * Plugin Namespace: Plugin\GrapesJSPlugin.
 *
 * For a working example,
 * see \Drupal\grapesjs_editor\Plugin\GrapesJSPlugin\DrupalAsset.
 *
 * @see \Drupal\grapesjs_editor\GrapesJSPluginInterface
 * @see \Drupal\grapesjs_editor\GrapesJSPluginBase
 * @see \Drupal\grapesjs_editor\GrapesJSPluginManager
 * @see hook_grapesjs_editor_plugin_info_alter()
 * @see plugin_api
 *
 * @Annotation
 */
class GrapesJSPlugin extends Plugin {

  /**
   * The plugin ID.
   *
   * This MUST match the name of the GrapesJS plugin itself (written in
   * JavaScript). Otherwise GrapesJS will throw JavaScript errors when it runs,
   * because it fails to load this GrapesJS plugin.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the GrapesJS plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
