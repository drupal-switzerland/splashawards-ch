<?php

namespace Drupal\splash_awards_base\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\splash_awards_base\Entity\Award;
use Drupal\splash_awards_base\Entity\SplashAwardCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * An example controller.
 */
class CaseSubmissionController extends ControllerBase {

  /**
   * The current user service.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The EntityTypeManager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * The entity repository.
   */
  protected EntityRepositoryInterface $entityRepository;

  /**
   * Constructs a new event controller.
   */
  public function __construct(
    AccountProxyInterface $currentUser,
    EntityTypeManager $entityTypeManager,
    EntityRepositoryInterface $entityRepository,
  ) {
    $this->currentUser = $currentUser;
    $this->entityTypeManager = $entityTypeManager;
    $this->entityRepository = $entityRepository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('entity.repository'),
    );
  }

  /**
   * Returns a render-able array for the event-management page.
   */
  public function caseSubmission(string $uuid = 'new', string $step = '1') {
    $case = NULL;
    $splashConfig = $this->config('splash_awards_base.settings');
    $isActive = $splashConfig->get('case_submission_active');
    $deadline = $splashConfig->get('deadline');
    $awardId = $splashConfig->get('award');

    $activeAward = $awardId
      ? $this->entityTypeManager->getStorage('taxonomy_term')->load($awardId)
      : NULL;

    if (!$isActive) {
      $this->messenger()->addWarning($this->t('The submission has been closed'));
      return new RedirectResponse(Url::fromRoute('user.page')->toString());
    }

    if ($deadline) {
      $deadlineTimeStamp = strtotime($deadline);
      $todayTimestamp = strtotime('today');

      if ($todayTimestamp && $deadlineTimeStamp && $todayTimestamp >= $deadlineTimeStamp) {
        $this->messenger()->addWarning($this->t('The submission deadline has expired'));
        return new RedirectResponse(Url::fromRoute('user.page')->toString());
      }
    }

    // If no UUID is provided, we are creating a new case.
    if ($uuid === 'new') {
      // Only allow first step for new cases.
      if ($step !== '1') {
        return new RedirectResponse(Url::fromRoute('splash_awards_base.case_submission', [
          'uuid' => 'new',
          'step' => '1',
        ])->toString());
      }

      $activeAwardCount = $activeAward
        ? $this->entityTypeManager->getStorage('node')->getQuery()
          ->condition('uid', $this->currentUser->id())
          ->condition('type', 'case')
          ->condition('field_award', $activeAward->id())
          ->accessCheck()
          ->count()
          ->execute()
        : NULL;

      if ($activeAwardCount >= 3) {
        $this->messenger()->addWarning($this->t('Only up to three cases may be submitted'));
        return new RedirectResponse(Url::fromRoute('user.page')->toString());
      }

      $case = $this->entityTypeManager()
        ->getStorage('node')
        ->create([
          'type' => 'case',
          'title' => $this->t('New case'),
          'uid' => $this->currentUser->id(),
        ]);
    }
    else {
      $case = $this->entityRepository->loadEntityByUuid('node', $uuid);

      if (!$case instanceof SplashAwardCase) {
        throw new NotFoundHttpException();
      }
    }

    // Get the current splash award.
    if ($uuid === 'new') {
      $award = $activeAward;
      $case->set('field_award', ['target_id' => $activeAward->id()]);
    }
    else {
      $award = $case->get('field_award')->entity;
    }

    if (!$award instanceof Award) {
      throw new NotFoundHttpException();
    }

    $stepFormTypeMap = [
      '1' => 'submission_step_1',
      '2' => 'submission_step_2',
      '3' => 'submission_step_3',
      '4' => 'submission_step_4',
      '5' => 'submission_step_5',
      '6' => 'submission_step_6',
    ];

    // Check if user is allowed to access the case.
    if ($uuid !== 'new' && !$case->access('update')) {
      throw new AccessDeniedHttpException();
    }

    $form = $this->entityTypeManager()
      ->getFormObject('node', $stepFormTypeMap[$step])
      ->setEntity($case);

    $build['#theme'] = 'case_submission';
    $build['#title'] = $award->getName() . ': ' . $this->t('Case submission');
    $build['#subtitle'] = $case->getTitle() . ' ' . sprintf('(%s/6)', $step);
    $build['#step'] = $step;
    $build['#form'] = $this->formBuilder()->getForm($form);
    return $build;
  }

}
