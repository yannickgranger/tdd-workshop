# TDD Workshop - Test-Driven Development with PHP

Workshop pratique pour apprendre le Test-Driven Development avec PHP et l'architecture hexagonale.

## Objectifs

- Comprendre le cycle **Red-Green-Refactor**
- Pratiquer le TDD sur un cas concret (validation IBAN/mod97)
- Voir comment le TDD s'intègre dans une architecture hexagonale
- Découvrir la vertu "discovery" du TDD

## Structure des branches

Ce repo utilise des branches pour illustrer chaque étape du TDD :

| Branche | Contenu |
|---------|---------|
| `01-setup` | Projet vide, PHPUnit configuré, premier test en échec |
| `02-red-green-refactor` | Premiers cycles TDD (empty, invalid chars, too short) |
| `03-algorithm-discovery` | Construction de l'algorithme mod97 test par test |
| `04-edge-cases` | Normalisation (lowercase, espaces, variantes) |
| `05-value-object` | Refactoring : Value Object Iban |
| `06-symfony-integration` | Intégration dans un contexte Symfony |
| `07-glossary-recap` | Glossaire des termes + récapitulatif |
| `main` | Version finale avec documentation complète |

## Utilisation

```bash
# Installer les dépendances
make install

# Lancer les tests
make test

# Lancer les tests avec couverture
make coverage
```

## Pour suivre le workshop

1. Cloner le repo
2. Checkout la branche `01-setup`
3. Suivre le guide dans `docs/`

## Philosophie

- **TDD sur le Domain** : logique métier pure, pas de mocks
- **Tests d'intégration pour l'Infrastructure** : services réels
- **"Mocks lie"** : on mock uniquement aux frontières
- **Architecture hexagonale** : Domain indépendant du framework

## Documentation

- [00 - Stratégie de tests](docs/00-strategie-tests.md) - Pyramide, types, approche TDD+BDD
- [01 - L'algorithme IBAN](docs/01-algorithme-iban.md) - Comprendre ce qu'on va coder
- [02 - Introduction au TDD](docs/02-introduction.md)
- [03 - La procédure TDD](docs/03-tdd-procedure.md)
- [04 - Walkthrough Mod97](docs/04-mod97-walkthrough.md)
- [05 - TDD et Hexagonal](docs/05-hexagonal-testing.md)
- [06 - Contexte Symfony](docs/06-symfony-context.md)
- [07 - Exemple SOFTWARE](docs/07-case-study-example.md) *(placeholder)*
- [08 - Glossaire](docs/08-glossaire.md) - Termes de test en français
- [09 - Récapitulatif](docs/09-recapitulatif.md) - Résumé du workshop
