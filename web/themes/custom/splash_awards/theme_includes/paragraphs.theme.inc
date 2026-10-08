<?php

/**
 * @file
 * Preprocess functions for the splash_awards paragraphs.
 */

use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\link\Plugin\Field\FieldType\LinkItem;
use Drupal\media\Entity\Media;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Implements hook_preprocess_paragraph().
 */
function splash_awards_preprocess_paragraph(&$variables): void {
  if (
    isset($variables['paragraph']) &&
    $variables['paragraph'] instanceof Paragraph
  ) {
    $paragraph = $variables['paragraph'];

    if ($paragraph->bundle() === 'columns') {
      $variables['leftColumn'] = $paragraph->get('field_left_column')->view('default');
      $variables['rightColumn'] = $paragraph->get('field_right_column')->view('default');
    }

    if ($paragraph->bundle() === 'text') {
      $variables['text'] = $paragraph->get('field_text')->view('default');
    }

    if ($paragraph->bundle() === 'cta') {
      $variables['type'] = $paragraph->get('field_type')->value;
      $variables['title'] = $paragraph->get('field_cta_title')->value;
      $variables['text'] = $paragraph->get('field_cta_text')->value;
      $variables['cta'] = [
        'url' => !$paragraph->get('field_cta_link')->isEmpty()
          ? Url::fromUri($paragraph->get('field_cta_link')->uri)->toString()
          : NULL,
        'text' => !$paragraph->get('field_cta_link')->isEmpty()
          ? $paragraph->get('field_cta_link')->title
          : NULL,
      ];
    }

    if ($paragraph->bundle() === 'testimonial') {
      $imageStyleStorage = \Drupal::entityTypeManager()->getStorage('image_style');
      $imageUrl = NULL;
      $media = $paragraph->get('field_portrait')->entity;

      if ($media instanceof Media) {
        $file = $media->get('field_media_image')->entity;

        if ($file instanceof File) {
          $imageUrl = $imageStyleStorage->load('testimonial')->buildUrl($file->getFileUri());
        }
      }

      $variables['img'] = $imageUrl;
      $variables['type'] = $paragraph->get('field_testimonial_type')->value;
      $variables['text'] = $paragraph->get('field_testimonial_text')->value;
      $variables['description'] = $paragraph->get('field_testimonial_description')->value;
    }

    if ($paragraph->bundle() === 'sponsor_grid') {
      $imageStyleStorage = \Drupal::entityTypeManager()->getStorage('image_style');
      $variables['title'] = $paragraph->get('field_sponsors_title')->value;
      $variables['text'] = $paragraph->get('field_sponsors_text')->value;
      $variables['sponsors'] = [];

      foreach ($paragraph->get('field_sponsors') as $element) {
        /** @var \Drupal\paragraphs\Entity\Paragraph $sponsorParagraph */
        $sponsorParagraph = $element->entity;

        if ($sponsorParagraph->bundle() === 'sponsor') {
          $logoUrl = NULL;
          $logoAlt = "";
          $media = $sponsorParagraph->get('field_sponsor_logo')->entity;

          if ($media instanceof Media) {
            $file = $media->get('field_media_image')->entity;

            if ($file instanceof File) {
              $logoUrl = $imageStyleStorage->load('sponsor')->buildUrl($file->getFileUri());
              $logoAlt = $media->get('field_media_image')->alt;
            }
          }

          $variables['sponsors'][] = [
            'img' => $logoUrl,
            'url' => $sponsorParagraph->get('field_sponsor_url')->first() instanceof LinkItem
              ? $sponsorParagraph->get('field_sponsor_url')->first()->getUrl()
              : Url::fromRoute('<nolink>')->toString(),
            'alt' => $logoAlt,
          ];
        }
      }
    }

    if ($paragraph->bundle() === 'jury_grid') {
      $variables['juryData'] = [];

      foreach ($paragraph->get('field_jury_member') as $element) {
        /** @var \Drupal\paragraphs\Entity\Paragraph $sponsorParagraph */
        $juryMemberParagraph = $element->entity;

        if ($juryMemberParagraph->bundle() === 'jury_member') {
          $imageStyleStorage = \Drupal::entityTypeManager()->getStorage('image_style');
          $imageUrl = NULL;
          $media = $juryMemberParagraph->get('field_portrait')->entity;

          if ($media instanceof Media) {
            $file = $media->get('field_media_image')->entity;

            if ($file instanceof File) {
              $imageUrl = $imageStyleStorage->load('jury')->buildUrl($file->getFileUri());
            }
          }

          $iconLinks = [];
          foreach ($juryMemberParagraph->get('field_icon_links') as $linkElement) {
            $link = $linkElement->entity;

            if ($link instanceof Paragraph) {
              $iconUrl = NULL;
              $iconAlt = "";
              $media = $link->get('field_icon')->entity;

              if ($media instanceof Media) {
                $file = $media->get('field_media_image')->entity;

                if ($file instanceof File) {

                  $iconUrl = $file->getMimeType() !== 'image/svg+xml'
                    ? $imageStyleStorage->load('icon')->buildUrl($file->getFileUri())
                    : $file->createFileUrl();
                  $iconAlt = $media->get('field_media_image')->alt;
                }
              }

              $iconLinks[] = [
                'img' => $iconUrl,
                'alt' => $iconAlt,
                'url' => $link->get('field_link')->first() instanceof LinkItem
                  ? $link->get('field_link')->first()->getUrl()->toString()
                  : Url::fromRoute('<nolink>')->toString(),
              ];
            }
          }

          $variables['juryData'][] = [
            'img' => $imageUrl,
            'name' => $juryMemberParagraph->get('field_jury_name')->value,
            'text' => $juryMemberParagraph->get('field_jury_text')->view('default'),
            'description' => $juryMemberParagraph->get('field_jury_description')->value,
            'iconLinks' => $iconLinks,
          ];
        }
      }
    }

    if ($paragraph->bundle() === 'images') {
      $imageStyleStorage = \Drupal::entityTypeManager()->getStorage('image_style');
      $variables['images'] = [];
      $variables['layout'] = $paragraph->get('field_layout')->value;
      $variables['inColumn'] = $paragraph->getParentEntity()->getEntityTypeId() === 'paragraph';

      foreach ($paragraph->get('field_images') as $element) {
        $imageUrl = NULL;
        $alt = "";
        $media = $element->entity;

        if ($media instanceof Media) {
          $file = $media->get('field_media_image')->entity;

          if ($file instanceof File) {
            $imageUrl = $imageStyleStorage->load('image_grid')->buildUrl($file->getFileUri());
            $alt = $media->get('field_media_image')->alt;
          }
          $variables['images'][] = [
            'src' => $imageUrl,
            'alt' => $alt,
          ];
        }
      }
    }

    if ($paragraph->bundle() === 'news') {
      $variables['newsView'] = [
        '#type' => 'view',
        '#name' => 'news',
        '#display_id' => $paragraph->get('field_teaser_mode')->value
          ? 'teaser'
          : 'all',
      ];
    }
  }
}
