<?php

namespace Drupal\grapesjs_editor\Plugin\GrapesJSPlugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\editor\Entity\Editor;
use Drupal\grapesjs_editor\GrapesJSPluginBase;
use Drupal\grapesjs_editor\GrapesJSPluginConfigurableInterface;
use Drupal\grapesjs_editor\Services\BlockManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the "drupal_blocks" plugin.
 *
 * @GrapesJSPlugin(
 *   id = "drupal_blocks",
 *   label = @Translation("Drupal Blocks"),
 *   weight = 20,
 *   module = "grapesjs_editor"
 * )
 */
class DrupalBlocks extends GrapesJSPluginBase implements ContainerFactoryPluginInterface, GrapesJSPluginConfigurableInterface {

  /**
   * The block manager.
   *
   * @var \Drupal\grapesjs_editor\Services\BlockManager
   */
  protected $blockManager;

  /**
   * DrupalBlocks constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\grapesjs_editor\Services\BlockManager $block_manager
   *   The block manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, BlockManager $block_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->blockManager = $block_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('grapesjs_editor.block_manager'),
    );
  }

  /**
   * Returns the block list.
   *
   * @return array
   *   The block list.
   */
  protected function getPluginBlocks() {
    // For page_title_block : https://www.drupal.org/node/2938129.
    $restricted_blocks = ['broken', 'page_title_block', 'system_main_block'];

    // Get blocks definition.
    $definitions = $this->blockManager->getFilteredDefinitions('block_ui', $this->contextRepository->getAvailableContexts());
    foreach ($restricted_blocks as $plugin_id) {
      if (isset($definitions[$plugin_id])) {
        unset($definitions[$plugin_id]);
      }
    }

    return $definitions;
  }

  /**
   * {@inheritDoc}
   */
  public function getLibraries(Editor $editor) {
    return [
      'grapesjs_editor/drupal-blocks',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    $blocks = [];
    $settings = $editor->getSettings();
    $allowed_blocks = $settings['plugins']['drupal_blocks'] ?? [];
    $plugin_blocks = $this->blockManager->getBlocks();

    foreach ($allowed_blocks as $plugin_id => $allowed) {
      if ($allowed) {
        $blocks[] = [
          'label' => $plugin_blocks[$plugin_id]['admin_label'],
          'plugin_id' => $plugin_id,
        ];
      }
    }

    return [
      'grapesSettings' => [
        'canvas' => [
          'styles' => [
            Url::fromRoute('<front>', [], ['absolute' => TRUE])
              ->toString() . drupal_get_path('module', 'grapesjs_editor') . '/libraries/css/plugins/drupal-blocks/canvas.css',
          ],
        ],
        'plugins' => [
          'drupal-blocks',
        ],
        'pluginsOpts' => [
          'drupal-blocks' => [
            'block_route' => Url::fromRoute('grapesjs_editor.get_block')
              ->toString(),
            'blocks' => $blocks,
          ],
        ],
      ],
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    $settings = $editor->getSettings();
    $config = $settings['plugins']['drupal_blocks'] ?? [];
    $groups = $this->blockManager->getGroupedBlocks();

    foreach ($groups as $key => $blocks) {
      $form['allowed_blocks'][$key] = [
        '#type' => 'fieldset',
        '#title' => $key,
      ];

      foreach ($blocks as $plugin_id => $definition) {
        $form['allowed_blocks'][$key][$plugin_id] = [
          '#title' => $definition['admin_label'],
          '#type' => 'checkbox',
          '#default_value' => !empty($config[$plugin_id]),
        ];
      }
    }

    $form['allowed_blocks']['#element_validate'][] = [
      $this,
      'validateAllowedBlocksSettings',
    ];

    return $form;
  }

  /**
   * Validation handler for the "allowed_blocks" element in settingsForm().
   *
   * @param array $element
   *   The render element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function validateAllowedBlocksSettings(array $element, FormStateInterface $form_state) {
    $settings = [];
    $groups = $this->blockManager->getGroupedBlocks();

    foreach ($groups as $key => $blocks) {
      $settings += $form_state->getValue([
        'editor',
        'settings',
        'plugins',
        'drupal_blocks',
        'allowed_blocks',
        $key,
      ]);
    }

    $form_state->unsetValue([
      'editor',
      'settings',
      'plugins',
      'drupal_blocks',
    ]);
    $form_state->setValue([
      'editor',
      'settings',
      'plugins',
      'drupal_blocks',
    ], $settings);
  }

}
