# Branche 05-value-object

## Objectif

Montrer le **REFACTORING** a grande echelle grace a la confiance des tests.

## Avant / Apres

**Avant (branche 04)** :
```php
$validator = new LuhnValidator();
if ($validator->validate($userInput)) {
    // OK mais $userInput est toujours une string
    // Rien ne garantit qu'elle est valide plus tard
}
```

**Apres (branche 05)** :
```php
$iban = new Iban($userInput); // Exception si invalide
// A partir d'ici, $iban est GARANTI valide
doSomething($iban);
```

## Le Value Object Iban

```php
final class Iban
{
    private string $value;

    public function __construct(string $value)
    {
        // Validation dans le constructeur
        // Impossible de creer un Iban invalide
    }

    public function getCountryCode(): string { ... }
    public function getCheckDigits(): string { ... }
    public function getBban(): string { ... }
    public function toFormattedString(): string { ... }
    public function equals(self $other): bool { ... }
}
```

## Avantages DDD

| Aspect | String | Value Object |
|--------|--------|--------------|
| Type-safety | Non | Oui |
| Validation | A chaque usage | Une seule fois |
| Methodes utiles | Non | Oui |
| Immutabilite | Non | Oui |

## Demonstration

```bash
./vendor/bin/phpunit

# LuhnValidatorTest : 17 tests
# IbanTest : 14 tests
# TOTAL : 31 tests, tous verts !
```

**Les tests existants passent toujours** - c'est la puissance du TDD pour le refactoring.

## Points cles

- **Les tests existants sont notre filet de securite**
- **On peut refactorer en confiance**
- **Le Value Object encapsule la validation**
- **Type-safety : le compilateur nous aide**

## Etape suivante

```bash
git checkout 06-symfony-integration
```

La branche suivante montre comment integrer le Domain dans Symfony.
