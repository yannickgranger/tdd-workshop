# TDD Workshop - Exercises

**Version exercices** du workshop TDD. Les tests sont vides avec des hints - a vous de les implementer !

> Pour les solutions completes, voir le repo `tdd-workshop` (sans `-exercises`).

## Quick Start

### Option 1: Exercices PHP

```bash
# Cloner et installer
git clone <repo-url>
cd tdd-workshop-exercises
make install

# Choisir un exercice
git checkout 01-setup

# Implementer les tests et verifier
make test
```

### Option 2: Documentation Web (Docker)

```bash
# Dev server avec hot reload (localhost:5173)
make docker-dev

# Ou production (localhost:8080)
make docker-prod
```

---

## Structure des branches

| Branche | Exercice | Difficulte |
|---------|----------|------------|
| `01-setup` | Premier test en echec | Facile |
| `02-red-green-refactor` | Cycles TDD de base | Facile |
| `03-algorithm-discovery` | Implementation IBAN | Moyen |
| `04-edge-cases` | Normalisation | Moyen |
| `05-value-object` | Refactoring Value Object | Avance |
| `06-symfony-integration` | Integration Symfony | Avance |

---

## Documentation Site

Le site de documentation est construit avec **VitePress** et dockerise.

### Commandes Make

```bash
make help              # Voir toutes les commandes

# PHP
make install           # Installer PHP deps
make test              # Lancer les tests
make coverage          # Tests avec couverture

# Documentation (Docker)
make docker-dev        # Dev server (localhost:5173)
make docker-prod       # Production (localhost:8080)
make docker-build      # Build statique
make docker-stop       # Arreter les containers

# Documentation (Local Node.js)
make docs-install      # npm install
make docs-dev          # Dev server local
make docs-build        # Build local
```

### Structure du site

```
website/
├── index.md           # Page d'accueil
├── guide/             # Documentation theorique
│   ├── 00-strategie-tests.md
│   ├── 01-algorithme-iban.md
│   ├── ...
│   └── 08-lock-steal-case-study.md  # Case study
└── exercises/         # Exercices interactifs
    ├── 01-setup.md
    ├── 02-red-green.md
    └── ...
```

### Composants Vue

- `<Solution>` - Bloc pliable pour les solutions
- `<Exercise>` - Carte d'exercice avec difficulte

---

## Philosophie

- **TDD sur le Domain** : logique metier pure, pas de mocks
- **"Mocks lie"** : on prefere les vrais services
- **Architecture hexagonale** : Domain independant du framework

---

## Pour les formateurs

1. Le repo `tdd-workshop` contient les **solutions**
2. Le repo `tdd-workshop-exercises` contient les **exercices**
3. Le site web peut etre deploye sur GitLab Pages / Netlify

### Deploiement GitLab Pages

```yaml
# .gitlab-ci.yml
pages:
  image: node:20
  script:
    - npm ci
    - npm run docs:build
    - mv website/.vitepress/dist public
  artifacts:
    paths:
      - public
  only:
    - main
```
