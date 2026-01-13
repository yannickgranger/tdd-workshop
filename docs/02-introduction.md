# 01 - Introduction au TDD

## Qu'est-ce que le TDD ?

**Test-Driven Development** (Developpement Guide par les Tests) est une methode de developpement ou l'on ecrit les tests **avant** le code.

## Le cycle Red-Green-Refactor

```
    ┌─────────────┐
    │    RED      │  Ecrire un test qui echoue
    └──────┬──────┘
           │
           ▼
    ┌─────────────┐
    │   GREEN     │  Ecrire le code minimal pour passer le test
    └──────┬──────┘
           │
           ▼
    ┌─────────────┐
    │  REFACTOR   │  Ameliorer le code sans casser les tests
    └──────┬──────┘
           │
           └──────────► Recommencer
```

### RED : Ecrire un test qui echoue

```php
public function test_empty_string_throws_exception(): void
{
    $validator = new Mod97Validator(); // Classe n'existe pas encore !

    $this->expectException(InvalidIbanException::class);
    $validator->validate('');
}
```

Le test echoue : `Class "Mod97Validator" not found`

### GREEN : Code minimal pour passer

```php
final class Mod97Validator
{
    public function validate(string $iban): bool
    {
        if ($iban === '') {
            throw new InvalidIbanException('IBAN cannot be empty');
        }
        return true;
    }
}
```

Le test passe !

### REFACTOR : Ameliorer

Une fois le test vert, on peut refactorer en toute confiance. Les tests nous protegent.

## Pourquoi le TDD ?

### 1. Design Feedback

Le test ecrit en premier force a reflechir a l'**interface** avant l'**implementation**.

> Si un test est difficile a ecrire, c'est que le design est mauvais.

### 2. Documentation vivante

Les tests documentent le comportement attendu :

```php
public function test_spaces_are_stripped(): void
{
    // Ce test DOCUMENTE que les espaces sont supprimes
    $this->assertTrue($validator->validate('FR76 3000 6000 ...'));
}
```

### 3. Confiance pour refactorer

Avec une bonne couverture de tests, on peut refactorer sans peur de tout casser.

### 4. Moins de bugs

Les bugs sont decouverts tot, pendant le developpement, pas en production.

## Quand le TDD est-il le plus utile ?

| Contexte | TDD utile ? |
|----------|-------------|
| Logique metier complexe | **OUI** |
| Algorithmes | **OUI** |
| Value Objects | **OUI** |
| CRUD simple | Non (peu de valeur) |
| UI/Vues | Non (difficile) |
| Code d'infrastructure | Integration tests |

## Dans ce workshop

On va appliquer le TDD sur un cas ideal : **la validation IBAN**.

- Algorithme complexe (ISO 13616)
- Pure logique metier
- Pas de dependances externes
- Edge cases nombreux

C'est exactement le type de code ou le TDD brille !

---

**Suivant** : [02 - La procedure TDD](03-tdd-procedure.md)
