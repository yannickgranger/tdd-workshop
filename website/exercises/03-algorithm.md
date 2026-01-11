---
title: "Exercice 03: Algorithm Discovery"
---

# Exercice 03: Decouverte de l'algorithme

<Exercise title="Implementer l'algorithme IBAN" difficulty="medium">

## Objectif

Implementer l'algorithme de validation IBAN (ISO 13616) en etant **guide par les tests**.

</Exercise>

## Setup

```bash
git checkout 03-algorithm-discovery
```

## Contexte

L'algorithme IBAN fonctionne ainsi :
1. Deplacer les 4 premiers caracteres a la fin
2. Convertir les lettres en nombres (A=10, B=11, ..., Z=35)
3. Calculer le modulo 97 → doit etre egal a 1

## Tests a implementer

### Test 4: Premier IBAN valide

```php
/**
 * EXERCICE:
 * - Utiliser assertTrue()
 * - IBAN de test: 'FR7630006000011234567890189'
 */
public function test_valid_french_iban_returns_true(): void
{
    $this->markTestIncomplete('TODO: Implement');
}
```

### Test 5: Generalisation (autre pays)

```php
/**
 * EXERCICE:
 * - IBAN de test: 'DE89370400440532013000'
 */
public function test_valid_german_iban_returns_true(): void
{
    $this->markTestIncomplete('TODO: Implement');
}
```

### Test 6: Checksum invalide

```php
/**
 * EXERCICE:
 * - Utiliser assertFalse()
 * - IBAN avec erreur: 'FR7630006000011234567890188'
 */
public function test_invalid_checksum_returns_false(): void
{
    $this->markTestIncomplete('TODO: Implement');
}
```

## La decouverte via TDD

En ecrivant ces tests, vous allez **decouvrir** :

1. Comment fonctionne l'algorithme ISO 13616
2. Le probleme des **grands nombres** (IBAN convertis depassent PHP_INT_MAX)
3. La technique du **modulo par morceaux**

## Solutions

<Solution title="Implementation de l'algorithme">

```php
private function verifyChecksum(string $iban): bool
{
    // Deplacer les 4 premiers caracteres a la fin
    $rearranged = substr($iban, 4) . substr($iban, 0, 4);

    // Convertir lettres -> nombres
    $numeric = $this->convertLettersToNumbers($rearranged);

    // Modulo 97 doit etre 1
    return $this->mod97($numeric) === 1;
}

private function convertLettersToNumbers(string $iban): string
{
    return preg_replace_callback(
        '/[A-Z]/',
        static fn(array $match): string => (string) (ord($match[0]) - ord('A') + 10),
        $iban
    ) ?? '';
}

private function mod97(string $numeric): int
{
    $remainder = 0;
    foreach (str_split($numeric, 7) as $chunk) {
        $remainder = (int) (($remainder . $chunk) % 97);
    }
    return $remainder;
}
```

</Solution>

➡️ [Exercice 04: Edge Cases](/exercises/04-edge-cases)
