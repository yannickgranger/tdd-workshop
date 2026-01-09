<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Banking;

use App\Domain\Banking\InvalidIbanException;
use App\Domain\Banking\LuhnValidator;
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
final class LuhnValidatorTest extends TestCase
{
    private LuhnValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new LuhnValidator();
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
     * Implementation minimale :
     * if ($iban === '') { throw new InvalidIbanException(...); }
     */
    public function test_empty_string_throws_exception(): void
    {
        $this->expectException(InvalidIbanException::class);
        $this->validator->validate('');
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
     * Implementation minimale :
     * if (!preg_match('/^[A-Za-z0-9]+$/', $iban)) { throw ... }
     */
    public function test_invalid_characters_throws_exception(): void
    {
        $this->expectException(InvalidIbanException::class);
        $this->validator->validate('FR76!@#$');
    }

    /**
     * Test 2b : Espace dans l'IBAN
     *
     * Decouverte pendant l'ecriture du test 2 :
     * "Et les espaces ? Les utilisateurs copient-collent avec des espaces..."
     *
     * DECISION : Pour l'instant, on refuse les espaces.
     * On reviendra sur cette decision dans la branche 04 (normalisation).
     */
    public function test_spaces_are_currently_invalid(): void
    {
        $this->expectException(InvalidIbanException::class);
        $this->validator->validate('FR76 3000 6000');
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
     * Note : En vrai, la longueur depend du pays.
     * Pour le workshop, on simplifie.
     */
    public function test_too_short_throws_exception(): void
    {
        $this->expectException(InvalidIbanException::class);
        $this->validator->validate('FR7');
    }

    /**
     * Test 3b : Cas limite - exactement 5 caracteres
     *
     * Decouverte : "Quelle est la limite exacte ?"
     * On teste le cas limite pour etre sur de notre implementation.
     */
    public function test_minimum_length_is_accepted(): void
    {
        // 5 caracteres = longueur minimale acceptee
        // Le test passe car on n'a pas encore implemente l'algorithme Luhn
        $result = $this->validator->validate('FR761');
        $this->assertTrue($result);
    }
}
