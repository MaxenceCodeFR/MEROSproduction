<?php

namespace App\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class PasswordRequirementsValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof PasswordRequirements) {
            throw new UnexpectedTypeException($constraint, PasswordRequirements::class);
        }

        if (null === $value || '' === $value) {
            return;  // Let NotBlank handle this case
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        // Regex to check the password strength
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $value, $matches)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
