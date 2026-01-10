<!--
  Stats Card Component
  
  Reusable card for displaying statistics
-->

<template>
  <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all p-6 transform hover:scale-105" :class="borderClass">
    <div class="flex items-center justify-between mb-4">
      <svg class="w-12 h-12" :class="iconColorClass" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="icon"></svg>
      <div :class="badgeClass" class="text-xs font-bold px-3 py-1 rounded-full">
        {{ badgeText }}
      </div>
    </div>
    <div>
      <p class="text-sm text-gray-600 mb-2">{{ title }}</p>
      <p class="text-3xl font-bold text-gray-900 mb-1">{{ value }}</p>
      <p v-if="subtitle" class="text-xs text-gray-500">{{ subtitle }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  title: string
  value: string | number
  icon: string
  subtitle?: string
  color?: 'blue' | 'green' | 'purple' | 'orange' | 'red'
}

const props = withDefaults(defineProps<Props>(), {
  color: 'blue'
})

const borderClass = computed(() => {
  const colors = {
    blue: 'border-l-4 border-blue-500',
    green: 'border-l-4 border-green-500',
    purple: 'border-l-4 border-purple-500',
    orange: 'border-l-4 border-orange-500',
    red: 'border-l-4 border-red-500',
  }
  return colors[props.color]
})

const iconColorClass = computed(() => {
  const colors = {
    blue: 'text-blue-600',
    green: 'text-green-600',
    purple: 'text-purple-600',
    orange: 'text-orange-600',
    red: 'text-red-600',
  }
  return colors[props.color]
})

const badgeClass = computed(() => {
  const colors = {
    blue: 'bg-blue-100 text-blue-700',
    green: 'bg-green-100 text-green-700',
    purple: 'bg-purple-100 text-purple-700',
    orange: 'bg-orange-100 text-orange-700',
    red: 'bg-red-100 text-red-700',
  }
  return colors[props.color]
})

const badgeText = computed(() => {
  const labels = {
    blue: 'Sales',
    green: 'Revenue',
    purple: 'Tax',
    orange: 'Discount',
    red: 'Alert',
  }
  return labels[props.color]
})
</script>
