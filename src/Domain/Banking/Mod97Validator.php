<?php

declare(strict_types=1);

namespace App\Domain\Banking;

/**
 * Validateur IBAN utilisant l'algorithme de Luhn (ISO 13616).
 *
 * C'est un SERVICE DOMAIN - PHP pur, aucune dependance framework.
 * Candidat ideal pour le TDD car :
 * - Pas de dependances externes a mocker
 * - Entrees/sorties claires
 * - Assez complexe pour beneficier du developpement incremental
 */
final class Mod97Validator
{
    private const MIN_LENGTH = 5;

    /**
     * Valide un IBAN.
     *
     * @throws InvalidIbanException si le format est invalide
     * @return bool true si le checksum est valide, false sinon
     */
    public function validate(string $iban): bool
    {
        // Cycle 1 : chaine vide
        if ($iban === '') {
            throw new InvalidIbanException('IBAN cannot be empty');
        }

        // Cycle 2 : caracteres invalides
        if (!preg_match('/^[A-Za-z0-9]+$/', $iban)) {
            throw new InvalidIbanException('IBAN contains invalid characters');
        }

        // Cycle 3 : trop court
        if (strlen($iban) < self::MIN_LENGTH) {
            throw new InvalidIbanException('IBAN is too short');
        }

        // TODO: Implementer l'algorithme Luhn (branche 03)
        return true;
    }
}
