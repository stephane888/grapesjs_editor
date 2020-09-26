<?php

namespace Drupal\grapesjs_editor\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RendererInterface;
use Drupal\grapesjs_editor\Services\BlockManager;
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
   * BlockController constructor.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\grapesjs_editor\Services\BlockManager $block_manager
   *   The block manager.
   */
  public function __construct(RendererInterface $renderer, BlockManager $block_manager) {
    $this->renderer = $renderer;
    $this->blockManager = $block_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer'),
      $container->get('grapesjs_editor.block_manager')
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

}
