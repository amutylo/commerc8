<?php

/**
 * @file
 */

namespace Drupal\news_formatters;

use Drupal\Core\Url;

/**
 * Class NewsFormattersHelper.
 *
 * @package Drupal\news_formatters
 */
class NewsFormattersHelper {

  /**
   * Generate like url, like as src/modules/contrib/like_dislike/src/Plugin/Field/FieldFormatter/LikeDislikeFormatter.php
   *
   * @param $entity
   * @return string
   */
  public function generateLikeUrl($entity) {
    $initial_data = [
      'entity_type' => $entity->getEntityTypeId(),
      'entity_id' => $entity->id(),
      'field_name' => 'field_news_portal_like',
    ];

    $values = $entity->field_news_portal_like->getValue();

    foreach ($entity as $delta => $item) {
      $initial_data['likes'] = $values[0]['likes'];
      $initial_data['dislikes'] = $values[0]['dislikes'];
    }

    $data = base64_encode(json_encode($initial_data));

    $like_url = Url::fromRoute(
      'like_dislike.manager', ['clicked' => 'like', 'data' => $data]
    )->toString();

    $user = \Drupal::currentUser()->id();

    $destination = '';

    if ($user == 0) {
      $path = \Drupal::service('path.current')->getPath();
      $destination = '?like-dislike-redirect=' . $host = \Drupal::request()->getHost() . \Drupal::service('path.alias_manager')->getAliasByPath($path);
    }

    $like_url = $like_url . $destination;

    return  $like_url;
  }

}
