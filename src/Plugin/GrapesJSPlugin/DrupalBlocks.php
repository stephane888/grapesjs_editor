<?php

namespace Drupal\grapesjs_editor\Plugin\GrapesJSPlugin;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\editor\Entity\Editor;
use Drupal\grapesjs_editor\GrapesJSPluginBase;
use Drupal\grapesjs_editor\GrapesJSPluginInterface;
use Drupal\views\Entity\View;
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
class DrupalBlocks extends GrapesJSPluginBase implements ContainerFactoryPluginInterface, GrapesJSPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

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
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
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
    $view_storage = $this->entityTypeManager->getStorage('view');
    $views = $view_storage->loadByProperties(['status' => TRUE]);
    $blocks = array_reduce($views, function ($carry, View $view) {
      $displays = $view->get('display');
      foreach ($displays as $display) {
        if ($display['display_plugin'] === 'block' && (!isset($display['display_options']['enabled']) || $display['display_options']['enabled'] !== FALSE)) {
          $carry[] = [
            'type' => 'view',
            'label' => $this->t('View %view - %block', [
              '%view' => $view->label(),
              '%block' => $display['display_title'],
            ]),
            'view_id' => $view->id(),
            'display_id' => $display['id'],
          ];
        }
      }

      return $carry;
    }, $blocks);

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

}
