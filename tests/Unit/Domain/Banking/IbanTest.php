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

    public function test_valid_iban_can_be_created(): void
    {
        $iban = new Iban('FR7630006000011234567890189');

        $this->assertInstanceOf(Iban::class, $iban);
    }

    public function test_invalid_iban_throws_exception(): void
    {
        $this->expectException(InvalidIbanException::class);
        new Iban('FR7630006000011234567890188'); // checksum invalide
    }

    public function test_empty_string_throws_exception(): void
    {
        $this->expectException(InvalidIbanException::class);
        new Iban('');
    }

    public function test_factory_method_works(): void
    {
        $iban = Iban::fromString('DE89370400440532013000');

        $this->assertInstanceOf(Iban::class, $iban);
    }

    // =========================================================================
    // NORMALISATION
    // =========================================================================

    public function test_lowercase_is_normalized(): void
    {
        $iban = new Iban('fr7630006000011234567890189');

        $this->assertSame('FR7630006000011234567890189', $iban->toString());
    }

    public function test_spaces_are_normalized(): void
    {
        $iban = new Iban('FR76 3000 6000 0112 3456 7890 189');

        $this->assertSame('FR7630006000011234567890189', $iban->toString());
    }

    // =========================================================================
    // ACCESSEURS
    // =========================================================================

    public function test_get_country_code(): void
    {
        $iban = new Iban('FR7630006000011234567890189');

        $this->assertSame('FR', $iban->getCountryCode());
    }

    public function test_get_country_code_germany(): void
    {
        $iban = new Iban('DE89370400440532013000');

        $this->assertSame('DE', $iban->getCountryCode());
    }

    public function test_get_check_digits(): void
    {
        $iban = new Iban('FR7630006000011234567890189');

        $this->assertSame('76', $iban->getCheckDigits());
    }

    public function test_get_bban(): void
    {
        $iban = new Iban('FR7630006000011234567890189');

        $this->assertSame('30006000011234567890189', $iban->getBban());
    }

    // =========================================================================
    // FORMATAGE
    // =========================================================================

    public function test_to_formatted_string(): void
    {
        $iban = new Iban('FR7630006000011234567890189');

        $this->assertSame('FR76 3000 6000 0112 3456 7890 189', $iban->toFormattedString());
    }

    public function test_to_string_magic_method(): void
    {
        $iban = new Iban('FR7630006000011234567890189');

        $this->assertSame('FR7630006000011234567890189', (string) $iban);
    }

    // =========================================================================
    // COMPARAISON
    // =========================================================================

    public function test_equals_same_iban(): void
    {
        $iban1 = new Iban('FR7630006000011234567890189');
        $iban2 = new Iban('FR7630006000011234567890189');

        $this->assertTrue($iban1->equals($iban2));
    }

    public function test_equals_different_iban(): void
    {
        $iban1 = new Iban('FR7630006000011234567890189');
        $iban2 = new Iban('DE89370400440532013000');

        $this->assertFalse($iban1->equals($iban2));
    }

    public function test_equals_normalized_versions(): void
    {
        // Meme IBAN, formats differents
        $iban1 = new Iban('FR7630006000011234567890189');
        $iban2 = new Iban('fr76 3000 6000 0112 3456 7890 189');

        $this->assertTrue($iban1->equals($iban2));
    }

    // =========================================================================
    // IMMUTABILITE
    // =========================================================================

    /**
     * Ce test documente l'immutabilite du Value Object.
     * Il n'y a pas de setter, donc l'IBAN ne peut pas changer.
     */
    public function test_iban_is_immutable(): void
    {
        $iban = new Iban('FR7630006000011234567890189');

        // On ne peut pas modifier l'IBAN
        // $iban->value = 'xxx'; // Erreur : propriete privee
        // $iban->setValue('xxx'); // Erreur : methode inexistante

        // La seule facon d'avoir un IBAN different est d'en creer un nouveau
        $this->assertSame('FR7630006000011234567890189', $iban->toString());
    }
}
