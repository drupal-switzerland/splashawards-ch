<?php
/**
 * @file
 * Add "bem" function for Pattern Lab & Drupal
 * Modified 23/05/2018: changed order & names of the bem variables to better match BEM naming
 */

use Drupal\Core\Template\Attribute;

$function = new Twig_SimpleFunction('bem', function ($context, $block = '', $element, $modifiers = array(), $extra = array()) {
  $classes = [];

  // If using a blockname to override default class.
  if ($block) {
    // Set blockname class.

    if ($element) {
      $classes[] = $block . '__' . $element;
    } else {
      $classes[] = $block;
    }

    // Set blockname--modifier classes for each modifier.
    if (isset($modifiers) && is_array($modifiers)) {
      foreach ($modifiers as $modifier) {

        if ($element) {
          $classes[] = $block . '__' . $element . '--' . $modifier;
        } else {
          $classes[] = $block . '--' . $modifier;
        }

      };
    }
  }
  // If not overriding base class.
  else {
    // Set base class.
    $classes[] = $element;
    // Set base--modifier class for each modifier.
    if (isset($modifiers) && is_array($modifiers)) {
      foreach ($modifiers as $modifier) {
        $classes[] = $element . '--' . $modifier;
      };
    }
  }

  // If extra non-BEM classes are added.
  if (isset($extra) && is_array($extra)) {
    foreach ($extra as $extra_class) {
      $classes[] = $extra_class;
    };
  }

  if (class_exists('Drupal') && isset($context['attributes'])  ) {
    $attributes = new Attribute();

    // Iterate the attributes available in context.
    foreach($context['attributes'] as $key => $value) {

      // If there are classes, add them to the classes array.
      if ($key === 'class') {
        foreach ($value as $class) {
          $classes[] = $class;
        }
      }
      // Otherwise add the attribute straightaway.
      else {
        $attributes->setAttribute($key, $value);
      }

      // Remove the attribute from context so it doesn't trickle down to
      // includes.
      $context['attributes']->removeAttribute($key);
    }

    // Add class attribute.
    if (!empty($classes)) {
      $attributes->setAttribute('class', $classes);
    }

    return $attributes;
  }
  else {
    $attributes = 'class="' . implode(' ', $classes) . '"';
    return $attributes;
  }

}, array('needs_context' => true, 'is_safe' => array('html')));
