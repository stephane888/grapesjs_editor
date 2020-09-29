<?php

namespace Drupal\grapesjs_reusable_blocks\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Defines a class to alter routes.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritDoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('block_content.add_form')) {
      $route->setRequirement('_entity_create_access', 'block_content:{block_content_type}');
    }
  }

}
