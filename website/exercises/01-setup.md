---
title: "Exercice 01: Setup"
---

# Exercice 01: Premier test en echec (RED)

<Exercise title="Ecrire le premier test" difficulty="easy">

## Objectif

Ecrire un test qui **echoue** parce que la classe n'existe pas encore.

C'est le point de depart du TDD : le test definit le comportement **avant** le code.

</Exercise>

## Setup

```bash
git checkout 01-setup
```

## Votre mission

Ouvrez le fichier `tests/Unit/Domain/Banking/Mod97ValidatorTest.php` :

```php
public function test_empty_string_throws_exception(): void
{
    $this->markTestIncomplete('TODO: Implement test_empty_string_throws_exception');
}
```

Implementez ce test pour qu'il :
1. Cree une instance de `Mod97Validator`
2. Attende une exception `InvalidIbanException`
3. Appelle `validate('')` avec une chaine vide

## Hints

::: tip Hint 1 - Instanciation
```php
$validator = new Mod97Validator();
```
:::

::: tip Hint 2 - Attendre une exception
```php
$this->expectException(InvalidIbanException::class);
```
:::

::: tip Hint 3 - Appel de la methode
```php
$validator->validate('');
```
:::

## Verification

```bash
make test
```

**Resultat attendu :**
```
Error: Class "App\Domain\Banking\Mod97Validator" not found
```

C'est **normal** ! Le test est en <span class="red">RED</span> parce que la classe n'existe pas encore.

## Solution

<Solution>

```php
public function test_empty_string_throws_exception(): void
{
    $validator = new Mod97Validator();

    $this->expectException(InvalidIbanException::class);
    $validator->validate('');
}
```

</Solution>

## Etape suivante

Maintenant que vous avez un test en <span class="red">RED</span>, passez a l'etape <span class="green">GREEN</span> :

1. Creez la classe `Mod97Validator` dans `src/Domain/Banking/`
2. Implementez le code **minimal** pour faire passer le test
3. Relancez `make test`

➡️ [Exercice 02: Red-Green-Refactor](/exercises/02-red-green)
