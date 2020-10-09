<?php

namespace Drupal\grapesjs_editor\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RendererInterface;
use Drupal\grapesjs_editor\Services\BlockManager;
use Drupal\grapesjs_editor\Services\FieldManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for Block routes.
 */
class BlockController extends ControllerBase {

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The block manager.
   *
   * @var \Drupal\grapesjs_editor\Services\BlockManager
   */
  protected $blockManager;

  /**
   * The field manager.
   *
   * @var \Drupal\grapesjs_editor\Services\FieldManager
   */
  protected $fieldManager;

  /**
   * BlockController constructor.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\grapesjs_editor\Services\BlockManager $block_manager
   *   The block manager.
   * @param \Drupal\grapesjs_editor\Services\FieldManager $field_manager
   *   The field manager.
   */
  public function __construct(RendererInterface $renderer, BlockManager $block_manager, FieldManager $field_manager) {
    $this->renderer = $renderer;
    $this->blockManager = $block_manager;
    $this->fieldManager = $field_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer'),
      $container->get('grapesjs_editor.block_manager'),
      $container->get('grapesjs_editor.field_manager')
    );
  }

  /**
   * Returns a Json response with the block render.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The Json response.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *   Thrown if the plugin is invalid.
   */
  public function block(Request $request) {
    if ($plugin_id = $request->query->get('block-plugin-id')) {
      if ($block = $this->blockManager->getBlock($plugin_id)) {
        return new JsonResponse($this->blockManager->renderBlock($block));
      }

      return new JsonResponse($this->t('Block access is forbidden'), Response::HTTP_FORBIDDEN);
    }

    return new JsonResponse($this->t('Block not found'), Response::HTTP_NOT_FOUND);
  }

  /**
   * Returns a Json response with the field render.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The Json response.
   *
   * @throws \Exception
   *   Thrown if renderer failed.
   */
  public function field(Request $request) {
    if ($name = $request->query->get('field-name')) {
      if ($field = $this->fieldManager->getField($name)) {
        if ($render = $this->fieldManager->renderField($field)) {
          return new JsonResponse($render);
        }
        else {
          return new JsonResponse($this->t('Entity must be saved for the preview to be visible.'));
        }
      }

      return new JsonResponse($this->t('Field access is forbidden'), Response::HTTP_FORBIDDEN);
    }

    return new JsonResponse($this->t('Field not found'), Response::HTTP_NOT_FOUND);
  }

}
