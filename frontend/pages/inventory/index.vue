<!--
  Inventory Management Page
  
  Features:
  - View all inventory with stock levels
  - Low stock alerts
  - Stock adjustments
  - Stock movement history
  - Export to CSV
-->

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $t('nav.inventory') }}</h1>
        <p class="text-gray-600 dark:text-gray-400">{{ $t('inventory.subtitle') }}</p>
      </div>
      <div class="flex gap-3">
        <button @click="exportToCSV" class="btn btn-secondary">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          {{ $t('common.export') }}
        </button>
        <button @click="openAdjustmentModal" class="btn btn-primary">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          {{ $t('inventory.adjustStock') }}
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <StatsCard
        :title="$t('inventory.totalProducts')"
        :value="stats.totalProducts"
        icon="📦"
        color="blue"
      />
      <StatsCard
        :title="$t('inventory.lowStock')"
        :value="stats.lowStock"
        icon="⚠️"
        color="yellow"
      />
      <StatsCard
        :title="$t('inventory.outOfStock')"
        :value="stats.outOfStock"
        icon="🚫"
        color="red"
      />
      <StatsCard
        :title="$t('inventory.totalValue')"
        :value="'$' + stats.totalValue.toFixed(2)"
        icon="💰"
        color="green"
      />
    </div>

    <!-- Filters -->
    <div class="card">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <input
            v-model="filters.search"
            type="text"
            :placeholder="$t('inventory.searchPlaceholder')"
            class="input"
            @input="fetchInventory"
          />
        </div>
        <div>
          <select v-model="filters.category" class="input" @change="fetchInventory">
            <option value="">{{ $t('inventory.allCategories') }}</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>
        <div>
          <select v-model="filters.stockStatus" class="input" @change="fetchInventory">
            <option value="">{{ $t('inventory.allStatus') }}</option>
            <option value="in_stock">{{ $t('inventory.inStock') }}</option>
            <option value="low_stock">{{ $t('inventory.lowStock') }}</option>
            <option value="out_of_stock">{{ $t('inventory.outOfStock') }}</option>
          </select>
        </div>
        <div>
          <select v-model="filters.sortBy" class="input" @change="fetchInventory">
            <option value="name">{{ $t('inventory.sortByName') }}</option>
            <option value="quantity_asc">{{ $t('inventory.sortByQtyAsc') }}</option>
            <option value="quantity_desc">{{ $t('inventory.sortByQtyDesc') }}</option>
            <option value="value_desc">{{ $t('inventory.sortByValue') }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Inventory Table -->
    <div class="card">
      <DataTable
        :columns="columns"
        :data="inventory"
        :loading="loading"
        @view="viewMovements"
        @edit="openAdjustmentModal"
      >
        <template #product="{ row }">
          <div class="flex items-center gap-3">
            <img
              v-if="row.product.image_url"
              :src="row.product.image_url"
              :alt="row.product.name"
              class="w-12 h-12 rounded-lg object-cover"
            />
            <div v-else class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-lg flex items-center justify-center">
              <span class="text-2xl">📦</span>
            </div>
            <div>
              <p class="font-semibold text-gray-900 dark:text-white">{{ row.product.name }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ row.product.sku }}</p>
            </div>
          </div>
        </template>

        <template #category="{ row }">
          <span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded text-sm">
            {{ row.product.category?.name || 'N/A' }}
          </span>
        </template>

        <template #quantity="{ row }">
          <div class="flex items-center gap-2">
            <span
              class="font-bold text-lg"
              :class="{
                'text-red-600 dark:text-red-400': row.quantity === 0,
                'text-yellow-600 dark:text-yellow-400': row.quantity > 0 && row.quantity <= row.product.min_stock_level,
                'text-green-600 dark:text-green-400': row.quantity > row.product.min_stock_level
              }"
            >
              {{ row.quantity }}
            </span>
            <span class="text-sm text-gray-500 dark:text-gray-400">/ {{ row.product.min_stock_level }} min</span>
          </div>
        </template>

        <template #status="{ row }">
          <span
            class="px-3 py-1 rounded-full text-xs font-semibold"
            :class="{
              'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': row.quantity === 0,
              'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': row.quantity > 0 && row.quantity <= row.product.min_stock_level,
              'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': row.quantity > row.product.min_stock_level
            }"
          >
            {{ getStockStatus(row) }}
          </span>
        </template>

        <template #value="{ row }">
          <span class="font-semibold text-gray-900 dark:text-white">
            ${{ (row.quantity * row.product.price).toFixed(2) }}
          </span>
        </template>

        <template #last_updated="{ row }">
          <span class="text-sm text-gray-600 dark:text-gray-400">
            {{ formatDate(row.updated_at) }}
          </span>
        </template>

        <template #actions="{ row }">
          <div class="flex items-center gap-2">
            <button
              @click="viewMovements(row)"
              class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
              :title="$t('inventory.viewHistory')"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </button>
            <button
              @click="openAdjustmentModal(row)"
              class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors"
              :title="$t('inventory.adjustStock')"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </button>
          </div>
        </template>
      </DataTable>
    </div>

    <!-- Stock Adjustment Modal -->
    <Modal
      :show="showAdjustmentModal"
      @close="showAdjustmentModal = false"
      :title="adjustmentForm.id ? $t('inventory.adjustStock') : $t('inventory.adjustStock')"
      size="md"
    >
      <form @submit.prevent="submitAdjustment" class="space-y-4">
        <!-- Product Selection -->
        <div v-if="!adjustmentForm.id">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $t('inventory.selectProduct') }}
          </label>
          <select v-model="adjustmentForm.product_id" class="input" required>
            <option value="">{{ $t('inventory.chooseProduct') }}</option>
            <option v-for="item in inventory" :key="item.id" :value="item.product_id">
              {{ item.product.name }} ({{ $t('inventory.current') }}: {{ item.quantity }})
            </option>
          </select>
        </div>

        <!-- Current Product Info -->
        <div v-else class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
          <div class="flex items-center gap-3 mb-2">
            <img
              v-if="adjustmentForm.product.image_url"
              :src="adjustmentForm.product.image_url"
              class="w-16 h-16 rounded-lg object-cover"
            />
            <div>
              <p class="font-semibold text-gray-900 dark:text-white">{{ adjustmentForm.product.name }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ adjustmentForm.product.sku }}</p>
            </div>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $t('inventory.currentStock') }}:</span>
            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ adjustmentForm.currentQuantity }}</span>
          </div>
        </div>

        <!-- Adjustment Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $t('inventory.adjustmentType') }}
          </label>
          <div class="grid grid-cols-2 gap-3">
            <button
              type="button"
              @click="adjustmentForm.type = 'in'"
              class="p-3 border-2 rounded-lg transition-colors"
              :class="adjustmentForm.type === 'in' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600'"
            >
              <div class="text-2xl mb-1">📥</div>
              <div class="font-semibold text-sm">{{ $t('inventory.stockIn') }}</div>
            </button>
            <button
              type="button"
              @click="adjustmentForm.type = 'out'"
              class="p-3 border-2 rounded-lg transition-colors"
              :class="adjustmentForm.type === 'out' ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-300 dark:border-gray-600'"
            >
              <div class="text-2xl mb-1">📤</div>
              <div class="font-semibold text-sm">{{ $t('inventory.stockOut') }}</div>
            </button>
          </div>
        </div>

        <!-- Quantity -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $t('inventory.quantity') }}
          </label>
          <input
            v-model.number="adjustmentForm.quantity"
            type="number"
            min="1"
            class="input"
            required
          />
        </div>

        <!-- Reference -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $t('inventory.reference') }}
          </label>
          <input
            v-model="adjustmentForm.reference"
            type="text"
            class="input"
            :placeholder="$t('inventory.referencePlaceholder')"
          />
        </div>

        <!-- Notes -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $t('inventory.notes') }}
          </label>
          <textarea
            v-model="adjustmentForm.notes"
            rows="3"
            class="input"
            :placeholder="$t('inventory.notesPlaceholder')"
          ></textarea>
        </div>

        <!-- New Stock Preview -->
        <div v-if="adjustmentForm.id" class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $t('inventory.newStock') }}:</span>
            <span class="text-xl font-bold text-blue-600 dark:text-blue-400">
              {{ calculateNewStock() }}
            </span>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 pt-4">
          <button type="button" @click="showAdjustmentModal = false" class="btn btn-secondary">
            {{ $t('common.cancel') }}
          </button>
          <button type="submit" :disabled="submitting" class="btn btn-primary">
            <LoadingSpinner v-if="submitting" size="sm" class="mr-2" />
            {{ $t('common.save') }}
          </button>
        </div>
      </form>
    </Modal>

    <!-- Stock Movements Modal -->
    <Modal
      :show="showMovementsModal"
      @close="showMovementsModal = false"
      :title="$t('inventory.stockHistory')"
      size="lg"
    >
      <div v-if="selectedInventory" class="space-y-4">
        <!-- Product Info -->
        <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
          <img
            v-if="selectedInventory.product.image_url"
            :src="selectedInventory.product.image_url"
            class="w-16 h-16 rounded-lg object-cover"
          />
          <div>
            <p class="font-semibold text-gray-900 dark:text-white">{{ selectedInventory.product.name }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ selectedInventory.product.sku }}</p>
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
              {{ $t('inventory.currentStock') }}: <span class="text-lg text-green-600 dark:text-green-400">{{ selectedInventory.quantity }}</span>
            </p>
          </div>
        </div>

        <!-- Movements List -->
        <div v-if="loadingMovements" class="flex justify-center py-8">
          <LoadingSpinner size="lg" />
        </div>

        <div v-else-if="movements.length === 0" class="text-center py-8 text-gray-500">
          {{ $t('inventory.noMovements') }}
        </div>

        <div v-else class="space-y-2">
          <div
            v-for="movement in movements"
            :key="movement.id"
            class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
          >
            <div class="flex items-center gap-3">
              <div
                class="w-10 h-10 rounded-full flex items-center justify-center"
                :class="movement.type === 'in' ? 'bg-green-100 dark:bg-green-900/20' : 'bg-red-100 dark:bg-red-900/20'"
              >
                <span class="text-xl">{{ movement.type === 'in' ? '📥' : '📤' }}</span>
              </div>
              <div>
                <p class="font-semibold text-gray-900 dark:text-white">
                  {{ movement.type === 'in' ? '+' : '-' }}{{ movement.quantity }} {{ $t('inventory.units') }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ movement.reference || 'N/A' }}</p>
                <p v-if="movement.notes" class="text-xs text-gray-400 dark:text-gray-500">{{ movement.notes }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(movement.created_at) }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-500">{{ movement.user?.name || 'System' }}</p>
            </div>
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { api } from '~/services/api'
import type { Product, Category } from '~/types'

definePageMeta({
  middleware: 'auth'
})

const { t } = useI18n()
const notificationStore = useNotificationStore()

// State
const loading = ref(true)
const submitting = ref(false)
const loadingMovements = ref(false)
const inventory = ref<any[]>([])
const categories = ref<Category[]>([])
const movements = ref<any[]>([])
const selectedInventory = ref<any>(null)
const showAdjustmentModal = ref(false)
const showMovementsModal = ref(false)

// Stats
const stats = computed(() => ({
  totalProducts: inventory.value.length,
  lowStock: inventory.value.filter(i => i.quantity > 0 && i.quantity <= i.product.min_stock_level).length,
  outOfStock: inventory.value.filter(i => i.quantity === 0).length,
  totalValue: inventory.value.reduce((sum, i) => sum + (i.quantity * i.product.price), 0)
}))

// Filters
const filters = ref({
  search: '',
  category: '',
  stockStatus: '',
  sortBy: 'name'
})

// Adjustment Form
const adjustmentForm = ref({
  id: null as number | null,
  product_id: '',
  product: null as any,
  currentQuantity: 0,
  type: 'in' as 'in' | 'out',
  quantity: 1,
  reference: '',
  notes: ''
})

// Table Columns
const columns = [
  { key: 'product', label: t('inventory.product'), slot: true },
  { key: 'category', label: t('inventory.category'), slot: true },
  { key: 'quantity', label: t('inventory.quantity'), slot: true },
  { key: 'status', label: t('inventory.status'), slot: true },
  { key: 'value', label: t('inventory.value'), slot: true },
  { key: 'last_updated', label: t('inventory.lastUpdated'), slot: true },
  { key: 'actions', label: t('common.actions'), slot: true }
]

// Fetch Inventory
const fetchInventory = async () => {
  try {
    loading.value = true
    const params: any = {}
    
    if (filters.value.search) params.search = filters.value.search
    if (filters.value.category) params.category_id = filters.value.category
    if (filters.value.stockStatus) params.stock_status = filters.value.stockStatus
    
    const response = await api.inventory.getAll(params)
    inventory.value = response.data || []
    
    // Sort
    if (filters.value.sortBy === 'name') {
      inventory.value.sort((a, b) => a.product.name.localeCompare(b.product.name))
    } else if (filters.value.sortBy === 'quantity_asc') {
      inventory.value.sort((a, b) => a.quantity - b.quantity)
    } else if (filters.value.sortBy === 'quantity_desc') {
      inventory.value.sort((a, b) => b.quantity - a.quantity)
    } else if (filters.value.sortBy === 'value_desc') {
      inventory.value.sort((a, b) => (b.quantity * b.product.price) - (a.quantity * a.product.price))
    }
  } catch (error) {
    console.error('Error fetching inventory:', error)
    notificationStore.error(t('common.error'), t('inventory.fetchError'))
  } finally {
    loading.value = false
  }
}

// Fetch Categories
const fetchCategories = async () => {
  try {
    const response = await api.categories.getAll()
    categories.value = response.data || []
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

// Get Stock Status
const getStockStatus = (item: any) => {
  if (item.quantity === 0) return t('inventory.outOfStock')
  if (item.quantity <= item.product.min_stock_level) return t('inventory.lowStock')
  return t('inventory.inStock')
}

// Open Adjustment Modal
const openAdjustmentModal = (item?: any) => {
  if (item) {
    adjustmentForm.value = {
      id: item.id,
      product_id: item.product_id,
      product: item.product,
      currentQuantity: item.quantity,
      type: 'in',
      quantity: 1,
      reference: '',
      notes: ''
    }
  } else {
    adjustmentForm.value = {
      id: null,
      product_id: '',
      product: null,
      currentQuantity: 0,
      type: 'in',
      quantity: 1,
      reference: '',
      notes: ''
    }
  }
  showAdjustmentModal.value = true
}

// Calculate New Stock
const calculateNewStock = () => {
  const current = adjustmentForm.value.currentQuantity
  const qty = adjustmentForm.value.quantity || 0
  return adjustmentForm.value.type === 'in' ? current + qty : Math.max(0, current - qty)
}

// Submit Adjustment
const submitAdjustment = async () => {
  try {
    submitting.value = true
    
    const productId = adjustmentForm.value.id 
      ? adjustmentForm.value.product_id 
      : adjustmentForm.value.product_id

    const data = {
      type: adjustmentForm.value.type,
      quantity: adjustmentForm.value.quantity,
      reference: adjustmentForm.value.reference,
      notes: adjustmentForm.value.notes
    }

    await api.inventory.adjust(Number(productId), data)
    
    notificationStore.success(t('common.success'), t('inventory.adjustSuccess'))
    showAdjustmentModal.value = false
    await fetchInventory()
  } catch (error: any) {
    console.error('Error adjusting stock:', error)
    notificationStore.error(t('common.error'), error?.data?.message || t('inventory.adjustError'))
  } finally {
    submitting.value = false
  }
}

// View Movements
const viewMovements = async (item: any) => {
  selectedInventory.value = item
  showMovementsModal.value = true
  
  try {
    loadingMovements.value = true
    const response = await api.inventory.getMovements(item.product_id)
    movements.value = response.data || []
  } catch (error) {
    console.error('Error fetching movements:', error)
    notificationStore.error(t('common.error'), t('inventory.movementsError'))
  } finally {
    loadingMovements.value = false
  }
}

// Format Date
const formatDate = (date: string) => {
  return new Date(date).toLocaleString()
}

// Export to CSV
const exportToCSV = () => {
  const headers = ['Product', 'SKU', 'Category', 'Quantity', 'Min Stock', 'Status', 'Value']
  const rows = inventory.value.map(item => [
    item.product.name,
    item.product.sku,
    item.product.category?.name || 'N/A',
    item.quantity,
    item.product.min_stock_level,
    getStockStatus(item),
    (item.quantity * item.product.price).toFixed(2)
  ])

  const csv = [
    headers.join(','),
    ...rows.map(row => row.map(cell => `"${cell}"`).join(','))
  ].join('\n')

  const blob = new Blob([csv], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `inventory_${new Date().toISOString().split('T')[0]}.csv`
  a.click()
  window.URL.revokeObjectURL(url)
  
  notificationStore.success(t('common.success'), t('inventory.exportSuccess'))
}

// Load Data
onMounted(() => {
  fetchInventory()
  fetchCategories()
})
</script>
