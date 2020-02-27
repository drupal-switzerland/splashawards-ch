<?php

namespace Drupal\dropsolid_event_core\Plugin\DsField;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Renders start and end date as one field.
 *
 * @DsField(
 *   id = "event_dates_dsfield",
 *   title = @Translation("Format the event dates"),
 *   entity_type = {
 *    "event"
 *   },
 *   ui_limit = {
 *    "event|*"
 *   },
 *   provider = "dropsolid_event_core"
 * )
 */
class EventDates extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'date_format' => 'd/m/Y',
      'time_format' => 'H:i',
      'combine_duplicates' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary($settings) {
    $config = $this->getConfiguration();

    $summary = [];
    $summary[] = 'date format: ' . $config['date_format'];
    $summary[] = 'time format: ' . $config['time_format'];
    $summary[] = 'Combine duplicates: ' . ($config['combine_duplicates'] ? 'Yes' : 'No');

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    $form['date_format'] = [
      '#title' => t('Date format'),
      '#description' => t('Enter a PHP date format to use as the fallback for when javascript is not available. Exclude time components.'),
      '#type' => 'textfield',
      '#default_value' => $config['date_format'],
    ];

    $form['time_format'] = [
      '#title' => t('Time format'),
      '#description' => t('Enter a PHP date format to use as the fallback for when javascript is not available. ONLY time components.'),
      '#type' => 'textfield',
      '#default_value' => $config['time_format'],
    ];

    $form['combine_duplicates'] = [
      '#title' => t('Combine duplicates'),
      '#description' => t('When the start and end dates are the same, only show it once. When the start and end times are the same, only show it once.'),
      '#type' => 'checkbox',
      '#default_value' => $config['combine_duplicates'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    /** @var \Drupal\dropsolid_event_core\Entity\EventInterface $entity */
    $entity = $this->entity();

    return $entity->buildDateComponent($config['date_format'], $config['time_format'], 'ds-field-range__event', $config['combine_duplicates']);
  }

}
