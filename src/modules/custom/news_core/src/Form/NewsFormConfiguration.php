<?php

namespace Drupal\news_core\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Field\FieldConfigInterface;

class NewsFormConfiguration extends ConfigFormBase {

  public function __construct(ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory);
  }

  public function getFormId() {
   return 'news_form_configuration';
  }

  public function getStorageTypes() {
    $contentTypes = \Drupal::service('entity.manager')->getStorage('node_type')->loadMultiple();

    $contentTypesList = [];
    foreach ($contentTypes as $contentType) {
      $contentTypesList[$contentType->id()] = $contentType->label();
    }

    return $contentTypesList;
  }

  public function getStorageDateFormats() {
    $dateTypes = \Drupal::service('entity.manager')->getStorage('date_format')->loadmultiple();
    $dateTypesList = [];
    foreach ($dateTypes as $dateType) {
      $dateTypesList[$dateType->id()] = $dateType->label();
    }

    return $dateTypesList;
  }

  public function getReferenceFields($entity_type, $default_type) {
    $entityManager = \Drupal::service('entity.manager');
    $fields = [];

    $items = array_filter(
      $entityManager->getFieldDefinitions($entity_type, $default_type), function ($field_definition) {
        return $field_definition->getType() == 'entity_reference' && $field_definition instanceof FieldConfigInterface;
      }
    );

    foreach ($items as $field) {
      $fields[$field->getName()] = $field->getLabel();
    }

    return $fields;
  }

  /**
   * Gets the configuration names that will be editable.
   */
  protected function getEditableConfigNames() {
    return [
      'news_core.settings'
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('news_core.settings');
    $default_type = $config->get('news_core_base_type') ? $config->get('news_core_base_type') : null ;

    $form['base'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Base settings'),
    );

    $form['base']['news_core_base_type'] = array(
      '#title' => 'Content type',
      '#type' => 'select',
      '#options' => array_merge(array('' => '- NONE -'), $this->getStorageTypes()),
      '#default_value' => $default_type,
      '#required' => TRUE,
      '#ajax' => array(
        'callback' => '::setNewsLineContentType',
      ),
    );

    if ($default_type) {
      $form['base']['news_core_base_field'] = array(
        '#title' => 'Entity reference field',
        '#type' => 'select',
        '#options' => array_merge(array('' => '- NONE -'), $this->getReferenceFields('node', $default_type)),
        '#default_value' => $config->get('news_core_base_field') ? $config->get('news_core_base_field') : array(),
        '#required' => TRUE,
      );

      $form['base']['news_core_line_form_count'] = array(
        '#title' => 'Minimum count of items',
        '#type' => 'textfield',
        '#default_value' => $config->get('news_core_line_form_count') ? $config->get('news_core_line_form_count') : '',
      );

    }

    $form['date'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Date settings'),
    );

    $form['date']['news_core_base_date_format'] = array(
      '#title' => 'Global date format',
      '#type' => 'select',
      '#options' => array_merge(array('' => '- NONE -'), $this->getStorageDateFormats()),
      '#default_value' => $config->get('news_core_base_date_format') ? $config->get('news_core_base_date_format') : array(),
    );

    $form['user'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('User settings'),
    );

    $form['user']['news_core_user_category_field'] = array(
      '#title' => 'User entity reference field',
      '#type' => 'select',
      '#options' => array_merge(array('' => '- NONE -'), $this->getReferenceFields('user', 'user')),
      '#default_value' => $config->get('news_core_user_category_field') ? $config->get('news_core_user_category_field') : array(),
    );

    $form['content'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Content settings'),
    );

    $form['content']['news_core_trim'] = array(
      '#title' => 'Title trim length',
      '#description' => 'Number of words news title to be trimmed of',
      '#type' => 'textfield',
      '#default_value' => $config->get('news_core_trim') ? $config->get('news_core_trim') : '',
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
      '#name' => 'form-submit',
      '#weight' => 1000,
    );

   return $form;
  }

  public function setNewsLineContentType(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $this->config('news_core.settings')
      ->set('news_core_base_type', $form_state->getValue(array('news_core_base_type')))
      ->save();

    $response->addCommand(new RedirectCommand(\Drupal::service('path.current')->getPath()));

    return $response;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->config('news_core.settings')
      ->set('news_core_base_type', $form_state->getValue(array('news_core_base_type')))
      ->set('news_core_base_field', $form_state->getValue(array('news_core_base_field')))
      ->set('news_core_line_form_count', $form_state->getValue(array('news_core_line_form_count')))
      ->set('news_core_base_date_format', $form_state->getValue(array('news_core_base_date_format')))
      ->set('news_core_user_category_field', $form_state->getValue(array('news_core_user_category_field')))
      ->set('news_core_trim', $form_state->getValue(array('news_core_trim')))
      ->save();
    parent::submitForm($form, $form_state);
  }
}