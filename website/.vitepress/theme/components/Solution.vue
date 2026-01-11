<script setup lang="ts">
defineProps<{
  title?: string
}>()
</script>

<template>
  <details class="solution-block">
    <summary class="solution-summary">
      <span class="solution-icon">💡</span>
      {{ title || 'Voir la solution' }}
    </summary>
    <div class="solution-content">
      <slot />
    </div>
  </details>
</template>

<style scoped>
.solution-block {
  margin: 1.5rem 0;
  border: 1px solid var(--vp-c-divider);
  border-radius: 8px;
  overflow: hidden;
  background: var(--vp-c-bg-soft);
}

.solution-summary {
  padding: 0.75rem 1rem;
  cursor: pointer;
  font-weight: 600;
  color: var(--vp-c-brand-1);
  background: var(--vp-c-bg-alt);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  user-select: none;
  transition: background 0.2s;
}

.solution-summary:hover {
  background: var(--vp-c-bg-soft);
}

.solution-icon {
  font-size: 1.1em;
}

.solution-content {
  padding: 1rem;
  border-top: 1px solid var(--vp-c-divider);
}

details[open] .solution-summary {
  border-bottom: 1px solid var(--vp-c-divider);
}
</style>
