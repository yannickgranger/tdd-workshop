---
title: "Exercice 02: Red-Green-Refactor"
---

# Exercice 02: Cycles Red-Green-Refactor

<Exercise title="Pratiquer le cycle TDD" difficulty="easy">

## Objectif

Implementer 3 tests complets en suivant le cycle <span class="red">RED</span> → <span class="green">GREEN</span> → <span class="refactor">REFACTOR</span>.

</Exercise>

## Setup

```bash
git checkout 02-red-green-refactor
```

## Les 3 tests a implementer

### Test 1: Chaine vide

```php
/**
 * EXERCICE:
 * - Utiliser $this->expectException(InvalidIbanException::class)
 * - Appeler $this->validator->validate('')
 */
public function test_empty_string_throws_exception(): void
{
    $this->markTestIncomplete('TODO: Implement');
}
```

### Test 2: Caracteres invalides

```php
/**
 * EXERCICE:
 * - Utiliser $this->expectException(InvalidIbanException::class)
 * - Appeler validate() avec 'FR76!@#$'
 */
public function test_invalid_characters_throws_exception(): void
{
    $this->markTestIncomplete('TODO: Implement');
}
```

### Test 3: Trop court

```php
/**
 * EXERCICE:
 * - Utiliser $this->expectException(InvalidIbanException::class)
 * - Tester avec 'FR7' (3 caracteres)
 */
public function test_too_short_throws_exception(): void
{
    $this->markTestIncomplete('TODO: Implement');
}
```

## Processus TDD

Pour **chaque** test :

1. **RED** : Implementez le test, verifiez qu'il echoue
2. **GREEN** : Ajoutez le code minimal dans `Mod97Validator`
3. **REFACTOR** : Ameliorez si necessaire (sans casser les tests)

```bash
# Apres chaque modification
make test
```

## Solutions

<Solution title="Solution Test 1 - Chaine vide">

```php
public function test_empty_string_throws_exception(): void
{
    $this->expectException(InvalidIbanException::class);
    $this->validator->validate('');
}
```

**Code minimal pour GREEN :**
```php
public function validate(string $iban): bool
{
    if ($iban === '') {
        throw new InvalidIbanException('IBAN cannot be empty');
    }
    return true;
}
```

</Solution>

<Solution title="Solution Test 2 - Caracteres invalides">

```php
public function test_invalid_characters_throws_exception(): void
{
    $this->expectException(InvalidIbanException::class);
    $this->validator->validate('FR76!@#$');
}
```

**Code ajoute pour GREEN :**
```php
if (!preg_match('/^[A-Za-z0-9]+$/', $iban)) {
    throw new InvalidIbanException('IBAN contains invalid characters');
}
```

</Solution>

<Solution title="Solution Test 3 - Trop court">

```php
public function test_too_short_throws_exception(): void
{
    $this->expectException(InvalidIbanException::class);
    $this->validator->validate('FR7');
}
```

**Code ajoute pour GREEN :**
```php
if (strlen($iban) < 5) {
    throw new InvalidIbanException('IBAN is too short');
}
```

</Solution>

## Verification finale

```bash
make test
# OK (5 tests, 5 assertions)
```

➡️ [Exercice 03: Algorithm Discovery](/exercises/03-algorithm)
