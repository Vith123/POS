<!--
  Returns Management Page
  Comprehensive returns handling with statistics and charts
-->

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold dark:text-white">{{ $t('returns.title') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $t('common.details') }}</p>
      </div>
      <div class="flex gap-3">
        <button @click="exportReturns" class="btn btn-secondary flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          {{ $t('common.export') }}
        </button>
        <button @click="openCreateModal" class="btn btn-primary flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          {{ $t('returns.addReturn') }}
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ $t('returns.stats.totalReturns') }}</p>
            <p class="text-3xl font-bold dark:text-white">{{ returns.length }}</p>
          </div>
          <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
          </svg>
        </div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ $t('returns.stats.pendingReturns') }}</p>
            <p class="text-3xl font-bold dark:text-white">{{ pendingCount }}</p>
          </div>
          <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ $t('returns.stats.approvedReturns') }}</p>
            <p class="text-3xl font-bold dark:text-white">{{ approvedCount }}</p>
          </div>
          <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ $t('returns.stats.totalRefunded') }}</p>
            <p class="text-3xl font-bold dark:text-white">${{ totalRefunded }}</p>
          </div>
          <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Return Reasons Pie Chart -->
      <div class="card dark:bg-gray-800">
        <h3 class="text-lg font-semibold mb-4 dark:text-white">{{ $t('returns.reason') }} {{ $t('common.status') }}</h3>
        <PieChart :chart-data="reasonsChartData" :height="300" />
      </div>

      <!-- Returns Trend Bar Chart -->
      <div class="card dark:bg-gray-800">
        <h3 class="text-lg font-semibold mb-4 dark:text-white">{{ $t('returns.title') }} {{ $t('reports.salesTrend') }}</h3>
        <BarChart :chart-data="trendChartData" :height="300" />
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="card dark:bg-gray-800">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="$t('common.search') + '...'"
            class="input dark:bg-gray-700 dark:text-white dark:border-gray-600"
            @input="filterReturns"
          />
        </div>
        <div>
          <select v-model="statusFilter" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600" @change="filterReturns">
            <option value="">{{ $t('common.all') }} {{ $t('common.status') }}</option>
            <option value="pending">{{ $t('sales.pending') }}</option>
            <option value="approved">{{ $t('returns.approved') }}</option>
            <option value="rejected">{{ $t('returns.rejected') }}</option>
          </select>
        </div>
        <div>
          <select v-model="sortBy" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600" @change="sortReturns">
            <option value="recent">{{ $t('common.date') }} ({{ $t('common.today') }})</option>
            <option value="oldest">{{ $t('common.date') }} (Old)</option>
            <option value="amount">{{ $t('common.amount') }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Returns Table -->
    <div class="card dark:bg-gray-800">
      <div v-if="loading" class="flex items-center justify-center py-12">
        <LoadingSpinner size="lg" :text="$t('common.loading')" />
      </div>

      <div v-else-if="filteredReturns.length === 0" class="text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
        </svg>
        <p class="text-gray-500 dark:text-gray-400 text-lg">{{ $t('messages.noData') }}</p>
        <button @click="openCreateModal" class="btn btn-primary mt-4">{{ $t('returns.addReturn') }}</button>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ $t('returns.returnNumber') }}
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ $t('returns.originalSale') }}
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ $t('returns.reason') }}
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ $t('returns.refundAmount') }}
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ $t('common.status') }}
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ $t('common.date') }}
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ $t('common.actions') }}
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="returnItem in filteredReturns" :key="returnItem.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium dark:text-white">
                {{ returnItem.return_number }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm dark:text-gray-300">
                {{ returnItem.sale?.sale_number || 'N/A' }}
              </td>
              <td class="px-6 py-4 text-sm dark:text-gray-300">
                {{ returnItem.reason }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold dark:text-white">
                ${{ returnItem.refund_amount.toFixed(2) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="{
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': returnItem.status === 'pending',
                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': returnItem.status === 'approved',
                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': returnItem.status === 'rejected'
                  }"
                >
                  {{ returnItem.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm dark:text-gray-300">
                {{ formatDate(returnItem.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end gap-2">
                  <button
                    v-if="returnItem.status === 'pending'"
                    @click="processReturn(returnItem, 'approved')"
                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                    :title="$t('returns.approved')"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </button>
                  <button
                    v-if="returnItem.status === 'pending'"
                    @click="processReturn(returnItem, 'rejected')"
                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                    :title="$t('returns.rejected')"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </button>
                  <button
                    @click="openEditModal(returnItem)"
                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                    :title="$t('common.edit')"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="deleteReturn(returnItem)"
                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                    :title="$t('common.delete')"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <Modal :show="showModal" @close="closeModal" :title="isEditing ? $t('returns.editReturn') : $t('returns.addReturn')">
      <form @submit.prevent="saveReturn" class="space-y-4">
        <div>
          <label class="block text-sm font-medium dark:text-gray-300 mb-1">{{ $t('returns.originalSale') }} *</label>
          <input
            v-model="form.sale_number"
            type="text"
            required
            class="input dark:bg-gray-700 dark:text-white dark:border-gray-600"
            :placeholder="$t('returns.originalSale')"
          />
        </div>

        <div>
          <label class="block text-sm font-medium dark:text-gray-300 mb-1">{{ $t('returns.reason') }} *</label>
          <select v-model="form.reason" required class="input dark:bg-gray-700 dark:text-white dark:border-gray-600">
            <option value="">{{ $t('common.select') }}...</option>
            <option value="damaged">{{ $t('returns.returnReasons.damaged') }}</option>
            <option value="defective">{{ $t('returns.returnReasons.defective') }}</option>
            <option value="wrong">{{ $t('returns.returnReasons.wrong') }}</option>
            <option value="not_needed">{{ $t('returns.returnReasons.notNeeded') }}</option>
            <option value="other">{{ $t('returns.returnReasons.other') }}</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium dark:text-gray-300 mb-1">{{ $t('returns.refundAmount') }} *</label>
          <input
            v-model.number="form.refund_amount"
            type="number"
            step="0.01"
            required
            min="0"
            class="input dark:bg-gray-700 dark:text-white dark:border-gray-600"
            placeholder="0.00"
          />
        </div>

        <div>
          <label class="block text-sm font-medium dark:text-gray-300 mb-1">{{ $t('common.description') }}</label>
          <textarea
            v-model="form.notes"
            rows="3"
            class="input dark:bg-gray-700 dark:text-white dark:border-gray-600"
            :placeholder="$t('common.description')"
          ></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4">
          <button type="button" @click="closeModal" class="btn btn-secondary">
            {{ $t('common.cancel') }}
          </button>
          <button type="submit" :disabled="saving" class="btn btn-primary">
            <span v-if="saving">{{ $t('common.saving') }}</span>
            <span v-else>{{ isEditing ? $t('common.save') : $t('common.create') }}</span>
          </button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { api } from '~/services/api'
import type { Return } from '~/types'

definePageMeta({
  middleware: 'auth'
})

const { t } = useI18n()
const notificationStore = useNotificationStore()

// State
const loading = ref(true)
const saving = ref(false)
const returns = ref<Return[]>([])
const filteredReturns = ref<Return[]>([])
const searchQuery = ref('')
const statusFilter = ref('')
const sortBy = ref('recent')
const showModal = ref(false)
const isEditing = ref(false)
const currentReturn = ref<Return | null>(null)

const form = ref({
  sale_number: '',
  reason: '',
  refund_amount: 0,
  notes: ''
})

// Computed
const pendingCount = computed(() => 
  returns.value.filter(r => r.status === 'pending').length
)

const approvedCount = computed(() => 
  returns.value.filter(r => r.status === 'approved').length
)

const totalRefunded = computed(() => 
  returns.value
    .filter(r => r.status === 'approved')
    .reduce((sum, r) => sum + r.refund_amount, 0)
    .toFixed(2)
)

// Chart Data
const reasonsChartData = computed(() => {
  const reasons = returns.value.reduce((acc, r) => {
    acc[r.reason] = (acc[r.reason] || 0) + 1
    return acc
  }, {} as Record<string, number>)

  return {
    labels: Object.keys(reasons),
    datasets: [{
      data: Object.values(reasons),
      backgroundColor: [
        'rgba(239, 68, 68, 0.8)',
        'rgba(59, 130, 246, 0.8)',
        'rgba(16, 185, 129, 0.8)',
        'rgba(245, 158, 11, 0.8)',
        'rgba(139, 92, 246, 0.8)',
      ]
    }]
  }
})

const trendChartData = computed(() => {
  const last7Days = Array.from({ length: 7 }, (_, i) => {
    const d = new Date()
    d.setDate(d.getDate() - (6 - i))
    return d.toISOString().split('T')[0]
  })

  const data = last7Days.map(date => 
    returns.value.filter(r => r.created_at.startsWith(date)).length
  )

  return {
    labels: last7Days.map(d => new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
    datasets: [{
      label: t('returns.title'),
      data,
      backgroundColor: 'rgba(239, 68, 68, 0.8)',
      borderColor: 'rgba(239, 68, 68, 1)',
      borderWidth: 1
    }]
  }
})

// Methods
const fetchReturns = async () => {
  try {
    loading.value = true
    const response = await api.returns.getAll()
    returns.value = response.data || []
    filterReturns()
  } catch (error) {
    console.error('Error fetching returns:', error)
    notificationStore.error(t('messages.error'), t('messages.failed'))
  } finally {
    loading.value = false
  }
}

const filterReturns = () => {
  let results = returns.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    results = results.filter(r =>
      r.return_number.toLowerCase().includes(query) ||
      r.reason.toLowerCase().includes(query)
    )
  }

  if (statusFilter.value) {
    results = results.filter(r => r.status === statusFilter.value)
  }

  filteredReturns.value = results
  sortReturns()
}

const sortReturns = () => {
  switch (sortBy.value) {
    case 'recent':
      filteredReturns.value.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
      break
    case 'oldest':
      filteredReturns.value.sort((a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime())
      break
    case 'amount':
      filteredReturns.value.sort((a, b) => b.refund_amount - a.refund_amount)
      break
  }
}

const openCreateModal = () => {
  isEditing.value = false
  currentReturn.value = null
  form.value = {
    sale_number: '',
    reason: '',
    refund_amount: 0,
    notes: ''
  }
  showModal.value = true
}

const openEditModal = (returnItem: Return) => {
  isEditing.value = true
  currentReturn.value = returnItem
  form.value = {
    sale_number: returnItem.sale?.sale_number || '',
    reason: returnItem.reason,
    refund_amount: returnItem.refund_amount,
    notes: returnItem.notes || ''
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveReturn = async () => {
  try {
    saving.value = true
    
    if (isEditing.value && currentReturn.value) {
      await api.returns.update(currentReturn.value.id, form.value)
      notificationStore.success(t('messages.success'), t('messages.updated'))
    } else {
      await api.returns.create(form.value)
      notificationStore.success(t('messages.success'), t('messages.created'))
    }
    
    closeModal()
    await fetchReturns()
  } catch (error: any) {
    console.error('Error saving return:', error)
    notificationStore.error(t('messages.error'), error?.data?.message || t('messages.failed'))
  } finally {
    saving.value = false
  }
}

const processReturn = async (returnItem: Return, status: string) => {
  if (!confirm(t('messages.confirm'))) return

  try {
    await api.returns.updateStatus(returnItem.id, status)
    notificationStore.success(t('messages.success'), t('messages.updated'))
    await fetchReturns()
  } catch (error: any) {
    console.error('Error processing return:', error)
    notificationStore.error(t('messages.error'), error?.data?.message || t('messages.failed'))
  }
}

const deleteReturn = async (returnItem: Return) => {
  if (!confirm(t('messages.confirmDelete'))) return

  try {
    await api.returns.delete(returnItem.id)
    notificationStore.success(t('messages.success'), t('messages.deleted'))
    await fetchReturns()
  } catch (error: any) {
    console.error('Error deleting return:', error)
    notificationStore.error(t('messages.error'), error?.data?.message || t('messages.failed'))
  }
}

const exportReturns = () => {
  const csv = [
    ['Return Number', 'Sale Number', 'Reason', 'Refund Amount', 'Status', 'Date'],
    ...returns.value.map(r => [
      r.return_number,
      r.sale?.sale_number || 'N/A',
      r.reason,
      r.refund_amount.toFixed(2),
      r.status,
      formatDate(r.created_at)
    ])
  ].map(row => row.join(',')).join('\n')

  const blob = new Blob([csv], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `returns-${new Date().toISOString().split('T')[0]}.csv`
  a.click()
  notificationStore.success(t('messages.success'), 'Exported successfully')
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Lifecycle
onMounted(() => {
  fetchReturns()
})
</script>
