<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Banking;

use App\Domain\Banking\InvalidIbanException;
use App\Domain\Banking\Mod97Validator;
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
final class Mod97ValidatorTest extends TestCase
{
    private Mod97Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Mod97Validator();
    }

    // =========================================================================
    // TESTS DE FORMAT (repris de branche 02)
    // =========================================================================

    /**
     * EXERCICE: expectException + validate('')
     */
    public function test_empty_string_throws_exception(): void
    {
        $this->markTestIncomplete('TODO: Implement');
    }

    /**
     * EXERCICE: expectException + validate('FR76!@#$')
     */
    public function test_invalid_characters_throws_exception(): void
    {
        $this->markTestIncomplete('TODO: Implement');
    }

    /**
     * EXERCICE: expectException + validate('FR7')
     */
    public function test_too_short_throws_exception(): void
    {
        $this->markTestIncomplete('TODO: Implement');
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
     * EXERCICE:
     * - Utiliser assertTrue()
     * - IBAN de test: 'FR7630006000011234567890189'
     */
    public function test_valid_french_iban_returns_true(): void
    {
        $this->markTestIncomplete('TODO: Implement test_valid_french_iban_returns_true');
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
     * EXERCICE:
     * - IBAN de test: 'DE89370400440532013000'
     */
    public function test_valid_german_iban_returns_true(): void
    {
        $this->markTestIncomplete('TODO: Implement test_valid_german_iban_returns_true');
    }

    /**
     * Test 5b : IBAN belge valide
     *
     * EXERCICE:
     * - IBAN de test: 'BE68539007547034'
     */
    public function test_valid_belgian_iban_returns_true(): void
    {
        $this->markTestIncomplete('TODO: Implement test_valid_belgian_iban_returns_true');
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
     * EXERCICE:
     * - Utiliser assertFalse()
     * - IBAN avec erreur: 'FR7630006000011234567890188' (dernier chiffre modifie)
     */
    public function test_invalid_checksum_returns_false(): void
    {
        $this->markTestIncomplete('TODO: Implement test_invalid_checksum_returns_false');
    }

    /**
     * Test 6b : Autre erreur de checksum
     *
     * EXERCICE:
     * - IBAN avec erreur au milieu: 'DE89370400440532013001'
     */
    public function test_checksum_error_in_middle_returns_false(): void
    {
        $this->markTestIncomplete('TODO: Implement test_checksum_error_in_middle_returns_false');
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
     *
     * EXERCICE:
     * - IBAN espagnol (24 caracteres): 'ES9121000418450200051332'
     */
    public function test_long_iban_handles_large_numbers(): void
    {
        $this->markTestIncomplete('TODO: Implement test_long_iban_handles_large_numbers');
    }

    // =========================================================================
    // TESTS DE REGRESSION (DataProvider)
    // =========================================================================

    /**
     * DataProvider pour tester plusieurs IBAN valides.
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
     * EXERCICE:
     * - Utiliser assertTrue() avec le $iban fourni par le DataProvider
     * - Ajouter un message d'erreur explicite avec sprintf()
     *
     * @dataProvider validIbanProvider
     */
    public function test_valid_ibans_from_various_countries(string $iban): void
    {
        $this->markTestIncomplete('TODO: Implement test_valid_ibans_from_various_countries');
    }
}
