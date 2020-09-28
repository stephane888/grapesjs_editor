<?php

namespace Drupal\grapesjs_editor\Services;

use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Defines a class to manage field object.
 */
class FieldManager {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The node type.
   *
   * @var \Drupal\node\NodeTypeInterface|null
   */
  protected $nodeType;

  /**
   * The node.
   *
   * @var \Drupal\node\NodeInterface|null
   */
  protected $node;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * FieldManager constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager, RouteMatchInterface $route_match, RendererInterface $renderer) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->routeMatch = $route_match;
    $this->renderer = $renderer;

    if ($node = $this->routeMatch->getParameter('node')) {
      /* @var \Drupal\node\NodeInterface $node */
      $this->nodeType = $node->get('type')->entity;
      $this->node = $node;
    }
    else {
      if ($node_type = $this->routeMatch->getParameter('node_type')) {
        /* @var \Drupal\node\NodeTypeInterface $node_type */
        $this->nodeType = $node_type;
        $this->node = NULL;
      }
    }
  }

  /**
   * Returns the node type object.
   *
   * @return \Drupal\node\NodeTypeInterface|null
   *   The node type.
   */
  public function getNodeType() {
    return $this->nodeType;
  }

  /**
   * Returns the node object.
   *
   * @return \Drupal\node\NodeInterface|null
   *   The node.
   */
  public function getNode() {
    return $this->node;
  }

  /**
   * Returns fields definitions without restricted fields.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface[]
   *   The field definitions array.
   */
  public function getFields() {
    // Get field definitions.
    $definitions = $this->entityFieldManager->getFieldDefinitions('node', $this->nodeType->id());
    unset($definitions['body']);

    return array_filter($definitions, function ($definition) {
      return $definition->getDisplayOptions('view');
    });
  }

  /**
   * Returns the field if access is allowed.
   *
   * @param string $name
   *   The field name.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface|null
   *   The field object or null if field is not found or not accessible.
   */
  public function getField(string $name) {
    $field_definitions = $this->entityFieldManager->getFieldDefinitions('node', $this->nodeType->id());

    if (!empty($this->node) && $this->node->hasField($name)) {
      /* @var \Drupal\Core\Field\FieldItemList $field_item_list */
      $field_item_list = $this->node->get($name);
      $access = $field_item_list->access();

      if (($access instanceof AccessResultInterface && !$access->isForbidden()) || $access === TRUE) {
        return $field_definitions[$name];
      }
    }
    else {
      if (!empty($field_definitions[$name])) {
        return $field_definitions[$name];
      }
    }

    return NULL;
  }

  /**
   * Gets and renders the field.
   *
   * @param string $name
   *   The field name.
   *
   * @return array|\Drupal\Component\Render\MarkupInterface|string|string[]
   *   The field render markup.
   *
   * @throws \Exception
   *   Thrown if renderer failed.
   */
  public function renderFieldByName(string $name) {
    if ($field = $this->getField($name)) {
      return $this->renderField($field);
    }

    return '';
  }

  /**
   * Renders the field.
   *
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field to render.
   *
   * @return \Drupal\Component\Render\MarkupInterface|null
   *   The field render markup.
   *
   * @throws \Exception
   *   Thrown if renderer failed.
   */
  public function renderField(FieldDefinitionInterface $field_definition) {
    if (!empty($this->node) && $this->node->hasField($field_definition->getName())) {
      /* @var \Drupal\Core\Field\FieldItemList $field_item_list */
      $field_item_list = $this->node->get($field_definition->getName());
      $access = $field_item_list->access();

      if (($access instanceof AccessResultInterface && !$access->isForbidden()) || $access === TRUE) {
        $render = $field_item_list->view();
        return $this->renderer->render($render);
      }
    }

    return NULL;
  }

}
