<?php

namespace Drupal\splash_awards_base\Entity;

use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\taxonomy\Entity\Term;

/**
 * Custom bundle class for award taxonomy terms.
 */
class Badge extends Term {

  /**
   * Returns the url to the badges uploaded image.
   */
  public function getLogoUrl(): ?string {
    // Load the referenced media entity for the image.
    $media = !$this->get('field_badge')->isEmpty()
    ? $this->get('field_badge')->entity
    : NULL;

    if ($media instanceof Media && $media->bundle() === 'image') {
      $file = $media->get('field_media_image')->entity;
      return $file instanceof File
        ? \Drupal::entityTypeManager()->getStorage('image_style')->load('image_grid')->buildUrl($file->getFileUri())
        : NULL;
    }

    return NULL;
  }

}
