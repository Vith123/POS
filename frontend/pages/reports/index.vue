<!--
  Reports & Analytics Page
  Comprehensive business reports with multiple chart types
-->

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold dark:text-white">{{ $t('reports.title') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $t('dashboard.viewReports') }}</p>
      </div>
      <div class="flex gap-3">
        <button @click="generatePDF" class="btn btn-secondary flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>
          {{ $t('reports.downloadPdf') }}
        </button>
        <button @click="refreshData" class="btn btn-primary flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          {{ $t('common.filter') }}
        </button>
      </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card dark:bg-gray-800">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium dark:text-gray-300 mb-1">{{ $t('common.from') }}</label>
          <input
            v-model="dateFrom"
            type="date"
            class="input dark:bg-gray-700 dark:text-white dark:border-gray-600"
          />
        </div>
        <div>
          <label class="block text-sm font-medium dark:text-gray-300 mb-1">{{ $t('common.to') }}</label>
          <input
            v-model="dateTo"
            type="date"
            class="input dark:bg-gray-700 dark:text-white dark:border-gray-600"
          />
        </div>
        <div>
          <label class="block text-sm font-medium dark:text-gray-300 mb-1">{{ $t('reports.selectPeriod') }}</label>
          <select v-model="period" @change="setPeriod" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600">
            <option value="today">{{ $t('common.today') }}</option>
            <option value="week">{{ $t('common.week') }}</option>
            <option value="month">{{ $t('common.month') }}</option>
            <option value="year">{{ $t('common.year') }}</option>
            <option value="custom">Custom</option>
          </select>
        </div>
        <div class="flex items-end">
          <button @click="fetchReports" class="btn btn-primary w-full">
            {{ $t('reports.generateReport') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-blue-100 mb-1">{{ $t('common.total') }} {{ $t('sales.title') }}</p>
            <p class="text-3xl font-bold">${{ reportData.totalSales?.toFixed(2) || '0.00' }}</p>
          </div>
          <svg class="w-12 h-12 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-green-100 mb-1">{{ $t('reports.profitReport') }}</p>
            <p class="text-3xl font-bold">${{ reportData.profit?.toFixed(2) || '0.00' }}</p>
          </div>
          <svg class="w-12 h-12 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
          </svg>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-purple-100 mb-1">{{ $t('common.total') }} {{ $t('sales.title') }}</p>
            <p class="text-3xl font-bold">{{ reportData.transactionCount || 0 }}</p>
          </div>
          <svg class="w-12 h-12 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-orange-100 mb-1">Avg {{ $t('common.amount') }}</p>
            <p class="text-3xl font-bold">${{ reportData.avgTransaction?.toFixed(2) || '0.00' }}</p>
          </div>
          <svg class="w-12 h-12 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0h2a2 2 0 012 2v-2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Main Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Sales Trend Chart -->
      <div class="card dark:bg-gray-800">
        <h3 class="text-lg font-semibold mb-4 dark:text-white flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          {{ $t('reports.salesTrend') }}
        </h3>
        <BarChart :chart-data="salesTrendData" :height="350" />
      </div>

      <!-- Sales by Category Pie Chart -->
      <div class="card dark:bg-gray-800">
        <h3 class="text-lg font-semibold mb-4 dark:text-white flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
          </svg>
          {{ $t('reports.salesByCategory') }}
        </h3>
        <PieChart :chart-data="categoryChartData" :height="350" />
      </div>

      <!-- Top Products Chart -->
      <div class="card dark:bg-gray-800">
        <h3 class="text-lg font-semibold mb-4 dark:text-white flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
          </svg>
          {{ $t('reports.topSellingProducts') }}
        </h3>
        <BarChart :chart-data="topProductsData" :height="350" />
      </div>

      <!-- Payment Methods Chart -->
      <div class="card dark:bg-gray-800">
        <h3 class="text-lg font-semibold mb-4 dark:text-white flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
          </svg>
          {{ $t('reports.salesByPaymentMethod') }}
        </h3>
        <PieChart :chart-data="paymentMethodData" :height="350" />
      </div>
    </div>

    <!-- Detailed Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Top Products Table -->
      <div class="card dark:bg-gray-800">
        <h3 class="text-lg font-semibold mb-4 dark:text-white">{{ $t('reports.topProducts') }}</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $t('products.productName') }}</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $t('common.quantity') }}</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $t('common.total') }}</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="(product, index) in topProducts" :key="index" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-4 py-3 text-sm dark:text-gray-300">{{ product.name }}</td>
                <td class="px-4 py-3 text-sm dark:text-gray-300">{{ product.quantity }}</td>
                <td class="px-4 py-3 text-sm font-semibold dark:text-white">${{ product.total.toFixed(2) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Revenue by Category Table -->
      <div class="card dark:bg-gray-800">
        <h3 class="text-lg font-semibold mb-4 dark:text-white">{{ $t('reports.salesByCategory') }}</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $t('categories.title') }}</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $t('sales.title') }}</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $t('common.total') }}</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="(category, index) in categoryStats" :key="index" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-4 py-3 text-sm dark:text-gray-300">{{ category.name }}</td>
                <td class="px-4 py-3 text-sm dark:text-gray-300">{{ category.count }}</td>
                <td class="px-4 py-3 text-sm font-semibold dark:text-white">${{ category.total.toFixed(2) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { api } from '~/services/api'

definePageMeta({
  middleware: 'auth'
})

const { t } = useI18n()
const notificationStore = useNotificationStore()

// State
const loading = ref(true)
const period = ref('month')
const dateFrom = ref('')
const dateTo = ref('')

const reportData = ref({
  totalSales: 0,
  profit: 0,
  transactionCount: 0,
  avgTransaction: 0
})

const topProducts = ref<Array<{ name: string; quantity: number; total: number }>>([])
const categoryStats = ref<Array<{ name: string; count: number; total: number }>>([])

// Initialize dates
const initDates = () => {
  const today = new Date()
  dateTo.value = today.toISOString().split('T')[0]
  
  const monthAgo = new Date()
  monthAgo.setMonth(monthAgo.getMonth() - 1)
  dateFrom.value = monthAgo.toISOString().split('T')[0]
}

const setPeriod = () => {
  const today = new Date()
  dateTo.value = today.toISOString().split('T')[0]
  
  switch (period.value) {
    case 'today':
      dateFrom.value = today.toISOString().split('T')[0]
      break
    case 'week':
      const weekAgo = new Date()
      weekAgo.setDate(weekAgo.getDate() - 7)
      dateFrom.value = weekAgo.toISOString().split('T')[0]
      break
    case 'month':
      const monthAgo = new Date()
      monthAgo.setMonth(monthAgo.getMonth() - 1)
      dateFrom.value = monthAgo.toISOString().split('T')[0]
      break
    case 'year':
      const yearAgo = new Date()
      yearAgo.setFullYear(yearAgo.getFullYear() - 1)
      dateFrom.value = yearAgo.toISOString().split('T')[0]
      break
  }
  
  if (period.value !== 'custom') {
    fetchReports()
  }
}

// Chart Data
const salesTrendData = computed(() => ({
  labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
  datasets: [{
    label: t('reports.dailySales'),
    data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
    backgroundColor: 'rgba(59, 130, 246, 0.8)',
    borderColor: 'rgba(59, 130, 246, 1)',
    borderWidth: 1
  }]
}))

const categoryChartData = computed(() => ({
  labels: categoryStats.value.map(c => c.name),
  datasets: [{
    data: categoryStats.value.map(c => c.total),
    backgroundColor: [
      'rgba(239, 68, 68, 0.8)',
      'rgba(59, 130, 246, 0.8)',
      'rgba(16, 185, 129, 0.8)',
      'rgba(245, 158, 11, 0.8)',
      'rgba(139, 92, 246, 0.8)',
      'rgba(236, 72, 153, 0.8)',
    ]
  }]
}))

const topProductsData = computed(() => ({
  labels: topProducts.value.map(p => p.name),
  datasets: [{
    label: t('sales.title'),
    data: topProducts.value.map(p => p.total),
    backgroundColor: 'rgba(16, 185, 129, 0.8)',
    borderColor: 'rgba(16, 185, 129, 1)',
    borderWidth: 1
  }]
}))

const paymentMethodData = computed(() => ({
  labels: ['Cash', 'Card', 'Mobile Money', 'Bank Transfer'],
  datasets: [{
    data: [45000, 32000, 18000, 12000],
    backgroundColor: [
      'rgba(34, 197, 94, 0.8)',
      'rgba(59, 130, 246, 0.8)',
      'rgba(245, 158, 11, 0.8)',
      'rgba(139, 92, 246, 0.8)',
    ]
  }]
}))

// Methods
const fetchReports = async () => {
  try {
    loading.value = true
    
    // Fetch sales report
    const salesResponse = await api.reports.sales({ from: dateFrom.value, to: dateTo.value })
    reportData.value = {
      totalSales: salesResponse.data?.total_sales || 0,
      profit: salesResponse.data?.profit || 0,
      transactionCount: salesResponse.data?.transaction_count || 0,
      avgTransaction: salesResponse.data?.avg_transaction || 0
    }
    
    // Fetch top products
    const productsResponse = await api.reports.topProducts({ from: dateFrom.value, to: dateTo.value })
    topProducts.value = productsResponse.data || []
    
    // Fetch category stats
    const categoriesResponse = await api.reports.categories({ from: dateFrom.value, to: dateTo.value })
    categoryStats.value = categoriesResponse.data || []
    
    notificationStore.success(t('messages.success'), 'Report generated successfully')
  } catch (error) {
    console.error('Error fetching reports:', error)
    notificationStore.error(t('messages.error'), t('messages.failed'))
  } finally {
    loading.value = false
  }
}

const refreshData = () => {
  fetchReports()
}

const generatePDF = () => {
  notificationStore.info(t('messages.info'), 'PDF generation feature coming soon')
}

// Lifecycle
onMounted(() => {
  initDates()
  fetchReports()
})
</script>
