# 05 - TDD dans un contexte Symfony

## Comment integrer le Domain TDD dans Symfony

Le Domain est **pur PHP**. Symfony n'intervient qu'aux couches externes.

## Structure type

```
src/
├── Domain/                      # PURE PHP - TDD
│   └── Banking/
│       ├── Iban.php
│       ├── Mod97Validator.php
│       └── InvalidIbanException.php
│
├── Application/                 # Use Cases
│   └── UseCase/
│       └── ValidatePaymentIban.php
│
├── Presentation/                # Symfony Controllers
│   └── Controller/
│       └── PaymentController.php
│
└── Infrastructure/              # Adaptateurs Symfony
    └── Symfony/
        └── Validator/
            ├── IbanConstraint.php
            └── IbanConstraintValidator.php
```

## Exemple : Use Case

```php
// src/Application/UseCase/ValidatePaymentIban.php

namespace App\Application\UseCase;

use App\Domain\Banking\Iban;
use App\Domain\Banking\InvalidIbanException;

final class ValidatePaymentIban
{
    public function execute(string $rawIban): ValidatePaymentIbanResult
    {
        try {
            $iban = new Iban($rawIban);
            return ValidatePaymentIbanResult::success($iban);
        } catch (InvalidIbanException $e) {
            return ValidatePaymentIbanResult::failure($e->getMessage());
        }
    }
}
```

Le Use Case :
- Appelle le Domain
- Ne contient pas de logique metier
- Gere les exceptions du Domain
- Retourne un Result object

## Exemple : Controller

```php
// src/Presentation/Controller/PaymentController.php

namespace App\Presentation\Controller;

use App\Application\UseCase\ValidatePaymentIban;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class PaymentController
{
    public function validateIban(
        Request $request,
        ValidatePaymentIban $useCase
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $result = $useCase->execute($data['iban'] ?? '');

        if ($result->isValid) {
            return new JsonResponse(['valid' => true, ...]);
        }

        return new JsonResponse(['error' => $result->errorMessage], 400);
    }
}
```

Le Controller :
- Transforme HTTP → Domain → HTTP
- Delegue au Use Case
- Ne contient pas de logique metier

## Exemple : Adaptateur Validator

```php
// src/Infrastructure/Symfony/Validator/IbanConstraintValidator.php

namespace App\Infrastructure\Symfony\Validator;

use App\Domain\Banking\Mod97Validator;
use Symfony\Component\Validator\ConstraintValidator;

final class IbanConstraintValidator extends ConstraintValidator
{
    private Mod97Validator $validator;

    public function __construct()
    {
        $this->validator = new Mod97Validator();
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        // Utilise le Domain depuis Symfony Validator
        if (!$this->validator->validate($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
```

L'adaptateur :
- Fait le pont entre Symfony Validator et le Domain
- Le Domain ne sait rien de Symfony
- On peut remplacer Symfony sans toucher au Domain

## Tests par couche

### Domain : Unit tests (TDD)

```bash
# Rapide, pas de framework
./vendor/bin/phpunit tests/Unit/Domain/
```

### Application : Unit tests

```bash
./vendor/bin/phpunit tests/Unit/Application/
```

### Infrastructure : Integration tests

```bash
# Utilise le kernel Symfony
./vendor/bin/phpunit tests/Integration/
```

### Presentation : Functional tests

```bash
# WebTestCase, requetes HTTP
./vendor/bin/phpunit tests/Functional/
```

## Configuration PHPUnit

```xml
<testsuites>
    <testsuite name="Unit">
        <directory>tests/Unit</directory>
    </testsuite>
    <testsuite name="Integration">
        <directory>tests/Integration</directory>
    </testsuite>
    <testsuite name="Functional">
        <directory>tests/Functional</directory>
    </testsuite>
</testsuites>
```

---

**Suivant** : [06 - Exemple Venus](06-venus-example.md)
