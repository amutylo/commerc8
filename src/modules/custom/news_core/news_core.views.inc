<?php

/**
 * @file
 * Provide views data for news_core.module.
 */

/**
 * Implements hook_views_data_alter().
 */
function news_core_views_data_alter(&$data) {
  if ($field_name = news_core_get_module_config('news_core_base_field')) {
    $data['node_field_data']['user_category'] = array(
      'title' => t('User category'),
      'help' => t('Display nodes sorted by user home feed.'),
      'argument' => array(
        'field' => 'field_user_home_feed',
        'id' => 'news_core_home_feed',
      ),
    );
  }
}
