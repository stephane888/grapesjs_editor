<?php

namespace Drupal\grapesjs_editor\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Defines a class to generate dynamic routes.
 */
class BlockRoutes {

  /**
   * Builds dynamic routes.
   *
   * @return \Symfony\Component\Routing\RouteCollection
   *   The route collection.
   */
  public function routes() {
    $route_collection = new RouteCollection();
    $defaults = [
      '_controller' => '\Drupal\grapesjs_editor\Controller\BlockController::field',
    ];
    $requirements = [
      '_permission' => 'use text format grapesjs_editor',
    ];
    $paths = [
      'grapesjs_editor.get_field_by_[bundle]_type' => '/grapesjs-editor/blocks/[bundle]/{[bundle]_type}/field',
      'grapesjs_editor.get_field_by_[bundle]' => '/grapesjs-editor/blocks/[bundle]/{[bundle]_type}/{[bundle]}/field',
    ];
    $bundles = [
      'block_content',
      'node',
    ];

    foreach ($bundles as $bundle) {
      foreach ($paths as $name => $path) {
        $options = [
          'parameters' => [
            $bundle . '_type' => ['type' => 'entity:' . $bundle . '_type']
          ],
        ];
        if ($pos = strpos($path, '{[bundle]}')) {
          $options['parameters'][$bundle] = ['type' => 'entity:' . $bundle];
        }

        $name = str_replace('[bundle]', $bundle, $name);
        $path = str_replace('[bundle]', $bundle, $path);
        $route = new Route($path, $defaults, $requirements, $options);
        $route_collection->add($name, $route);
      }
    }

    return $route_collection;
  }

}
