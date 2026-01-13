# Integration Symfony - Architecture Hexagonale

Ce dossier montre comment le Domain (TDD) s'integre dans une application Symfony.

## Structure

```
src/
├── Domain/                    # PURE PHP - teste avec TDD
│   └── Banking/
│       ├── Iban.php           # Value Object (de la branche 05)
│       ├── Mod97Validator.php  # Service Domain
│       └── InvalidIbanException.php
│
├── Application/               # Use Cases - orchestre le Domain
│   └── UseCase/
│       └── ValidatePaymentIban.php
│
├── Presentation/              # Controllers Symfony
│   └── Controller/
│       └── PaymentController.php
│
└── Infrastructure/            # Adaptateurs Symfony
    └── Symfony/
        └── Validator/
            └── IbanConstraint.php
            └── IbanConstraintValidator.php
```

## Principe Hexagonal

```
┌─────────────────────────────────────────────────────────────┐
│                      PRESENTATION                            │
│  Controllers, Forms, API Endpoints                          │
│  (Symfony HttpFoundation)                                   │
└─────────────────────────────┬───────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      APPLICATION                             │
│  Use Cases, Orchestration                                   │
│  (Appelle le Domain, gere les transactions)                 │
└─────────────────────────────┬───────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                        DOMAIN                                │
│  Regles metier pures, Value Objects, Services               │
│  (PURE PHP - PAS de Symfony, PAS de Doctrine)               │
│  >>> C'est ICI que le TDD est le plus efficace <<<          │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    INFRASTRUCTURE                            │
│  Adaptateurs : Doctrine, Redis, Symfony Validator           │
│  (Implemente les interfaces du Domain)                      │
└─────────────────────────────────────────────────────────────┘
```

## Ou tester quoi ?

| Couche | Type de test | Mocks ? |
|--------|--------------|---------|
| Domain | Unit tests (TDD) | **NON** - pure PHP |
| Application | Unit tests | Minimal (ports) |
| Presentation | Functional tests | WebTestCase |
| Infrastructure | Integration tests | Services reels |

## Point cle

> Le Domain est PURE PHP.
> Pas de `use Symfony\...` dans le Domain.
> C'est ce qui permet de le tester sans mocks.
