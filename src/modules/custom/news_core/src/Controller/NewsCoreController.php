<?php

namespace Drupal\news_core\Controller;

use Drupal\Core\Controller\ControllerBase;

class NewsCoreController extends ControllerBase {

  public function pagecontent() {
    $build = array(
     '#type' => 'markup',
     '#markup' => t('Empty page!!!'),
   );

   return $build;
  }

}