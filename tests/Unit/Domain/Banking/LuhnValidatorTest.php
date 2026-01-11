<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Banking;

use App\Domain\Banking\InvalidIbanException;
use App\Domain\Banking\LuhnValidator;
use PHPUnit\Framework\TestCase;

/**
 * Branch 05-value-object : LuhnValidator tests restent les memes.
 * Voir IbanTest.php pour les nouveaux tests du Value Object.
 */
final class LuhnValidatorTest extends TestCase
{
    private LuhnValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new LuhnValidator();
    }

    public function test_empty_string_throws_exception(): void
    {
        $this->markTestIncomplete('TODO: Implement');
    }

    public function test_invalid_characters_throws_exception(): void
    {
        $this->markTestIncomplete('TODO: Implement');
    }

    public function test_too_short_throws_exception(): void
    {
        $this->markTestIncomplete('TODO: Implement');
    }

    public function test_valid_french_iban_returns_true(): void
    {
        $this->markTestIncomplete('TODO: Implement');
    }

    public function test_valid_german_iban_returns_true(): void
    {
        $this->markTestIncomplete('TODO: Implement');
    }

    public function test_invalid_checksum_returns_false(): void
    {
        $this->markTestIncomplete('TODO: Implement');
    }

    public function test_lowercase_iban_is_normalized(): void
    {
        $this->markTestIncomplete('TODO: Implement');
    }

    public function test_mixed_case_iban_is_normalized(): void
    {
        $this->markTestIncomplete('TODO: Implement');
    }

    public function test_iban_with_spaces_is_normalized(): void
    {
        $this->markTestIncomplete('TODO: Implement');
    }

    public function test_iban_with_leading_trailing_spaces(): void
    {
        $this->markTestIncomplete('TODO: Implement');
    }

    public function test_iban_with_spaces_and_lowercase(): void
    {
        $this->markTestIncomplete('TODO: Implement');
    }

    public function test_tabs_are_invalid(): void
    {
        $this->markTestIncomplete('TODO: Implement');
    }

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
        $this->markTestIncomplete('TODO: Implement');
    }
}
