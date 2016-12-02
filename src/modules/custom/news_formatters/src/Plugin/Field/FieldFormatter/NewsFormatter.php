<?php
/**
 * @file
 * Contains \Drupal\news_formatters\Plugin\field\fieldformatter\newsformatter.
 */

namespace Drupal\news_formatters\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'news_like_link' formatter.
 *
 * @FieldFormatter(
 *   id = "news_like_formatter",
 *   label = @Translation("News-Like Formatter"),
 *   field_types = {
 *     "like_dislike"
 *   }
 * )
 */

class NewsFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#theme' => 'news_formatters',
        '#likes' => $items[$delta]->likes,
        '#dislikes' => $items[$delta]->dislikes,
      ];
    }
    return $elements;
  }
}