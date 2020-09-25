<?php

namespace Drupal\grapesjs_editor\Plugin\GrapesJSPlugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\editor\Entity\Editor;
use Drupal\grapesjs_editor\GrapesJSPluginBase;
use Drupal\grapesjs_editor\GrapesJSPluginConfigurableInterface;
use Drupal\grapesjs_editor\Services\AssetManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the "drupal_asset" plugin.
 *
 * @GrapesJSPlugin(
 *   id = "drupal_asset",
 *   label = @Translation("Asset"),
 *   module = "grapesjs_editor"
 * )
 */
class DrupalAsset extends GrapesJSPluginBase implements ContainerFactoryPluginInterface, GrapesJSPluginConfigurableInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The asset manager service.
   *
   * @var \Drupal\grapesjs_editor\Services\AssetManager
   */
  protected $assetManager;

  /**
   * DrupalAsset constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Drupal\grapesjs_editor\Services\AssetManager $asset_manager
   *   The asset manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountProxyInterface $current_user, AssetManager $asset_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
    $this->assetManager = $asset_manager;
  }

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('grapesjs_editor.asset_manager')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getLibraries(Editor $editor) {
    return [
      'grapesjs_editor/drupal-asset',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    $settings = [];
    if (!empty($editor->getImageUploadSettings()['status']) && $this->currentUser->hasPermission('access files overview')) {
      $settings['grapesSettings'] = [
        'plugins' => [
          'drupal-asset',
        ],
        'assetManager' => [
          'assets' => $this->assetManager->getAssets(),
          'upload' => Url::fromRoute('grapesjs_editor.upload_assets')
            ->toString(),
          'uploadName' => 'files[files]',
        ],
      ];
    }

    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    $form_state->loadInclude('editor', 'admin.inc');
    $form['image_upload'] = editor_image_upload_settings_form($editor);
    $form['image_upload']['#element_validate'][] = [
      $this,
      'validateImageUploadSettings',
    ];
    return $form;
  }

  /**
   * Validation handler for the "image_upload" element in settingsForm().
   *
   * Moves the text editor's image upload settings from the DrupalImage plugin's
   * own settings into $editor->image_upload.
   *
   * @param array $element
   *   The render element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @see \Drupal\editor\Form\EditorImageDialog
   * @see editor_image_upload_settings_form()
   */
  public function validateImageUploadSettings(array $element, FormStateInterface $form_state) {
    $settings = &$form_state->getValue([
      'editor',
      'settings',
      'plugins',
      'drupal_asset',
      'image_upload',
    ]);
    $form_state->get('editor')->setImageUploadSettings($settings);
    $form_state->unsetValue(['editor', 'settings', 'plugins', 'drupal_asset']);
  }

}
