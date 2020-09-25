<?php

namespace Drupal\grapesjs_editor;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\editor\Entity\Editor;
use Drupal\grapesjs_editor\Annotation\GrapesJSPlugin;

/**
 * Defines a class to manage GrapesJS plugins.
 */
class PluginManager extends DefaultPluginManager {

  /**
   * PluginManager constructor.
   *
   * @param \Traversable $namespaces
   *   The namespaces.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/GrapesJSPlugin', $namespaces, $module_handler, GrapesJSPluginInterface::class, GrapesJSPlugin::class);
    $this->alterInfo('grapesjs_plugin_info');
    $this->setCacheBackend($cache_backend, 'grapesjs_plugins');
  }

  /**
   * Retrieves enabled plugins, keyed by plugin ID.
   *
   * @param \Drupal\editor\Entity\Editor $editor
   *   A configured text editor object.
   *
   * @return array
   *   A list of the enabled GrapesJS plugins, with the plugin IDs as keys and
   *   the Drupal root-relative plugin files as values.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *   Thrown if the plugin can't be found.
   */
  public function getEnabledPlugins(Editor $editor) {
    $plugins = array_keys($this->getDefinitions());
    $enabled_plugins = [];

    foreach ($plugins as $plugin_id) {
      $plugin = $this->createInstance($plugin_id);
      $enabled_plugins[$plugin_id] = $plugin;
    }

    // Always return plugins in the same order.
    asort($enabled_plugins);

    return $enabled_plugins;
  }

  /**
   * Injects the GrapesJS plugins settings forms as a vertical tabs subform.
   *
   * @param array &$form
   *   A reference to an associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param \Drupal\editor\Entity\Editor $editor
   *   A configured text editor object.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *   Thrown if the plugin can't be found.
   */
  public function injectPluginSettingsForm(array &$form, FormStateInterface $form_state, Editor $editor) {
    $definitions = $this->getDefinitions();

    foreach (array_keys($definitions) as $plugin_id) {
      $plugin = $this->createInstance($plugin_id);
      if ($plugin instanceof GrapesJSPluginConfigurableInterface) {
        $plugin_settings_form = [];
        $form['plugins'][$plugin_id] = [
          '#type' => 'details',
          '#title' => $definitions[$plugin_id]['label'],
          '#open' => TRUE,
          '#group' => 'editor][settings][plugin_settings',
          '#attributes' => [
            'data-grapesjs-editor-plugin-id' => $plugin_id,
          ],
        ];

        $form['plugins'][$plugin_id] += $plugin->settingsForm($plugin_settings_form, $form_state, $editor);
      }
    }
  }

}
