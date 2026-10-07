<?php

namespace Drupal\splash_awards_base\Entity;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\node\Entity\Node;

/**
 * Custom bundle class for award taxonomy terms.
 */
class SplashAwardCase extends Node implements PageInterface {

  use StringTranslationTrait;

  /**
   * Returns the url to the cases badge with the highest weight.
   */
  public function getBadgeUrl(): ?string {
    $badgeUrl = NULL;
    if (!$this->get('field_badge')->isEmpty()) {

      $weight = NULL;
      foreach ($this->get('field_badge') as $element) {
        $badgeEntity = $element->entity;

        if ($badgeEntity instanceof Badge) {
          if (!$weight || $badgeEntity->get('field_weight')->value > $weight) {
            $weight = $badgeEntity->get('field_weight')->value;
            $badgeUrl = $badgeEntity->getLogoUrl();
          }
        }
      }
    }
    return $badgeUrl;
  }

  /**
   * Returns true if this case refrences at least one "nominated" badge term.
   */
  public function isNominated(): bool {
    $nomineeBadges = \Drupal::entityQuery('taxonomy_term')
      ->condition('vid', 'badges')
      ->condition('status', 1)
      ->accessCheck('TRUE')
      ->condition('field_qualified', TRUE)
      ->execute();

    // Convert the nominee badge term IDs to a simple array.
    $nomineeBadgeIds = array_values($nomineeBadges);

    // Check if any of the referenced badges match the nominated badges.
    foreach ($this->get('field_badge')->getValue() as $badgeElement) {
      if (isset($badgeElement['target_id']) && in_array($badgeElement['target_id'], $nomineeBadgeIds)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Returns true if this case references a non-"nominated" badge.
   *
   * This indicates that the case has won an award, is a runner-up, or has a
   * special mention.
   */
  protected function hasExtraBadge(): bool {
    foreach ($this->get('field_badge') as $badgeElement) {
      $badge = $badgeElement->entity;

      if ($badge instanceof Badge && !$badge->get('field_qualified')->value) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Returns the hero image of the page.
   */
  public function getHeroImage($teaser = FALSE): ?string {
    $file = !$this->get('field_images')->isEmpty()
      ? $this->get('field_images')->first()->entity
      : NULL;

    if (!$file instanceof File) {
      return NULL;
    }

    return $teaser
      ? \Drupal::entityTypeManager()->getStorage('image_style')->load('teaser')->buildUrl($file->getFileUri())
      : $file->createFileUrl(FALSE);
  }

  /**
   * Returns the state / badge of a case.
   *
   * If it has no badge it rated as "submission".
   */
  public function getStatus() {
    $status = $this->t('Submission');

    if (!$this->get('field_badge')->isEmpty()) {
      $badge = $this->get('field_badge')->entity;

      if ($badge instanceof Badge) {
        $status = $badge->name->value;
      }
    }

    return $status;
  }

  /**
   * Returns image of a specific field with a specific style.
   */
  public function getImageUrl(string $field, ?string $styleName = NULL) {
    $file = $this->hasField($field) && !$this->get($field)->isEmpty()
      ? $this->get($field)->entity
      : NULL;
    $style = NULL;

    if ($styleName) {
      $storage = \Drupal::entityTypeManager()->getStorage('image_style');
      $style = $storage->load($styleName);
    }

    if ($file instanceof File && $style === NULL) {
      return $file->createFileUrl(FALSE);
    }

    if ($file instanceof File && $style instanceof ImageStyle) {
      return $style->buildUrl($file->getFileUri());
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getHeroData(): array {
    return [
      'title' => $this->getTitle(),
      'image' => $this->getHeroImage(),
    ];
  }

  /**
   * Returns the teaser data for the page.
   */
  public function getTeaserData(): array {
    $themeName = \Drupal::theme()->getActiveTheme()->getName();

    return [
      'img' => $this->getHeroImage(TRUE) ?? \Drupal::service('extension.list.theme')->getPath($themeName) . '/images/teaser.jpg',
      'title' => $this->getTitle(),
      'logo' => $this->getImageUrl('field_company_logo', 'logo_teaser'),
      'badge' => $this->hasExtraBadge() ? $this->getBadgeUrl() : NULL,
      'teaserText' => $this->get('field_summary')->view('default'),
      'url' => $this->toUrl()->toString(),
    ];
  }

}
