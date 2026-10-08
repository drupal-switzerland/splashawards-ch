<?php

/**
 * @file
 * Platform.sh settings, included from settings.php when on Platform.sh.
 */

use Platformsh\ConfigReader\Config;

$platformsh = new Config();
if (!$platformsh->isValidPlatform() || !$platformsh->inRuntime()) {
  return;
}

if ($platformsh->hasRelationship('database')) {
  $database = $platformsh->credentials('database');
  $databases['default']['default'] = [
    'driver' => $database['scheme'],
    'database' => $database['path'],
    'username' => $database['username'],
    'password' => $database['password'],
    'host' => $database['host'],
    'port' => $database['port'],
    'init_commands' => ['isolation_level' => 'SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED'],
  ];
}

$config['system.logging']['error_level'] = $platformsh->onProduction() ? 'hide' : 'verbose';

$settings['file_private_path'] = $platformsh->appDir . '/private';
$settings['file_temp_path'] = $platformsh->appDir . '/tmp';
$settings['php_storage']['default']['directory'] = $settings['file_private_path'];
$settings['php_storage']['twig']['directory'] = $settings['file_private_path'];
$settings['hash_salt'] = $platformsh->projectEntropy;
$settings['deployment_identifier'] = $platformsh->treeId;
// The Platform.sh router only forwards configured routes.
$settings['trusted_host_patterns'] = ['.*'];

if ($platformsh->hasRelationship('redis') && extension_loaded('redis')) {
  $redis = $platformsh->credentials('redis');
  $settings['redis.connection']['interface'] = 'PhpRedis';
  $settings['redis.connection']['host'] = $redis['host'];
  $settings['redis.connection']['port'] = $redis['port'];
  $settings['cache']['default'] = 'cache.backend.redis';
  $settings['container_yamls'][] = 'modules/contrib/redis/example.services.yml';
}

// Fallback caps if redis is unavailable: page cache rows are permanent and
// filled the DB disk once already (Sep 2026).
$settings['database_cache_max_rows']['bins']['page'] = 1000;
$settings['database_cache_max_rows']['bins']['dynamic_page_cache'] = 1000;
