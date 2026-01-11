---
title: Exercices TDD
---

# Exercices Pratiques

Mettez en pratique le TDD avec ces exercices guides.

## Avant de commencer

### Prerequis

```bash
# Cloner le repo d'exercices
git clone https://gitlab.com/nicesoft/tdd-workshop-exercises.git
cd tdd-workshop-exercises

# Installer les dependances
make install
```

### Structure des branches

Chaque exercice correspond a une branche Git :

| Branche | Exercice |
|---------|----------|
| `01-setup` | Premier test en echec |
| `02-red-green-refactor` | Cycles TDD de base |
| `03-algorithm-discovery` | Decouverte de l'algorithme |
| `04-edge-cases` | Gestion des cas limites |
| `05-value-object` | Refactoring en Value Object |

### Comment utiliser

1. **Checkout la branche** de l'exercice
2. **Lisez les instructions** dans le fichier test
3. **Implementez les tests** (ils sont vides avec des hints)
4. **Lancez les tests** pour verifier
5. **Comparez** avec la branche solution (`tdd-workshop` original)

```bash
# Exemple
git checkout 01-setup
make test
# Implementez...
make test
```

---

## Exercices

<div class="exercise-grid">

### [01 - Setup](/exercises/01-setup)
**Difficulte:** Facile
Premier test qui echoue. Decouvrez le point de depart du TDD.

### [02 - Red-Green-Refactor](/exercises/02-red-green)
**Difficulte:** Facile
Pratiquez les 3 premiers cycles TDD complets.

### [03 - Algorithm Discovery](/exercises/03-algorithm)
**Difficulte:** Moyen
Implementez l'algorithme IBAN en etant guide par les tests.

### [04 - Edge Cases](/exercises/04-edge-cases)
**Difficulte:** Moyen
Gerez les cas limites : minuscules, espaces, etc.

### [05 - Value Object](/exercises/05-value-object)
**Difficulte:** Avance
Refactorez vers un Value Object immutable.

</div>

---

## Besoin d'aide ?

- 📖 Consultez le [Guide TDD](/guide/)
- 💡 Les hints sont dans les docblocks des tests
- ✅ Les solutions sont dans le repo `tdd-workshop` (pas `-exercises`)

<style>
.exercise-grid h3 {
  margin-top: 1.5rem;
}
</style>
