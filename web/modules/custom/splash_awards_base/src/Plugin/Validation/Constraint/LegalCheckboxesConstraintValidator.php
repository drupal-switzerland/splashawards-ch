<?php

namespace Drupal\splash_awards_base\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the LegalCheckboxes constraint.
 */
class LegalCheckboxesConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    $checked = $value->first() && (bool) $value->first()->value;

    if (!$checked) {
      $this->context->addViolation($constraint->message);
    }
  }

}
