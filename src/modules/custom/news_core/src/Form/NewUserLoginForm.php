<?php

namespace Drupal\news_core\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Form\UserLoginForm;

/**
 * Provides a user login form.
 */
class NewUserLoginForm extends UserLoginForm {
  //TODO::Left for future dev
  public function buildForm(array $form, FormStateInterface $form_state) {
//    $form['email'] = [
//      '#type' => 'email',
//      '#default_value' => ''
//          ];
         return $form;
      }
}