<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Banking;

use App\Domain\Banking\InvalidIbanException;
use App\Domain\Banking\LuhnValidator;
use PHPUnit\Framework\TestCase;

/**
 * TDD Walkthrough - Branch 03-algorithm-discovery
 *
 * Cette branche montre comment le TDD aide a DECOUVRIR l'algorithme.
 * Chaque test nous force a implementer une partie de la logique.
 *
 * L'algorithme IBAN (ISO 13616) :
 * 1. Deplacer les 4 premiers caracteres a la fin
 * 2. Convertir les lettres en nombres (A=10, B=11, ..., Z=35)
 * 3. Le reste de la division par 97 doit etre 1
 */
final class LuhnValidatorTest extends TestCase
{
    private LuhnValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new LuhnValidator();
    }

    // =========================================================================
    // TESTS DE FORMAT (repris de branche 02)
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
    // CYCLE 4 : Premier IBAN valide
    // =========================================================================

    /**
     * Test 4 : IBAN francais valide
     *
     * C'est ici que le TDD devient interessant.
     * On a un VRAI IBAN valide - le test FORCE l'implementation de l'algorithme.
     *
     * Decouverte : "Comment fonctionne l'algorithme IBAN ?"
     * -> On doit rechercher et comprendre ISO 13616
     * -> Le test nous GUIDE vers l'implementation correcte
     */
    public function test_valid_french_iban_returns_true(): void
    {
        // IBAN de test francais (banque fictive, mais checksum valide)
        $this->assertTrue(
            $this->validator->validate('FR7630006000011234567890189')
        );
    }

    // =========================================================================
    // CYCLE 5 : Autre pays (generalisation)
    // =========================================================================

    /**
     * Test 5 : IBAN allemand valide
     *
     * Pourquoi tester un autre pays ?
     * - Verifier que l'algorithme est generique
     * - Decouvrir si on a fait des hypotheses specifiques a la France
     *
     * Si ce test echoue, c'est qu'on a "sur-specialise" pour FR.
     */
    public function test_valid_german_iban_returns_true(): void
    {
        $this->assertTrue(
            $this->validator->validate('DE89370400440532013000')
        );
    }

    /**
     * Test 5b : IBAN belge valide
     *
     * Un troisieme pays pour confirmer la generalisation.
     */
    public function test_valid_belgian_iban_returns_true(): void
    {
        $this->assertTrue(
            $this->validator->validate('BE68539007547034')
        );
    }

    // =========================================================================
    // CYCLE 6 : Checksum invalide
    // =========================================================================

    /**
     * Test 6 : IBAN avec checksum invalide
     *
     * C'est le test le plus important pour valider l'algorithme.
     * On prend un IBAN valide et on modifie un chiffre.
     *
     * Decouverte : "Est-ce que notre algorithme detecte vraiment les erreurs ?"
     */
    public function test_invalid_checksum_returns_false(): void
    {
        // IBAN francais avec dernier chiffre modifie (189 -> 188)
        $this->assertFalse(
            $this->validator->validate('FR7630006000011234567890188')
        );
    }

    /**
     * Test 6b : Autre erreur de checksum
     *
     * On modifie un chiffre au milieu pour verifier que
     * l'algorithme detecte les erreurs partout.
     */
    public function test_checksum_error_in_middle_returns_false(): void
    {
        // IBAN allemand avec un chiffre modifie au milieu
        $this->assertFalse(
            $this->validator->validate('DE89370400440532013001')
        );
    }

    // =========================================================================
    // CYCLE 7 : Decouverte - Grands nombres
    // =========================================================================

    /**
     * Test 7 : IBAN long (test des grands nombres)
     *
     * Decouverte pendant l'implementation :
     * "Les IBAN convertis en nombres sont TROP GRANDS pour PHP !"
     *
     * Exemple : DE89370400440532013000 devient 370400440532013000131489
     * Ce nombre depasse PHP_INT_MAX.
     *
     * Solution : calculer le modulo par morceaux (technique standard).
     * Ce test aurait echoue sans cette decouverte !
     */
    public function test_long_iban_handles_large_numbers(): void
    {
        // IBAN espagnol (24 caracteres)
        $this->assertTrue(
            $this->validator->validate('ES9121000418450200051332')
        );
    }

    // =========================================================================
    // TESTS DE REGRESSION
    // =========================================================================

    /**
     * DataProvider pour tester plusieurs IBAN valides.
     *
     * Ces tests servent de regression : si on refactore,
     * on veut etre sur de ne rien casser.
     *
     * @return array<string, array{string}>
     */
    public static function validIbanProvider(): array
    {
        return [
            'France' => ['FR7630006000011234567890189'],
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
    public function test_valid_ibans_from_various_countries(string $iban): void
    {
        $this->assertTrue(
            $this->validator->validate($iban),
            sprintf('IBAN %s should be valid', $iban)
        );
    }
}
