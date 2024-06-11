<?php

namespace App\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PasswordRequirements extends Constraint
{
    public string $message = 'Votre mot de passe doit contenir au moins 8 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial (@$!%*?&)';
    public string $messageLength = 'Votre mot de passe doit contenir au moins 8 caractères.';
    public string $messageUppercase = 'Votre mot de passe doit contenir au moins une lettre majuscule.';
    public string $messageLowercase = 'Votre mot de passe doit contenir au moins une lettre minuscule.';
    public string $messageDigit = 'Votre mot de passe doit contenir au moins un chiffre.';
    public string $messageSpecialChar = 'Votre mot de passe doit contenir au moins un caractère spécial (@$!%*?&).';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}

