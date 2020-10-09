<?php

namespace Drupal\grapesjs_editor\Services;

use Drupal\block_content\BlockContentTypeInterface;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\node\NodeTypeInterface;

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
   * The entity type.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface|null
   */
  protected $entityType;

  /**
   * The entity.
   *
   * @var \Drupal\Core\Entity\EntityInterface|null
   */
  protected $entity;

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

    if ($block_content = $this->routeMatch->getParameter('block_content')) {
      /* @var \Drupal\block_content\BlockContentInterface $block_content */
      $this->entityType = $block_content->get('type')->entity;
      $this->entity = $block_content;
    }
    else if ($block_content_type = $this->routeMatch->getParameter('block_content_type')) {
      /* @var \Drupal\block_content\BlockContentTypeInterface $block_content_type */
      $this->entityType = $block_content_type;
      $this->entity = NULL;
    }

    if ($node = $this->routeMatch->getParameter('node')) {
      /* @var \Drupal\node\NodeInterface $node */
      $this->entityType = $node->get('type')->entity;
      $this->entity = $node;
    }
    else if ($node_type = $this->routeMatch->getParameter('node_type')) {
      /* @var \Drupal\node\NodeTypeInterface $node_type */
      $this->entityType = $node_type;
      $this->entity = NULL;
    }
  }

  /**
   * Returns the entity type object.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface|null
   *   The entity type.
   */
  public function getEntityType() {
    return $this->entityType;
  }

  /**
   * Returns the entity object.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The entity.
   */
  public function getEntity() {
    return $this->entity;
  }

  /**
   * Returns the field definition array.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface[]
   *   The field definitions.
   */
  protected function getFieldDefinitions() {
    $entity_type_id = $this->entity ? $this->entity->getEntityTypeId() : NULL;
    if (!$entity_type_id && $this->entityType instanceof BlockContentTypeInterface) {
      $entity_type_id = 'block_content';
    }
    if (!$entity_type_id && $this->entityType instanceof NodeTypeInterface) {
      $entity_type_id = 'node';
    }

    return $this->entityFieldManager->getFieldDefinitions($entity_type_id, $this->entityType->id());
  }

  /**
   * Returns fields definitions without restricted fields.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface[]
   *   The field definitions array.
   */
  public function getFields() {
    $definitions = $this->getFieldDefinitions();

    return array_filter($definitions, function ($definition) {
      /* @var \Drupal\Core\Field\BaseFieldDefinition $definition */
      if (in_array($definition->getType(), [
        'text',
        'text_long',
        'text_with_summary',
      ])) {
        return FALSE;
      }

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
    $definitions = $this->getFieldDefinitions();

    if (!empty($this->entity) && $this->entity->hasField($name)) {
      /* @var \Drupal\Core\Field\FieldItemList $field_item_list */
      $field_item_list = $this->entity->get($name);
      $access = $field_item_list->access();

      if (($access instanceof AccessResultInterface && !$access->isForbidden()) || $access === TRUE) {
        return $definitions[$name];
      }
    }
    else {
      if (!empty($definitions[$name])) {
        return $definitions[$name];
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
    if (!empty($this->entity) && $this->entity->hasField($field_definition->getName())) {
      /* @var \Drupal\Core\Field\FieldItemList $field_item_list */
      $field_item_list = $this->entity->get($field_definition->getName());
      $access = $field_item_list->access();

      if (($access instanceof AccessResultInterface && !$access->isForbidden()) || $access === TRUE) {
        $render = $field_item_list->view();
        return $this->renderer->render($render);
      }
    }

    return NULL;
  }

  /**
   * Generate the field route to request field renderer.
   *
   * @return \Drupal\Core\GeneratedUrl|string
   *   The field route.
   */
  public function generateFieldRoute() {
    $bundle = $this->entityType instanceof NodeTypeInterface ? 'node' : 'block_content';
    if ($this->entity) {
      $route_name = 'grapesjs_editor.get_field_by_' . $bundle;
      $route_parameters[$bundle . '_type'] = $this->entity->bundle();
      $route_parameters[$bundle] = $this->entity->id();
    }
    else {
      $route_name = 'grapesjs_editor.get_field_by_' . $bundle . '_type';
      $route_parameters[$bundle . '_type'] = $this->entityType->id();
    }

    return Url::fromRoute($route_name, $route_parameters)
      ->toString();
  }

}
