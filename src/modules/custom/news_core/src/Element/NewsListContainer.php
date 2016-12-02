<?php
/**
 * @file
 * Contains \Drupal\news_core\Element\NewsListContainer.
 */

namespace Drupal\news_core\Element;

use Drupal\Core\Render\Element\RenderElement;
use Drupal\Core\Render\Element;

/**
 * Provides an list element.
 *
 * @RenderElement("list_container")
 */
class NewsListContainer extends RenderElement {
  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#theme' => 'list_container',
      '#label' => 'Default Label',
      '#description' => 'Default Description',
      '#pre_render' => [
        [$class, 'preRenderListContainer'],
      ],
    ];
  }

  /**
   * Prepare the render array for the template.
   */
  public static function preRenderListContainer($element) {
    foreach (Element::children($element) as $key) {
      $element['content'][$key] = $element[$key];
    }
    return $element;
  }
}