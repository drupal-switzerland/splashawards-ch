<?php

namespace Drupal\splash_awards_base\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\splash_awards_base\Entity\SplashAwardCase;
use Drupal\splash_awards_base\Services\RatingHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Custom form to rate cases.
 */
class RatingForm extends FormBase {

  /**
   * The rating helper service.
   */
  protected RatingHelper $helper;

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  public function __construct(RatingHelper $helper, EntityTypeManagerInterface $entityTypeManager) {
    $this->helper = $helper;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create($container) {
    return new static(
      $container->get('splash_awards_base.rating_helper'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'splash_awards_base.rating_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $case = $this->getRouteMatch()->getParameter('case');

    if (!$case instanceof SplashAwardCase) {
      throw new NotFoundHttpException();
    }

    // Post to the canonical form route regardless of where the markup was
    // loaded from (e.g. the modal partial endpoint), so submitForm() runs and
    // redirects back to the dashboard.
    $form['#action'] = Url::fromRoute('splash_awards_base.rating_form', ['case' => $case->id()])->toString();

    $form['intro'] = [
      '#markup' => '<div class="mb-6 border-b border-saw-pale-blue pb-4">'
      . '<span class="saw-tiny-text uppercase tracking-wide text-saw-bright-blue">' . $this->t('Rate submission') . '</span>'
      . '<h2 class="saw-small-headline text-saw-dark-blue m-0">' . $case->getTitle() . '</h2>'
      . '</div>',
    ];

    $ratings = $this->helper->getRating($this->currentUser()->id(), $case->id());

    // Two-column layout: the category ratings on the left, the jury member's
    // internal note on the right (stacks on small screens).
    $form['columns'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['flex', 'flex-col', 'lg:flex-row', 'gap-6'],
      ],
    ];

    $form['columns']['ratings'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['lg:flex-1'],
      ],
    ];

    foreach ($this->helper::CATEGORIES as $key => $values) {
      $form['columns']['ratings'][$key] = [
        '#type' => 'radios',
        '#title' => $values['title'],
        '#options' => $values['options'],
        '#default_value' => $ratings[$key] ?? NULL,
        '#attributes' => [
          'class' => ['mb-5'],
        ],
      ];
    }

    // Right column: the jury member's own note and (for the chair) every
    // member's note below it.
    $form['columns']['side'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['lg:w-72', 'flex-shrink-0'],
      ],
    ];

    // Read-only context for the jury: the hosting details and the
    // submitter's own note to the jury, both entered during case
    // submission. Neither is shown on the public case page, so this is
    // the only place jury members can see them. Placed above the jury
    // member's own note so it's visible before they write theirs.
    $hosting = trim((string) $case->get('field_hosting')->value);
    $noteToJury = trim((string) $case->get('field_note')->value);

    if ($hosting !== '' || $noteToJury !== '') {
      $form['columns']['side']['submission_info'] = [
        '#type' => 'inline_template',
        '#template' => <<<'TWIG'
          <div class="mb-4 pb-4 border-b border-saw-pale-blue space-y-3">
            {% if hosting %}
              <div>
                <h3 class="saw-tiny-text uppercase tracking-wide text-saw-bright-blue mb-2">{{ 'Hosting'|t }}</h3>
                <div class="saw-small-text whitespace-pre-line rounded-lg bg-saw-pale-blue/40 p-3 text-saw-dark-blue">{{ hosting }}</div>
              </div>
            {% endif %}
            {% if note %}
              <div class="mt-3">
                <h3 class="saw-tiny-text uppercase tracking-wide text-saw-bright-blue mb-2">{{ 'Note to the jury'|t }}</h3>
                <div class="saw-small-text whitespace-pre-line rounded-lg bg-saw-pale-blue/40 p-3 text-saw-dark-blue">{{ note }}</div>
              </div>
            {% endif %}
          </div>
          TWIG,
        '#context' => [
          'hosting' => $hosting,
          'note' => $noteToJury,
        ],
      ];
    }

    $form['columns']['side']['note'] = [
      '#type' => 'textarea',
      '#title' => '📝' . $this->t('Internal note'),
      '#description' => $this->t('Only visible to you.'),
      '#default_value' => $ratings['note'] ?? '',
      '#rows' => 5,
      '#attributes' => [
        'class' => ['w-full', 'rounded-lg', 'border', 'border-saw-pale-blue', 'p-3'],
      ],
    ];

    // The jury chair sees every member's note for this case, switchable via a
    // dropdown, directly under their own note.
    if (in_array('jury_chair', $this->currentUser()->getRoles(), TRUE)) {
      $jury = $this->entityTypeManager->getStorage('user')
        ->loadByProperties(['roles' => 'jury']);

      $members = [];
      foreach ($jury as $juryId => $member) {
        $memberRating = $this->helper->getRating($juryId, $case->id());
        $members[] = [
          'uid' => (int) $juryId,
          'name' => $member->getDisplayName(),
          'note' => $memberRating['note'] ?? '',
        ];
      }

      if ($members) {
        // Alpine handles the dropdown switching; if it fails to init the notes
        // simply stay all visible (still readable). Note text is autoescaped.
        $form['columns']['side']['member_notes'] = [
          '#type' => 'inline_template',
          '#template' => <<<'TWIG'
            <div class="mt-4 pt-4 border-t border-saw-pale-blue" x-data="{ note: {{ first }} }">
              <h3 class="saw-tiny-text uppercase tracking-wide text-saw-bright-blue mb-2">{{ 'Member notes'|t }}</h3>
              <select x-model.number="note" class="w-full rounded-lg border border-saw-pale-blue p-2 mb-2 cursor-pointer bg-white">
                {% for m in members %}
                  <option value="{{ m.uid }}">{{ m.name }}</option>
                {% endfor %}
              </select>
              {% for m in members %}
                <div x-show="note === {{ m.uid }}" class="saw-small-text whitespace-pre-line rounded-lg bg-saw-pale-blue/40 p-3 text-saw-dark-blue">{% if m.note %}{{ m.note }}{% else %}<span class="opacity-60">{{ 'No note yet.'|t }}</span>{% endif %}</div>
              {% endfor %}
            </div>
          TWIG,
          '#context' => [
            'members' => $members,
            'first' => $members[0]['uid'],
          ],
        ];
      }
    }

    $form['actions'] = [
      '#type' => 'actions',
      '#attributes' => [
        'class' => ['flex', 'justify-end', 'mt-6', 'pt-4', 'border-t', 'border-saw-pale-blue'],
      ],
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#attributes' => [
        'class' => [
          'py-2', 'px-8', 'rounded-lg', 'border-0', 'text-white', 'bg-saw-dark-blue',
          'hover:opacity-80', 'transition-all', 'cursor-pointer',
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $case = $this->getRouteMatch()->getParameter('case');

    if (!$case instanceof SplashAwardCase) {
      throw new NotFoundHttpException();
    }

    // Ratings are optional — a jury member may save a partial rating and come
    // back later. Only store the categories that were actually given a value.
    $rating = [];
    foreach (array_keys($this->helper::CATEGORIES) as $key) {
      $value = $form_state->getValue($key);
      if ($value !== NULL && $value !== '') {
        $rating[$key] = $value;
      }
    }

    // The jury member's internal note is stored alongside the rating.
    $note = trim((string) $form_state->getValue('note'));
    if ($note !== '') {
      $rating['note'] = $note;
    }

    $userId = $this->currentUser()->id();
    $this->helper->setRating($userId, $case->id(), $rating);

    // When submitted from the Alpine modal (fetch/XHR), return JSON so the
    // client can close the modal and patch the row in place instead of a full
    // page reload. setResponse() short-circuits the form's redirect handling.
    if ($this->getRequest()->isXmlHttpRequest()) {
      $totals = $this->helper->getTotals($userId, $case->id());
      $form_state->setResponse(new JsonResponse([
        'status' => 'ok',
        'caseId' => (int) $case->id(),
        'score' => $totals['score'],
        'emotion' => $totals['emotion'],
        'ratingStatus' => $this->helper->getStatus($userId, $case->id()),
        'message' => (string) $this->t('Your ratings for the case "@case" have been saved.', ['@case' => $case->getTitle()]),
      ]));
      return;
    }

    $form_state->setRedirect('splash_awards_base.jury_dashboard', ['user' => $userId]);
    $this->messenger()->addMessage($this->t('Your ratings for the case "@case" have been saved.', ['@case' => $case->getTitle()]));
  }

}
