<?php

/**
 * @file
 * Preprocess functions for the splash_awards pages.
 */

use Drupal\Core\Url;

/**
 * Implements hook_preprocess_page().
 */
function splash_awards_preprocess_page(&$variables): void {
  $siteSettings = \Drupal::config('system.site');
  $variables['frontUrl'] = Url::fromRoute('<front>')->setAbsolute()->toString();
  $variables['siteName'] = $siteSettings->get('name');
  $variables['siteSlogan'] = $siteSettings->get('slogan');
  $variables['mainMenu'] = _splash_awards_menu_structure('main');
  $variables['footerMenu'] = _splash_awards_menu_structure('footer');
  $variables['socialMenu'] = _splash_awards_menu_structure('social-icons');

  // Add a menu entry for the jury-dashboard if the user has the permission.
  if (\Drupal::currentUser()->hasPermission('access jury dashboard')) {
    $variables['mainMenu'][] = [
      'text' => t('Rate cases'),
      'url' => Url::fromRoute('splash_awards_base.jury_dashboard', [
        'user' => \Drupal::currentUser()->id(),
      ])->setAbsolute()->toString(),
      'target' => '_self',
    ];
  }

  $termStorage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');

  // Check if nominee route.
  if (\Drupal::routeMatch()->getRouteName() === 'view.nominees.all') {
    $openAward = $termStorage->loadByProperties(
      [
        'vid' => 'splash_awards',
        'status' => 1,
        'field_slug' => \Drupal::routeMatch()->getParameter('arg_0'),
      ]
    );

    $openAward = reset($openAward);
  }

  // Add data for the nominee dropdown button.
  $splashAwards = $termStorage->loadTree('splash_awards', 0, NULL, TRUE);

  $nomineeOptions = [];
  foreach ($splashAwards as $term) {
    if ($term->isPublished() && !$term->get('field_slug')->isEmpty()) {
      // Set the first term as base.
      if (!isset($variables['nomineeDropdown']['text'])) {
        $firstTerm = $openAward ?? $term;
        $variables['nomineeDropdown']['text'] = $firstTerm->getName();
        $variables['nomineeDropdown']['url'] = Url::fromRoute('view.nominees.all', [
          'arg_0' => $firstTerm->get('field_slug')->value,
        ])->setAbsolute()->toString();
      }
      if ($term->id() !== $firstTerm->id()) {
        // Set all other terms as options.
        $nomineeOptions[] = [
          'text' => $term->getName(),
          'url' => Url::fromRoute('view.nominees.all', [
            'arg_0' => $term->get('field_slug')->value,
          ])->setAbsolute()->toString(),
        ];
      }
    }
  }

  $variables['nomineeDropdown']['options'] = $nomineeOptions;

  // Add data for the headers login button.
  $variables['loginButton'] = [
    'text' => t('Case submission'),
    'url' => Url::fromRoute('user.page')->setAbsolute()->toString(),
  ];

  // Add cache dependencies on the menus.
  $variables['#cache']['tags'][] = 'config:system.menu.main';
  $variables['#cache']['tags'][] = 'config:system.menu.social-icons';
  $variables['#cache']['tags'][] = 'config:system.menu.footer';
}
