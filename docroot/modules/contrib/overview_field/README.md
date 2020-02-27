# Overview field

## Overview
Provides a custom field with a dropdown list that contains a list of
overview / custom blocks.

## Usage

Add an option to the dropdown list:

```
/**
 * Implements hook_overview_options_alter().
 *
 * Add an option to the overview field.
 */
function overview_field_example_overview_options_alter(&$options) {
  $options['recent_content'] = t('Show a list of the recent content on
  the site');
}
```

Load the output for the previously declared option.
```
/**
 * Implements hook_overview_output_alter().
 */
function overview_field_example_overview_output_alter($key, &$output) {
  if ($key == 'recent_content') {
    $output = OverviewFieldFormatter->loadView('content_recent', 'block_1');
  }
}
```

## Roadmap
* Add tests to the module.
* Add extra options with Plugin derivatives instead of alters.
