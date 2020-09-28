<?php

namespace Drupal\grapesjs_editor\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\grapesjs_editor\Services\FieldManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a filter to render drupal fields.
 *
 * @Filter(
 *   id = "grapesjs_filter_field",
 *   title = @Translation("GrapesJS - Filter Drupal Field"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class FilterField extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The field manager.
   *
   * @var \Drupal\grapesjs_editor\Services\FieldManager
   */
  protected $fieldManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The result metadata.
   *
   * @var \Drupal\Core\Render\BubbleableMetadata
   */
  protected $metadata;

  /**
   * FilterField constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\grapesjs_editor\Services\FieldManager $field_manager
   *   The field manager.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RendererInterface $renderer, FieldManager $field_manager, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->renderer = $renderer;
    $this->fieldManager = $field_manager;
    $this->currentUser = $current_user;
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
      $container->get('grapesjs_editor.field_manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function process($text, $langcode) {
    $text = preg_replace_callback('#<drupal-field[^>]*></drupal-field>#', [
      $this,
      'renderField',
    ], $text);
    $result = new FilterProcessResult($text);
    $result->addCacheableDependency($this->metadata);

    return $result;
  }

  /**
   * Returns the field render.
   *
   * @param array $match
   *   The custom tag match.
   *
   * @return \Drupal\Component\Render\MarkupInterface|string
   *   The field render.
   *
   * @throws \Exception
   *   Thrown if the plugin definition is invalid.
   */
  protected function renderField(array $match) {
    $html = Html::load($match[0]);
    $tag_elements = $html->getElementsByTagName('drupal-field');
    foreach ($tag_elements as $tag_element) {
      /* @var \DOMElement $tag_element */
      $name = $tag_element->getAttribute('field-name');
      return $this->fieldManager->renderFieldByName($name);
    }

    return '';
  }

}
