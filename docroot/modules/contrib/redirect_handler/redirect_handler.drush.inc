<?php

/**
 * @file
 * Some drush commands for the redirect handler module.
 */

/**
 * Implements hook_drush_command().
 */
function redirect_handler_drush_command() {
  $items = [];
  $items['redirect-handler-delete'] = [
    'description' => 'Remove all redirects',
    'callback' => 'redirect_handler_remove_all',
    'drupal dependencies' => [
      'redirect_handler',
    ],
  ];
  $items['redirect-handler-import'] = [
    'description' => 'Import all redirects',
    'callback' => 'redirect_handler_import_all',
    'drupal dependencies' => [
      'redirect_handler',
    ],
  ];

  return $items;
}

/**
 * Drush callback for "redirect-handler-delete".
 *
 * This function will remove all existing redirects.
 */
function redirect_handler_remove_all() {
  drush_print(dt('This will remove all redirects from your system.'));
  if (!drush_confirm(dt('Do you really want to continue?'))) {
    return drush_user_abort();
  }
  \Drupal::service('redirect_handler.manager')->delete();
}

/**
 * Drush callback for "redirect-handler-import".
 */
function redirect_handler_import_all() {
  drush_print(dt('Import started...'));
  $result = \Drupal::service('redirect_handler.manager')->performImport();
  drush_print(dt('Finished import: created @count redirects', ['@count' => $result]));
}
