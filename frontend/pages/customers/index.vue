<!--
  Customers Page
  Full CRUD operations for managing customers with loyalty points
-->

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Customers</h1>
        <p class="text-gray-600 mt-1">Manage your customer database</p>
      </div>
      <div class="flex gap-3">
        <button @click="exportCustomers" class="btn btn-secondary flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Export
        </button>
        <button @click="openCreateModal" class="btn btn-primary flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Add Customer
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 mb-1">Total Customers</p>
            <p class="text-3xl font-bold text-gray-900">{{ customers.length }}</p>
          </div>
          <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
      </div>
      <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg shadow p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 mb-1">VIP Customers</p>
            <p class="text-3xl font-bold text-gray-900">{{ vipCount }}</p>
          </div>
          <svg class="w-12 h-12 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
          </svg>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 mb-1">Total Purchases</p>
            <p class="text-3xl font-bold text-gray-900">${{ totalPurchases }}</p>
          </div>
          <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 mb-1">Loyalty Points</p>
            <p class="text-3xl font-bold text-gray-900">{{ totalLoyaltyPoints }}</p>
          </div>
          <svg class="w-12 h-12 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="card">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by name, email, or phone..."
            class="input"
            @input="filterCustomers"
          />
        </div>
        <div>
          <select v-model="customerTypeFilter" class="input" @change="filterCustomers">
            <option value="">All Types</option>
            <option value="vip">VIP Only</option>
            <option value="regular">Regular Only</option>
          </select>
        </div>
        <div>
          <select v-model="sortBy" class="input" @change="sortCustomers">
            <option value="name">Name A-Z</option>
            <option value="name_desc">Name Z-A</option>
            <option value="purchases">Most Purchases</option>
            <option value="loyalty">Most Points</option>
            <option value="recent">Most Recent</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Customers Grid -->
    <div v-if="loading" class="flex items-center justify-center py-12">
      <LoadingSpinner size="lg" text="Loading customers..." />
    </div>

    <div v-else-if="filteredCustomers.length === 0" class="card text-center py-12">
      <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
      </svg>
      <p class="text-gray-500 text-lg">No customers found</p>
      <button @click="openCreateModal" class="btn btn-primary mt-4">Add First Customer</button>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="customer in filteredCustomers"
        :key="customer.id"
        class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all p-6 border-l-4"
        :class="customer.is_vip ? 'border-purple-500' : 'border-blue-500'"
      >
        <!-- Customer Header -->
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center">
            <div
              class="w-14 h-14 rounded-full flex items-center justify-center text-white font-bold text-xl"
              :class="customer.is_vip ? 'bg-gradient-to-br from-purple-500 to-pink-500' : 'bg-gradient-to-br from-blue-500 to-blue-600'"
            >
              {{ getInitials(customer.name) }}
            </div>
            <div class="ml-3">
              <h3 class="font-bold text-gray-900">{{ customer.name }}</h3>
              <span
                v-if="customer.is_vip"
                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
              >
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                VIP
              </span>
            </div>
          </div>
        </div>

        <!-- Customer Details -->
        <div class="space-y-2 text-sm mb-4">
          <div class="flex items-center text-gray-600">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            {{ customer.email }}
          </div>
          <div class="flex items-center text-gray-600">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            {{ customer.phone }}
          </div>
          <div v-if="customer.address" class="flex items-center text-gray-600">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {{ customer.address }}
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 gap-3 mb-4 p-3 bg-gray-50 rounded-lg">
          <div>
            <p class="text-xs text-gray-600">Total Purchases</p>
            <p class="text-lg font-bold text-gray-900">${{ customer.total_purchases?.toFixed(2) || '0.00' }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-600">Loyalty Points</p>
            <p class="text-lg font-bold text-orange-600">{{ customer.loyalty_points || 0 }}</p>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-2">
          <button
            @click="openLoyaltyModal(customer)"
            class="flex-1 px-3 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors text-sm font-medium"
          >
            Manage Points
          </button>
          <button
            @click="openEditModal(customer)"
            class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors"
            title="Edit"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </button>
          <button
            @click="deleteCustomer(customer)"
            class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors"
            title="Delete"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <Modal :show="showModal" @close="closeModal" :title="isEditing ? 'Edit Customer' : 'Create Customer'">
      <form @submit.prevent="saveCustomer" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="input"
            placeholder="Enter customer name"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="input"
            placeholder="customer@example.com"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
          <input
            v-model="form.phone"
            type="tel"
            required
            class="input"
            placeholder="+1 234 567 8900"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
          <textarea
            v-model="form.address"
            rows="2"
            class="input"
            placeholder="Enter customer address"
          ></textarea>
        </div>

        <div class="flex items-center">
          <input
            v-model="form.is_vip"
            type="checkbox"
            id="is_vip"
            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
          />
          <label for="is_vip" class="ml-2 block text-sm text-gray-900">
            VIP Customer
          </label>
        </div>

        <div class="flex justify-end gap-3 pt-4">
          <button type="button" @click="closeModal" class="btn btn-secondary">
            Cancel
          </button>
          <button type="submit" :disabled="saving" class="btn btn-primary">
            <span v-if="saving">Saving...</span>
            <span v-else>{{ isEditing ? 'Update' : 'Create' }}</span>
          </button>
        </div>
      </form>
    </Modal>

    <!-- Loyalty Points Modal -->
    <Modal :show="showLoyaltyModal" @close="closeLoyaltyModal" title="Manage Loyalty Points">
      <div v-if="currentCustomer" class="space-y-4">
        <div class="bg-gradient-to-br from-orange-50 to-yellow-50 rounded-lg p-4">
          <p class="text-sm text-gray-600">Current Points</p>
          <p class="text-4xl font-bold text-orange-600">{{ currentCustomer.loyalty_points || 0 }}</p>
        </div>

        <form @submit.prevent="manageLoyaltyPoints" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
            <select v-model="loyaltyForm.action" class="input" required>
              <option value="add">Add Points</option>
              <option value="subtract">Subtract Points</option>
              <option value="set">Set Points</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Points</label>
            <input
              v-model.number="loyaltyForm.points"
              type="number"
              required
              min="0"
              class="input"
              placeholder="Enter points"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
            <input
              v-model="loyaltyForm.reason"
              type="text"
              class="input"
              placeholder="Optional reason"
            />
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button type="button" @click="closeLoyaltyModal" class="btn btn-secondary">
              Cancel
            </button>
            <button type="submit" :disabled="saving" class="btn btn-primary">
              <span v-if="saving">Updating...</span>
              <span v-else>Update Points</span>
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { api } from '~/services/api'
import type { Customer } from '~/types'

definePageMeta({
  middleware: 'auth'
})

const notificationStore = useNotificationStore()

// State
const loading = ref(true)
const saving = ref(false)
const customers = ref<Customer[]>([])
const filteredCustomers = ref<Customer[]>([])
const searchQuery = ref('')
const customerTypeFilter = ref('')
const sortBy = ref('name')
const showModal = ref(false)
const showLoyaltyModal = ref(false)
const isEditing = ref(false)
const currentCustomer = ref<Customer | null>(null)

const form = ref({
  name: '',
  email: '',
  phone: '',
  address: '',
  is_vip: false
})

const loyaltyForm = ref({
  action: 'add',
  points: 0,
  reason: ''
})

// Computed
const vipCount = computed(() => 
  customers.value.filter(c => c.is_vip).length
)

const totalPurchases = computed(() => 
  customers.value.reduce((sum, c) => sum + (c.total_purchases || 0), 0).toFixed(2)
)

const totalLoyaltyPoints = computed(() => 
  customers.value.reduce((sum, c) => sum + (c.loyalty_points || 0), 0)
)

// Methods
const fetchCustomers = async () => {
  try {
    loading.value = true
    const response = await api.customers.getAll()
    customers.value = response.data || []
    filterCustomers()
  } catch (error) {
    console.error('Error fetching customers:', error)
    notificationStore.error('Error', 'Failed to fetch customers')
  } finally {
    loading.value = false
  }
}

const filterCustomers = () => {
  let results = customers.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    results = results.filter(c =>
      c.name.toLowerCase().includes(query) ||
      c.email.toLowerCase().includes(query) ||
      c.phone.toLowerCase().includes(query)
    )
  }

  if (customerTypeFilter.value === 'vip') {
    results = results.filter(c => c.is_vip)
  } else if (customerTypeFilter.value === 'regular') {
    results = results.filter(c => !c.is_vip)
  }

  filteredCustomers.value = results
  sortCustomers()
}

const sortCustomers = () => {
  switch (sortBy.value) {
    case 'name':
      filteredCustomers.value.sort((a, b) => a.name.localeCompare(b.name))
      break
    case 'name_desc':
      filteredCustomers.value.sort((a, b) => b.name.localeCompare(a.name))
      break
    case 'purchases':
      filteredCustomers.value.sort((a, b) => (b.total_purchases || 0) - (a.total_purchases || 0))
      break
    case 'loyalty':
      filteredCustomers.value.sort((a, b) => (b.loyalty_points || 0) - (a.loyalty_points || 0))
      break
    case 'recent':
      filteredCustomers.value.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
      break
  }
}

const openCreateModal = () => {
  isEditing.value = false
  currentCustomer.value = null
  form.value = {
    name: '',
    email: '',
    phone: '',
    address: '',
    is_vip: false
  }
  showModal.value = true
}

const openEditModal = (customer: Customer) => {
  isEditing.value = true
  currentCustomer.value = customer
  form.value = {
    name: customer.name,
    email: customer.email,
    phone: customer.phone,
    address: customer.address || '',
    is_vip: customer.is_vip
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.value = {
    name: '',
    email: '',
    phone: '',
    address: '',
    is_vip: false
  }
}

const openLoyaltyModal = (customer: Customer) => {
  currentCustomer.value = customer
  loyaltyForm.value = {
    action: 'add',
    points: 0,
    reason: ''
  }
  showLoyaltyModal.value = true
}

const closeLoyaltyModal = () => {
  showLoyaltyModal.value = false
  currentCustomer.value = null
}

const saveCustomer = async () => {
  try {
    saving.value = true
    
    if (isEditing.value && currentCustomer.value) {
      await api.customers.update(currentCustomer.value.id, form.value)
      notificationStore.success('Success', 'Customer updated successfully')
    } else {
      await api.customers.create(form.value)
      notificationStore.success('Success', 'Customer created successfully')
    }
    
    closeModal()
    await fetchCustomers()
  } catch (error: any) {
    console.error('Error saving customer:', error)
    const message = error?.data?.message || 'Failed to save customer'
    notificationStore.error('Error', message)
  } finally {
    saving.value = false
  }
}

const manageLoyaltyPoints = async () => {
  if (!currentCustomer.value) return

  try {
    saving.value = true
    await api.customers.manageLoyaltyPoints(currentCustomer.value.id, loyaltyForm.value)
    notificationStore.success('Success', 'Loyalty points updated successfully')
    closeLoyaltyModal()
    await fetchCustomers()
  } catch (error: any) {
    console.error('Error updating loyalty points:', error)
    const message = error?.data?.message || 'Failed to update loyalty points'
    notificationStore.error('Error', message)
  } finally {
    saving.value = false
  }
}

const deleteCustomer = async (customer: Customer) => {
  if (!confirm(`Are you sure you want to delete "${customer.name}"? This action cannot be undone.`)) {
    return
  }

  try {
    await api.customers.delete(customer.id)
    notificationStore.success('Success', 'Customer deleted successfully')
    await fetchCustomers()
  } catch (error: any) {
    console.error('Error deleting customer:', error)
    const message = error?.data?.message || 'Failed to delete customer'
    notificationStore.error('Error', message)
  }
}

const exportCustomers = () => {
  const csv = [
    ['Name', 'Email', 'Phone', 'Address', 'VIP', 'Total Purchases', 'Loyalty Points'],
    ...customers.value.map(c => [
      c.name,
      c.email,
      c.phone,
      c.address || '',
      c.is_vip ? 'Yes' : 'No',
      c.total_purchases?.toFixed(2) || '0.00',
      c.loyalty_points || 0
    ])
  ].map(row => row.join(',')).join('\n')

  const blob = new Blob([csv], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `customers-${new Date().toISOString().split('T')[0]}.csv`
  a.click()
  notificationStore.success('Success', 'Customers exported successfully')
}

const getInitials = (name: string) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

// Lifecycle
onMounted(() => {
  fetchCustomers()
})
</script>
