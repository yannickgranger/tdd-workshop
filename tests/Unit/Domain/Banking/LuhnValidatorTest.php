<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Banking;

use App\Domain\Banking\InvalidIbanException;
use App\Domain\Banking\LuhnValidator;
use PHPUnit\Framework\TestCase;

/**
 * TDD Walkthrough - Branch 04-edge-cases
 *
 * Cette branche montre comment TDD aide a gerer les CAS LIMITES.
 *
 * Questions decouvertes en ecrivant les tests :
 * - "Et si l'utilisateur tape en minuscules ?"
 * - "Et s'il copie-colle un IBAN avec des espaces ?"
 * - "Et les tabulations ?"
 *
 * Chaque question = un nouveau test = une decision de design.
 */
final class LuhnValidatorTest extends TestCase
{
    private LuhnValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new LuhnValidator();
    }

    // =========================================================================
    // TESTS DE FORMAT
    // =========================================================================

    public function test_empty_string_throws_exception(): void
    {
        $this->expectException(InvalidIbanException::class);
        $this->validator->validate('');
    }

    public function test_invalid_characters_throws_exception(): void
    {
        $this->expectException(InvalidIbanException::class);
        $this->validator->validate('FR76!@#$');
    }

    public function test_too_short_throws_exception(): void
    {
        $this->expectException(InvalidIbanException::class);
        $this->validator->validate('FR7');
    }

    // =========================================================================
    // TESTS DE VALIDATION (algorithme)
    // =========================================================================

    public function test_valid_french_iban_returns_true(): void
    {
        $this->assertTrue(
            $this->validator->validate('FR7630006000011234567890189')
        );
    }

    public function test_valid_german_iban_returns_true(): void
    {
        $this->assertTrue(
            $this->validator->validate('DE89370400440532013000')
        );
    }

    public function test_invalid_checksum_returns_false(): void
    {
        $this->assertFalse(
            $this->validator->validate('FR7630006000011234567890188')
        );
    }

    // =========================================================================
    // CYCLE 8 : Normalisation - Minuscules
    // =========================================================================

    /**
     * Test 8 : IBAN en minuscules
     *
     * Decouverte : "Et si l'utilisateur tape en minuscules ?"
     *
     * Options :
     * a) Rejeter -> Mauvaise UX
     * b) Normaliser -> Meilleure UX
     *
     * Decision : Normaliser (b)
     * Le test DOCUMENTE cette decision.
     */
    public function test_lowercase_iban_is_normalized(): void
    {
        $this->assertTrue(
            $this->validator->validate('fr7630006000011234567890189')
        );
    }

    /**
     * Test 8b : IBAN en casse mixte
     *
     * On verifie que la normalisation fonctionne aussi
     * pour une casse mixte (cas reel de copier-coller).
     */
    public function test_mixed_case_iban_is_normalized(): void
    {
        $this->assertTrue(
            $this->validator->validate('Fr7630006000011234567890189')
        );
    }

    // =========================================================================
    // CYCLE 9 : Normalisation - Espaces
    // =========================================================================

    /**
     * Test 9 : IBAN avec espaces (format affichage)
     *
     * Decouverte : "Les utilisateurs copient-collent depuis des PDF/documents
     * ou les IBAN sont formates avec des espaces pour la lisibilite."
     *
     * Exemple : FR76 3000 6000 0112 3456 7890 189
     *
     * Decision : Supprimer les espaces avant validation.
     */
    public function test_iban_with_spaces_is_normalized(): void
    {
        $this->assertTrue(
            $this->validator->validate('FR76 3000 6000 0112 3456 7890 189')
        );
    }

    /**
     * Test 9b : IBAN avec espaces au debut/fin
     *
     * Cas reel : copier-coller peut inclure des espaces en trop.
     */
    public function test_iban_with_leading_trailing_spaces(): void
    {
        $this->assertTrue(
            $this->validator->validate('  FR7630006000011234567890189  ')
        );
    }

    /**
     * Test 9c : Combinaison espaces + minuscules
     *
     * Le cas le plus realiste : l'utilisateur copie-colle
     * un IBAN formate et le modifie manuellement.
     */
    public function test_iban_with_spaces_and_lowercase(): void
    {
        $this->assertTrue(
            $this->validator->validate('fr76 3000 6000 0112 3456 7890 189')
        );
    }

    // =========================================================================
    // CYCLE 10 : Autres caracteres blancs
    // =========================================================================

    /**
     * Test 10 : Tabulations et autres blancs
     *
     * Decouverte : "Et les tabulations ? Les retours a la ligne ?"
     *
     * Decision : On ne gere que les espaces pour l'instant.
     * Les tabulations restent des caracteres invalides.
     *
     * Pourquoi ? YAGNI - si le besoin se presente, on ajoutera un test.
     */
    public function test_tabs_are_invalid(): void
    {
        $this->expectException(InvalidIbanException::class);
        $this->validator->validate("FR76\t3000");
    }

    // =========================================================================
    // TESTS DE REGRESSION
    // =========================================================================

    /**
     * @return array<string, array{string}>
     */
    public static function validIbanProvider(): array
    {
        return [
            'France uppercase' => ['FR7630006000011234567890189'],
            'France lowercase' => ['fr7630006000011234567890189'],
            'France with spaces' => ['FR76 3000 6000 0112 3456 7890 189'],
            'Germany' => ['DE89370400440532013000'],
            'Belgium' => ['BE68539007547034'],
            'Spain' => ['ES9121000418450200051332'],
            'Italy' => ['IT60X0542811101000000123456'],
            'Netherlands' => ['NL91ABNA0417164300'],
        ];
    }

    /**
     * @dataProvider validIbanProvider
     */
    public function test_valid_ibans_with_various_formats(string $iban): void
    {
        $this->assertTrue(
            $this->validator->validate($iban),
            sprintf('IBAN %s should be valid', $iban)
        );
    }
}
