<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Contrainte Symfony pour valider un IBAN.
 *
 * Cette classe fait partie de la couche INFRASTRUCTURE.
 * C'est un ADAPTATEUR qui connecte le Domain a Symfony Validator.
 *
 * Usage dans un Form ou Entity :
 *
 * ```php
 * #[IbanConstraint]
 * private string $iban;
 * ```
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class IbanConstraint extends Constraint
{
    public string $message = 'The IBAN "{{ value }}" is not valid.';
}
