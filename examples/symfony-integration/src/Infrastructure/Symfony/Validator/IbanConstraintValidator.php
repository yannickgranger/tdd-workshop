<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Validator;

use App\Domain\Banking\InvalidIbanException;
use App\Domain\Banking\LuhnValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validateur pour IbanConstraint.
 *
 * Cette classe est un ADAPTATEUR Infrastructure.
 * Elle utilise le Domain (LuhnValidator) depuis Symfony Validator.
 *
 * Pattern :
 * - Infrastructure depend du Domain
 * - Le Domain ne sait rien de Symfony
 * - On peut remplacer Symfony sans toucher au Domain
 *
 * Tests :
 * - Le Domain est teste avec des Unit tests (TDD)
 * - L'adaptateur est teste avec des Integration tests
 */
final class IbanConstraintValidator extends ConstraintValidator
{
    private LuhnValidator $validator;

    public function __construct()
    {
        $this->validator = new LuhnValidator();
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof IbanConstraint) {
            throw new UnexpectedTypeException($constraint, IbanConstraint::class);
        }

        if (null === $value || '' === $value) {
            return; // Laisser NotBlank gerer le cas vide
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        try {
            $isValid = $this->validator->validate($value);

            if (!$isValid) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $value)
                    ->addViolation();
            }
        } catch (InvalidIbanException) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
