<?php

namespace Drupal\news_formatters\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Plugin implementation of the 'news entity reference label' formatter.
 *
 * @FieldFormatter(
 *   id = "news_entity_reference_label",
 *   label = @Translation("News Categories Label"),
 *   description = @Translation("Display the label of the referenced entities."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class NewsFormatterCategories extends EntityReferenceLabelFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
        'class' => '',
      ) + parent::defaultSettings();
  }
  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);
    $elements['class'] = array(
      '#title' => t('Additional class on wrapper'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('class'),
    );

    return $elements;
  }

  /**
   * Get user categories.
   */
  public static function getUserCategory() {
    return news_core_get_user_home_feed();
  }

  /**
   * Check not chosen category.
   */
  public function isAllowCategory($id) {
    $categories = self::getUserCategory();

    return isset($categories[$id]) ? FALSE : TRUE;
  }

  public function createLink($id) {
    $url = Url::fromUserInput('#', array(
      'attributes' => array(
        'data-item' => $id
      ),
    ));

    return array(
      '#type' => 'link',
      '#title' => new FormattableMarkup('<i class="fa fa-plus" aria-hidden="true"></i>',array()),
      '#url' => $url,
      '#attributes' => array(
        'class' => array('add-category-btn'),
        'title' => t('Add news category to Home feed'),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $field_name = news_core_get_module_config('news_core_base_field');
    $current_entity = $items->getEntity();

    if (!$current_entity->hasField($field_name)) {
      return parent::viewElements($items, $langcode);
    }

    $elements = array();
    $output_as_link = $this->getSetting('link');
    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {

      $label = $entity->label();
      // If the link is to be displayed and the entity has a uri, display a
      // link.
      if ($output_as_link && !$entity->isNew()) {
        try {
          $uri = $entity->urlInfo();
        }
        catch (UndefinedLinkTemplateException $e) {
          // This exception is thrown by \Drupal\Core\Entity\Entity::urlInfo()
          // and it means that the entity type doesn't have a link template nor
          // a valid "uri_callback", so don't bother trying to output a link for
          // the rest of the referenced entities.
          $output_as_link = FALSE;
        }
      }

      if ($output_as_link && isset($uri) && !$entity->isNew()) {

        if ($this->isAllowCategory($entity->id())) {
          $elements[$delta][] = $this->createLink($entity->id());
        }

        $elements[$delta][] = [
          '#type' => 'link',
          '#title' => $label,
          '#url' => $uri,
          '#options' => $uri->getOptions(),
        ];

        if (!empty($items[$delta]->_attributes)) {
          $elements[$delta]['#options'] += array('attributes' => array());
          $elements[$delta]['#options']['attributes'] += $items[$delta]->_attributes;
          // Unset field item attributes since they have been included in the
          // formatter output and shouldn't be rendered in the field template.
          unset($items[$delta]->_attributes);
        }
      }
      else {
        $elements[$delta] = array('#plain_text' => $label);
      }
      $elements[$delta]['#cache']['tags'] = $entity->getCacheTags();
    }

    return array(
      '#theme' => 'item_list',
      '#items' => $elements,
      '#attributes' => array(
        'class' => explode(',', $this->getSetting('class'))
      )
    );
  }

}
