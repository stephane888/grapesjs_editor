<?php

namespace Drupal\grapesjs_editor\Controller;

use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RendererInterface;
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
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected $blockManager;

  /**
   * BlockController constructor.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\Core\Block\BlockManagerInterface $block_manager
   *   The block manager.
   */
  public function __construct(RendererInterface $renderer, BlockManagerInterface $block_manager) {
    $this->renderer = $renderer;
    $this->blockManager = $block_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer'),
      $container->get('plugin.manager.block')
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
    if ($request->query->get('block-plugin-id')) {
      /* @var \Drupal\Core\Block\BlockBase $plugin_block */
      $plugin_block = $this->blockManager->createInstance($request->query->get('block-plugin-id'));
      $access = $plugin_block->access($this->currentUser());
      if (($access instanceof AccessResultInterface && !$access->isForbidden()) || $access === TRUE) {
        $render = $plugin_block->build();
        $block_render = $this->renderer->render($render);
        return new JsonResponse($block_render);
      }

      return new JsonResponse($this->t('Block access is forbidden'), Response::HTTP_FORBIDDEN);
    }

    return new JsonResponse($this->t('Block not found'), Response::HTTP_NOT_FOUND);
  }

}
