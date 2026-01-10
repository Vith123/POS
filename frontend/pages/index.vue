<!--
  Dashboard Page
  
  Main dashboard with business statistics and quick insights
-->

<template>
  <div class="space-y-8">
    <!-- Welcome Header with Gradient -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-xl p-8 text-white">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-bold mb-2">Dashboard</h1>
          <ClientOnly>
            <p class="text-blue-100 text-lg">Welcome back, {{ authStore.user?.name }}! 👋</p>
            <template #fallback>
              <p class="text-blue-100 text-lg">Welcome back! 👋</p>
            </template>
          </ClientOnly>
          <p class="text-blue-200 text-sm mt-2">{{ currentDate }}</p>
        </div>
        <div class="hidden md:block">
          <div class="bg-white/20 backdrop-blur-sm rounded-xl p-6 text-center">
            <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="text-sm font-medium">POS System</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-20">
      <LoadingSpinner size="lg" text="Loading dashboard..." />
    </div>

    <!-- Dashboard Content -->
    <div v-else-if="stats" class="space-y-8">
      <!-- Stats Cards Row 1 - Today -->
      <div>
        <div class="flex items-center justify-between mb-5">
          <h2 class="text-2xl font-bold text-gray-900 flex items-center">
            <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Today's Overview
          </h2>
          <span class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-full font-medium">Live Data</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <StatsCard
            title="Sales Today"
            :value="stats.today.sales_count.toString()"
            subtitle="Transactions"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />'
            color="blue"
          />
          <StatsCard
            title="Revenue Today"
            :value="formatCurrency(stats.today.revenue)"
            subtitle="Total earnings"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
            color="green"
          />
          <StatsCard
            title="Tax Collected"
            :value="formatCurrency(stats.today.tax)"
            subtitle="Government tax"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />'
            color="purple"
          />
          <StatsCard
            title="Discounts Given"
            :value="formatCurrency(stats.today.discount)"
            subtitle="Customer savings"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />'
            color="orange"
          />
        </div>
      </div>

      <!-- Stats Cards Row 2 - This Week -->
      <div>
        <div class="flex items-center justify-between mb-5">
          <h2 class="text-2xl font-bold text-gray-900 flex items-center">
            <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            This Week's Performance
          </h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <StatsCard
            title="Weekly Sales"
            :value="stats.week.sales_count.toString()"
            subtitle="Total transactions"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />'
            color="blue"
          />
          <StatsCard
            title="Weekly Revenue"
            :value="formatCurrency(stats.week.revenue)"
            subtitle="Total earnings"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />'
            color="green"
          />
          <StatsCard
            title="Tax Collected"
            :value="formatCurrency(stats.week.tax)"
            subtitle="Weekly tax"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />'
            color="purple"
          />
          <StatsCard
            title="Discounts Given"
            :value="formatCurrency(stats.week.discount)"
            subtitle="Weekly savings"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />'
            color="orange"
          />
        </div>
      </div>

      <!-- Inventory & Business Stats -->
      <div>
        <h2 class="text-2xl font-bold text-gray-900 mb-5 flex items-center">
          <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
          </svg>
          Inventory & Business Stats
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-3">
              <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
              <div class="bg-blue-100 text-blue-600 text-xs font-bold px-3 py-1 rounded-full">Total</div>
            </div>
            <p class="text-sm text-gray-600 mb-1">Total Products</p>
            <p class="text-3xl font-bold text-gray-900">{{ stats.inventory.total_products }}</p>
            <p class="text-xs text-gray-500 mt-2">In inventory</p>
          </div>

          <NuxtLink to="/products?filter=low-stock" class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl shadow-lg p-6 border-l-4 border-yellow-500 hover:shadow-xl transition-all hover:scale-105 cursor-pointer">
            <div class="flex items-center justify-between mb-3">
              <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <div class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full">Alert</div>
            </div>
            <p class="text-sm text-gray-600 mb-1">Low Stock Items</p>
            <p class="text-3xl font-bold text-gray-900">{{ stats.inventory.low_stock }}</p>
            <p class="text-xs text-gray-500 mt-2">Needs restocking</p>
          </NuxtLink>

          <NuxtLink to="/products?filter=out-of-stock" class="bg-gradient-to-br from-red-50 to-pink-50 rounded-xl shadow-lg p-6 border-l-4 border-red-500 hover:shadow-xl transition-all hover:scale-105 cursor-pointer">
            <div class="flex items-center justify-between mb-3">
              <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
              </svg>
              <div class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">Urgent</div>
            </div>
            <p class="text-sm text-gray-600 mb-1">Out of Stock</p>
            <p class="text-3xl font-bold text-gray-900">{{ stats.inventory.out_of_stock }}</p>
            <p class="text-xs text-gray-500 mt-2">Immediate action required</p>
          </NuxtLink>

          <NuxtLink to="/returns?filter=pending" class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-all hover:scale-105 cursor-pointer">
            <div class="flex items-center justify-between mb-3">
              <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <div class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">Pending</div>
            </div>
            <p class="text-sm text-gray-600 mb-1">Pending Returns</p>
            <p class="text-3xl font-bold text-gray-900">{{ stats.pending_actions.returns_to_process }}</p>
            <p class="text-xs text-gray-500 mt-2">Awaiting approval</p>
          </NuxtLink>
        </div>
      </div>

      <!-- Customer Stats -->
      <div>
        <h2 class="text-2xl font-bold text-gray-900 mb-5 flex items-center">
          <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          Customer Insights
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-3">
              <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <p class="text-sm text-gray-600 mb-1">Total Customers</p>
            <p class="text-3xl font-bold text-gray-900">{{ stats.customers.total }}</p>
            <p class="text-xs text-gray-500 mt-2">Registered users</p>
          </div>

          <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-3">
              <svg class="w-12 h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
              </svg>
              <div class="bg-purple-100 text-purple-700 text-xs font-bold px-3 py-1 rounded-full">VIP</div>
            </div>
            <p class="text-sm text-gray-600 mb-1">VIP Customers</p>
            <p class="text-3xl font-bold text-gray-900">{{ stats.customers.vip }}</p>
            <p class="text-xs text-gray-500 mt-2">Premium members</p>
          </div>

          <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-3">
              <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <p class="text-sm text-gray-600 mb-1">Regular Customers</p>
            <p class="text-3xl font-bold text-gray-900">{{ stats.customers.regular }}</p>
            <p class="text-xs text-gray-500 mt-2">Standard members</p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-2xl shadow-xl p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
          <svg class="w-8 h-8 mr-3 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
          Quick Actions
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <NuxtLink to="/pos" class="group flex flex-col items-center justify-center p-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl hover:from-blue-600 hover:to-blue-700 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
            <svg class="w-16 h-16 mb-3 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="text-base font-bold text-white">New Sale</span>
            <span class="text-xs text-blue-100 mt-1">Start POS</span>
          </NuxtLink>

          <NuxtLink to="/products" class="group flex flex-col items-center justify-center p-8 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl hover:from-green-600 hover:to-green-700 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
            <svg class="w-16 h-16 mb-3 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span class="text-base font-bold text-white">Products</span>
            <span class="text-xs text-green-100 mt-1">Manage inventory</span>
          </NuxtLink>

          <NuxtLink to="/customers" class="group flex flex-col items-center justify-center p-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl hover:from-indigo-600 hover:to-indigo-700 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
            <svg class="w-16 h-16 mb-3 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="text-base font-bold text-white">Customers</span>
            <span class="text-xs text-indigo-100 mt-1">View customers</span>
          </NuxtLink>

          <NuxtLink to="/reports" class="group flex flex-col items-center justify-center p-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl hover:from-purple-600 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
            <svg class="w-16 h-16 mb-3 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span class="text-base font-bold text-white">Reports</span>
            <span class="text-xs text-purple-100 mt-1">Analytics</span>
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { api } from '~/services/api'
import type { DashboardStats } from '~/types'

definePageMeta({
  middleware: 'auth'
})

const authStore = useAuthStore()

// State
const loading = ref(true)
const stats = ref<DashboardStats | null>(null)

// Current date
const currentDate = computed(() => {
  return new Date().toLocaleDateString('en-US', { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
})

// Format currency
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(value)
}

// Fetch dashboard data
const fetchDashboard = async () => {
  try {
    loading.value = true
    const response = await api.reports.getDashboard()
    if (response.success) {
      stats.value = response.data
    }
  } catch (error) {
    console.error('Error fetching dashboard:', error)
  } finally {
    loading.value = false
  }
}

// Load data on mount
onMounted(() => {
  fetchDashboard()
})
</script>
