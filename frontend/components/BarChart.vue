<script setup lang="ts">
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const props = defineProps<{
  chartData: {
    labels: string[]
    datasets: Array<{
      label: string
      data: number[]
      backgroundColor?: string | string[]
      borderColor?: string | string[]
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
      labels: {
        color: colorMode.value === 'dark' ? '#e5e7eb' : '#374151'
      }
    }
  },
  scales: {
    x: {
      ticks: {
        color: colorMode.value === 'dark' ? '#9ca3af' : '#6b7280'
      },
      grid: {
        color: colorMode.value === 'dark' ? '#374151' : '#e5e7eb'
      }
    },
    y: {
      ticks: {
        color: colorMode.value === 'dark' ? '#9ca3af' : '#6b7280'
      },
      grid: {
        color: colorMode.value === 'dark' ? '#374151' : '#e5e7eb'
      }
    }
  },
  ...props.options
}))
</script>

<template>
  <div :style="{ height: `${height || 300}px` }">
    <Bar :data="chartData" :options="defaultOptions" />
  </div>
</template>
