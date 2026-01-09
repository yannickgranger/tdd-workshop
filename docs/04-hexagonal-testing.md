# 04 - TDD et Architecture Hexagonale

## Rappel : Architecture Hexagonale

```
┌─────────────────────────────────────────────┐
│              PRESENTATION                    │
│  Controllers, API, CLI                      │
│  (Symfony, HTTP, Console)                   │
└─────────────────────┬───────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────┐
│              APPLICATION                     │
│  Use Cases, Orchestration                   │
│  (Coordonne le Domain)                      │
└─────────────────────┬───────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────┐
│                DOMAIN                        │
│  Regles metier, Value Objects, Services     │
│  >>> PURE PHP - PAS DE FRAMEWORK <<<        │
│  >>> C'EST ICI QUE LE TDD BRILLE <<<        │
└─────────────────────┬───────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────┐
│             INFRASTRUCTURE                   │
│  Doctrine, Redis, APIs externes             │
│  (Adaptateurs vers le monde exterieur)      │
└─────────────────────────────────────────────┘
```

## Pourquoi le Domain est ideal pour le TDD

### 1. Pas de dependances framework

```php
// Domain/Banking/LuhnValidator.php

namespace App\Domain\Banking;

// Aucun "use Symfony\..." !
// C'est du PHP pur.

final class LuhnValidator
{
    public function validate(string $iban): bool
    {
        // Logique metier pure
    }
}
```

### 2. Pas besoin de mocks

Puisqu'il n'y a pas de dependances, pas besoin de les mocker :

```php
// Test simple, pas de mock
public function test_valid_iban(): void
{
    $validator = new LuhnValidator();
    $this->assertTrue($validator->validate('FR76...'));
}
```

### 3. Tests rapides

Sans framework, sans base de donnees, les tests s'executent en millisecondes.

## La philosophie : "Mocks lie"

> Les mocks mentent. Ils simulent un comportement qui peut diverger de la realite.

### Exemple de mock qui ment

```php
// Le mock dit que la methode retourne toujours true
$repository = $this->createMock(UserRepository::class);
$repository->method('exists')->willReturn(true);

// Mais en vrai, la methode pourrait lever une exception,
// retourner null, ou avoir un comportement different.
```

### La solution : tester avec les vrais services

| Couche | Type de test | Mocks ? |
|--------|--------------|---------|
| Domain | Unit tests (TDD) | **NON** |
| Application | Unit tests | Minimal (ports seulement) |
| Infrastructure | Integration tests | **Services reels** |
| Presentation | Functional tests | **Stack complete** |

## Strategie de test par couche

### Domain : TDD pur

```php
// tests/Unit/Domain/Banking/LuhnValidatorTest.php
final class LuhnValidatorTest extends TestCase
{
    public function test_valid_iban(): void
    {
        $validator = new LuhnValidator();
        $this->assertTrue($validator->validate('FR76...'));
    }
}
```

- Pas de mock
- Test rapide
- TDD naturel

### Infrastructure : Integration tests

```php
// tests/Integration/Infrastructure/Doctrine/UserRepositoryTest.php
final class UserRepositoryTest extends KernelTestCase
{
    public function test_find_by_email(): void
    {
        // Utilise la VRAIE base de donnees (de test)
        $repository = self::getContainer()->get(UserRepository::class);
        $user = $repository->findByEmail('test@example.com');
        // ...
    }
}
```

- Base de donnees reelle (de test)
- Pas de mock
- Verifie le vrai comportement

### Presentation : Functional tests

```php
// tests/Functional/Controller/PaymentControllerTest.php
final class PaymentControllerTest extends WebTestCase
{
    public function test_validate_iban_endpoint(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/payment/validate-iban', [], [], [],
            json_encode(['iban' => 'FR76...'])
        );

        $this->assertResponseIsSuccessful();
    }
}
```

- Vraie requete HTTP
- Stack complete
- Teste l'integration reelle

## Resume

```
DOMAIN     → TDD, pas de mock, tests rapides
APPLICATION → Tests unitaires, mocks minimaux aux ports
INFRA      → Integration tests, services reels
PRESENTATION → Functional tests, stack complete
```

---

**Suivant** : [05 - Contexte Symfony](05-symfony-context.md)
