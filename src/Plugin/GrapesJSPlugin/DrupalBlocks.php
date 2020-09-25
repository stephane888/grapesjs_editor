<?php

namespace Drupal\grapesjs_editor\Plugin\GrapesJSPlugin;

use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\Context\ContextRepositoryInterface;
use Drupal\Core\Url;
use Drupal\editor\Entity\Editor;
use Drupal\grapesjs_editor\GrapesJSPluginBase;
use Drupal\grapesjs_editor\GrapesJSPluginConfigurableInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the "drupal_blocks" plugin.
 *
 * @GrapesJSPlugin(
 *   id = "drupal_blocks",
 *   label = @Translation("Drupal Blocks"),
 *   module = "grapesjs_editor"
 * )
 */
class DrupalBlocks extends GrapesJSPluginBase implements ContainerFactoryPluginInterface, GrapesJSPluginConfigurableInterface {

  /**
   * The block manager.
   *
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected $blockManager;

  /**
   * The context repository.
   *
   * @var \Drupal\Core\Plugin\Context\ContextRepositoryInterface
   */
  protected $contextRepository;

  /**
   * DrupalBlocks constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Block\BlockManagerInterface $block_manager
   *   The block manager.
   * @param \Drupal\Core\Plugin\Context\ContextRepositoryInterface $context_repository
   *   The context repository.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, BlockManagerInterface $block_manager, ContextRepositoryInterface $context_repository) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->blockManager = $block_manager;
    $this->contextRepository = $context_repository;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.block'),
      $container->get('context.repository')
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
    $plugin_blocks = $this->getPluginBlocks();

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
    $plugin_blocks = $this->getPluginBlocks();
    $groups = $this->blockManager->getGroupedDefinitions($plugin_blocks);

    foreach ($groups as $key => $blocks) {
      $group_reference = preg_replace('@[^a-z0-9-]+@', '_', strtolower($key));
      $form['allowed_blocks'][$group_reference] = [
        '#type' => 'fieldset',
        '#title' => $key,
      ];

      foreach ($blocks as $plugin_id => $definition) {
        $form['allowed_blocks'][$group_reference][$plugin_id] = [
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
    $plugin_blocks = $this->getPluginBlocks();
    $groups = $this->blockManager->getGroupedDefinitions($plugin_blocks);

    foreach ($groups as $key => $blocks) {
      $group_reference = preg_replace('@[^a-z0-9-]+@', '_', strtolower($key));
      $settings += $form_state->getValue([
        'editor',
        'settings',
        'plugins',
        'drupal_blocks',
        'allowed_blocks',
        $group_reference,
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
