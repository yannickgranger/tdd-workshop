<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Banking;

use App\Domain\Banking\Iban;
use App\Domain\Banking\InvalidIbanException;
use PHPUnit\Framework\TestCase;

/**
 * TDD Walkthrough - Branch 05-value-object
 *
 * Cette branche montre le REFACTORING a grande echelle.
 *
 * On extrait un Value Object Iban qui :
 * - Encapsule la validation
 * - Garantit l'immutabilite
 * - Fournit des methodes utiles (getCountryCode, toFormattedString...)
 *
 * IMPORTANT : Les tests existants (LuhnValidatorTest) continuent de passer !
 * C'est la garantie du TDD : on peut refactorer en toute confiance.
 */
final class IbanTest extends TestCase
{
    // =========================================================================
    // CONSTRUCTION
    // =========================================================================

    /**
     * EXERCICE: new Iban('FR7630006000011234567890189')
     * Verifier avec assertInstanceOf()
     */
    public function test_valid_iban_can_be_created(): void
    {
        $this->markTestIncomplete('TODO: Implement test_valid_iban_can_be_created');
    }

    /**
     * EXERCICE: expectException + new Iban('FR7630006000011234567890188')
     * (checksum invalide - dernier chiffre modifie)
     */
    public function test_invalid_iban_throws_exception(): void
    {
        $this->markTestIncomplete('TODO: Implement test_invalid_iban_throws_exception');
    }

    /**
     * EXERCICE: expectException + new Iban('')
     */
    public function test_empty_string_throws_exception(): void
    {
        $this->markTestIncomplete('TODO: Implement test_empty_string_throws_exception');
    }

    /**
     * EXERCICE: Iban::fromString('DE89370400440532013000')
     */
    public function test_factory_method_works(): void
    {
        $this->markTestIncomplete('TODO: Implement test_factory_method_works');
    }

    // =========================================================================
    // NORMALISATION
    // =========================================================================

    /**
     * EXERCICE:
     * - Creer avec 'fr7630006000011234567890189' (minuscules)
     * - Verifier toString() retourne 'FR7630006000011234567890189'
     */
    public function test_lowercase_is_normalized(): void
    {
        $this->markTestIncomplete('TODO: Implement test_lowercase_is_normalized');
    }

    /**
     * EXERCICE:
     * - Creer avec 'FR76 3000 6000 0112 3456 7890 189' (espaces)
     * - Verifier toString() retourne version sans espaces
     */
    public function test_spaces_are_normalized(): void
    {
        $this->markTestIncomplete('TODO: Implement test_spaces_are_normalized');
    }

    // =========================================================================
    // ACCESSEURS
    // =========================================================================

    /**
     * EXERCICE: getCountryCode() doit retourner 'FR'
     */
    public function test_get_country_code(): void
    {
        $this->markTestIncomplete('TODO: Implement test_get_country_code');
    }

    /**
     * EXERCICE: getCountryCode() pour IBAN allemand -> 'DE'
     */
    public function test_get_country_code_germany(): void
    {
        $this->markTestIncomplete('TODO: Implement test_get_country_code_germany');
    }

    /**
     * EXERCICE: getCheckDigits() doit retourner '76'
     */
    public function test_get_check_digits(): void
    {
        $this->markTestIncomplete('TODO: Implement test_get_check_digits');
    }

    /**
     * EXERCICE: getBban() doit retourner '30006000011234567890189'
     */
    public function test_get_bban(): void
    {
        $this->markTestIncomplete('TODO: Implement test_get_bban');
    }

    // =========================================================================
    // FORMATAGE
    // =========================================================================

    /**
     * EXERCICE: toFormattedString() ajoute des espaces tous les 4 caracteres
     * -> 'FR76 3000 6000 0112 3456 7890 189'
     */
    public function test_to_formatted_string(): void
    {
        $this->markTestIncomplete('TODO: Implement test_to_formatted_string');
    }

    /**
     * EXERCICE: (string) $iban doit fonctionner via __toString()
     */
    public function test_to_string_magic_method(): void
    {
        $this->markTestIncomplete('TODO: Implement test_to_string_magic_method');
    }

    // =========================================================================
    // COMPARAISON
    // =========================================================================

    /**
     * EXERCICE: equals() retourne true pour deux IBAN identiques
     */
    public function test_equals_same_iban(): void
    {
        $this->markTestIncomplete('TODO: Implement test_equals_same_iban');
    }

    /**
     * EXERCICE: equals() retourne false pour IBAN differents
     */
    public function test_equals_different_iban(): void
    {
        $this->markTestIncomplete('TODO: Implement test_equals_different_iban');
    }

    /**
     * EXERCICE: equals() compare les versions normalisees
     * Deux IBAN avec formats differents mais meme valeur -> true
     */
    public function test_equals_normalized_versions(): void
    {
        $this->markTestIncomplete('TODO: Implement test_equals_normalized_versions');
    }

    // =========================================================================
    // IMMUTABILITE
    // =========================================================================

    /**
     * Ce test documente l'immutabilite du Value Object.
     * Il n'y a pas de setter, donc l'IBAN ne peut pas changer.
     *
     * EXERCICE: Verifier que toString() retourne toujours la meme valeur
     */
    public function test_iban_is_immutable(): void
    {
        $this->markTestIncomplete('TODO: Implement test_iban_is_immutable');
    }
}
