# Branche 04-edge-cases

## Objectif

Montrer comment TDD aide a gerer les **CAS LIMITES** et a prendre des decisions de design.

## Questions decouvertes par les tests

| Question | Test | Decision |
|----------|------|----------|
| Minuscules ? | `test_lowercase_iban_is_normalized` | Normaliser en majuscules |
| Espaces ? | `test_iban_with_spaces_is_normalized` | Supprimer les espaces |
| Tabulations ? | `test_tabs_are_invalid` | Refuser (YAGNI) |

## Le pattern : Question -> Test -> Decision

```
1. "Et si l'utilisateur tape en minuscules ?"
   -> Ecrire un test : validate('fr76...') doit retourner true
   -> Implementer la normalisation
   -> Le test DOCUMENTE la decision

2. "Et les espaces ?"
   -> Ecrire un test : validate('FR76 3000 ...') doit retourner true
   -> Implementer str_replace(' ', '', $iban)
   -> Le test DOCUMENTE la decision
```

## Demonstration

```bash
./vendor/bin/phpunit

# 17 tests, 17 assertions
```

## Code ajoute

```php
private function normalize(string $iban): string
{
    // Supprimer les espaces
    $iban = str_replace(' ', '', $iban);

    // Convertir en majuscules
    return strtoupper($iban);
}
```

## Points cles

- **Chaque cas limite = un test explicite**
- **Le test documente la decision de design**
- **YAGNI : on ne gere pas ce qu'on n'a pas teste**
- **Les tests de regression protegent les decisions passees**

## Etape suivante

```bash
git checkout 05-value-object
```

La branche suivante montre le REFACTORING : extraire un Value Object Iban.
