<?php
use \Drupal\Core\Routing;
use Drupal\Core\Render\BubbleableMetadata;

/**
 * Implements hook_token_info().
 */
function news_core_token_info() {
  $info['tokens']['view']['search-term'] = array(
    'name' => t('Search terms'),
    'description' => t('The search terms value. Filter identifier must have value "search-term"'),
  );
  return $info;
}

/**
 * Implements hook_tokens().
 */
function news_core_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $url_options = array('absolute' => TRUE);
  if (isset($options['language'])) {
    $url_options['language'] = $options['language'];
  }
  $replacements = array();

  if ($type == 'view' && !empty($data['view'])) {
    /** @var \Drupal\views\ViewExecutable $view */
    $view = $data['view'];

    $bubbleable_metadata->addCacheableDependency($view->storage);
    foreach ($tokens as $name => $original) {
      switch ($name) {
        case 'search-term':
          $search_term = \Drupal::request()->query->get($name);
          $replacements[$original] = (!empty($search_term))? $search_term : 'empty search';
          break;
      }
    }
  }
  return $replacements;
}
