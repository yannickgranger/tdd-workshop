import DefaultTheme from 'vitepress/theme'
import Solution from './components/Solution.vue'
import Exercise from './components/Exercise.vue'
import './custom.css'

export default {
  extends: DefaultTheme,
  enhanceApp({ app }) {
    app.component('Solution', Solution)
    app.component('Exercise', Exercise)
  }
}
