<?php

namespace Drupal\grapesjs_editor\Plugin\GrapesJSPlugin;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\editor\Entity\Editor;
use Drupal\grapesjs_editor\GrapesJSPluginBase;
use Drupal\grapesjs_editor\GrapesJSPluginInterface;
use Drupal\grapesjs_editor\Services\FieldManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the "drupal_fields" plugin.
 *
 * @GrapesJSPlugin(
 *   id = "drupal_fields",
 *   label = @Translation("Drupal Fields"),
 *   weight = 10,
 *   module = "grapesjs_editor"
 * )
 */
class DrupalFields extends GrapesJSPluginBase implements ContainerFactoryPluginInterface, GrapesJSPluginInterface {

  /**
   * The field manager service.
   *
   * @var \Drupal\grapesjs_editor\Services\FieldManager
   */
  private $fieldManager;

  /**
   * DrupalFields constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\grapesjs_editor\Services\FieldManager $field_manager
   *   The field manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FieldManager $field_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->fieldManager = $field_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('grapesjs_editor.field_manager')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getLibraries(Editor $editor) {
    return [
      'grapesjs_editor/drupal-fields',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    $fields = [];
    $field_definitions = $this->fieldManager->getFields();

    foreach ($field_definitions as $name => $definition) {
      $fields[] = [
        'label' => $definition->getLabel(),
        'name' => $name,
      ];
    }

    if ($node = $this->fieldManager->getNode()) {
      $route_name = 'grapesjs_editor.get_field_by_node';
      $route_parameters['node_type'] = $this->fieldManager->getNodeType()->id();
      $route_parameters['node'] = $node->id();
    }
    else {
      $route_name = 'grapesjs_editor.get_field_by_node_type';
      $route_parameters['node_type'] = $this->fieldManager->getNodeType()->id();
    }

    return [
      'grapesSettings' => [
        'plugins' => [
          'drupal-fields',
        ],
        'pluginsOpts' => [
          'drupal-fields' => [
            'field_route' => Url::fromRoute($route_name, $route_parameters)
              ->toString(),
            'fields' => $fields,
          ],
        ],
      ],
    ];
  }

}
