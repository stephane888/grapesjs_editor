<?php

namespace Drupal\grapesjs_editor\Plugin\Editor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\editor\Entity\Editor;
use Drupal\editor\Plugin\EditorBase;
use Drupal\grapesjs_editor\PluginManager;
use Drupal\grapesjs_editor\Services\AssetManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a GrapesJS text editor for Drupal.
 *
 * @Editor(
 *   id = "grapesjs_editor",
 *   label = @Translation("GrapesJS Editor"),
 *   supports_content_filtering = TRUE,
 *   supports_inline_editing = FALSE,
 *   is_xss_safe = FALSE,
 *   supported_element_types = {
 *     "textarea"
 *   }
 * )
 */
class GrapesJSEditor extends EditorBase implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The plugin manager service.
   *
   * @var \Drupal\grapesjs_editor\PluginManager
   */
  protected $pluginManager;

  /**
   * The asset manager service.
   *
   * @var \Drupal\grapesjs_editor\Services\AssetManager
   */
  protected $assetManager;

  /**
   * GrapesJSEditor constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Drupal\grapesjs_editor\PluginManager $plugin_manager
   *   The plugin manager service.
   * @param \Drupal\grapesjs_editor\Services\AssetManager $asset_manager
   *   The asset manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountProxyInterface $current_user, PluginManager $plugin_manager, AssetManager $asset_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
    $this->pluginManager = $plugin_manager;
    $this->assetManager = $asset_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('grapesjs_editor.plugin_manager'),
      $container->get('grapesjs_editor.asset_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $editor = $form_state->get('editor');

    $form['plugin_settings'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('GrapesJSEditor plugin settings'),
    ];

    $this->pluginManager->injectPluginSettingsForm($form, $form_state, $editor);
    if (count(Element::children($form['plugins'])) === 0) {
      unset($form['plugins']);
      unset($form['plugin_settings']);
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Remove the plugin settings' vertical tabs state; no need to save that.
    if ($form_state->hasValue('plugins')) {
      $form_state->unsetValue('plugin_settings');
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getJSSettings(Editor $editor) {
    $settings = [
      'grapesSettings' => [
        'plugins' => [],
        'pluginsOpts' => [],
      ],
    ];

    // Get the settings for all enabled plugins.
    $enabled_plugins = array_keys($this->pluginManager->getEnabledPlugins($editor));
    foreach ($enabled_plugins as $plugin_id) {
      $plugin = $this->pluginManager->createInstance($plugin_id);
      $settings = array_merge_recursive($settings, $plugin->getConfig($editor));
    }

    return $settings;
  }

  /**
   * {@inheritDoc}
   */
  public function getLibraries(Editor $editor) {
    $libraries = [
      'grapesjs_editor/editor',
    ];

    // Get the settings for all enabled plugins.
    $enabled_plugins = array_keys($this->pluginManager->getEnabledPlugins($editor));
    foreach ($enabled_plugins as $plugin_id) {
      $plugin = $this->pluginManager->createInstance($plugin_id);
      $libraries = array_merge($libraries, $plugin->getLibraries($editor));
    }

    return $libraries;
  }

}
