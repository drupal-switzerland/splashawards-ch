<?php

namespace Drupal\dropsolid_event_core\Plugin\DsField;

use Drupal\dropsolid_event_core\Entity\Event;
use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Renders the first event's earliest date with the last event's last date.
 *
 * @DsField(
 *   id = "events_date_range_formatter",
 *   title = @Translation("Date range for events: start & end"),
 *   entity_type = {
 *    "node"
 *   },
 *   ui_limit = {
 *    "event|*"
 *   },
 *   provider = "dropsolid_event_core"
 * )
 */
class EventsDateRangeMultipleFormatter extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    /** @var \Drupal\node\NodeInterface $entity */
    $entity = $this->entity();
    $list = $entity->get('field_events')->getValue();

    if (empty($list)) {
      return $build;
    }
    $first = reset($list);
    $last = end($list);

    if ($first && $last) {
      /** @var \Drupal\dropsolid_event_core\Entity\EventInterface $first */
      $first = Event::load($first['target_id']);
      /** @var \Drupal\dropsolid_event_core\Entity\EventInterface $last */
      $last = Event::load($last['target_id']);

      $first_date_formatter = format_date($first->getStartDate(), 'custom', 'd M');
      $last_date_formatter = format_date($last->getStartDate(), 'custom', 'd M');

      $build['date']['#markup'] =
        '<div class="date-wrapper first-date">' . $first_date_formatter . '</div>
        <div class="date-wrapper last-date">' . $last_date_formatter . '</div>';

      if ($first_date_formatter === $last_date_formatter) {
        $build['date']['#markup'] =
          '<div class="date-wrapper single-date">' . $first_date_formatter . '</div>';
      }
    }

    $build['#cache']['contexts'][] = 'timezone';
    return $build;
  }

}
