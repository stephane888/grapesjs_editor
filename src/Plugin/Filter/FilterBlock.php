<?php

namespace Drupal\grapesjs_editor\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Render\RendererInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a filter to render drupal blocks.
 *
 * @Filter(
 *   id = "grapesjs_filter_block",
 *   title = @Translation("GrapesJS - Filter Drupal Block"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class FilterBlock extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The result metadata.
   *
   * @var \Drupal\Core\Render\BubbleableMetadata
   */
  protected $metadata;

  /**
   * FilterBlock constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RendererInterface $renderer, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->renderer = $renderer;
    $this->entityTypeManager = $entity_type_manager;
    $this->metadata = new BubbleableMetadata();
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('renderer'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function process($text, $langcode) {
    $text = preg_replace_callback('#<drupal-block[^>]*></drupal-block>#', [
      $this,
      'renderBlock',
    ], $text);
    $result = new FilterProcessResult($text);
    $result->addCacheableDependency($this->metadata);

    return $result;
  }

  /**
   * Returns the block render.
   *
   * @param array $match
   *   The custom tag match.
   *
   * @return \Drupal\Component\Render\MarkupInterface|string
   *   The block render.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   Thrown if the plugin definition is invalid.
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *   Thrown if the plugin can't be found.
   */
  protected function renderBlock(array $match) {
    $html = Html::load($match[0]);
    $tag_elements = $html->getElementsByTagName('drupal-block');
    foreach ($tag_elements as $tag_element) {
      /* @var \DOMElement $tag_element */
      $block_type = $tag_element->getAttribute('block-type');
      $block_id = $tag_element->getAttribute('block-id');
      $display_id = $tag_element->getAttribute('block-display-id');

      $storage = $this->entityTypeManager->getStorage('view');
      /* @var \Drupal\views\Entity\View $view */
      if (($view = $storage->load($block_id)) && $view->getDisplay($display_id)) {
        $this->metadata->addCacheableDependency(BubbleableMetadata::createFromObject($view));

        $block = views_embed_view($view->id(), $display_id);
        return $this->renderer->render($block);
      }
    }

    return '';
  }

}
