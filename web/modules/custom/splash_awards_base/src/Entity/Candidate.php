<?php

namespace Drupal\splash_awards_base\Entity;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\user\Entity\User;

/**
 * Custom bundle class for award taxonomy terms.
 */
class Candidate extends User implements PageInterface {

  use StringTranslationTrait;

  /**
   * Returns the agencies logo url.
   */
  public function getLogo() {
    $file = !$this->get('field_company_logo')->isEmpty()
    ? $this->get('field_company_logo')->entity
    : NULL;

    return $file instanceof File
      ? \Drupal::entityTypeManager()->getStorage('image_style')->load('logo_teaser')->buildUrl($file->getFileUri())
      : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getHeroData(): array {
    return [
      'title' => $this->t('My case submissions'),
    ];
  }

  /**
   * Returns the teaser data for the page.
   */
  public function getUserData(): array {
    return [
      'logoUrl' => $this->getLogo(),
      'name' => $this->getDisplayName(),
      'linkUrl' => !$this->get('field_company_url')->isEmpty()
        ? $this->get('field_company_url')->uri
        : NULL,
      'size' => $this->get('field_company_size')->value,
      'contactName' => $this->get('field_company_contact_person')->value,
      'contactPhone' => $this->get('field_phone_contact_person')->value,
      'contactMail' => $this->get('field_mail_contact_person')->value,
      'taxAddress' => $this->get('field_invoice_address')->value,
      'taxMail' => $this->get('field_mail_invoice')->value,
      'taxId' => $this->get('field_tax_id')->value,
    ];
  }

  /**
   * Returns the teaser data for the page.
   */
  public function getAwardData(): array {
    $data = [];

    $caseIds = \Drupal::entityQuery('node')
      ->condition('uid', $this->id())
      ->condition('type', 'case')
      ->accessCheck()
      ->execute();

    $splashBaseSettings = \Drupal::config('splash_awards_base.settings');
    $activeAward = $splashBaseSettings->get('award')
      ? \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($splashBaseSettings->get('award'))
      : NULL;

    // Add active award in case the user has no cases.
    if ($activeAward instanceof Award) {
      $data[$activeAward->id()]['name'] = $activeAward->getName();
      $data[$activeAward->id()]['active'] = $splashBaseSettings->get('case_submission_active');

      if ($splashBaseSettings->get('deadline')) {
        $deadline = $splashBaseSettings->get('deadline');
        $date = new DrupalDateTime($deadline);
        $formatted = $date->format('d.m.Y');

        if ($deadline) {
          $data[$activeAward->id()]['text'] = $this->t('Submission deadline @date', [
            '@date' => $formatted,
          ]);
        }
      }
    }

    // Load the nodes from the IDs.
    foreach ($caseIds as $caseId) {
      $case = \Drupal::entityTypeManager()->getStorage('node')->load($caseId);

      if ($case instanceof SplashAwardCase) {
        /** @var Award $award */
        $award = !$case->get('field_award')->isEmpty()
          ? $case->get('field_award')->entity
          : NULL;

        if ($award instanceof Award) {
          $data[$award->id()]['id'] = $award->id();
          $data[$award->id()]['name'] = $award->getName();

          $data[$award->id()]['cases'][] = [
            'url' => $case->isPublished() ? $case->toUrl()->toString() : NULL,
            'title' => $case->getTitle(),
            'imageSrc' => $case->getImageUrl('field_customer_logo', 'image_grid'),
            'status' => $case->getStatus(),
            'submitted' => $case->hasField('field_submitted') && (bool) $case->get('field_submitted')->value,
            'category' => !$case->get('field_category')->isEmpty()
              ? $case->get('field_category')->entity->getName()
              : '-',
            'editUrl' => Url::fromRoute('splash_awards_base.case_submission', [
              'uuid' => $case->uuid(),
              'step' => '1',
            ])->toString(),
            'options' => [
             [
               'text' => '1 - ' . $this->t('Company'),
               'url' => Url::fromRoute('splash_awards_base.case_submission', [
                 'uuid' => $case->uuid(),
                 'step' => '1',
               ])->toString(),
             ],
              [
                'text' => '2 - ' . $this->t('Project'),
                'url' => Url::fromRoute('splash_awards_base.case_submission', [
                  'uuid' => $case->uuid(),
                  'step' => '2',
                ])->toString(),
              ],
              [
                'text' => '3 - ' . $this->t('Client'),
                'url' => Url::fromRoute('splash_awards_base.case_submission', [
                  'uuid' => $case->uuid(),
                  'step' => '3',
                ])->toString(),
              ],
              [
                'text' => '4 - ' . $this->t('Project details'),
                'url' => Url::fromRoute('splash_awards_base.case_submission', [
                  'uuid' => $case->uuid(),
                  'step' => '4',
                ])->toString(),
              ],
             [
               'text' => '5 - ' . $this->t('Media'),
               'url' => Url::fromRoute('splash_awards_base.case_submission', [
                 'uuid' => $case->uuid(),
                 'step' => '5',
               ])->toString(),
             ],
              [
                'text' => '6 - ' . $this->t('Completion'),
                'url' => Url::fromRoute('splash_awards_base.case_submission', [
                  'uuid' => $case->uuid(),
                  'step' => '6',
                ])->toString(),
              ],
            ],
          ];
        }
      }
    }

    return $data;
  }

}
