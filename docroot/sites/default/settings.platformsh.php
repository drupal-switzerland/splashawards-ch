<?php
/**
 * @file
 * Platform.sh settings.
 */

use Platformsh\ConfigReader\Config;

$platformsh = new Config();

if (!$platformsh->isAvailable()) {
  return NULL;
}

// You can check for any particular value being available (recommended):
if (isset($config->relationships['database'][0])) {
  $database = $config->relationships['database'][0];

  // Now $database is an array representing a database service.
  $databases['default']['default'] = [
    'driver' => $database['scheme'],
    'database' => $database['path'],
    'username' => $database['username'],
    'password' => $database['password'],
    'host' => $database['host'],
    'port' => $database['port'],
    'pdo' => [PDO::MYSQL_ATTR_COMPRESS => !empty($database['query']['compression'])]
  ];
}

// Enable verbose error messages on development branches, but not on the production branch.
// You may add more debug-centric settings here if desired to have them automatically enable
// on development but not production.
if (isset($platformsh->branch)) {
  // Production type environment.
  if ($platformsh->branch == 'master' || (isset($database) && $database['query']['is_master'])) {
    $config['system.logging']['error_level'] = 'hide';
  } // Development type environment.
  else {
    $config['system.logging']['error_level'] = 'verbose';
  }
}

// Configure private and temporary file paths.
if (!isset($settings['file_private_path'])) {
  $settings['file_private_path'] = $platformsh->app_dir . '/private';
}
if (!isset($settings['file_temp_path'])) {
  $settings['file_temp_path'] = $platformsh->app_dir . '/tmp';
}

// Configure the default PhpStorage and Twig template cache directories.
if (!isset($settings['php_storage']['default'])) {
  $settings['php_storage']['default']['directory'] = $settings['file_private_path'];
}
if (!isset($settings['php_storage']['twig'])) {
  $settings['php_storage']['twig']['directory'] = $settings['file_private_path'];
}

// Set the project-specific entropy value, used for generating one-time
// keys and such.
$settings['hash_salt'] = empty($settings['hash_salt']) ? $platformsh->projectEntropy : $settings['hash_salt'];

// Set the deployment identifier, which is used by some Drupal cache systems.
$settings['deployment_identifier'] = $settings['deployment_identifier'] ?? $platformsh->treeId;

// The 'trusted_hosts_pattern' setting allows an admin to restrict the Host header values
// that are considered trusted.  If an attacker sends a request with a custom-crafted Host
// header then it can be an injection vector, depending on how the Host header is used.
// However, Platform.sh already replaces the Host header with the route that was used to reach
// Platform.sh, so it is guaranteed to be safe.  The following line explicitly allows all
// Host headers, as the only possible Host header is already guaranteed safe.
$settings['trusted_host_patterns'] = ['.*'];

// Import variables prefixed with 'd8settings:' into $settings
// and 'd8config:' into $config.
$variables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), TRUE);

foreach ($variables as $name => $value) {
  $parts = explode(':', $name);
  list($prefix, $key) = array_pad($parts, 3, null);
  switch ($prefix) {
    // Variables that begin with `d8settings` or `drupal` get mapped
    // to the $settings array verbatim, even if the value is an array.
    // For example, a variable named d8settings:example-setting' with
    // value 'foo' becomes $settings['example-setting'] = 'foo';
    case 'd8settings':
    case 'drupal':
      $settings[$key] = $value;
      break;
    // Variables that begin with `d8config` get mapped to the $config
    // array.  Deeply nested variable names, with colon delimiters,
    // get mapped to deeply nested array elements. Array values
    // get added to the end just like a scalar. Variables without
    // both a config object name and property are skipped.
    // Example: Variable `d8config:conf_file:prop` with value `foo` becomes
    // $config['conf_file']['prop'] = 'foo';
    // Example: Variable `d8config:conf_file:prop:subprop` with value `foo` becomes
    // $config['conf_file']['prop']['subprop'] = 'foo';
    // Example: Variable `d8config:conf_file:prop:subprop` with value ['foo' => 'bar'] becomes
    // $config['conf_file']['prop']['subprop']['foo'] = 'bar';
    // Example: Variable `d8config:prop` is ignored.
    case 'd8config':
      if (count($parts) > 2) {
        $temp = &$config[$key];
        foreach (array_slice($parts, 2) as $n) {
          $prev = &$temp;
          $temp = &$temp[$n];
        }
        $prev[$n] = $value;
      }
      break;
  }
}
