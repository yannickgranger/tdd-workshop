<?php

declare(strict_types=1);

namespace App\Domain\Banking;

/**
 * Validateur IBAN utilisant l'algorithme ISO 13616 (mod 97).
 *
 * Note : Le terme "Luhn" est utilise pour simplifier dans le workshop,
 * mais l'IBAN utilise en realite l'algorithme ISO 7064 mod 97-10.
 *
 * C'est un SERVICE DOMAIN - PHP pur, aucune dependance framework.
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
        $this->assertValidFormat($iban);

        return $this->verifyChecksum($iban);
    }

    /**
     * Verifie le format de base de l'IBAN.
     *
     * @throws InvalidIbanException
     */
    private function assertValidFormat(string $iban): void
    {
        // Cycle 1 : chaine vide
        if ($iban === '') {
            throw new InvalidIbanException('IBAN cannot be empty');
        }

        // Cycle 2 : caracteres invalides (espaces geres dans branche 04)
        if (!preg_match('/^[A-Za-z0-9]+$/', $iban)) {
            throw new InvalidIbanException('IBAN contains invalid characters');
        }

        // Cycle 3 : trop court
        if (strlen($iban) < self::MIN_LENGTH) {
            throw new InvalidIbanException('IBAN is too short');
        }
    }

    /**
     * Verifie le checksum selon ISO 13616.
     *
     * Algorithme :
     * 1. Deplacer les 4 premiers caracteres a la fin
     * 2. Convertir les lettres en nombres (A=10, B=11, ..., Z=35)
     * 3. Le nombre resultant modulo 97 doit etre egal a 1
     */
    private function verifyChecksum(string $iban): bool
    {
        // Normaliser en majuscules
        $iban = strtoupper($iban);

        // Etape 1 : Deplacer les 4 premiers caracteres a la fin
        // Exemple : FR76... -> ...FR76
        $rearranged = substr($iban, 4) . substr($iban, 0, 4);

        // Etape 2 : Convertir lettres -> nombres
        $numeric = $this->convertLettersToNumbers($rearranged);

        // Etape 3 : Modulo 97 doit etre 1
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
     *
     * On ne peut pas utiliser l'operateur % directement car les IBAN
     * produisent des nombres trop grands pour les entiers PHP.
     */
    private function mod97(string $numeric): int
    {
        // Technique : calculer le mod par morceaux
        $remainder = 0;

        foreach (str_split($numeric, 7) as $chunk) {
            $remainder = (int) (($remainder . $chunk) % 97);
        }

        return $remainder;
    }
}
