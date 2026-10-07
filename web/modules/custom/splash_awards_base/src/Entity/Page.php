<?php

namespace Drupal\splash_awards_base\Entity;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;

/**
 * Custom bundle class for award taxonomy terms.
 */
class Page extends Node implements PageInterface {

  use StringTranslationTrait;

  /**
   * Returns the title of the page.
   */
  public function getHeroTitle() {
    return $this->hasField('field_hero_title') && !$this->get('field_hero_title')->isEmpty()
      ? $this->get('field_hero_title')->value
      : $this->getTitle();
  }

  /**
   * Returns the hero image of the page.
   */
  public function getHeroImage($teaser = FALSE): ?string {
    // Load the referenced media entity for the image or video.
    $media = $this->get('field_hero_media')->entity;

    if ($media instanceof Media) {
      if ($media->bundle() === 'image') {
        $file = $media->get('field_media_image')->entity;

        if (!$file instanceof File) {
          return NULL;
        }

        return $teaser
          ? \Drupal::entityTypeManager()->getStorage('image_style')->load('teaser')->buildUrl($file->getFileUri())
          : $file->createFileUrl(FALSE);
      }
    }
    return NULL;
  }

  /**
   * Returns the hero video of the page.
   */
  public function getHeroVideo(): ?string {
    // Load the referenced media entity for the image or video.
    $media = $this->get('field_hero_media')->entity;

    if ($media instanceof Media) {

      if ($media->bundle() === 'video') {
        $file = $media->get('field_media_video_file')->entity;
        return $file instanceof File ? $file->createFileUrl(FALSE) : NULL;
      }
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getHeroData(): array {
    // Load the referenced links for ctas.
    $cta = [];
    /** @var \Drupal\link\Plugin\Field\FieldType\LinkItem $item */
    foreach ($this->get('field_hero_cta') as $item) {
      $cta[] = [
        'text' => $item->getTitle(),
        'url' => $item->getUrl(),
      ];
    }

    $authorPortraitUrl = NULL;
    if ($this->hasField('field_author_portrait')) {
      $media = $this->get('field_author_portrait')->entity;

      if ($media instanceof Media) {
        if ($media->bundle() === 'image') {
          $file = $media->get('field_media_image')->entity;

          $authorPortraitUrl = $file instanceof File
            ? \Drupal::entityTypeManager()->getStorage('image_style')->load('testimonial')->buildUrl($file->getFileUri())
            : NULL;
        }
      }
    }

    return [
      'title' => $this->getHeroTitle(),
      'image' => $this->getHeroImage(),
      'video' => $this->getHeroVideo(),
      'cta' => $cta,
      'meta' => (
        $this->hasField('field_author') ||
        $this->hasField('field_date') ||
        $this->hasField('field_author_portrait') ||
        $this->hasField('field_reading_time')
      ) ? [
        'author' => $this->hasField('field_author')
          ? $this->get('field_author')->value
          : NULL,
        'date' => $this->getDate('d.m.Y'),
        'portrait' => $authorPortraitUrl,
        'readTime' => $this->hasField('field_reading_time')
          ? $this->get('field_reading_time')->value . ' ' . $this->t('Minutes reading time')
          : NULL,
      ]
        : NULL,
    ];
  }

  /**
   * Returns the teaser data for the page.
   */
  public function getTeaserData(): array {
    $themeName = \Drupal::theme()->getActiveTheme()->getName();

    return [
      'img' => $this->getHeroImage(TRUE) ?? \Drupal::service('extension.list.theme')->getPath($themeName) . '/images/news.jpg',
      'date' => $this->getDate(),
      'title' => $this->getHeroTitle(),
      'teaserText' => $this->hasField('field_teaser_text')
        ? $this->get('field_teaser_text')->value
        : NULL,
      'url' => $this->toUrl()->toString(),
    ];
  }

  /**
   * Returns the date of the page.
   */
  public function getDate(string $format = 'd. F Y'): ?string {
    return $this->hasField('field_date')
      ? $this->get('field_date')->date->format($format)
      : NULL;
  }

}
