# 03 - Walkthrough : Validation IBAN avec TDD

## L'algorithme IBAN (ISO 13616)

Un IBAN est valide si :
1. Format correct (lettres + chiffres, longueur minimale)
2. Checksum valide (modulo 97)

L'algorithme de verification :
1. Deplacer les 4 premiers caracteres a la fin
2. Convertir les lettres en nombres (A=10, B=11, ..., Z=35)
3. Le reste de la division par 97 doit etre 1

## Session TDD complete

### Cycle 1 : Chaine vide

**Test (RED)**
```php
public function test_empty_string_throws_exception(): void
{
    $this->expectException(InvalidIbanException::class);
    $this->validator->validate('');
}
```

**Code (GREEN)**
```php
if ($iban === '') {
    throw new InvalidIbanException('IBAN cannot be empty');
}
return true;
```

---

### Cycle 2 : Caracteres invalides

**Decouverte** : "Quels caracteres sont valides ?"
→ Un IBAN ne contient que des lettres et des chiffres.

**Test (RED)**
```php
public function test_invalid_characters_throws_exception(): void
{
    $this->expectException(InvalidIbanException::class);
    $this->validator->validate('FR76!@#$');
}
```

**Code (GREEN)**
```php
if (!preg_match('/^[A-Za-z0-9]+$/', $iban)) {
    throw new InvalidIbanException('IBAN contains invalid characters');
}
```

---

### Cycle 3 : Longueur minimale

**Decouverte** : "Quelle longueur minimale ?"
→ 2 lettres pays + 2 chiffres check + au moins 1 caractere = 5 minimum.

**Test (RED)**
```php
public function test_too_short_throws_exception(): void
{
    $this->expectException(InvalidIbanException::class);
    $this->validator->validate('FR7');
}
```

**Code (GREEN)**
```php
if (strlen($iban) < 5) {
    throw new InvalidIbanException('IBAN is too short');
}
```

---

### Cycle 4 : Premier IBAN valide

**Decouverte** : "Comment fonctionne l'algorithme ?"
→ Recherche ISO 13616, comprehension du mod 97.

**Test (RED)**
```php
public function test_valid_french_iban_returns_true(): void
{
    $this->assertTrue(
        $this->validator->validate('FR7630006000011234567890189')
    );
}
```

**Code (GREEN)**
```php
private function verifyChecksum(string $iban): bool
{
    $iban = strtoupper($iban);
    $rearranged = substr($iban, 4) . substr($iban, 0, 4);
    $numeric = $this->convertLettersToNumbers($rearranged);
    return $this->mod97($numeric) === 1;
}
```

---

### Cycle 5 : Autre pays (generalisation)

**Decouverte** : "L'algorithme est-il generique ?"

**Test (RED)**
```php
public function test_valid_german_iban_returns_true(): void
{
    $this->assertTrue(
        $this->validator->validate('DE89370400440532013000')
    );
}
```

Le test passe sans modification ! Notre algorithme est bien generique.

---

### Cycle 6 : Checksum invalide

**Decouverte** : "Detecte-t-on vraiment les erreurs ?"

**Test (RED)**
```php
public function test_invalid_checksum_returns_false(): void
{
    $this->assertFalse(
        $this->validator->validate('FR7630006000011234567890188') // 189 -> 188
    );
}
```

Le test passe ! Notre algorithme detecte les erreurs.

---

### Cycle 7 : Grands nombres

**Decouverte** : "Le nombre converti depasse PHP_INT_MAX !"

Exemple : `DE89370400440532013000` → `370400440532013000131489`

Ce nombre est trop grand pour un `int` PHP.

**Solution** : Calculer le modulo par morceaux.

```php
private function mod97(string $numeric): int
{
    $remainder = 0;
    foreach (str_split($numeric, 7) as $chunk) {
        $remainder = (int)(($remainder . $chunk) % 97);
    }
    return $remainder;
}
```

---

## Resume des decouvertes

| Cycle | Question | Reponse (codifiee dans un test) |
|-------|----------|--------------------------------|
| 1 | Chaine vide ? | Exception |
| 2 | Caracteres valides ? | A-Za-z0-9 seulement |
| 3 | Longueur minimale ? | 5 caracteres |
| 4 | Algorithme ? | ISO 13616 mod 97 |
| 5 | Generique ? | Oui, fonctionne pour tous pays |
| 6 | Detection erreurs ? | Oui, checksum verifie |
| 7 | Grands nombres ? | Modulo par morceaux |

---

**Suivant** : [04 - TDD et Hexagonal](04-hexagonal-testing.md)
