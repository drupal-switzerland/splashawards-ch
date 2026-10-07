<?php

// phpcs:ignoreFile

$databases = [];
$settings['config_sync_directory'] = '../config/default';
$settings['file_private_path'] = '../private';
$settings['update_free_access'] = FALSE;
$settings['entity_update_batch_size'] = 50;
$settings['entity_update_backup'] = TRUE;
$settings['migrate_node_migrate_type_classic'] = FALSE;
$settings['file_scan_ignore_directories'] = ['node_modules', 'bower_components'];

if (getenv('PLATFORM_RELATIONSHIPS') && file_exists(__DIR__ . '/settings.platformsh.php')) {
  include __DIR__ . '/settings.platformsh.php';
}

if (getenv('IS_DDEV_PROJECT') == 'true' && file_exists(__DIR__ . '/settings.ddev.php')) {
  include __DIR__ . '/settings.ddev.php';
}

if (file_exists(__DIR__ . '/settings.local.php')) {
  include __DIR__ . '/settings.local.php';
}
