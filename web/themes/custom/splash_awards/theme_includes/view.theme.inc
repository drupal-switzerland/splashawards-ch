<?php

/**
 * @file
 * Preprocess functions for the splash_awards views.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\splash_awards_base\Entity\Award;
use Drupal\splash_awards_base\Entity\SplashAwardCase;

/**
 * Implements hook_FORM_ID_form_alter().
 *
 * Remove badge terms ids for nominated badge from the status filter.
 */
function splash_awards_form_views_exposed_form_alter(&$form, FormStateInterface $form_state) {
  /** @var \Drupal\views\ViewExecutable $view */
  $view = $form_state->get('view');

  if ($view->id() === 'nominees') {

    $nomineeBadges = \Drupal::entityQuery('taxonomy_term')
      ->condition('vid', 'badges')
      ->condition('status', 1)
      ->accessCheck('TRUE')
      ->condition('field_qualified', TRUE)
      ->execute();

    // Convert the nominee badge term IDs to a simple array.
    $nomineeBadgeIds = array_values($nomineeBadges);

    foreach ($form['status']['#options'] as $index => $value) {
      if (in_array($index, $nomineeBadgeIds)) {
        unset($form['status']['#options'][$index]);
      }
    }

    // Restrict category filter to tags that also have cases as results
    // Load awards term from url.
    $award = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(
      [
        'vid' => 'splash_awards',
        'status' => 1,
        'field_slug' => $form_state->get('view')->args[0],
      ]
    );

    $award = reset($award);

    // Loop through all categories and remove option if no case is tagged.
    foreach ($form['category']['#options'] as $index => $value) {
      $cases = \Drupal::entityQuery('node')
        ->condition('type', 'case')
        ->condition('status', 1)
        ->condition('field_award', $award->id())
        ->condition('field_category', $index)
        ->accessCheck(FALSE)
        ->execute();

      if (empty($cases) && $index !== 'All') {
        unset($form['category']['#options'][$index]);
      }
    }
  }
}

/**
 * Implements hook_preprocess_paragraph().
 */
function splash_awards_preprocess_views_view__nominees(&$variables): void {
  /** @var \Drupal\views\ViewExecutable $view */
  $view = $variables['view'];

  if (isset($view->args[0])) {
    // Load awards term from url.
    $award = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(
      [
        'vid' => 'splash_awards',
        'status' => 1,
        'field_slug' => $view->args[0],
      ]
    );
    $award = reset($award);

    if ($award instanceof Award) {
      $variables['hero'] = $award->getHeroData();
    }

  }

  // Filter view results based on badge that is marked as 'nominated'.
  $filteredResults = [];
  foreach ($view->result as $row) {
    $case = $row->_entity;

    if ($case instanceof SplashAwardCase && $case->isNominated()) {
      $filteredResults[] = $row;
    }
  }

  $view->result = $filteredResults;
  $variables['rows'] = $view->style_plugin->render();
}
