<?php

namespace Drupal\splash_awards_base\Services;

use Drupal\user\UserDataInterface;

/**
 * Stores and computes jury ratings for cases.
 */
class RatingHelper {

  /**
   * The user data service.
   */
  protected UserDataInterface $userData;

  public function __construct(UserDataInterface $userData) {
    $this->userData = $userData;
  }

  const CATEGORIES = [
    'business_case' => [
      'title' => '💼 Business case',
      'options' => [
        0 => '0',
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
      ],
    ],
    'community' => [
      'title' => '🤝 Community value',
      'options' => [
        0 => '0',
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
      ],
    ],

    'concept' => [
      'title' => '💡 Concept & strategy',
      'options' => [
        0 => '0',
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
      ],
    ],
    'ux' => [
      'title' => '🎨 Design & UX',
      'options' => [
        0 => '0',
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
      ],
    ],
    'quality' => [
      'title' => '🏆 Quality of execution',
      'options' => [
        0 => '0',
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
      ],
    ],
    'innovativeness' => [
      'title' => '🚀 Innovativeness',
      'options' => [
        0 => '0',
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
      ],
    ],
    'technology' => [
      'title' => '⚙️ Technology',
      'options' => [
        0 => '0',
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
      ],
    ],
    'accessibility' => [
      'title' => '♿ Accessibility',
      'options' => [
        0 => '0',
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
      ],
    ],
    'emotion' => [
      'title' => '❤️ Emotion',
      'options' => [
        0 => '0',
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
      ],
    ],
  ];

  /**
   * Retrieves a rating for a user and case from the database.
   */
  public function getRating($userId, $caseId): array {
    return $this->userData->get('splash_awards_base', $userId, 'rating_' . $caseId) ?? [];
  }

  /**
   * Saves a rating for a user and case to the database.
   */
  public function setRating($userId, $caseId, array $rating): void {
    $this->userData->set('splash_awards_base', $userId, 'rating_' . $caseId, $rating);
  }

  /**
   * Returns all of a user's case ratings in a single read.
   *
   * @return array
   *   Keyed by case id, each value the stored rating array. Useful for
   *   aggregating across many cases without a query per case.
   */
  public function getAllRatings($userId): array {
    $stored = $this->userData->get('splash_awards_base', $userId) ?? [];

    $ratings = [];
    foreach ($stored as $key => $value) {
      if (is_array($value) && str_starts_with($key, 'rating_')) {
        $ratings[(int) substr($key, strlen('rating_'))] = $value;
      }
    }

    return $ratings;
  }

  /**
   * Score totals for a single rating array.
   *
   * @return array
   *   ['score' => int, 'emotion' => int] — the summed non-emotion categories
   *   (max 40) and the emotion value (max 5).
   */
  public function computeTotals(array $rating): array {
    $emotion = (int) ($rating['emotion'] ?? 0);
    $score = 0;
    // Only the rating categories count toward the score — never extra stored
    // data such as the internal note.
    foreach (array_keys(self::CATEGORIES) as $key) {
      if ($key !== 'emotion') {
        $score += (int) ($rating[$key] ?? 0);
      }
    }

    return ['score' => $score, 'emotion' => $emotion];
  }

  /**
   * Progress of a single rating array.
   *
   * @return string
   *   'none' (no category rated), 'in_progress' (at least one but not all) or
   *   'complete' (every category rated).
   */
  public function computeStatus(array $rating): string {
    $filled = 0;
    foreach (array_keys(self::CATEGORIES) as $key) {
      if (isset($rating[$key]) && $rating[$key] !== '') {
        $filled++;
      }
    }

    if ($filled === 0) {
      return 'none';
    }

    return $filled === count(self::CATEGORIES) ? 'complete' : 'in_progress';
  }

  /**
   * Returns a user's score totals for a case.
   */
  public function getTotals($userId, $caseId): array {
    return $this->computeTotals($this->getRating($userId, $caseId));
  }

  /**
   * Rating progress of a user for a case.
   */
  public function getStatus($userId, $caseId): string {
    return $this->computeStatus($this->getRating($userId, $caseId));
  }

}
