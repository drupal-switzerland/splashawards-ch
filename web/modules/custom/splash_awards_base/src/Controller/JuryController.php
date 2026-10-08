<?php

namespace Drupal\splash_awards_base\Controller;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormState;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\splash_awards_base\Form\RatingForm;
use Drupal\splash_awards_base\Services\RatingHelper;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for handling jury specific routes.
 */
class JuryController extends ControllerBase {

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
   * The rating helper service.
   */
  protected RatingHelper $ratingHelper;

  /**
   * The renderer.
   */
  protected RendererInterface $renderer;

  /**
   * The class resolver.
   */
  protected ClassResolverInterface $classResolver;

  /**
   * Constructs a new event controller.
   */
  public function __construct(
    AccountProxyInterface $currentUser,
    EntityTypeManager $entityTypeManager,
    RatingHelper $ratingHelper,
    RendererInterface $renderer,
    ClassResolverInterface $classResolver,
  ) {
    $this->currentUser = $currentUser;
    $this->entityTypeManager = $entityTypeManager;
    $this->ratingHelper = $ratingHelper;
    $this->renderer = $renderer;
    $this->classResolver = $classResolver;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('splash_awards_base.rating_helper'),
      $container->get('renderer'),
      $container->get('class_resolver'),
    );
  }

  /**
   * Returns a render-able array for the event-management page.
   */
  public function dashboard($user): array|RedirectResponse {
    $splashConfig = $this->config('splash_awards_base.settings');
    $awardId = $splashConfig->get('award');

    if (!$awardId) {
      throw new NotFoundHttpException();
    }

    $currentUser = $this->currentUser();

    // Redirect to current users dashboard if the id does not match.
    if ($user->id() !== $currentUser->id()) {
      return new RedirectResponse(
        Url::fromRoute('splash_awards_base.jury_dashboard',
          ['user' => $currentUser->id()]
        )->toString());
    }

    $caseIds = $this->entityTypeManager->getStorage('node')->getQuery()
      ->condition('type', 'case')
      ->condition('field_award', $awardId)
      ->accessCheck()
      ->execute();

    $jury = $this->entityTypeManager
      ->getStorage('user')
      ->loadByProperties([
        'roles' => 'jury',
      ]);

    foreach ($jury as $juryMember) {
      $build['#juryData'][] = [
        'name' => $juryMember->getDisplayName(),
      ];
    }
    $juryCount = count($jury);

    $ratingHelper = $this->ratingHelper;

    // Aggregate the whole jury's points per case in one read per member, so the
    // jury rating/emotion and the rank reflect all jury members' ratings. The
    // per-member ratings are kept for the chair's team progress view.
    $juryAggregate = [];
    $allJuryRatings = [];
    foreach ($jury as $juryId => $juryMember) {
      $memberRatings = $ratingHelper->getAllRatings($juryId);
      $allJuryRatings[$juryId] = $memberRatings;
      foreach ($memberRatings as $ratedCaseId => $rating) {
        $totals = $ratingHelper->computeTotals($rating);
        $juryAggregate[$ratedCaseId]['score'] = ($juryAggregate[$ratedCaseId]['score'] ?? 0) + $totals['score'];
        $juryAggregate[$ratedCaseId]['emotion'] = ($juryAggregate[$ratedCaseId]['emotion'] ?? 0) + $totals['emotion'];
      }
    }

    // The current user's own ratings (single read).
    $myRatings = $ratingHelper->getAllRatings($currentUser->id());

    $cases = [];
    $validCaseIds = [];
    $totalCases = 0;
    $ratedCases = 0;
    $inProgressCases = 0;
    foreach ($caseIds as $caseId) {
      $case = $this->entityTypeManager->getStorage('node')->load($caseId);
      // The jury chair can re-sort a case into another category for the jury.
      // That override lives in field_jury_category; the original field_category
      // is left untouched. Group by the override when present.
      $juryCategory = $case instanceof Node && !$case->get('field_jury_category')->isEmpty()
        ? $case->get('field_jury_category')->entity
        : NULL;
      $category = $juryCategory ?: $case->get('field_category')->entity;

      if ($case instanceof Node && $category instanceof TermInterface) {
        $categoryName = $category->getName();

        $juryScore = $juryAggregate[$caseId]['score'] ?? 0;
        $juryEmotion = $juryAggregate[$caseId]['emotion'] ?? 0;
        $myRating = $myRatings[$caseId] ?? [];
        $myTotals = $ratingHelper->computeTotals($myRating);
        $myStatus = $ratingHelper->computeStatus($myRating);

        $validCaseIds[] = $caseId;
        $totalCases++;
        if ($myStatus === 'complete') {
          $ratedCases++;
        }
        elseif ($myStatus === 'in_progress') {
          $inProgressCases++;
        }

        $cases[$categoryName][] = [
          'id' => (int) $caseId,
          // Used for ranking/sorting, removed before output.
          'juryScore' => $juryScore,
          'juryEmotion' => $juryEmotion,
          'status' => $myStatus,
          'url' => $case->toUrl()->toString(),
          'rateUrl' => Url::fromRoute('splash_awards_base.rating_form_partial', ['case' => $caseId])->toString(),
          'title' => $case->getTitle(),
          'imageSrc' => $case->getImageUrl('field_customer_logo', 'image_grid'),
          'category' => $categoryName,
          'categoryId' => (int) $category->id(),
          'rating' => sprintf('%d/%d', $juryScore, $juryCount * 40),
          'emotion' => sprintf('%d/%d', $juryEmotion, $juryCount * 5),
          'myRating' => sprintf('%d/40', $myTotals['score']),
          'myEmotion' => sprintf('%d/5', $myTotals['emotion']),
        ];
      }
    }

    // Rank and the jury rating/emotion (and the rank-based order) are only
    // exposed to the jury chair; a normal jury member must not be able to infer
    // the standing.
    $canSeeJuryRatings = in_array('jury_chair', $currentUser->getRoles(), TRUE);

    // Within each category, sort by jury score (highest first), breaking ties
    // by jury emotion, and assign the rank from that order. Two cases only
    // share a rank when both their jury score and emotion are equal.
    foreach ($cases as $categoryName => $categoryCases) {
      usort($categoryCases, fn($a, $b) =>
        $b['juryScore'] <=> $a['juryScore']
        ?: $b['juryEmotion'] <=> $a['juryEmotion']
      );

      $rank = 0;
      $position = 0;
      $previous = NULL;
      foreach ($categoryCases as &$row) {
        $position++;
        $current = [$row['juryScore'], $row['juryEmotion']];
        if ($current !== $previous) {
          $rank = $position;
          $previous = $current;
        }
        $row['rank'] = $rank;
        unset($row['juryScore'], $row['juryEmotion']);
      }
      unset($row);

      // Normal jury members see a neutral, alphabetical order that does not
      // reveal the ranking.
      if (!$canSeeJuryRatings) {
        usort($categoryCases, fn($a, $b) => strcasecmp($a['title'], $b['title']));
      }

      $build['#caseData'][$categoryName] = $categoryCases;
    }

    $activeAward = $this->entityTypeManager->getStorage('taxonomy_term')->load($awardId);

    // Without this, the page is cached (Dynamic Page Cache applies to
    // authenticated users too) with no dependency on the active-award config
    // or the requesting user, so switching the award in the settings form
    // does not bust the cache, and a cached page could in theory be served
    // to a different jury member than the one it was built for.
    $build['#cache']['contexts'][] = 'user';
    $build['#cache']['tags'] = Cache::mergeTags(
      $splashConfig->getCacheTags(),
      $activeAward instanceof TermInterface ? $activeAward->getCacheTags() : []
    );

    $build['#theme'] = 'jury_dashboard';
    $build['#title'] = Markup::create($this->t('Jury dashboard') . '<br>' . $this->t('Submissions for @name', ['@name' => $activeAward->getName()]));
    $build['#name'] = $user->getDisplayName();
    $build['#canSeeJuryRatings'] = $canSeeJuryRatings;

    // The chair gets the full category list to re-sort cases.
    $build['#categories'] = [];
    if ($canSeeJuryRatings) {
      $terms = $this->entityTypeManager->getStorage('taxonomy_term')
        ->loadByProperties(['vid' => 'categories']);
      foreach ($terms as $term) {
        $build['#categories'][] = ['id' => (int) $term->id(), 'name' => $term->getName()];
      }
      usort($build['#categories'], fn($a, $b) => strcasecmp($a['name'], $b['name']));
    }
    $build['#progress'] = [
      'rated' => $ratedCases,
      'inProgress' => $inProgressCases,
      'total' => $totalCases,
      'percentage' => $totalCases > 0 ? (int) round($ratedCases / $totalCases * 100) : 0,
      'inProgressPercentage' => $totalCases > 0 ? (int) round($inProgressCases / $totalCases * 100) : 0,
    ];

    // The chair additionally sees each individual jury member's progress.
    $build['#juryProgress'] = [];
    if ($canSeeJuryRatings) {
      foreach ($jury as $juryId => $juryMember) {
        // The chair's own progress is already shown in the main widget above.
        if ($juryId == $currentUser->id()) {
          continue;
        }

        $complete = 0;
        $inProgress = 0;
        foreach ($validCaseIds as $cid) {
          $status = $ratingHelper->computeStatus($allJuryRatings[$juryId][$cid] ?? []);
          if ($status === 'complete') {
            $complete++;
          }
          elseif ($status === 'in_progress') {
            $inProgress++;
          }
        }

        $build['#juryProgress'][] = [
          'name' => $juryMember->getDisplayName(),
          'rated' => $complete,
          'inProgress' => $inProgress,
          'total' => $totalCases,
          'percentage' => $totalCases > 0 ? (int) round($complete / $totalCases * 100) : 0,
          'inProgressPercentage' => $totalCases > 0 ? (int) round($inProgress / $totalCases * 100) : 0,
        ];
      }
    }

    return $build;
  }

  /**
   * Renders the bare rating form markup for the Alpine modal.
   *
   * The form is fetched client-side and injected into a custom modal, so we
   * return only the form HTML instead of the full themed page. The case is
   * picked up from the route by RatingForm::buildForm().
   */
  public function ratingFormPartial(NodeInterface $case): Response {
    // Build the form with an explicit FormState so that a successful XHR
    // submit (which sets a JsonResponse in RatingForm::submitForm) is returned
    // directly. On GET, or when validation fails, we render only the form
    // markup for (re-)injection into the Alpine modal.
    $form_state = new FormState();
    $form_object = $this->classResolver->getInstanceFromDefinition(RatingForm::class);
    $form = $this->formBuilder()->buildForm($form_object, $form_state);

    if ($response = $form_state->getResponse()) {
      return $response;
    }

    // Render any validation messages alongside the form so they are visible
    // inside the modal on a failed submit.
    $build = [
      'messages' => ['#type' => 'status_messages'],
      'form' => $form,
    ];
    $html = (string) $this->renderer->renderInIsolation($build);

    return new Response($html);
  }

  /**
   * Sets the jury-category override for a case (jury chair only).
   *
   * Stores the chosen category in field_jury_category and leaves the original
   * field_category untouched.
   */
  public function setJuryCategory(NodeInterface $case, Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE);
    $categoryId = isset($data['category']) ? (int) $data['category'] : 0;

    // An empty value clears the override (falls back to the original category).
    if ($categoryId === 0) {
      $case->set('field_jury_category', NULL);
    }
    else {
      $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($categoryId);
      if (!$term instanceof TermInterface || $term->bundle() !== 'categories') {
        throw new NotFoundHttpException();
      }
      $case->set('field_jury_category', $categoryId);
    }

    $case->save();

    return new JsonResponse(['status' => 'ok']);
  }

}
