<script setup lang="ts">
defineProps<{
  title: string
  difficulty?: 'easy' | 'medium' | 'hard'
}>()

const difficultyColors = {
  easy: '#10b981',
  medium: '#f59e0b',
  hard: '#ef4444'
}

const difficultyLabels = {
  easy: 'Facile',
  medium: 'Moyen',
  hard: 'Difficile'
}
</script>

<template>
  <div class="exercise-block">
    <div class="exercise-header">
      <span class="exercise-icon">🎯</span>
      <span class="exercise-title">{{ title }}</span>
      <span
        v-if="difficulty"
        class="exercise-difficulty"
        :style="{ backgroundColor: difficultyColors[difficulty] }"
      >
        {{ difficultyLabels[difficulty] }}
      </span>
    </div>
    <div class="exercise-content">
      <slot />
    </div>
  </div>
</template>

<style scoped>
.exercise-block {
  margin: 1.5rem 0;
  border: 2px solid var(--vp-c-brand-1);
  border-radius: 8px;
  overflow: hidden;
}

.exercise-header {
  padding: 0.75rem 1rem;
  background: var(--vp-c-brand-soft);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
}

.exercise-icon {
  font-size: 1.2em;
}

.exercise-title {
  flex: 1;
  color: var(--vp-c-brand-1);
}

.exercise-difficulty {
  font-size: 0.75rem;
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
  color: white;
  font-weight: 500;
}

.exercise-content {
  padding: 1rem;
}
</style>
