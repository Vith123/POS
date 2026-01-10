<!--
  Loading Spinner Component
  
  Simple loading spinner for async operations
  
  Props:
  - size: 'sm' | 'md' | 'lg' - Spinner size
  - text: string - Optional loading text
-->

<template>
  <div class="flex flex-col items-center justify-center gap-3">
    <svg
      class="animate-spin text-primary-600"
      :class="sizeClass"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <p v-if="text" class="text-sm text-gray-600">{{ text }}</p>
  </div>
</template>

<script setup lang="ts">
interface Props {
  size?: 'sm' | 'md' | 'lg'
  text?: string
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md'
})

const sizeClass = computed(() => {
  switch (props.size) {
    case 'sm': return 'h-4 w-4'
    case 'md': return 'h-8 w-8'
    case 'lg': return 'h-12 w-12'
    default: return 'h-8 w-8'
  }
})
</script>
