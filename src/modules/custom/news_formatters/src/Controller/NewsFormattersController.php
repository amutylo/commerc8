<?php

/**
 * @file
 * Contains \Drupal\like_dislike\Controller\LikeDislikeController.
 */
namespace Drupal\news_formatters\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\like_dislike\Controller\LikeDislikeController;
use Symfony\Component\HttpFoundation\RequestStack;


/**
 * Class NewsFormattersController.
 *
 * @package Drupal\like_dislike\Controller
 */
class NewsFormattersController extends LikeDislikeController {

  /**
   * Overrides Like or Dislike handler, change $already_clicked variables,
   * Change like-access for anonymous, save it likes in cookies.
   *
   * @param string $clicked
   *   Status of the click link.
   * @param string $data
   *   Data passed from the formatter.
   *
   * @return AjaxResponse|string
   *   Response count for the like/dislike.
   */
  public function newshandler($clicked, $data) {
    $return = '';
    $response = new AjaxResponse();

    $decode_data = json_decode(base64_decode($data));

    $entity_data = $this->entityTypeManager
      ->getStorage($decode_data->entity_type)
      ->load($decode_data->entity_id);
    $field_name = $decode_data->field_name;

    $nid = $decode_data->entity_id;
    $users = json_decode($entity_data->$field_name->clicked_by);

    if ($users == NULL) {
      $users = new \stdClass();
      $users->default = 'default';
    }
    $user = $this->currentUser->id();

    if ($clicked == 'like') {
      if (!news_formatters_is_liked($entity_data, $decode_data)) {
        $entity_data->$field_name->likes++;
        $users->$user = 'like';
        news_core_set_cookies('liked_news', array($nid), TRUE);
      }
      else {
        return $this->news_formatters_like_dislike_status($response);
      }

      $return = [
        '#theme' => 'news_formatters_hide',
        '#liked' => TRUE,
        '#likes' => $entity_data->$field_name->likes,
      ];

      $response->addCommand(
        new ReplaceCommand('.like', render($return))
      );

      $response->addCommand(
        new ReplaceCommand('.news-comments', $this->getTinyLikeTheme($entity_data, $field_name))
      );

      $return = $response;
    }
    elseif ($clicked == 'dislike') {
      if (!news_formatters_is_liked($entity_data, $decode_data)) {
        $entity_data->$field_name->dislikes--;
        $users->$user = "dislike";
        news_core_set_cookies('liked_news', array($nid), TRUE);
      }
      else {
        return $this->news_formatters_like_dislike_status($response);
      }

      $return = $response->addCommand(
        new HtmlCommand('#dislike', $entity_data->$field_name->dislikes)
      );
    }

    $entity_data->$field_name->clicked_by = json_encode($users);
    $entity_data->save();
    return $return;
  }

  public function getTinyLikeTheme($entity_data, $field_name) {
    $item = array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => array('news-comments')
      )
    );
    $item['like'] = array(
      '#markup' => new FormattableMarkup('<i class="fa fa-heart-o" aria-hidden="true"></i> <div>@count</div>', array(
        '@count' => $entity_data->$field_name->likes
      )),
    );

    return render($item);
  }

  /**
   * Respond with the status, if user already liked/disliked.
   *
   * @param AjaxResponse $response
   * @return AjaxResponse
   */
  protected function news_formatters_like_dislike_status(AjaxResponse $response) {
    return $response->addCommand(
      new ReplaceCommand('.like', 'Already liked!')
    );
  }
}
