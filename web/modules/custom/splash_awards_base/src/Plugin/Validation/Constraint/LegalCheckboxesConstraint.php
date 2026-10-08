<?php

namespace Drupal\splash_awards_base\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Ensures that the legally required checkboxes are checked.
 *
 * @Constraint(
 *   id = "LegalCheckboxes",
 *   label = @Translation("Legal checkboxes required", context = "Validation"),
 * )
 */
class LegalCheckboxesConstraint extends Constraint {

  /**
   * The violation message.
   *
   * @var string
   */
  public $message = 'You must first complete all fields marked with an asterisk before the submission can be saved';

}
