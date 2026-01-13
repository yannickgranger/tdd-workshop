---
layout: home

hero:
  name: TDD Workshop
  text: Test-Driven Development avec PHP
  tagline: Apprenez le cycle Red-Green-Refactor sur un cas concret
  image:
    src: /hero-image.svg
    alt: TDD Cycle
  actions:
    - theme: brand
      text: Commencer le Guide
      link: /guide/
    - theme: alt
      text: Exercices Pratiques
      link: /exercises/

features:
  - icon: 🔴
    title: RED
    details: Ecrivez un test qui echoue. Le test definit le comportement attendu avant meme que le code existe.
  - icon: 🟢
    title: GREEN
    details: Ecrivez le code minimal pour faire passer le test. Pas plus, pas moins.
  - icon: 🔵
    title: REFACTOR
    details: Ameliorez le code en toute confiance. Les tests vous protegent des regressions.
  - icon: 🏗️
    title: Architecture Hexagonale
    details: Le TDD s'integre naturellement dans une architecture propre. Domain pur, sans framework.
  - icon: 🧪
    title: Cas Pratique IBAN
    details: Implementez un validateur IBAN etape par etape, en decouvrant l'algorithme via les tests.
  - icon: 🏥
    title: Cas Reel
    details: Decouvrez comment le TDD a ete applique sur un systeme medical en production.
---

<style>
:root {
  --vp-home-hero-name-color: transparent;
  --vp-home-hero-name-background: linear-gradient(120deg, #10b981 30%, #3b82f6);
}
</style>
