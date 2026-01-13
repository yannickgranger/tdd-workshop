<?php

declare(strict_types=1);

namespace App\Domain\Banking;

/**
 * Value Object representant un IBAN valide.
 *
 * En DDD, un Value Object est :
 * - Immutable
 * - Defini par ses attributs (pas d'identite)
 * - Auto-valide (impossible de creer une instance invalide)
 *
 * Avantages :
 * - Type-safety : une methode qui attend un Iban ne peut pas recevoir une string invalide
 * - Validation centralisee : la logique est dans le constructeur
 * - Immutabilite : pas de surprise, l'IBAN ne change jamais
 *
 * Branche 05 : Refactoring - extraction du Value Object
 */
final class Iban
{
    private string $value;

    /**
     * @throws InvalidIbanException si l'IBAN est invalide
     */
    public function __construct(string $value)
    {
        $validator = new Mod97Validator();

        // Si la validation echoue, une exception est levee
        // Donc si on arrive ici, l'IBAN est valide
        if (!$validator->validate($value)) {
            throw new InvalidIbanException('Invalid IBAN checksum');
        }

        // Stocker la valeur normalisee
        $this->value = self::normalize($value);
    }

    /**
     * Factory method pour une creation plus lisible.
     *
     * @throws InvalidIbanException
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Retourne l'IBAN normalise (majuscules, sans espaces).
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Retourne l'IBAN formate pour l'affichage (avec espaces).
     *
     * Exemple : FR7630006000011234567890189 -> FR76 3000 6000 0112 3456 7890 189
     */
    public function toFormattedString(): string
    {
        return trim(chunk_split($this->value, 4, ' '));
    }

    /**
     * Retourne le code pays (2 premieres lettres).
     */
    public function getCountryCode(): string
    {
        return substr($this->value, 0, 2);
    }

    /**
     * Retourne les chiffres de controle (positions 3-4).
     */
    public function getCheckDigits(): string
    {
        return substr($this->value, 2, 2);
    }

    /**
     * Retourne le BBAN (Basic Bank Account Number).
     */
    public function getBban(): string
    {
        return substr($this->value, 4);
    }

    /**
     * Comparaison de valeur.
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function normalize(string $iban): string
    {
        return strtoupper(str_replace(' ', '', $iban));
    }
}
