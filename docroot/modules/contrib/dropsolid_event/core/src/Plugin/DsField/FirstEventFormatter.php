<?php

namespace Drupal\dropsolid_event_core\Plugin\DsField;

use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Formats and renders the first event's start date.
 *
 * @DsField(
 *   id = "first_event_formatter",
 *   title = @Translation("First event formatter"),
 *   entity_type = {
 *    "node"
 *   },
 *   ui_limit = {
 *    "event|*"
 *   },
 *   provider = "dropsolid_event_core"
 * )
 */
class FirstEventFormatter extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    /** @var \Drupal\node\NodeInterface $entity */
    $entity = $this->entity();

    $event = $entity->get('field_events')->first()->entity;
    if ($event) {
      // Only need the first event.
      $build['date_first_teaser']['#markup'] = '<div class="date-first-teaser"><span class="day">' . format_date($event->getStartDate(), 'custom', 'd') . '</span><span class="month">' . format_date($event->getStartDate(), 'custom', 'M') . '</span></div>';
      $build['#cache']['contexts'][] = 'timezone';
    }
    return $build;
  }

}
