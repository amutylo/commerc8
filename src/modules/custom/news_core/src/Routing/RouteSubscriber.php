<?php
/**
 * @file
 * Contains \Drupal\news_core\Routing\RouteSubscriber.
 */

namespace Drupal\news_core\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
//    if ($route = $collection->get('user.login')) {
//      $route->setDefault('_form', '\Drupal\news_core\Form\NewUserLoginForm');
//    }

  }
}
