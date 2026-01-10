<script setup lang="ts">
import { Pie } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const props = defineProps<{
  chartData: {
    labels: string[]
    datasets: Array<{
      data: number[]
      backgroundColor?: string[]
      borderColor?: string[]
      borderWidth?: number
    }>
  }
  options?: any
  height?: number
}>()

const colorMode = useColorMode()

const defaultOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom' as const,
      labels: {
        color: colorMode.value === 'dark' ? '#e5e7eb' : '#374151',
        padding: 15,
        font: {
          size: 12
        }
      }
    },
    tooltip: {
      backgroundColor: colorMode.value === 'dark' ? '#1f2937' : '#ffffff',
      titleColor: colorMode.value === 'dark' ? '#e5e7eb' : '#111827',
      bodyColor: colorMode.value === 'dark' ? '#e5e7eb' : '#374151',
      borderColor: colorMode.value === 'dark' ? '#374151' : '#e5e7eb',
      borderWidth: 1
    }
  },
  ...props.options
}))
</script>

<template>
  <div :style="{ height: `${height || 300}px` }">
    <Pie :data="chartData" :options="defaultOptions" />
  </div>
</template>
