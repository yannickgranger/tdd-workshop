---
title: "Exercice 04: Edge Cases"
---

# Exercice 04: Gestion des cas limites

<Exercise title="Normalisation et edge cases" difficulty="medium">

## Objectif

Gerer les cas limites decouverts via TDD : minuscules, espaces, tabulations.

</Exercise>

## Setup

```bash
git checkout 04-edge-cases
```

## Questions decouvertes

En ecrivant les tests, ces questions emergent :

- "Et si l'utilisateur tape en **minuscules** ?"
- "Et s'il copie-colle avec des **espaces** ?"
- "Et les **tabulations** ?"

**Chaque question = un nouveau test = une decision de design.**

## Tests a implementer

### Normalisation - Minuscules

```php
/**
 * EXERCICE:
 * - Tester avec 'fr7630006000011234567890189' (minuscules)
 * - Doit retourner true (normalisation)
 */
public function test_lowercase_iban_is_normalized(): void
{
    $this->markTestIncomplete('TODO: Implement');
}
```

### Normalisation - Espaces

```php
/**
 * EXERCICE:
 * - Tester avec 'FR76 3000 6000 0112 3456 7890 189'
 */
public function test_iban_with_spaces_is_normalized(): void
{
    $this->markTestIncomplete('TODO: Implement');
}
```

### Tabulations (rejet)

```php
/**
 * EXERCICE:
 * - Tester avec "FR76\t3000" (tabulation)
 * - Doit lever InvalidIbanException
 */
public function test_tabs_are_invalid(): void
{
    $this->markTestIncomplete('TODO: Implement');
}
```

## Decision de design

| Input | Decision | Raison |
|-------|----------|--------|
| Minuscules | Normaliser | Meilleure UX |
| Espaces | Normaliser | Copier-coller courant |
| Tabulations | Rejeter | YAGNI |

## Solution - Normalisation

<Solution>

```php
private function normalize(string $iban): string
{
    // Supprimer les espaces
    $iban = str_replace(' ', '', $iban);

    // Convertir en majuscules
    return strtoupper($iban);
}
```

</Solution>

➡️ [Exercice 05: Value Object](/exercises/05-value-object)
