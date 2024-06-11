<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use App\Entity\ContactCompany;

class DateRangeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DateRange) {
            throw new UnexpectedTypeException($constraint, DateRange::class);
        }

        if (!$value instanceof ContactCompany) {
            throw new UnexpectedValueException($value, ContactCompany::class);
        }

        if ($value->getStart() > $value->getEnd()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('end')
                ->setParameter('{{ start }}', $value->getStart()->format('Y-m-d H:i'))
                ->setParameter('{{ end }}', $value->getEnd()->format('Y-m-d H:i'))
                ->addViolation();
        }
    }
}