<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute] class DateRange extends Constraint
{
    public string $message = 'La date de fin "{{ end }}" doit être postérieure à la date de début "{{ start }}"';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}