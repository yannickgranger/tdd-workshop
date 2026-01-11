import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'TDD Workshop',
  description: 'Workshop pratique Test-Driven Development avec PHP',
  lang: 'fr-FR',
  ignoreDeadLinks: true,

  head: [
    ['link', { rel: 'icon', href: '/favicon.ico' }]
  ],

  themeConfig: {
    logo: '/logo.svg',

    nav: [
      { text: 'Guide', link: '/guide/' },
      { text: 'Exercices', link: '/exercises/' },
      { text: 'Venus Case Study', link: '/guide/08-venus-lock-steal' }
    ],

    sidebar: {
      '/guide/': [
        {
          text: 'Introduction',
          items: [
            { text: 'Accueil', link: '/guide/' },
            { text: 'Strategie de tests', link: '/guide/00-strategie-tests' },
            { text: 'Algorithme IBAN', link: '/guide/01-algorithme-iban' }
          ]
        },
        {
          text: 'TDD Fondamentaux',
          items: [
            { text: 'Introduction au TDD', link: '/guide/02-introduction' },
            { text: 'La procedure TDD', link: '/guide/03-tdd-procedure' },
            { text: 'Walkthrough Luhn', link: '/guide/04-luhn-walkthrough' }
          ]
        },
        {
          text: 'Architecture',
          items: [
            { text: 'TDD et Hexagonal', link: '/guide/05-hexagonal-testing' },
            { text: 'Contexte Symfony', link: '/guide/06-symfony-context' }
          ]
        },
        {
          text: 'Cas Pratique',
          items: [
            { text: 'Exemple Venus', link: '/guide/07-venus-example' },
            { text: 'Venus Lock Steal', link: '/guide/08-venus-lock-steal' }
          ]
        },
        {
          text: 'Reference',
          items: [
            { text: 'Glossaire', link: '/guide/09-glossaire' },
            { text: 'Recapitulatif', link: '/guide/10-recapitulatif' }
          ]
        }
      ],
      '/exercises/': [
        {
          text: 'Exercices',
          items: [
            { text: 'Vue d\'ensemble', link: '/exercises/' },
            { text: '01 - Setup', link: '/exercises/01-setup' },
            { text: '02 - Red-Green-Refactor', link: '/exercises/02-red-green' },
            { text: '03 - Algorithm Discovery', link: '/exercises/03-algorithm' },
            { text: '04 - Edge Cases', link: '/exercises/04-edge-cases' },
            { text: '05 - Value Object', link: '/exercises/05-value-object' }
          ]
        }
      ]
    },

    socialLinks: [
      { icon: 'gitlab', link: 'https://gitlab.com/nicesoft/tdd-workshop' }
    ],

    footer: {
      message: 'TDD Workshop - Softway Medical',
      copyright: 'MIT License'
    },

    search: {
      provider: 'local'
    },

    outline: {
      level: [2, 3],
      label: 'Sur cette page'
    }
  },

  markdown: {
    lineNumbers: true,
    theme: {
      light: 'github-light',
      dark: 'github-dark'
    }
  }
})
