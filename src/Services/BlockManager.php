<?php

namespace Drupal\grapesjs_editor\Services;

use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Plugin\Context\ContextRepositoryInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Defines a class to manage block object.
 */
class BlockManager {

  /**
   * The block manager service.
   *
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected $blockManager;

  /**
   * The context repository service.
   *
   * @var \Drupal\Core\Plugin\Context\ContextRepositoryInterface
   */
  protected $contextRepository;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * BlockManager constructor.
   *
   * @param \Drupal\Core\Block\BlockManagerInterface $block_manager
   *   The block manager service.
   * @param \Drupal\Core\Plugin\Context\ContextRepositoryInterface $context_repository
   *   The context repository service.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   */
  public function __construct(BlockManagerInterface $block_manager, ContextRepositoryInterface $context_repository, AccountProxyInterface $current_user, RendererInterface $renderer) {
    $this->blockManager = $block_manager;
    $this->contextRepository = $context_repository;
    $this->currentUser = $current_user;
    $this->renderer = $renderer;
  }

  /**
   * Returns restricted blocks.
   *
   * @return string[]
   *   The restricted block list.
   *
   * @see https://www.drupal.org/node/2938129
   */
  public function getRestrictedBlocks() {
    return ['broken', 'page_title_block', 'system_main_block'];
  }

  /**
   * Returns block definitions without restricted blocks.
   *
   * @return array
   *   The block definitions array.
   */
  public function getBlocks() {
    // Get blocks definition.
    $definitions = $this->blockManager->getFilteredDefinitions('block_ui', $this->contextRepository->getAvailableContexts());
    foreach ($this->getRestrictedBlocks() as $plugin_id) {
      if (isset($definitions[$plugin_id])) {
        unset($definitions[$plugin_id]);
      }
    }

    return $definitions;
  }

  /**
   * Returns grouped block definitions.
   *
   * @return array
   *   The grouped block definitions array.
   */
  public function getGroupedBlocks() {
    $definitions = $this->getBlocks();
    $definitions = $this->blockManager->getGroupedDefinitions($definitions);
    $keys = array_map(function ($key) {
      return preg_replace('@[^a-z0-9-]+@', '_', strtolower($key));
    }, array_keys($definitions));

    return array_combine($keys, array_values($definitions));
  }

  /**
   * Returns the block if access is allowed.
   *
   * @param string $id
   *   The block ID.
   *
   * @return \Drupal\Core\Block\BlockBase|null
   *   The block object or null if block is not found or not accessible.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *   Thrown if the plugin definition is invalid.
   */
  public function getBlock(string $id) {
    /* @var BlockBase $block */
    $block = $this->blockManager->createInstance($id);
    $access = $block->access($this->currentUser);

    if (($access instanceof AccessResultInterface && !$access->isForbidden()) || $access === TRUE) {
      return $block;
    }

    return NULL;
  }

  /**
   * Gets and renders the block.
   *
   * @param string $id
   *   The block ID.
   *
   * @return \Drupal\Component\Render\MarkupInterface|string
   *   The block render markup.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *   Thrown if the plugin definition is invalid.
   * @throws \Exception
   *   Thrown if the plugin definition is invalid.
   */
  public function renderBlockById(string $id) {
    if ($block = $this->getBlock($id)) {
      return $this->renderBlock($block);
    }

    return '';
  }

  /**
   * Renders the block.
   *
   * @param \Drupal\Core\Block\BlockBase $block
   *   The block to render.
   *
   * @return \Drupal\Component\Render\MarkupInterface
   *   The block render markup.
   *
   * @throws \Exception
   *   Thrown if the plugin definition is invalid.
   */
  public function renderBlock(BlockBase $block) {
    $render = $block->build();
    return $this->renderer->render($render);
  }

}
