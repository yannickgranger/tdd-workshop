# 08 - Recapitulatif du Workshop

## Ce que nous avons appris

### 1. Le cycle TDD

```
┌─────────┐     ┌─────────┐     ┌───────────┐
│   RED   │ ──► │  GREEN  │ ──► │ REFACTOR  │
│ (test)  │     │ (code)  │     │(ameliorer)│
└─────────┘     └─────────┘     └─────┬─────┘
     ▲                                │
     └────────────────────────────────┘
```

**Toujours dans cet ordre. Jamais de code sans test.**

---

### 2. Progression du workshop

| Branche | Concept | Ce qu'on apprend |
|---------|---------|------------------|
| `01-setup` | Premier test | Le test echoue car le code n'existe pas |
| `02-red-green-refactor` | Rythme TDD | 3 cycles complets |
| `03-algorithm-discovery` | Decouverte | TDD guide l'implementation |
| `04-edge-cases` | Cas limites | Chaque question = un test |
| `05-value-object` | Refactoring | Les tests protegent |
| `06-symfony-integration` | Architecture | Domain pur + Symfony |
| `07-glossary-recap` | Reference | Vocabulaire et concepts |

---

### 3. L'algorithme IBAN qu'on a construit

```php
final class LuhnValidator
{
    public function validate(string $iban): bool
    {
        $normalized = $this->normalize($iban);      // Espaces, majuscules
        $this->assertValidFormat($normalized);       // Vide, caracteres, longueur
        return $this->verifyChecksum($normalized);   // ISO 13616 mod 97
    }
}
```

**31 tests** couvrent tous les cas :
- Format invalide (vide, caracteres, longueur)
- Checksum invalide
- Normalisation (espaces, minuscules)
- Plusieurs pays (FR, DE, BE, ES, IT, NL)

---

### 4. Philosophie hexagonale

```
┌─────────────────────────────────────────────┐
│              PRESENTATION                    │
│  Controllers Symfony                        │
│  Tests : Functional (WebTestCase)           │
└─────────────────────┬───────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────┐
│              APPLICATION                     │
│  Use Cases                                  │
│  Tests : Unit (mocks minimaux)              │
└─────────────────────┬───────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────┐
│                DOMAIN                        │
│  LuhnValidator, Iban, Exception             │
│  Tests : Unit TDD (PAS DE MOCKS)            │  ◄── C'est ici qu'on fait du TDD
└─────────────────────┬───────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────┐
│             INFRASTRUCTURE                   │
│  Doctrine, Symfony Validator                │
│  Tests : Integration (services reels)       │
└─────────────────────────────────────────────┘
```

---

### 5. Pourquoi "Mocks lie"

| Approche | Probleme | Solution |
|----------|----------|----------|
| Mock tout | Le mock peut diverger de la realite | Tester avec vrais services |
| Mock le Domain | Pas de logique testee | Domain = pure PHP, pas de mock |
| Mock l'infra | Bugs d'integration caches | Integration tests |

**Notre approche** :
- **Domain** : TDD pur, pas de mock (c'est du PHP pur)
- **Infrastructure** : Integration tests avec vrais services
- **Mocks** : Seulement aux frontieres (ports) si necessaire

---

### 6. Les vertus du TDD decouvertes

#### Decouverte (Discovery)

Les tests nous ont force a decouvrir :
- L'algorithme ISO 13616
- Le probleme des grands nombres
- Les cas limites (espaces, minuscules)

#### Documentation

Les tests documentent le comportement :
```php
// Ce test DOCUMENTE que les espaces sont acceptes
public function test_iban_with_spaces_is_normalized(): void
```

#### Confiance

On a pu extraire le Value Object `Iban` sans peur :
```
Branch 04 : LuhnValidator seul
Branch 05 : + Iban Value Object
Tous les tests passent toujours !
```

---

## Checklist pour appliquer le TDD

### Avant de coder

- [ ] Ai-je identifie la regle metier a implementer ?
- [ ] Puis-je l'isoler dans le Domain (pure PHP) ?
- [ ] Quel est le test le plus simple que je peux ecrire ?

### Pendant le TDD

- [ ] Mon test echoue-t-il AVANT d'ecrire le code ?
- [ ] Mon code est-il MINIMAL pour passer le test ?
- [ ] Dois-je refactorer maintenant ?

### Apres le TDD

- [ ] Mes tests documentent-ils le comportement ?
- [ ] Puis-je refactorer en confiance ?
- [ ] Ai-je decouvert de nouveaux cas a tester ?

---

## Ressources pour aller plus loin

### Livres
- "Test-Driven Development by Example" - Kent Beck
- "Growing Object-Oriented Software, Guided by Tests" - Freeman & Pryce
- "Working Effectively with Legacy Code" - Michael Feathers
- "xUnit Test Patterns" - Gerard Meszaros

### Articles
- "Mocks Aren't Stubs" - Martin Fowler
- "The Practical Test Pyramid" - Ham Vocke

### Outils PHP
- PHPUnit : https://phpunit.de
- Behat : https://behat.org
- Infection (mutation testing) : https://infection.github.io

---

## Resume en une phrase

> **Ecrire le test d'abord, c'est designer l'interface avant l'implementation.**

Le TDD n'est pas une technique de test, c'est une technique de **design**.

---

**Fin du workshop** - Retour au [README](../README.md)
