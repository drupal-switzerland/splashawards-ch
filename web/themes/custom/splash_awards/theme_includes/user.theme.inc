<?php

/**
 * @file
 * Preprocess functions for the splash_awards user.
 */

use Drupal\Core\Url;
use Drupal\splash_awards_base\Entity\Candidate;

/**
 * Implements hook_preprocess_HOOK_preprocess_user().
 */
function splash_awards_preprocess_user(&$variables) {
  // Add the user data.
  $user = $variables['user'];
  $candidate = \Drupal::entityTypeManager()->getStorage('user')->load($user->id());

  if ($candidate instanceof Candidate) {
    $variables['hero'] = $candidate->getHeroData();
    $variables['userData'] = $candidate->getUserData();
    $variables['awardData'] = $candidate->getAwardData();
    $variables['caseSubmissionUrl'] = Url::fromRoute('splash_awards_base.case_submission', [
      'uuid' => 'new',
      'step' => 1,
    ]);
    $variables['userEditUrl'] = Url::fromRoute('splash_awards_base.user_edit_form', [
      'user' => $candidate->id(),
    ]);
    $variables['logoutUrl'] = Url::fromRoute('user.logout');
  }

  $variables['#cache']['tags'][] = 'config:splash_awards_base.settings';
}
