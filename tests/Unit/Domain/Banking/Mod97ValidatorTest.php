<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Banking;

use App\Domain\Banking\InvalidIbanException;
use App\Domain\Banking\Mod97Validator;
use PHPUnit\Framework\TestCase;

/**
 * TDD Walkthrough - Branch 02-red-green-refactor
 *
 * Cette branche montre les 3 premiers cycles TDD :
 * 1. Chaine vide -> exception
 * 2. Caracteres invalides -> exception
 * 3. Trop court -> exception
 *
 * Chaque test a ete ecrit AVANT son implementation.
 */
final class Mod97ValidatorTest extends TestCase
{
    private Mod97Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Mod97Validator();
    }

    // =========================================================================
    // CYCLE 1 : Chaine vide
    // =========================================================================

    /**
     * Test 1 : Cas le plus simple - chaine vide
     *
     * Pourquoi ce test en premier ?
     * - C'est le cas d'erreur le plus simple a gerer
     * - Il force la creation de la classe
     * - Il definit le contrat : validate() leve une exception si vide
     *
     * EXERCICE:
     * - Utiliser $this->expectException(InvalidIbanException::class)
     * - Appeler $this->validator->validate('')
     */
    public function test_empty_string_throws_exception(): void
    {
        $this->markTestIncomplete('TODO: Implement test_empty_string_throws_exception');
    }

    // =========================================================================
    // CYCLE 2 : Caracteres invalides
    // =========================================================================

    /**
     * Test 2 : Caracteres speciaux interdits
     *
     * Pourquoi ce test ?
     * - Un IBAN ne contient que des lettres et chiffres
     * - En ecrivant ce test, on DECOUVRE cette regle metier
     * - Le test nous FORCE a definir ce qu'est un "caractere valide"
     *
     * EXERCICE:
     * - Utiliser $this->expectException(InvalidIbanException::class)
     * - Appeler validate() avec 'FR76!@#$'
     */
    public function test_invalid_characters_throws_exception(): void
    {
        $this->markTestIncomplete('TODO: Implement test_invalid_characters_throws_exception');
    }

    /**
     * Test 2b : Espace dans l'IBAN
     *
     * Decouverte pendant l'ecriture du test 2 :
     * "Et les espaces ? Les utilisateurs copient-collent avec des espaces..."
     *
     * DECISION : Pour l'instant, on refuse les espaces.
     * On reviendra sur cette decision dans la branche 04 (normalisation).
     *
     * EXERCICE:
     * - Meme pattern que test 2
     * - Tester avec 'FR76 3000 6000'
     */
    public function test_spaces_are_currently_invalid(): void
    {
        $this->markTestIncomplete('TODO: Implement test_spaces_are_currently_invalid');
    }

    // =========================================================================
    // CYCLE 3 : Longueur minimale
    // =========================================================================

    /**
     * Test 3 : IBAN trop court
     *
     * Pourquoi ce test ?
     * - Un IBAN a une longueur minimale (2 lettres pays + 2 chiffres check + data)
     * - On choisit 5 comme minimum pour simplifier
     *
     * EXERCICE:
     * - Utiliser $this->expectException(InvalidIbanException::class)
     * - Tester avec 'FR7' (3 caracteres)
     */
    public function test_too_short_throws_exception(): void
    {
        $this->markTestIncomplete('TODO: Implement test_too_short_throws_exception');
    }

    /**
     * Test 3b : Cas limite - exactement 5 caracteres
     *
     * Decouverte : "Quelle est la limite exacte ?"
     * On teste le cas limite pour etre sur de notre implementation.
     *
     * EXERCICE:
     * - Appeler validate('FR761') - 5 caracteres
     * - Utiliser $this->assertTrue() sur le resultat
     */
    public function test_minimum_length_is_accepted(): void
    {
        $this->markTestIncomplete('TODO: Implement test_minimum_length_is_accepted');
    }
}
