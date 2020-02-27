<?php

namespace Drupal\dropsolid_event_core\Plugin\DsField;

use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Renders each Event entity as a row, with parts in cells.
 *
 * @DsField(
 *   id = "event_listing_table",
 *   title = @Translation("List all events in table"),
 *   entity_type = {
 *    "node"
 *   },
 *   ui_limit = {
 *    "event|*"
 *   },
 *   provider = "dropsolid_event_core"
 * )
 */
class ListAllEventsInTable extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    /** @var \Drupal\node\NodeInterface $entity */
    $entity = $this->entity();

    foreach ($entity->get('field_events') as $item) {
      /** @var \Drupal\dropsolid_event_core\Entity\EventInterface $event */
      $event = $item->entity;
      if (!$event) {
        continue;
      }

      $time = $event->buildDateComponent(NULL, 'H:i', 'time-table-element');
      if ($event->get('field_event_closed')->value) {
        $time = ['#markup' => '/'];
      }

      $build['#rows'][] = [
        'weekday' =>
          [
            'data' =>
              [
                '#markup' => format_date($event->getStartDate(), 'custom', 'D') . ':',
              ],
          ],
        'date' =>
          [
            'data' =>
              [
                $event->buildDateComponent('d/m/Y', NULL, 'date-table-element'),
              ],
          ],
        'time' =>
          [
            'data' => [
              $time,
            ],
          ],
      ];
    }

    if (!empty($build)) {
      $build['#type'] = 'table';
      $build['#responsive'] = FALSE;
      $build['#cache']['contexts'][] = 'timezone';
    }

    return $build;
  }

}
