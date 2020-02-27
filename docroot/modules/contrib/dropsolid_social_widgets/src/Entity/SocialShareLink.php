<?php

namespace Drupal\dropsolid_social_widgets\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\dropsolid_social_widgets\SocialShareLinkInterface;

/**
 * Defines the SocialShareLink entity.
 *
 * @ConfigEntityType(
 *   id = "social_share_link",
 *   label = @Translation("Social Share Link"),
 *   handlers = {
 *     "list_builder" = "Drupal\dropsolid_social_widgets\Controller\SocialShareLinkListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dropsolid_social_widgets\Form\SocialShareLinkForm",
 *       "edit" = "Drupal\dropsolid_social_widgets\Form\SocialShareLinkForm",
 *       "delete" = "Drupal\dropsolid_social_widgets\Form\SocialShareLinkDeleteForm",
 *     }
 *   },
 *   config_prefix = "social_share_link",
 *   admin_permission = "configure dropsolid social widgets settings",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "sharer_url" = "sharer_url",
 *     "onclick" = "onclick",
 *     "weight" = "weight",
 *     "target" = "target"
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/services/social-share/{social_share_link}",
 *     "delete-form" = "/admin/config/services/social-share/{social_share_link}/delete",
 *   }
 * )
 */
class SocialShareLink extends ConfigEntityBase implements SocialShareLinkInterface {

  /**
   * The Social Share Link machine name.
   *
   * @var string
   */
  public $id;

  /**
   * The Social Share Link label.
   *
   * @var string
   */
  public $label;

  /**
   * The Social Share Link sharer URL.
   *
   * @var string
   */
  public $sharer_url;

  /**
   * The Social Share Link onclick setting.
   *
   * @var string
   */
  public $onclick;

  /**
   * The Social Share Link target attribute.
   *
   * @var string
   */
  public $target;

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->get('weight');
  }

  /**
   * {@inheritdoc}
   */
  public function setWeight($weight) {
    $this->set('weight', $weight);
    return $this;
  }
}
