# 07 - Glossaire des termes de test

## Methodologies

### TDD (Test-Driven Development)

**Developpement guide par les tests.**

Methode ou l'on ecrit le test AVANT le code. Cycle : Red → Green → Refactor.

```
1. Ecrire un test qui echoue (RED)
2. Ecrire le code minimal pour passer (GREEN)
3. Ameliorer le code (REFACTOR)
4. Recommencer
```

**Avantages** : Design emergent, documentation vivante, confiance pour refactorer.

---

### BDD (Behavior-Driven Development)

**Developpement guide par le comportement.**

Evolution du TDD qui met l'accent sur le comportement metier plutot que l'implementation technique. Utilise un langage naturel (Gherkin).

```gherkin
Scenario: Validation d'un IBAN valide
  Given un utilisateur sur le formulaire de paiement
  When il saisit l'IBAN "FR7630006000011234567890189"
  Then le formulaire affiche "IBAN valide"
```

**Outils** : Behat (PHP), Cucumber (multi-langages).

**Difference avec TDD** : TDD se concentre sur les tests unitaires techniques, BDD sur les scenarios metier comprehensibles par tous.

---

### ATDD (Acceptance Test-Driven Development)

**Developpement guide par les tests d'acceptation.**

Variante du TDD ou les tests d'acceptation sont ecrits en premier, en collaboration avec le metier.

**Difference avec BDD** : ATDD est plus technique, BDD plus oriente communication.

---

## Types de tests

### Test unitaire (Unit Test)

**Teste une unite de code isolee.**

- Teste une seule classe ou fonction
- Pas de dependances externes (DB, API, fichiers)
- Tres rapide (millisecondes)
- Le plus nombreux dans la pyramide des tests

```php
public function test_empty_string_throws_exception(): void
{
    $validator = new Mod97Validator();
    $this->expectException(InvalidIbanException::class);
    $validator->validate('');
}
```

---

### Test d'integration (Integration Test)

**Teste l'interaction entre plusieurs composants.**

- Verifie que les composants fonctionnent ensemble
- Peut utiliser une vraie base de donnees (de test)
- Plus lent que les tests unitaires
- Detecte les problemes d'integration

```php
public function test_repository_persists_user(): void
{
    $repository = self::getContainer()->get(UserRepository::class);
    $user = new User('test@example.com');
    $repository->save($user);

    $found = $repository->findByEmail('test@example.com');
    $this->assertEquals($user->getId(), $found->getId());
}
```

---

### Test fonctionnel (Functional Test)

**Teste une fonctionnalite complete du point de vue utilisateur.**

- Simule une interaction utilisateur
- Traverse toutes les couches de l'application
- Utilise le framework (Symfony WebTestCase)

```php
public function test_user_can_validate_iban(): void
{
    $client = static::createClient();
    $client->request('POST', '/api/payment/validate-iban', [], [], [],
        json_encode(['iban' => 'FR7630006000011234567890189'])
    );

    $this->assertResponseIsSuccessful();
    $this->assertJsonContains(['valid' => true]);
}
```

---

### Test d'acceptation (Acceptance Test)

**Verifie qu'une user story est complete.**

- Ecrit du point de vue du metier
- Valide les criteres d'acceptation
- Souvent en langage naturel (Gherkin/Behat)
- Execute par le Product Owner pour valider

```gherkin
Feature: Validation IBAN
  En tant qu'utilisateur
  Je veux valider mon IBAN
  Afin d'eviter les erreurs de saisie

  Scenario: IBAN francais valide
    Given je suis sur le formulaire de paiement
    When je saisis "FR76 3000 6000 0112 3456 7890 189"
    Then je vois le message "IBAN valide"
```

---

### Test de bout en bout (End-to-End / E2E Test)

**Teste l'application complete comme un utilisateur reel.**

- Utilise un vrai navigateur (Selenium, Playwright, Panther)
- Teste l'interface utilisateur reelle
- Le plus lent et fragile
- Reserve aux parcours critiques

```php
public function test_complete_payment_flow(): void
{
    $client = static::createPantherClient();
    $client->request('GET', '/payment');
    $client->submitForm('Valider', ['iban' => 'FR76...']);
    $this->assertSelectorTextContains('.success', 'Paiement enregistre');
}
```

---

### Test de regression (Regression Test)

**Verifie qu'un bug corrige ne reapparait pas.**

Tout test qui a ete ecrit suite a un bug. Reste dans la suite de tests pour toujours.

```php
/**
 * Regression: Bug #1234 - Les IBAN avec espaces echouaient
 */
public function test_iban_with_spaces_is_valid(): void
{
    $this->assertTrue($this->validator->validate('FR76 3000 6000 ...'));
}
```

---

### Test de non-regression

**Ensemble des tests qui garantissent que le systeme fonctionne toujours.**

Inclut : tests unitaires, integration, fonctionnels. Lance avant chaque deploiement.

---

## Doublures de test (Test Doubles)

### Doublure (Double)

**Terme generique pour tout objet qui remplace un vrai objet dans un test.**

Gerard Meszaros a defini 5 types de doublures dans "xUnit Test Patterns".

---

### Dummy (Fantoche)

**Objet passe en parametre mais jamais utilise.**

Sert juste a remplir une signature de methode.

```php
// Le logger n'est jamais appele dans ce test
$dummyLogger = $this->createMock(LoggerInterface::class);
$service = new PaymentService($validator, $dummyLogger);
```

---

### Stub (Bouchon)

**Retourne des reponses predefinies.**

Ne verifie rien, fournit juste des donnees pour le test.

```php
$stub = $this->createStub(ExchangeRateProvider::class);
$stub->method('getRate')->willReturn(1.12);

$converter = new CurrencyConverter($stub);
$result = $converter->convert(100, 'EUR', 'USD');

$this->assertEquals(112, $result);
```

---

### Spy (Espion)

**Enregistre les appels pour verification ulterieure.**

Comme un stub, mais garde trace de ce qui s'est passe.

```php
$spy = $this->createMock(EmailSender::class);
$spy->expects($this->once())
    ->method('send')
    ->with($this->equalTo('user@example.com'));

$service = new NotificationService($spy);
$service->notifyUser($user);
```

---

### Mock (Simulacre)

**Objet pre-programme avec des attentes.**

Verifie que certaines methodes sont appelees avec certains arguments.

```php
$mock = $this->createMock(PaymentGateway::class);
$mock->expects($this->once())
    ->method('charge')
    ->with(
        $this->equalTo(100),
        $this->equalTo('EUR')
    )
    ->willReturn(true);

$service = new PaymentService($mock);
$service->processPayment(100, 'EUR');
```

**Attention** : "Mocks lie" - Les mocks peuvent masquer des problemes reels. A utiliser avec parcimonie.

---

### Fake (Faux)

**Implementation fonctionnelle simplifiee.**

Fonctionne vraiment, mais de maniere simplifiee (ex: base en memoire).

```php
// Fake repository qui stocke en memoire
final class InMemoryUserRepository implements UserRepositoryInterface
{
    private array $users = [];

    public function save(User $user): void
    {
        $this->users[$user->getId()] = $user;
    }

    public function findById(string $id): ?User
    {
        return $this->users[$id] ?? null;
    }
}
```

**Avantage** : Plus realiste qu'un mock, tests plus fiables.

---

## Architectures

### Clean Architecture (Architecture Propre)

**Architecture en couches concentriques de Robert C. Martin (Uncle Bob).**

```
┌─────────────────────────────────────┐
│           Frameworks & UI           │  <- Details (Symfony, Doctrine)
├─────────────────────────────────────┤
│           Interface Adapters        │  <- Controllers, Presenters
├─────────────────────────────────────┤
│           Application (Use Cases)   │  <- Logique applicative
├─────────────────────────────────────┤
│           Entities (Domain)         │  <- Regles metier
└─────────────────────────────────────┘
```

**Regle** : Les dependances pointent vers l'interieur. Le Domain ne connait pas Symfony.

---

### Architecture Hexagonale (Ports & Adapters)

**Architecture d'Alistair Cockburn.**

```
              ┌─────────────┐
     HTTP ───►│             │◄─── CLI
              │             │
    REST ────►│   DOMAIN    │◄─── Tests
              │             │
  GraphQL ───►│             │◄─── Queue
              └──────┬──────┘
                     │
         ┌───────────┼───────────┐
         ▼           ▼           ▼
      Database    Redis       Email
```

**Ports** : Interfaces definies par le Domain.
**Adapters** : Implementations concretes (Doctrine, Redis...).

**Avantage pour TDD** : Le Domain est pur PHP, testable sans mocks.

---

### DDD (Domain-Driven Design)

**Conception guidee par le domaine metier.**

Approche de conception logicielle centree sur le metier. Concepts cles :

- **Bounded Context** : Frontiere semantique d'un modele
- **Entity** : Objet avec identite (User, Order)
- **Value Object** : Objet defini par ses attributs (Email, Iban)
- **Aggregate** : Groupe d'objets traites comme une unite
- **Repository** : Abstraction de la persistance
- **Domain Service** : Logique metier sans etat

```php
// Value Object
final class Iban
{
    public function __construct(private string $value)
    {
        // Auto-validation
    }
}

// Entity
final class BankAccount
{
    public function __construct(
        private AccountId $id,      // Identite
        private Iban $iban,         // Value Object
    ) {}
}
```

---

## Principes et patterns

### Arrange-Act-Assert (AAA)

**Structure standard d'un test.**

```php
public function test_valid_iban_returns_true(): void
{
    // Arrange - Preparer
    $validator = new Mod97Validator();

    // Act - Executer
    $result = $validator->validate('FR7630006000011234567890189');

    // Assert - Verifier
    $this->assertTrue($result);
}
```

---

### Given-When-Then (GWT)

**Equivalent BDD de AAA.**

```gherkin
Given un validateur IBAN
When je valide "FR7630006000011234567890189"
Then le resultat est valide
```

---

### FIRST (principes des bons tests)

- **Fast** : Rapides (millisecondes)
- **Independent** : Independants les uns des autres
- **Repeatable** : Reproductibles (pas de random, pas de date)
- **Self-validating** : Resultat clair (pass/fail)
- **Timely** : Ecrits au bon moment (avant le code en TDD)

---

### Pyramide des tests

```
        /\
       /  \      E2E (peu, lents, fragiles)
      /────\
     /      \    Integration (moyen)
    /────────\
   /          \  Unitaires (beaucoup, rapides, stables)
  /────────────\
```

**Regle** : Plus de tests unitaires, moins de tests E2E.

---

### Test de contrat (Contract Test)

**Verifie qu'un producteur et un consommateur s'accordent sur une API.**

```php
// Le producteur garantit ce format
public function test_api_returns_expected_format(): void
{
    $response = $this->get('/api/users/1');
    $this->assertJsonStructure([
        'id', 'email', 'name'
    ]);
}

// Le consommateur attend ce format
public function test_client_parses_user_correctly(): void
{
    $json = '{"id": 1, "email": "...", "name": "..."}';
    $user = UserDto::fromJson($json);
    $this->assertEquals(1, $user->id);
}
```

---

### Code Coverage (Couverture de code)

**Pourcentage de code execute par les tests.**

```bash
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text
```

**Attention** : 100% de couverture != 100% de qualite. Un code couvert peut avoir des bugs.

---

## Outils PHP

| Outil | Usage |
|-------|-------|
| **PHPUnit** | Framework de tests unitaires standard |
| **Behat** | BDD, tests d'acceptation en Gherkin |
| **Prophecy** | Alternative aux mocks PHPUnit |
| **Mockery** | Autre alternative pour les mocks |
| **Infection** | Tests de mutation |
| **PHPStan/Psalm** | Analyse statique (complement aux tests) |
| **DAMA DoctrineTestBundle** | Rollback automatique en DB |
| **Symfony Panther** | Tests E2E avec navigateur |

---

## Anti-patterns

### Test fragile (Brittle Test)

Test qui casse pour des raisons non pertinentes (ordre, timing, donnees).

### Test flaky

Test qui passe ou echoue de maniere aleatoire.

### Test lie a l'implementation

Test qui connait trop les details internes. Casse au moindre refactoring.

### Test Iceberg

Trop de tests E2E, pas assez de tests unitaires.

### Mock hell

Trop de mocks, le test ne teste plus rien de reel.

---

**Fin du glossaire** - Retour au [README](../README.md)
