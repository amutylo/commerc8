<?php

namespace Drupal\news_core\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Link;


class HomeFeedForm extends NewsLineForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
   return 'home_feed_form';
  }

  /**
   * Is link active.
   */
  public function isActiveLink($tid) {
    if ($this->request_rout->getRouteName() == 'entity.taxonomy_term.canonical') {
      if ($this->request_rout->getRawParameter('taxonomy_term') == $tid) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#attributes']['class'][] = 'home-feed-forms';

    $form['wrapper'] = array(
      '#type' => 'list_container',
      '#attributes' => array(
        'class' => array('nav-wrapper')
      )
    );

    $corner = array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => array('corner-top')
      )
    );

    $home_feed = array(
      '#title' => t('Home feed <i class="fa fa-home" aria-hidden="true"></i>'),
      '#type' => 'link',
      '#url' => Url::fromRoute('<front>'),
      '#attributes' => array(
        'class' => \Drupal::service('path.matcher')->isFrontPage() ? array('is-active') : array()
      )
    );

    $conf_feed = array(
      '#title' => t('<i class="fa fa-cog" aria-hidden="true"></i>'),
      '#type' => 'link',
      '#url' => Url::fromRoute('<front>'),
      '#attributes' => array(
        'class' => array('settings'),
        'id' => 'categories-settings',
        'title' => t('Home feed settings')
      )
    );

    $form['wrapper'][]['element'] = array(
      '#prefix' => render($home_feed) . render($conf_feed) . render($corner),
      '#type' => 'container',
      '#attributes' => array(
        'class' => array('checkbox')
      ),
    );

    $default_category = $this->getDefaultOptions();

    foreach ($this->getOptions() as $id => $title) {
      $form['wrapper'][$id]['category'] = array(
        '#prefix' => Link::createFromRoute($title, 'entity.taxonomy_term.canonical', array('taxonomy_term' => $id), array(
          'attributes' => array(
            'class' => $this->isActiveLink($id) ? array('is-active') : array()
          )))->toString(),
        '#type' => 'container',
        '#attributes' => array(
          'class' => array('checkbox')
        ),
        '#tree' => TRUE,
      );
      $form['wrapper'][$id]['category'][$id] = array(
        '#type' => 'checkbox',
        '#title' => '<i class="fa fa-check" aria-hidden="true"></i>',
        '#title_display' => 'after',
        '#default_value' => isset($default_category[$id]) ? TRUE : FALSE,
        '#array_parents' => array('category')
      );
    }

    if ($config = $this->config('news_core.settings')) {
      $form['description'] = array(
        '#type' => 'container',
        '#attributes' => array(
          'class' => array('settings-description'),
          'id' => 'settings-description-error',
        )
      );

      $form['description']['text'] = array(
        '#markup' => t('You must select at least @count category to create your Home Feed!', array(
          '@count' => $config->get('news_core_line_form_count')
        )),
      );
    }

    $form['actions'] = array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => array('btn-wrapper')
      )
    );

    if (empty($default_category)) {
      $form['feed_popup'] = array(
        '#title' => t('feed'),
        '#type' => 'link',
        '#url' => Url::fromRoute('news_core.news_line_form', [], ['query' => \Drupal::destination()->getAsArray()]),
        '#attributes' => array(
          'class' => array('use-ajax', 'hidden'),
          'id' => 'feed-popup-link',
          'data-dialog-type' => 'modal',
          'data-dialog-options' =>  Json::encode(['width' => 420]),
        )
      );
    }

    $form['actions']['save'] = array(
      '#type' => 'submit',
      '#value' => t('Update Home Feed'),
      '#name' => 'form-submit',
      '#ajax' => array(
        'callback' => '::validateCategoryCount',
        'progress' => ['type' => ''],
      )
    );

    return $form;
  }

}