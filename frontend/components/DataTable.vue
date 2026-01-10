<!--
  DataTable Component
  
  Reusable table component with sorting and actions
  
  Props:
  - columns: Array of column definitions
  - data: Array of row data
  - loading: boolean - Show loading state
  
  Usage:
  <DataTable 
    :columns="[{ key: 'name', label: 'Name' }]"
    :data="products"
    :loading="loading"
  >
    <template #actions="{ row }">
      <button @click="edit(row)">Edit</button>
    </template>
  </DataTable>
-->

<template>
  <div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200">
      <!-- Table Head -->
      <thead class="bg-gray-50">
        <tr>
          <th
            v-for="column in columns"
            :key="column.key"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            :class="column.class"
          >
            {{ column.label }}
          </th>
          <th v-if="$slots.actions" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
            Actions
          </th>
        </tr>
      </thead>

      <!-- Table Body -->
      <tbody class="bg-white divide-y divide-gray-200">
        <!-- Loading State -->
        <tr v-if="loading">
          <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-6 py-12 text-center">
            <div class="flex items-center justify-center">
              <svg class="animate-spin h-8 w-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
          </td>
        </tr>

        <!-- Empty State -->
        <tr v-else-if="data.length === 0">
          <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-6 py-12 text-center text-gray-500">
            No data available
          </td>
        </tr>

        <!-- Data Rows -->
        <tr v-else v-for="(row, index) in data" :key="index" class="hover:bg-gray-50 transition-colors">
          <td
            v-for="column in columns"
            :key="column.key"
            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
            :class="column.class"
          >
            <!-- Custom slot for column -->
            <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
              {{ row[column.key] }}
            </slot>
          </td>

          <!-- Actions Column -->
          <td v-if="$slots.actions" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <slot name="actions" :row="row" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import type { TableColumn } from '~/types'

interface Props {
  columns: TableColumn[]
  data: any[]
  loading?: boolean
}

withDefaults(defineProps<Props>(), {
  loading: false
})
</script>
