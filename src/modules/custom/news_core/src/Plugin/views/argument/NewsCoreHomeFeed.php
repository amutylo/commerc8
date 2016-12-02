<?php

namespace Drupal\news_core\Plugin\views\argument;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\views\Plugin\views\argument\ArgumentPluginBase;
use Drupal\views\Plugin\views\join\JoinPluginBase;

/**
 * Argument handler for user home feed.
 *
 * @ingroup views_argument_handlers
 *
 * @ViewsArgument("news_core_home_feed")
 */
class NewsCoreHomeFeed extends ArgumentPluginBase implements ContainerFactoryPluginInterface {

  public function query($group_by = FALSE) {
    if ($values = news_core_get_user_home_feed($this->argument)) {
      $content_field_name = news_core_get_module_config('news_core_base_field');
      if (!empty($values) && $content_field_name) {
        $params = array(
          'table' => 'node__' . $content_field_name,
          'left_table' => 'node_field_data',
          'left_field' => 'nid',
          'field' => 'entity_id'
        );
        $join = new JoinPluginBase($params, $this->view->id(), array());
        $this->query->addRelationship($content_field_name, $join, 'node');
        $this->query->addWhere(2, $content_field_name . '.' . $content_field_name . '_target_id', $values, 'IN');
      }
    }
  }
}
