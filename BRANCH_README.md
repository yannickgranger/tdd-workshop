# Branche 01-setup

## Objectif

Montrer le point de depart du TDD : **le test existe, le code non**.

## Etat actuel

- PHPUnit configure
- Premier test ecrit : `test_empty_string_throws_exception()`
- Classes `Mod97Validator` et `InvalidIbanException` n'existent pas encore

## Demonstration

```bash
# Installer les dependances
composer install

# Lancer le test - IL DOIT ECHOUER (RED)
./vendor/bin/phpunit
```

Vous verrez une erreur :
```
Error: Class "App\Domain\Banking\Mod97Validator" not found
```

C'est **exactement** ce qu'on veut. Le test echoue parce que le code n'existe pas.

## Etape suivante

Passez a la branche `02-red-green-refactor` pour voir comment on passe au GREEN.

```bash
git checkout 02-red-green-refactor
```

## Point cle

> En TDD, on ecrit TOUJOURS le test avant le code.
> Le test definit le comportement attendu.
> L'erreur "class not found" est notre premier RED.
