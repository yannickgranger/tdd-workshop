# 00 - L'algorithme de validation IBAN

## Qu'est-ce qu'un IBAN ?

**IBAN** = International Bank Account Number

Format standardise (ISO 13616) pour identifier un compte bancaire a l'international.

```
FR76 3000 6000 0112 3456 7890 189
││   └─────────────────────────┘
││              │
││              └── BBAN (Basic Bank Account Number)
│└── Chiffres de controle (check digits)
└── Code pays (ISO 3166)
```

## Structure d'un IBAN

| Partie | Position | Description | Exemple |
|--------|----------|-------------|---------|
| Code pays | 1-2 | ISO 3166-1 alpha-2 | FR |
| Check digits | 3-4 | Verification mod 97 | 76 |
| BBAN | 5+ | Numero de compte national | 30006000011234567890189 |

## Longueur par pays

| Pays | Longueur | Exemple |
|------|----------|---------|
| France | 27 | FR76 3000 6000 0112 3456 7890 189 |
| Allemagne | 22 | DE89 3704 0044 0532 0130 00 |
| Belgique | 16 | BE68 5390 0754 7034 |
| Espagne | 24 | ES91 2100 0418 4502 0005 1332 |

## L'algorithme de validation (ISO 7064 Mod 97-10)

### Etape 1 : Reorganiser

Deplacer les 4 premiers caracteres a la fin.

```
FR7630006000011234567890189
    └──────────────────────┘└──┘
              BBAN           FR76

Resultat : 30006000011234567890189FR76
```

### Etape 2 : Convertir les lettres en nombres

Chaque lettre est convertie : A=10, B=11, ..., Z=35

```
30006000011234567890189FR76
                      │││ │
                      │││ └─ 6
                      ││└─── 7 (R = 27)
                      │└──── 1
                      └───── 5 (F = 15)

Resultat : 30006000011234567890189152776
```

### Etape 3 : Calculer modulo 97

Le nombre obtenu modulo 97 doit etre egal a **1**.

```
30006000011234567890189152776 mod 97 = 1  ✓ VALIDE
```

Si le resultat est 1, l'IBAN est valide.

## Probleme : Les grands nombres

Le nombre converti est **ENORME** :

```
30006000011234567890189152776
```

Ce nombre depasse `PHP_INT_MAX` (9223372036854775807 sur 64 bits).

### Solution : Modulo par morceaux

On peut calculer le modulo par parties grace a cette propriete mathematique :

```
(A × 10^n + B) mod 97 = ((A mod 97) × 10^n + B) mod 97
```

En pratique, on decoupe en morceaux de 7-9 chiffres :

```php
function mod97(string $number): int
{
    $remainder = 0;
    foreach (str_split($number, 7) as $chunk) {
        $remainder = (int)(($remainder . $chunk) % 97);
    }
    return $remainder;
}
```

## Exemple pas a pas

### IBAN : `DE89370400440532013000`

**Etape 1** : Reorganiser
```
DE89370400440532013000
    └──────────────┘└──┘
         BBAN       DE89

→ 370400440532013000DE89
```

**Etape 2** : Convertir les lettres
```
D = 13
E = 14

→ 370400440532013000131489
```

**Etape 3** : Modulo 97
```
370400440532013000131489 mod 97 = 1  ✓
```

L'IBAN est valide !

## Pourquoi c'est un bon exemple pour le TDD ?

1. **Algorithme non trivial** - On ne peut pas le deviner, il faut le construire
2. **Pure logique** - Pas de base de donnees, pas d'API
3. **Cas limites nombreux** - Espaces, minuscules, longueurs, pays
4. **Verification facile** - On peut tester avec de vrais IBAN

## IBAN de test

Voici des IBAN valides pour les tests (checksums corrects) :

```php
// France
'FR7630006000011234567890189'

// Allemagne
'DE89370400440532013000'

// Belgique
'BE68539007547034'

// Espagne
'ES9121000418450200051332'

// Italie
'IT60X0542811101000000123456'

// Pays-Bas
'NL91ABNA0417164300'
```

## Ce qu'on va construire en TDD

```php
final class LuhnValidator
{
    public function validate(string $iban): bool
    {
        // 1. Normaliser (espaces, majuscules)
        // 2. Valider le format
        // 3. Verifier le checksum mod 97
    }
}
```

On va construire cette classe **test par test**, en decouvrant l'algorithme au fur et a mesure.

---

**Suivant** : [01 - Introduction au TDD](01-introduction.md)
