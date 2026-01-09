<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Banking\Iban;
use App\Domain\Banking\InvalidIbanException;

/**
 * Use Case : Valider l'IBAN d'un paiement.
 *
 * Cette classe fait partie de la couche APPLICATION.
 * Elle orchestre l'utilisation du Domain.
 *
 * Caracteristiques :
 * - Pas de logique metier (c'est dans le Domain)
 * - Coordonne les appels au Domain
 * - Gere les transactions si necessaire
 * - Peut appeler des ports (interfaces) vers l'Infrastructure
 */
final class ValidatePaymentIban
{
    /**
     * Execute le use case.
     *
     * @return ValidatePaymentIbanResult
     */
    public function execute(string $rawIban): ValidatePaymentIbanResult
    {
        try {
            // Le Domain fait le travail
            $iban = new Iban($rawIban);

            return ValidatePaymentIbanResult::success(
                normalizedIban: $iban->toString(),
                countryCode: $iban->getCountryCode(),
                formattedIban: $iban->toFormattedString()
            );
        } catch (InvalidIbanException $e) {
            return ValidatePaymentIbanResult::failure($e->getMessage());
        }
    }
}

/**
 * Result object pour le use case.
 *
 * Pattern : au lieu de lever des exceptions,
 * on retourne un objet qui contient le resultat ou l'erreur.
 */
final class ValidatePaymentIbanResult
{
    private function __construct(
        public readonly bool $isValid,
        public readonly ?string $normalizedIban,
        public readonly ?string $countryCode,
        public readonly ?string $formattedIban,
        public readonly ?string $errorMessage,
    ) {
    }

    public static function success(
        string $normalizedIban,
        string $countryCode,
        string $formattedIban
    ): self {
        return new self(
            isValid: true,
            normalizedIban: $normalizedIban,
            countryCode: $countryCode,
            formattedIban: $formattedIban,
            errorMessage: null
        );
    }

    public static function failure(string $errorMessage): self
    {
        return new self(
            isValid: false,
            normalizedIban: null,
            countryCode: null,
            formattedIban: null,
            errorMessage: $errorMessage
        );
    }
}
