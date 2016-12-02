<?php

/**
 * @file
 * Contains \Drupal\news_formatters\Routing\RouteSubscriber.
 */

namespace Drupal\news_formatters\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * @package Drupal\news_formatters\Routing
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('like_dislike.manager')) {
      $route->setDefaults(array(
        '_controller' => '\Drupal\news_formatters\Controller\NewsFormattersController::newshandler',
      ));
    }
  }

}