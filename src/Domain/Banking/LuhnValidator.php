<?php

declare(strict_types=1);

namespace App\Domain\Banking;

/**
 * Validateur IBAN utilisant l'algorithme ISO 13616 (mod 97).
 *
 * C'est un SERVICE DOMAIN - PHP pur, aucune dependance framework.
 *
 * Branche 04 : Ajout de la normalisation (espaces, minuscules).
 */
final class LuhnValidator
{
    private const MIN_LENGTH = 5;

    /**
     * Valide un IBAN.
     *
     * L'IBAN est normalise avant validation :
     * - Espaces supprimes
     * - Conversion en majuscules
     *
     * @throws InvalidIbanException si le format est invalide
     * @return bool true si le checksum est valide, false sinon
     */
    public function validate(string $iban): bool
    {
        // Etape 1 : Normaliser AVANT la validation de format
        $normalized = $this->normalize($iban);

        // Etape 2 : Valider le format
        $this->assertValidFormat($normalized);

        // Etape 3 : Verifier le checksum
        return $this->verifyChecksum($normalized);
    }

    /**
     * Normalise l'IBAN.
     *
     * Decisions prises grace aux tests :
     * - Les espaces sont courants (copier-coller depuis documents)
     * - Les minuscules arrivent (saisie manuelle)
     */
    private function normalize(string $iban): string
    {
        // Supprimer les espaces
        $iban = str_replace(' ', '', $iban);

        // Convertir en majuscules
        return strtoupper($iban);
    }

    /**
     * Verifie le format de base de l'IBAN.
     *
     * @throws InvalidIbanException
     */
    private function assertValidFormat(string $iban): void
    {
        if ($iban === '') {
            throw new InvalidIbanException('IBAN cannot be empty');
        }

        // Apres normalisation, seuls A-Z et 0-9 sont valides
        if (!preg_match('/^[A-Z0-9]+$/', $iban)) {
            throw new InvalidIbanException('IBAN contains invalid characters');
        }

        if (strlen($iban) < self::MIN_LENGTH) {
            throw new InvalidIbanException('IBAN is too short');
        }
    }

    /**
     * Verifie le checksum selon ISO 13616.
     */
    private function verifyChecksum(string $iban): bool
    {
        // Deplacer les 4 premiers caracteres a la fin
        $rearranged = substr($iban, 4) . substr($iban, 0, 4);

        // Convertir lettres -> nombres
        $numeric = $this->convertLettersToNumbers($rearranged);

        // Modulo 97 doit etre 1
        return $this->mod97($numeric) === 1;
    }

    /**
     * Convertit les lettres en nombres (A=10, B=11, ..., Z=35).
     */
    private function convertLettersToNumbers(string $iban): string
    {
        return preg_replace_callback(
            '/[A-Z]/',
            static fn(array $match): string => (string) (ord($match[0]) - ord('A') + 10),
            $iban
        ) ?? '';
    }

    /**
     * Calcule le modulo 97 d'un grand nombre represente en string.
     */
    private function mod97(string $numeric): int
    {
        $remainder = 0;

        foreach (str_split($numeric, 7) as $chunk) {
            $remainder = (int) (($remainder . $chunk) % 97);
        }

        return $remainder;
    }
}
