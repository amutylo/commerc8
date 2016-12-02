<?php

namespace Drupal\news_core\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\Core\Link;


class NewsLineForm extends FormBase {

  /**
   * Default vocabulary id.
   */
  public $taxonomy_vid;

  /**
   * Request rout.
   */
  public $request_rout;

  /**
   * {@inheritdoc}
   */
  public function __construct() {
    $this->taxonomy_vid = NEWS_CORE_TAXONOMY_VID;
    $this->request_rout = \Drupal::routeMatch();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
   return 'news_line_form';
  }

  /**
   * Get vocabulary terms.
   */
  public function getTaxonomyTerms() {
    $container = \Drupal::getContainer();
    return $container->get('entity.manager')->getStorage('taxonomy_term')->loadTree($this->taxonomy_vid);
  }

  /**
   * Create options list.
   */
  public function getOptions() {
    $options = [];
    $container = \Drupal::getContainer();
    if ($terms = $this->getTaxonomyTerms()) {
      if (!empty($terms)) {
        foreach ($terms as $term) {
          $term = $container->get('entity.manager')->getStorage('taxonomy_term')->load($term->tid);
          if ($term->field_visible->value) {
            $options[$term->id()] = $term->name->value;
          }
        }
      }
    }

    return $options;
  }

  /**
   * Get request category.
   */
  public function getRequestCategory() {
    return \Drupal::request()->query->get('item');
  }

  /**
   * Get user default options.
   */
  public function getDefaultOptions() {
    $defaults = news_core_get_user_home_feed();

    if ($item = $this->getRequestCategory()) {
      $defaults[$item] = $item;
    }

    return $defaults;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    if (!\Drupal::request()->request->get('js') && !\Drupal::request()->request->get('_drupal_ajax')) {
      throw new AccessDeniedHttpException();
    }

    $form['#attributes']['class'][] = 'home-feed-forms';

    $form['container'] = array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => array('customization-popup popup'),
        'id' => 'popup'
      )
    );

    $form['container']['header'] = array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => array('popup-header')
      )
    );

    $form['container']['header']['title'] = array(
      '#markup' => '<h3>' . t('Follow topics') . '</h3>',
    );

    if ($config = $this->config('news_core.settings')) {
      $form['container']['header']['description'] = array(
        '#type' => 'container',
        '#attributes' => array(
          'class' => array('popup-description')
        ),
      );
      $form['container']['header']['description']['description'] = array(
        '#markup' => t('Please, choose topics for your personal newsline (at least @count)', array(
          '@count' => $config->get('news_core_line_form_count')
        )),
      );
    }

    $form['container']['content'] = array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => array('customization-form')
      )
    );

    $form['container']['content']['category'] = array(
      '#type' => 'checkboxes',
      '#options' => $this->getOptions(),
      '#default_value' => $this->getDefaultOptions(),
      '#required' => TRUE,
    );

    $form['container']['submit'] = array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => array('popup-submit-btn')
      )
    );
    $form['container']['submit']['save_news'] = array(
      '#type' => 'submit',
      '#value' => t('Create home feed'),
      '#name' => 'form-submit',
      '#ajax' => array(
        'callback' => '::validateCategoryCount',
        'progress' => ['type' => ''],
      ),
    );

    return $form;
  }

  public function validateCategoryCount(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $categories = array_filter($form_state->getValue('category'));

    if ($config = $this->config('news_core.settings')) {
      $min_count = $config->get('news_core_line_form_count') ? $config->get('news_core_line_form_count') : 0;
      if (count($categories) < $min_count) {
        $response->addCommand(new InvokeCommand('#settings-description-error', 'addClass', array('error')));
      }
      else {
        $path = \Drupal::service('path.current')->getPath();
        $destination = \Drupal::service('path.alias_manager')->getAliasByPath($path);

        if (\Drupal::request()->query->has('destination')) {
          $destination = \Drupal::request()->query->get('destination');
        }

        $options = $this->getOptions();
//        if (($category_id = $this->getRequestCategory()) && isset($options[$category_id])) {
//          drupal_set_message(t('The category <strong>@category_name</strong> was added!', array('@category_name' => $options[$category_id])));
//        }

        $response->addCommand(new RedirectCommand($destination));
      }
    }

    return $response;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($options = $form_state->getValue('category')) {
      news_core_set_cookies(NEWS_CORE_COOKIES_KEY, array_filter($options));
      if (!\Drupal::currentUser()->isAnonymous()) {
        if ($content_field_name = news_core_get_module_config('news_core_user_category_field')) {
          $user = User::load(\Drupal::currentUser()->id());
          $terms = [];
          foreach (array_filter($options) as $tid) {
            $terms[] = array('target_id' => $tid);
          }
          $user->$content_field_name = $terms;
          $user->save();
        }
      }
    }
  }
}