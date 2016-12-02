<?php
/**
 * @file
 * Contains \Drupal\news_formatters\Plugin\field\fieldformatter\newsformatterhide.
 */

namespace Drupal\news_formatters\Plugin\Field\FieldFormatter;

use Drupal\like_dislike\Plugin\Field\FieldFormatter\LikeDislikeFormatter;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'news_like_hide' formatter.
 *
 * @FieldFormatter(
 *   id = "news_like_hide_formatter",
 *   label = @Translation("News-Like-Hide Formatter"),
 *   field_types = {
 *     "like_dislike"
 *   }
 * )
 */
class NewsFormatterHide extends LikeDislikeFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    $entity = $items->getEntity();
    $initial_data = [
      'entity_type' => $entity->getEntityTypeId(),
      'entity_id' => $entity->id(),
      'field_name' => $items->getFieldDefinition()->getName(),
    ];

    $field_name = $initial_data['field_name'];
    $users = json_decode($entity->$field_name->clicked_by);

    if ($users == NULL) {
      $users = new \stdClass();
      $users->default = 'default';
    }

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#theme' => 'news_formatters_hide',
        '#liked' => news_formatters_is_liked($entity, (object) $initial_data),
        '#likes' => $items[$delta]->likes,
        '#dislikes' => $items[$delta]->dislikes,
        '#like_url' => $elements[$delta]['#like_url'],
        '#dislike_url' => $elements[$delta]['#dislike_url'],
      ];
    }

    return $elements;
  }

}
