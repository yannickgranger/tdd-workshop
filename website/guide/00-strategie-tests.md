# 00 - Strategie de tests

## La pyramide des tests

```
            /\
           /  \        Tests E2E / UI
          /    \       (peu, lents, fragiles)
         /──────\
        /        \     Tests d'acceptation / BDD
       /          \    (scenarios metier)
      /────────────\
     /              \  Tests d'integration
    /                \ (composants reels)
   /──────────────────\
  /                    \ Tests unitaires
 /                      \(beaucoup, rapides, isoles)
/────────────────────────\
```

**Regle** : Plus on monte, moins on en ecrit. La base est solide, le sommet est leger.

---

## Les differents types de tests

### Tests unitaires

**Objectif** : Tester une unite de code isolee (classe, fonction).

```php
public function test_valid_iban_returns_true(): void
{
    $validator = new LuhnValidator();
    $this->assertTrue($validator->validate('FR7630006000011234567890189'));
}
```

| Caracteristique | Valeur |
|-----------------|--------|
| Vitesse | Tres rapide (ms) |
| Isolation | Complete |
| Dependances | Aucune |
| Quantite | Beaucoup |

**Ou** : Couche Domain (logique metier pure).

---

### Tests d'acceptation / BDD

**Objectif** : Valider un scenario metier du point de vue utilisateur.

```gherkin
Feature: Validation IBAN
  En tant qu'utilisateur
  Je veux valider mon IBAN
  Afin d'eviter les erreurs de paiement

  Scenario: IBAN francais valide
    Given je suis sur le formulaire de paiement
    When je saisis l'IBAN "FR76 3000 6000 0112 3456 7890 189"
    Then le systeme confirme que l'IBAN est valide
```

| Caracteristique | Valeur |
|-----------------|--------|
| Vitesse | Moyenne |
| Lisibilite | Metier (Gherkin) |
| Collaboration | Product + Dev |
| Quantite | Par feature |

**Quand utiliser le BDD** :
- Decouverte du besoin metier en codant
- Prototypage rapide avec le Product Owner
- Synchronisation equipe sur les scenarios
- Documentation vivante des features

**Outils** : Behat (PHP), Cucumber.

---

### Tests d'integration

**Objectif** : Verifier que les composants fonctionnent ensemble.

```php
public function test_repository_persists_and_retrieves_user(): void
{
    $repository = self::getContainer()->get(UserRepository::class);

    $user = new User('test@example.com');
    $repository->save($user);

    $found = $repository->findByEmail('test@example.com');
    $this->assertEquals($user->getId(), $found->getId());
}
```

| Caracteristique | Valeur |
|-----------------|--------|
| Vitesse | Lent |
| Dependances | Reelles (DB, Redis...) |
| Fiabilite | Haute |
| Quantite | Moderee |

**Ou** : Couche Infrastructure (repositories, APIs externes).

---

### Tests E2E (End-to-End)

**Objectif** : Tester l'application complete comme un utilisateur reel.

```php
public function test_complete_payment_flow(): void
{
    $client = static::createPantherClient();
    $client->request('GET', '/payment');
    $client->submitForm('Payer', ['iban' => 'FR76...']);
    $this->assertSelectorTextContains('.success', 'Paiement effectue');
}
```

| Caracteristique | Valeur |
|-----------------|--------|
| Vitesse | Tres lent |
| Fragilite | Haute |
| Realisme | Maximum |
| Quantite | Tres peu |

**Outils** : Symfony Panther, Playwright, Selenium.

---

## Notre approche : TDD Hexagonal + Contract-First

### Principe

```
┌─────────────────────────────────────────────────────────────┐
│                    TESTS D'ACCEPTATION                       │
│                         (Behat)                              │
│              In-Memory pour la vitesse                       │
└─────────────────────────────┬───────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      TESTS UNITAIRES                         │
│                    (PHPUnit + TDD)                           │
│                 Domain = Pure PHP                            │
└─────────────────────────────┬───────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                   TESTS D'INTEGRATION                        │
│                    (Contract-First)                          │
│              Services reels (DB, Redis...)                   │
└─────────────────────────────────────────────────────────────┘
```

### 1. Acceptance tests avec In-Memory

Pour la decouverte metier et le prototypage, on utilise Behat avec des **implementations In-Memory** :

```php
// tests/Double/InMemoryIbanRepository.php
final class InMemoryIbanRepository implements IbanRepositoryInterface
{
    private array $ibans = [];

    public function save(Iban $iban): void
    {
        $this->ibans[$iban->toString()] = $iban;
    }

    public function exists(string $iban): bool
    {
        return isset($this->ibans[$iban]);
    }
}
```

**Avantages** :
- Tests rapides (pas de DB)
- Focus sur le comportement metier
- Prototypage avec le Product Owner
- Feedback immediat

### 2. TDD sur le Domain

Le coeur metier est developpe en TDD pur :

```php
// Pas de mock, pas de framework
public function test_iban_validation(): void
{
    $validator = new LuhnValidator();
    $this->assertTrue($validator->validate('FR76...'));
}
```

**Pourquoi pas de mock** :
- Le Domain est du PHP pur
- Pas de dependances a mocker
- Tests fiables et rapides

### 3. Integration tests avec Contract-First

Une fois le comportement valide en In-Memory, on ajoute des tests d'integration pour verifier les **vrais** adaptateurs :

```php
// Le contrat definit ce que l'adaptateur DOIT faire
interface IbanRepositoryInterface
{
    public function save(Iban $iban): void;
    public function exists(string $iban): bool;
}

// Test du VRAI repository Doctrine
public function test_doctrine_repository_implements_contract(): void
{
    $repository = self::getContainer()->get(DoctrineIbanRepository::class);

    // Meme tests que InMemory
    $iban = new Iban('FR76...');
    $repository->save($iban);
    $this->assertTrue($repository->exists('FR76...'));
}
```

**Contract-First** = L'interface (contrat) est definie d'abord, puis :
1. Implementation In-Memory pour les tests d'acceptation
2. Implementation reelle (Doctrine, Redis...) pour la production
3. Tests d'integration verifient que l'implementation reelle respecte le contrat

---

## Quand utiliser quoi ?

| Situation | Type de test |
|-----------|--------------|
| Nouvelle regle metier | TDD unitaire (Domain) |
| Decouverte avec Product | BDD/Behat (In-Memory) |
| Nouvel adaptateur (DB, API) | Integration (Contract-First) |
| Parcours critique utilisateur | E2E (parcimonie) |
| Bug en production | Test de regression unitaire |

---

## Resume

```
BDD (Behat)          → Decouverte metier, prototypage, In-Memory
TDD (PHPUnit)        → Domain pur, pas de mock
Integration          → Contract-First, services reels
E2E                  → Parcours critiques uniquement
```

**Notre philosophie** :
- "Mocks lie" → On prefere In-Memory et vrais services
- TDD sur le Domain → Logique metier testee sans framework
- Contract-First → L'interface avant l'implementation
- BDD pour decouvrir → Synchronisation avec le metier

---

**Suivant** : [01 - L'algorithme IBAN](01-algorithme-iban.md)
