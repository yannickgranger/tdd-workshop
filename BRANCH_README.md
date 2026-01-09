# Branche 02-red-green-refactor

## Objectif

Montrer le **rythme TDD** : Red -> Green -> Refactor, repete 3 fois.

## Les 3 cycles

### Cycle 1 : Chaine vide
```
RED   : test_empty_string_throws_exception() echoue (classe n'existe pas)
GREEN : Creer LuhnValidator avec if ($iban === '') throw...
```

### Cycle 2 : Caracteres invalides
```
RED   : test_invalid_characters_throws_exception() echoue
GREEN : Ajouter preg_match('/^[A-Za-z0-9]+$/', $iban)
```

### Cycle 3 : Longueur minimale
```
RED   : test_too_short_throws_exception() echoue
GREEN : Ajouter strlen($iban) < 5
```

## Demonstration

```bash
# Les tests passent maintenant (GREEN)
./vendor/bin/phpunit

# Voir la couverture
make coverage
```

## Decouvertes pendant le TDD

En ecrivant les tests, on a decouvert des questions :

1. "Et les espaces ?" -> On decide de les refuser pour l'instant
2. "Quelle longueur minimale ?" -> On choisit 5 (simplifie)
3. "Majuscules/minuscules ?" -> On accepte les deux pour l'instant

Ces decisions sont documentees dans les tests !

## Points cles

- **Chaque test = une decision de design**
- **On n'implemente que ce qui est teste**
- **Les tests documentent le comportement**

## Etape suivante

```bash
git checkout 03-algorithm-discovery
```
