---
title: "Exercice 05: Value Object"
---

# Exercice 05: Refactoring en Value Object

<Exercise title="Extraire un Value Object Iban" difficulty="hard">

## Objectif

Refactorer le code en extrayant un **Value Object** `Iban` qui encapsule la validation.

</Exercise>

## Setup

```bash
git checkout 05-value-object
```

## Pourquoi un Value Object ?

| Avantage | Description |
|----------|-------------|
| **Type-safety** | Une methode qui attend `Iban` ne peut pas recevoir une string invalide |
| **Validation centralisee** | La logique est dans le constructeur |
| **Immutabilite** | L'IBAN ne change jamais apres creation |

## Tests a implementer

### Construction

```php
public function test_valid_iban_can_be_created(): void
{
    $this->markTestIncomplete('TODO: Implement');
}

public function test_invalid_iban_throws_exception(): void
{
    $this->markTestIncomplete('TODO: Implement');
}
```

### Accesseurs

```php
public function test_get_country_code(): void
{
    $this->markTestIncomplete('TODO: Implement');
}

public function test_get_check_digits(): void
{
    $this->markTestIncomplete('TODO: Implement');
}
```

### Comparaison

```php
public function test_equals_same_iban(): void
{
    $this->markTestIncomplete('TODO: Implement');
}

public function test_equals_normalized_versions(): void
{
    $this->markTestIncomplete('TODO: Implement');
}
```

## Structure du Value Object

<Solution title="Implementation complete">

```php
final class Iban
{
    private string $value;

    public function __construct(string $value)
    {
        $validator = new Mod97Validator();

        if (!$validator->validate($value)) {
            throw new InvalidIbanException('Invalid IBAN checksum');
        }

        $this->value = self::normalize($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function getCountryCode(): string
    {
        return substr($this->value, 0, 2);
    }

    public function getCheckDigits(): string
    {
        return substr($this->value, 2, 2);
    }

    public function getBban(): string
    {
        return substr($this->value, 4);
    }

    public function toFormattedString(): string
    {
        return trim(chunk_split($this->value, 4, ' '));
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    private static function normalize(string $iban): string
    {
        return strtoupper(str_replace(' ', '', $iban));
    }
}
```

</Solution>

## Points cles du refactoring

1. **Les tests existants passent toujours** → Filet de securite
2. **Nouveaux tests pour le Value Object** → Documentation
3. **Immutabilite garantie** → Pas de setter

---

## Felicitations !

Vous avez complete tous les exercices du workshop TDD.

### Recapitulatif

- ✅ Premier test en echec (RED)
- ✅ Cycles Red-Green-Refactor
- ✅ Decouverte d'algorithme via TDD
- ✅ Gestion des edge cases
- ✅ Refactoring en Value Object

### Pour aller plus loin

- 📖 [Lock Steal Case Study](/guide/08-lock-steal-case-study) - TDD sur un cas reel
- 📚 [Glossaire](/guide/09-glossaire) - Termes de test
