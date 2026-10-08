<?php

namespace Drupal\splash_awards_base\Entity;

use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\taxonomy\Entity\Term;

/**
 * Custom bundle class for award taxonomy terms.
 */
class Award extends Term implements PageInterface {

  /**
   * {@inheritdoc}
   */
  public function getHeroData(): array {
    $imageUrl = NULL;
    $videoUrl = NULL;
    $cta = [];

    // Load the referenced media entity for the image or video.
    $media = $this->get('field_hero_media')->entity;

    if ($media instanceof Media) {

      if ($media->bundle() === 'video') {
        $file = $media->get('field_media_video_file')->entity;
        $videoUrl = $file instanceof File ? $file->createFileUrl(FALSE) : NULL;
      }

      if ($media->bundle() === 'image') {
        $file = $media->get('field_media_image')->entity;
        $imageUrl = $file instanceof File ? $file->createFileUrl(FALSE) : NULL;
      }
    }

    // Load the referenced links for ctas.
    /** @var \Drupal\link\Plugin\Field\FieldType\LinkItem $item */
    foreach ($this->get('field_hero_cta') as $item) {
      $cta[] = [
        'text' => $item->getTitle(),
        'url' => $item->getUrl(),
      ];
    }

    return [
      'title' => !$this->get('field_hero_title')->isEmpty()
        ? $this->get('field_hero_title')->value
        : $this->getName(),
      'image' => $imageUrl,
      'video' => $videoUrl,
      'cta' => $cta,
      'color' => NULL,
    ];
  }

}
