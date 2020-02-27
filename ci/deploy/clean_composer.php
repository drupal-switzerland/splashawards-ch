<?php

$read = '';
if (isset($argv[1])) {
  $read = $argv[1];
}
$write = '';
if (isset($argv[2])) {
  $write = $argv[2];
}

$composer = file_get_contents($read . 'composer.json');
$composer = json_decode($composer, TRUE);

foreach ($composer['require'] as $key => $value) {
  if (strpos($key, "dropsolid-drupal8/") === 0) {
    // Get rid of it.
    unset($composer['require'][$key]);
  }
}

foreach ($composer['repositories'] as $idx => $value) {
  if ($value['type'] === 'composer' && $value['url'] === 'https://packagist.dropsolid.com') {
    // Get rid of it.
    unset($composer['repositories'][$idx]);
  }
}

foreach ($composer['scripts']['post-install-cmd'] as $idx => $script) {
  if (strpos($script, 'cd docroot/themes/custom/dropsolid-') === 0) {
    // Get rid of it.
    unset($composer['scripts']['post-install-cmd'][$idx]);
  }
  if (strpos($script, "if [ -e ") === 0) {
    // Get rid of it.
    unset($composer['scripts']['post-install-cmd'][$idx]);
  }
}
$composer['scripts']['post-install-cmd'] = array_values($composer['scripts']['post-install-cmd']);
$composer['scripts']['post-install-cmd'][] = 'cp etc/.gitignore .gitignore';

foreach ($composer['scripts']['post-update-cmd'] as $idx => $script) {
  if (strpos($script, 'cd docroot/themes/custom/dropsolid-') === 0) {
    // Get rid of it.
    unset($composer['scripts']['post-update-cmd'][$idx]);
  }
  if (strpos($script, "if [ -e") === 0) {
    // Get rid of it.
    unset($composer['scripts']['post-update-cmd'][$idx]);
  }
}
$composer['scripts']['post-update-cmd'] = array_values($composer['scripts']['post-update-cmd']);

unset($composer['_readme']);

file_put_contents($write . 'composer.json', json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

$lock = file_get_contents($read . 'composer.lock');
$lock = json_decode($lock, TRUE);

$lock['content-hash'] = _get_composer_content_hash($composer);

foreach ($lock['packages'] as $idx => $value) {
  if (strpos($value['name'], "dropsolid-drupal8/") === 0) {
    unset($lock['packages'][$idx]);
  }
}
$lock['packages'] = array_values($lock['packages']);

foreach ($lock['stability-flags'] as $key => $flag) {
  if (strpos($key, "dropsolid-drupal8/") === 0) {
    unset($lock['stability-flags'][$key]);
  }
}

file_put_contents($write . 'composer.lock', json_encode($lock, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

/**
 * Recalculates the Content Hash.
 *
 * @param $content
 *
 * @return string
 */
function _get_composer_content_hash($content) {
  $relevantKeys = [
    'name',
    'version',
    'require',
    'require-dev',
    'conflict',
    'replace',
    'provide',
    'minimum-stability',
    'prefer-stable',
    'repositories',
    'extra',
  ];
  $relevantContent = [];
  foreach (array_intersect($relevantKeys, array_keys($content)) as $key) {
    $relevantContent[$key] = $content[$key];
  }
  if (isset($content['config']['platform'])) {
    $relevantContent['config']['platform'] = $content['config']['platform'];
  }
  ksort($relevantContent);
  return md5(json_encode($relevantContent));
}