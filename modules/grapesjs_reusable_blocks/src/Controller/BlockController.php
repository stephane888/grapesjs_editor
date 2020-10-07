<?php

namespace Drupal\grapesjs_reusable_blocks\Controller;

use Drupal\block_content\Entity\BlockContent;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for Block routes.
 */
class BlockController extends ControllerBase {

  /**
   * Returns a Json response with the block data.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The Json response.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *   Thrown if the entity is invalid.
   */
  public function createBlock(Request $request) {
    if ($title = $request->request->get('title')) {
      $title = Html::escape($title);
      $body = Xss::filter($request->request->get('body'), array_merge(Xss::getAdminTagList(), [
        'style',
        'drupal-block',
        'drupal-field',
      ]));
      /* @var \Drupal\block_content\Entity\BlockContent $block */
      $block = BlockContent::create(['type' => 'grapesjs_block']);
      $block->setInfo($title);
      $block->set('body', ['value' => $body, 'format' => 'grapesjs_editor']);
      $block->enforceIsNew();

      $violations = $block->validate();
      if ($violations->count() === 0) {
        $block->save();
        return new JsonResponse([
          'id' => 'block_content:' . $block->uuid(),
          'label' => $block->label(),
        ], Response::HTTP_CREATED);
      }

      return new JsonResponse($violations[0]->getMessage(), Response::HTTP_NOT_ACCEPTABLE);
    }

    return new JsonResponse($this->t('Block name is required'), Response::HTTP_NOT_ACCEPTABLE);
  }

}
