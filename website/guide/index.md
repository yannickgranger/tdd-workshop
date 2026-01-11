---
title: Guide TDD Workshop
---

# Guide TDD Workshop

Bienvenue dans le guide du workshop TDD. Ce guide couvre la theorie et les concepts du Test-Driven Development.

## Parcours recommande

### 1. Fondations

| Section | Description | Temps |
|---------|-------------|-------|
| [Strategie de tests](./00-strategie-tests.md) | Pyramide des tests, types, approche TDD+BDD | 10 min |
| [Algorithme IBAN](./01-algorithme-iban.md) | Comprendre ce qu'on va implementer | 5 min |

### 2. TDD en pratique

| Section | Description | Temps |
|---------|-------------|-------|
| [Introduction au TDD](./02-introduction.md) | Le cycle Red-Green-Refactor | 10 min |
| [La procedure TDD](./03-tdd-procedure.md) | Etape par etape, erreurs courantes | 10 min |
| [Walkthrough Luhn](./04-luhn-walkthrough.md) | Implementation guidee test par test | 20 min |

### 3. Architecture

| Section | Description | Temps |
|---------|-------------|-------|
| [TDD et Hexagonal](./05-hexagonal-testing.md) | Ou placer les tests, "Mocks lie" | 10 min |
| [Contexte Symfony](./06-symfony-context.md) | Integration dans un projet Symfony | 10 min |

### 4. Cas pratiques

| Section | Description | Temps |
|---------|-------------|-------|
| [Exemple Venus (placeholder)](./07-venus-example.md) | Pour les exercices en autonomie | - |
| [Venus Lock Steal](./08-venus-lock-steal.md) | **Case study complet** - RBAC en production | 30 min |

### 5. Reference

| Section | Description |
|---------|-------------|
| [Glossaire](./09-glossaire.md) | Termes de test en francais |
| [Recapitulatif](./10-recapitulatif.md) | Resume du workshop |

---

## Le cycle TDD

```
    ┌─────────────┐
    │    RED      │  Ecrire un test qui echoue
    └──────┬──────┘
           │
           ▼
    ┌─────────────┐
    │   GREEN     │  Code minimal pour passer
    └──────┬──────┘
           │
           ▼
    ┌─────────────┐
    │  REFACTOR   │  Ameliorer sans casser
    └──────┬──────┘
           │
           └──────────► Recommencer
```

## Pret a pratiquer ?

➡️ [Commencer les exercices](/exercises/)
