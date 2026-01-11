<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Banking;

use App\Domain\Banking\InvalidIbanException;
use App\Domain\Banking\LuhnValidator;
use PHPUnit\Framework\TestCase;

/**
 * TDD Walkthrough - Branch 01-setup
 *
 * Ce premier test est volontairement en ECHEC (RED).
 * La classe LuhnValidator n'existe pas encore.
 *
 * C'est le point de depart du TDD : on ecrit le test AVANT le code.
 */
final class LuhnValidatorTest extends TestCase
{
    /**
     * Test 1 : Cas le plus simple - chaine vide
     *
     * Pourquoi ce test en premier ?
     * - C'est le cas d'erreur le plus simple a gerer
     * - Il force la creation de la classe
     * - Il definit le contrat : validate() leve une exception si vide
     *
     * EXERCICE:
     * 1. Instancier LuhnValidator
     * 2. Utiliser $this->expectException() pour attendre InvalidIbanException
     * 3. Appeler validate('') avec une chaine vide
     */
    public function test_empty_string_throws_exception(): void
    {
        $this->markTestIncomplete('TODO: Implement test_empty_string_throws_exception');
    }
}
