<?php

namespace Drupal\grapesjs_editor\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RendererInterface;
use Drupal\views\Entity\View;
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
   * BlockController constructor.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   */
  public function __construct(RendererInterface $renderer) {
    $this->renderer = $renderer;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer')
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
   */
  public function block(Request $request) {
    switch ($request->query->get('block-type')) {
      case 'view':
        $storage = $this->entityTypeManager()->getStorage('view');
        $view = $storage->load($request->query->get('block-id'));
        if ($view instanceof View && $view->getDisplay($request->query->get('block-display-id'))) {
          $block = views_embed_view($view->id(), $request->query->get('block-display-id'));
          $block_render = $this->renderer->render($block);
          return new JsonResponse($block_render);
        }
    }

    return new JsonResponse($this->t('Block not found'), Response::HTTP_NOT_FOUND);
  }

}
