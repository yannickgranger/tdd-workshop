# Branche 06-symfony-integration

## Objectif

Montrer comment le **Domain TDD** s'integre dans une application **Symfony**.

## Architecture Hexagonale

```
PRESENTATION (Symfony Controllers)
       │
       ▼
APPLICATION (Use Cases)
       │
       ▼
DOMAIN (Pure PHP - TDD)  ◄── C'est ici qu'on fait du TDD
       │
       ▼
INFRASTRUCTURE (Symfony Validator, Doctrine...)
```

## Fichiers d'exemple

```
examples/symfony-integration/
├── src/
│   ├── Application/
│   │   └── UseCase/
│   │       └── ValidatePaymentIban.php    # Orchestre le Domain
│   │
│   ├── Presentation/
│   │   └── Controller/
│   │       └── PaymentController.php      # HTTP -> Use Case -> HTTP
│   │
│   └── Infrastructure/
│       └── Symfony/
│           └── Validator/
│               ├── IbanConstraint.php           # Annotation Symfony
│               └── IbanConstraintValidator.php  # Adaptateur vers Domain
```

## Ou tester quoi ?

| Couche | Fichier | Type de test | Mocks ? |
|--------|---------|--------------|---------|
| Domain | `Mod97Validator.php` | Unit test (TDD) | **NON** |
| Domain | `Iban.php` | Unit test (TDD) | **NON** |
| Application | `ValidatePaymentIban.php` | Unit test | Minimal |
| Presentation | `PaymentController.php` | Functional test | WebTestCase |
| Infrastructure | `IbanConstraintValidator.php` | Integration test | Service reel |

## Le principe : Domain = Pure PHP

```php
// MAUVAIS - Domain depend de Symfony
namespace App\Domain\Banking;

use Symfony\Component\Validator\...; // NON !

// BON - Domain est pur
namespace App\Domain\Banking;

// Pas de use Symfony\...
final class Mod97Validator { ... }
```

## Points cles

- **TDD sur le Domain** : c'est la ou les regles metier vivent
- **Pas de mocks dans le Domain** : c'est du pure PHP
- **Infrastructure = adaptateurs** : connectent le Domain a Symfony
- **Tests fonctionnels pour Presentation** : WebTestCase, vrais requetes HTTP

## Votre philosophie appliquee

> "Mocks lie" - on reserve les mocks aux frontieres (ports)
> "Integration tests against real services" - Infrastructure testee avec vrais services
> "TDD Hexagonal" - le Domain est le coeur teste en TDD
