<?php

/**
 * @file
 * Preprocess functions for the splash_awards nodes.
 */

use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\splash_awards_base\Entity\PageInterface;
use Drupal\taxonomy\TermInterface;

/**
 * Implements hook_preprocess_node().
 */
function splash_awards_preprocess_node(&$variables): void {
  if (
    isset($variables['node']) &&
    $variables['node'] instanceof PageInterface
  ) {
    $node = $variables['node'];

    if ($node->bundle() === 'page') {
      $variables['hero'] = $node->getHeroData();
      $variables['paragraphs'] = $node->get('field_paragraphs')->view('default');
    }

    if ($node->bundle() === 'article') {

      if ($variables['view_mode'] === 'full') {
        $variables['hero'] = $node->getHeroData();
        $variables['paragraphs'] = $node->get('field_article_paragraphs')->view('default');
      }

      if ($variables['view_mode'] === 'teaser') {
        $variables['teaserData'] = $node->getTeaserData();
      }

    }

    if ($node->bundle() === 'case') {

      if ($variables['view_mode'] === 'full') {
        $variables['hero'] = $node->getHeroData();

        $award = $node->get('field_award')->entity;
        $category = $node->get('field_category')->entity;
        $badge = $node->get('field_badge')->entity;

        $variables['overviewUrl'] = $award instanceof TermInterface
          ? Url::fromRoute('view.nominees.all', [
            'arg_0' => $award->id(),
          ])->setAbsolute()->toString()
          : Url::fromRoute('<front>')->setAbsolute()->toString();

        $variables['category'] = $category instanceof TermInterface
          ? $category->getName()
          : '-';

        $variables['status'] = $badge instanceof TermInterface
          ? $badge->getName()
          : '-';

        $variables['projectUrl'] = !$node->get('field_url')->isEmpty()
          ? $node->get('field_url')->uri
          : NULL;

        $variables['candidateUrl'] = !$node->get('field_company_url')->isEmpty()
          ? $node->get('field_company_url')->uri
          : NULL;

        $variables['candidateName'] = $node->get('field_company_name')->value;
        $variables['candidateLogo'] = $node->getImageUrl('field_company_logo', 'logo_teaser');
        $variables['clientName'] = $node->get('field_client')->value;
        $variables['clientLogo'] = $node->getImageUrl('field_customer_logo', 'logo_teaser');

        $variables['imagesTop'] = [];
        foreach (array_slice($node->get('field_images')->getValue(), 0, 3) as $image) {
          $imageFile = File::load($image['target_id']);

          if ($imageFile instanceof File) {
            $variables['imagesTop'][] = [
              'src' => \Drupal::entityTypeManager()->getStorage('image_style')->load('teaser')->buildUrl($imageFile->getFileUri()),
              'alt' => $imageFile->alt,
            ];
          }
        }

        $variables['imagesBottom'] = [];
        foreach (array_slice($node->get('field_images')->getValue(), 3) as $image) {
          $imageFile = File::load($image['target_id']);

          if ($imageFile instanceof File) {
            $variables['imagesBottom'][] = [
              'src' => \Drupal::entityTypeManager()->getStorage('image_style')->load('teaser')->buildUrl($imageFile->getFileUri()),
              'alt' => $imageFile->alt,
            ];
          }
        }

        $variables['videoUrl'] = !$node->get('field_video_url')->isEmpty()
          ? $node->get('field_video_url')->uri
          : NULL;

        $variables['summary'] = !$node->get('field_summary')->isEmpty()
          ? $node->get('field_summary')->view('default')
          : NULL;
        $variables['description'] = !$node->get('field_description')->isEmpty()
          ? $node->get('field_description')->view('default')
          : NULL;

        $variables['goals'] = !$node->get('field_goals_and_results')->isEmpty()
          ? $node->get('field_goals_and_results')->view('default')
          : NULL;

        $variables['challenges'] = !$node->get('field_challenges')->isEmpty()
          ? $node->get('field_challenges')->view('default')
          : NULL;

        $variables['contributions'] = !$node->get('field_contributions')->isEmpty()
          ? $node->get('field_contributions')->view('default')
          : NULL;

        $variables['motivation'] = !$node->get('field_motivation')->isEmpty()
          ? $node->get('field_motivation')->view('default')
          : NULL;

        $variables['badgeUrl'] = $node->getBadgeUrl();
      }

      if ($variables['view_mode'] === 'teaser') {
        $variables['teaserData'] = $node->getTeaserData();
      }

    }
  }
}
