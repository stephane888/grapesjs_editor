<?php

namespace Drupal\grapesjs_editor\Services;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Defines a class to check if editor must be enable.
 */
class EditorChecker {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * EditorChecker constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(ConfigFactoryInterface $config_factory, RouteMatchInterface $route_match) {
    $this->configFactory = $config_factory;
    $this->routeMatch = $route_match;
  }

  /**
   * Check if bundle is allowed in GrapesJS Editor configuration.
   *
   * @param string $bundle
   *   The node bundle.
   *
   * @return bool
   *   Return TRUE if the node bundle is allowed.
   */
  public function bundleIsAllowed(string $bundle): bool {
    $bundles = $this->configFactory->get('grapesjs_editor.settings')
      ->get('bundles');

    return !empty($bundles[$bundle]);
  }

  /**
   * Check if GrapesJS Editor is enabled for the node form.
   *
   * @return bool
   *   Return TRUE if GrapesJS Editor can be enable on current the node form.
   */
  public function isGrapesJsEditorNodeForm(): bool {
    $route_name = $this->routeMatch->getRouteName();

    if (in_array($route_name, ['node.add', 'entity.node.edit_form'])) {
      $bundle = NULL;

      if ($node = $this->routeMatch->getParameter('node')) {
        /* @var \Drupal\node\NodeInterface $node */
        $bundle = $node->bundle();
      }
      else {
        if ($node_type = $this->routeMatch->getParameter('node_type')) {
          /* @var \Drupal\node\NodeTypeInterface $node_type */
          $bundle = $node_type->id();
        }
      }

      return $bundle && $this->bundleIsAllowed($bundle);
    }

    return FALSE;
  }

}
