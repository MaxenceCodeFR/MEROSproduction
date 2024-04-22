<?php

namespace App\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PasswordRequirements extends Constraint
{
    public string $message = 'Votre mot de passe doit contenir au moins 8 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial (@$!%*?&)';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
