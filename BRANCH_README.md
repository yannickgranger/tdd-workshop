# Branche 03-algorithm-discovery

## Objectif

Montrer la vertu **DISCOVERY** du TDD : les tests nous guident vers l'implementation.

## Ce que le TDD nous a fait decouvrir

### 1. L'algorithme IBAN (ISO 13616)

En ecrivant `test_valid_french_iban_returns_true()`, on a du comprendre :
- Deplacer les 4 premiers caracteres a la fin
- Convertir les lettres en nombres (A=10, ..., Z=35)
- Modulo 97 doit etre 1

### 2. La generalisation

En testant plusieurs pays (FR, DE, BE), on a verifie que notre implementation n'etait pas trop specifique.

### 3. Le probleme des grands nombres

```php
// IBAN converti = 370400440532013000131489
// Ce nombre > PHP_INT_MAX !
```

Le test `test_long_iban_handles_large_numbers()` a revele ce bug potentiel. Sans TDD, on l'aurait decouvert en production.

**Solution** : Calculer le modulo par morceaux de 7 chiffres.

## Demonstration

```bash
# Tous les tests passent
./vendor/bin/phpunit

# 13 tests, 13 assertions
```

## Structure du code

```
src/Domain/Banking/
├── InvalidIbanException.php  # Exception metier
└── Mod97Validator.php         # Service domain avec algorithme complet
    ├── assertValidFormat()   # Validation de format
    ├── verifyChecksum()      # Algorithme ISO 13616
    ├── convertLettersToNumbers()
    └── mod97()               # Modulo pour grands nombres
```

## Points cles

- **Les tests nous ont FORCE a comprendre l'algorithme**
- **Chaque nouveau test = une nouvelle decouverte**
- **Les bugs potentiels sont decouverts TOT (grands nombres)**
- **Le code est documente par les tests**

## Etape suivante

```bash
git checkout 04-edge-cases
```

La branche suivante montre comment TDD aide a gerer les cas limites (espaces, minuscules).
