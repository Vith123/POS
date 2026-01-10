<!--
  Sales History Page
  
  View all past sales transactions
-->

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Sales History</h1>
      <p class="text-gray-600">View all sales transactions</p>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Search by sale number..."
            class="input"
            @input="fetchSales"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select v-model="filters.status" class="input" @change="fetchSales">
            <option value="">All Status</option>
            <option value="completed">Completed</option>
            <option value="pending">Pending</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
          <input
            v-model="filters.from_date"
            type="date"
            class="input"
            @change="fetchSales"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
          <input
            v-model="filters.to_date"
            type="date"
            class="input"
            @change="fetchSales"
          />
        </div>
      </div>
    </div>

    <!-- Today's Summary -->
    <div v-if="todaySummary" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="card border-l-4 border-blue-500">
        <p class="text-sm text-gray-600 mb-1">Today's Sales</p>
        <p class="text-2xl font-bold text-gray-900">{{ todaySummary.sales_count }}</p>
      </div>
      <div class="card border-l-4 border-green-500">
        <p class="text-sm text-gray-600 mb-1">Today's Revenue</p>
        <p class="text-2xl font-bold text-gray-900">${{ todaySummary.total_revenue?.toFixed(2) }}</p>
      </div>
      <div class="card border-l-4 border-purple-500">
        <p class="text-sm text-gray-600 mb-1">Average Sale</p>
        <p class="text-2xl font-bold text-gray-900">${{ todaySummary.average_sale?.toFixed(2) }}</p>
      </div>
      <div class="card border-l-4 border-orange-500">
        <p class="text-sm text-gray-600 mb-1">Total Tax</p>
        <p class="text-2xl font-bold text-gray-900">${{ todaySummary.total_tax?.toFixed(2) }}</p>
      </div>
    </div>

    <!-- Sales Table -->
    <DataTable
      :columns="columns"
      :data="sales"
      :loading="loading"
    >
      <!-- Sale Number -->
      <template #cell-invoice_number="{ row }">
        <span class="font-mono text-sm">{{ row.invoice_number }}</span>
      </template>

      <!-- Customer -->
      <template #cell-customer="{ row }">
        {{ row.customer?.name || 'Walk-in' }}
      </template>

      <!-- Final Amount -->
      <template #cell-total="{ row }">
        ${{ row.total?.toFixed(2) }}
      </template>

      <!-- Payment Method -->
      <template #cell-payment_method="{ row }">
        <span class="capitalize">{{ row.payment_method }}</span>
      </template>

      <!-- Status -->
      <template #cell-status="{ row }">
        <span
          class="px-2 py-1 rounded-full text-xs font-semibold"
          :class="{
            'bg-green-100 text-green-800': row.status === 'completed',
            'bg-yellow-100 text-yellow-800': row.status === 'pending',
            'bg-red-100 text-red-800': row.status === 'cancelled'
          }"
        >
          {{ row.status }}
        </span>
      </template>

      <!-- Created At -->
      <template #cell-created_at="{ row }">
        {{ formatDate(row.created_at) }}
      </template>

      <!-- Actions -->
      <template #actions="{ row }">
        <button
          @click="viewSale(row)"
          class="text-blue-600 hover:text-blue-800 font-medium"
        >
          View
        </button>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { api } from '~/services/api'
import type { Sale, TableColumn } from '~/types'

definePageMeta({
  middleware: 'auth'
})

// State
const loading = ref(true)
const sales = ref<Sale[]>([])
const todaySummary = ref<any>(null)

// Filters
const filters = reactive({
  search: '',
  status: '',
  from_date: '',
  to_date: ''
})

// Table columns
const columns: TableColumn[] = [
  { key: 'invoice_number', label: 'Sale #' },
  { key: 'customer', label: 'Customer' },
  { key: 'total', label: 'Amount' },
  { key: 'payment_method', label: 'Payment' },
  { key: 'status', label: 'Status' },
  { key: 'created_at', label: 'Date' },
]

// Fetch sales
const fetchSales = async () => {
  try {
    loading.value = true
    const params: any = {}
    
    if (filters.search) params.search = filters.search
    if (filters.status) params.status = filters.status
    if (filters.from_date) params.from_date = filters.from_date
    if (filters.to_date) params.to_date = filters.to_date

    const response = await api.sales.getAll(params) as any
    console.log('Sales response:', response)
    
    // Handle both formats: {success, data} and Laravel pagination {data, meta}
    if (response.data) {
      sales.value = Array.isArray(response.data) ? response.data : response.data
    } else if (Array.isArray(response)) {
      sales.value = response
    }
  } catch (error) {
    console.error('Error fetching sales:', error)
  } finally {
    loading.value = false
  }
}

// Fetch today's summary
const fetchTodaySummary = async () => {
  try {
    const response = await api.sales.getTodaySummary()
    todaySummary.value = response.data
  } catch (error) {
    console.error('Error fetching today summary:', error)
  }
}

// Format date
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// View sale details
const viewSale = (sale: Sale) => {
  navigateTo(`/sales/${sale.id}`)
}

// Load data on mount
onMounted(() => {
  fetchSales()
  fetchTodaySummary()
})
</script>
