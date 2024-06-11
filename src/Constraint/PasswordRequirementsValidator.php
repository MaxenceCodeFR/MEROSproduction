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

        // Check password length
        if (strlen($value) < 8) {
            $this->context->buildViolation($constraint->messageLength)
                ->addViolation();
            return;
        }

        // Check for uppercase letter
        if (!preg_match('/[A-Z]/', $value)) {
            $this->context->buildViolation($constraint->messageUppercase)
                ->addViolation();
        }

        // Check for lowercase letter
        if (!preg_match('/[a-z]/', $value)) {
            $this->context->buildViolation($constraint->messageLowercase)
                ->addViolation();
        }

        // Check for digit
        if (!preg_match('/\d/', $value)) {
            $this->context->buildViolation($constraint->messageDigit)
                ->addViolation();
        }

        // Check for special character
        if (!preg_match('/[@$!%*?&]/', $value)) {
            $this->context->buildViolation($constraint->messageSpecialChar)
                ->addViolation();
        }
    }
}
