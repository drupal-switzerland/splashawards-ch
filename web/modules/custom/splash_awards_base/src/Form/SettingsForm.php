<?php

namespace Drupal\splash_awards_base\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Custom form for the splash awards module config.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, TypedConfigManagerInterface $typedConfigManager, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($config_factory, $typedConfigManager);
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'splash_awards_base.settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config($this->getEditableConfigNames()[0]);
    $dateTimezone = $this->configFactory->get('system.date')->get('timezone')['default'];

    $options = [];
    $splashAwardEntities = $this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties([
      'vid' => 'splash_awards',
    ]);

    foreach ($splashAwardEntities as $option) {
      $options[$option->id()] = $option->label();
    }

    $form['award'] = [
      '#type' => 'select',
      '#title' => $this->t('Award'),
      '#options' => $options,
      '#description' => $this->t("Select the award term, new cases will be created for."),
      '#default_value' => $config->get('award') ?? NULL,
      '#size' => 1,
    ];

    $form['case_submission_active'] = [
      '#type' => 'checkbox',
      '#title' => $this->t("Open the case submission form"),
      '#description' => $this->t("If this is checked, the case submission form is open."),
      '#default_value' => $config->get('case_submission_active') ?? NULL,
    ];

    $form['deadline'] = [
      '#type' => 'datetime',
      '#title' => $this->t("Case submission deadline"),
      '#default_value' => $config->get('deadline')
        ? new DrupalDateTime($config->get('deadline'))
        : NULL,
      '#description' => $this->t("Set the deadline for case submissions."),
      '#date_date_element' => 'date',
      '#date_time_element' => 'none',
      '#date_timezone' => $dateTimezone,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'splash_awards_base.settings',
    ];
  }

  /**
   * Form submission handler saves config.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->config('splash_awards_base.settings')
      ->set('case_submission_active', $form_state->getValue('case_submission_active'))
      ->save();

    $this->config('splash_awards_base.settings')
      ->set('award', $form_state->getValue('award'))
      ->save();

    if ($form_state->hasValue('deadline')) {
      $deadlineFormattedValue = $form_state->getValue('deadline')
        ? $form_state->getValue('deadline')->format('Y-m-d')
        : NULL;

      $this->config('splash_awards_base.settings')
        ->set('deadline', $deadlineFormattedValue)
        ->save();
    }
    else {
      $this->config('splash_awards_base.settings')
        ->set('deadline', NULL)
        ->save();
    }
  }

}
