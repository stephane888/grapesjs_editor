<?php

namespace Drupal\grapesjs_reusable_blocks\Plugin\GrapesJSPlugin;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\editor\Entity\Editor;
use Drupal\grapesjs_editor\GrapesJSPluginBase;
use Drupal\grapesjs_editor\GrapesJSPluginInterface;
use Drupal\grapesjs_editor\Services\BlockManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the "drupal_reusable_blocks" plugin.
 *
 * @GrapesJSPlugin(
 *   id = "drupal_reusable_blocks",
 *   label = @Translation("Reusable Blocks"),
 *   module = "grapesjs_reusable_blocks"
 * )
 */
class DrupalReusableBlocks extends GrapesJSPluginBase implements ContainerFactoryPluginInterface, GrapesJSPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

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
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\grapesjs_editor\Services\BlockManager $block_manager
   *   The block manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, BlockManager $block_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
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
      $container->get('entity_type.manager'),
      $container->get('grapesjs_editor.block_manager'),
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getLibraries(Editor $editor) {
    return [
      'grapesjs_reusable_blocks/drupal-reusable-blocks',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    $blocks = [];
    $custom_blocks = $this->entityTypeManager->getStorage('block_content')
      ->loadByProperties(['type' => 'grapesjs_block']);

    foreach ($custom_blocks as $block) {
      $blocks[] = [
        'label' => $block->label(),
        'plugin_id' => 'block_content:' . $block->uuid(),
      ];
    }

    return [
      'grapesSettings' => [
        'plugins' => [
          'drupal-reusable-blocks',
        ],
        'pluginsOpts' => [
          'drupal-reusable-blocks' => [
            'block_create_route' => Url::fromRoute('grapesjs_reusable_blocks.create_block')
              ->toString(),
            'block_route' => Url::fromRoute('grapesjs_editor.get_block')
              ->toString(),
            'blocks' => $blocks,
          ],
        ],
      ],
    ];
  }

}
