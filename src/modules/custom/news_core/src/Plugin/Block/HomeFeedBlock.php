<?php

namespace Drupal\news_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Provides a 'NewsLine' Block
 *
 * @Block(
 *   id = "home_feed_block",
 *   admin_label = @Translation("Home feed block")
 * )
 */
class HomeFeedBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\news_core\Form\HomeFeedForm');

    return array(
      '#markup' => render($form),
      '#cache' => array(
        'max_age' => 0,
      ),
    );
  }
}