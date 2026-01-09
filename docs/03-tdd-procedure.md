# 02 - La procedure TDD

## Le rythme TDD

Le TDD a un rythme precis. C'est comme une danse :

```
TEST → ECHEC → CODE → SUCCES → REFACTOR → TEST → ...
```

## Etape par etape

### 1. Ecrire UN test

**Un seul test a la fois.** Le test le plus simple possible.

```php
public function test_empty_string_throws_exception(): void
{
    $this->expectException(InvalidIbanException::class);
    $this->validator->validate('');
}
```

### 2. Voir le test echouer (RED)

C'est **obligatoire**. Si le test passe immediatement, il y a un probleme :
- Le test ne teste rien
- Le code existe deja
- Le test est mal ecrit

### 3. Ecrire le code MINIMAL (GREEN)

**Juste assez pour passer le test.** Pas plus.

```php
// MAUVAIS - trop de code
public function validate(string $iban): bool
{
    if ($iban === '') throw new InvalidIbanException('...');
    if (!preg_match(...)) throw new InvalidIbanException('...');
    if (strlen($iban) < 5) throw new InvalidIbanException('...');
    // ... tout l'algorithme
}

// BON - code minimal
public function validate(string $iban): bool
{
    if ($iban === '') {
        throw new InvalidIbanException('IBAN cannot be empty');
    }
    return true;
}
```

### 4. Refactorer si necessaire

Une fois le test vert, on peut ameliorer :
- Extraire des methodes
- Renommer des variables
- Simplifier la logique

**Les tests nous protegent** pendant le refactoring.

### 5. Recommencer

Ecrire le prochain test et repeter.

## Les erreurs courantes

### Erreur 1 : Ecrire plusieurs tests d'un coup

```php
// MAUVAIS
public function test_validation(): void
{
    // Test vide
    $this->expectException(...);
    $this->validator->validate('');

    // Test caracteres invalides
    $this->expectException(...);
    $this->validator->validate('!@#$');

    // Test trop court
    // ...
}
```

Probleme : Si ca echoue, on ne sait pas ou.

### Erreur 2 : Ecrire trop de code

Ne pas anticiper. Ecrire **seulement** ce que le test demande.

### Erreur 3 : Ne pas voir le test echouer

Toujours verifier que le test echoue AVANT d'ecrire le code.

### Erreur 4 : Tester l'implementation

```php
// MAUVAIS - teste l'implementation
public function test_uses_preg_match(): void
{
    // On ne devrait pas savoir COMMENT c'est implemente
}

// BON - teste le comportement
public function test_invalid_characters_throws_exception(): void
{
    $this->expectException(InvalidIbanException::class);
    $this->validator->validate('FR76!@#$');
}
```

## La regle des 3A

Chaque test suit le pattern **Arrange-Act-Assert** :

```php
public function test_valid_iban_returns_true(): void
{
    // Arrange - preparer
    $validator = new LuhnValidator();

    // Act - executer
    $result = $validator->validate('FR7630006000011234567890189');

    // Assert - verifier
    $this->assertTrue($result);
}
```

## Conseils pratiques

1. **Commencer par les cas d'erreur** - ils sont plus simples
2. **Nommer les tests clairement** - `test_empty_string_throws_exception`
3. **Un comportement par test** - pas de tests "fourre-tout"
4. **Executer les tests souvent** - apres chaque petit changement

---

**Suivant** : [03 - Walkthrough Luhn](04-luhn-walkthrough.md)
