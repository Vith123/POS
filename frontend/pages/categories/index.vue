<!--
  Categories Page
  Full CRUD operations for managing product categories
-->

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
        <p class="text-gray-600 mt-1">Manage your product categories</p>
      </div>
      <button @click="openCreateModal" class="btn btn-primary flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Category
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <p class="text-sm text-gray-600 mb-1">Total Categories</p>
        <p class="text-3xl font-bold text-gray-900">{{ categories.length }}</p>
      </div>
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <p class="text-sm text-gray-600 mb-1">Active</p>
        <p class="text-3xl font-bold text-gray-900">{{ activeCount }}</p>
      </div>
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <p class="text-sm text-gray-600 mb-1">Inactive</p>
        <p class="text-3xl font-bold text-gray-900">{{ inactiveCount }}</p>
      </div>
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
        <p class="text-sm text-gray-600 mb-1">Most Products</p>
        <p class="text-lg font-bold text-gray-900">{{ mostProductsCategory?.name || 'N/A' }}</p>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="card">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search categories by name or description..."
            class="input"
            @input="filterCategories"
          />
        </div>
        <div>
          <select v-model="statusFilter" class="input" @change="filterCategories">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Categories Table -->
    <div class="card">
      <div v-if="loading" class="flex items-center justify-center py-12">
        <LoadingSpinner size="lg" text="Loading categories..." />
      </div>

      <div v-else-if="filteredCategories.length === 0" class="text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
        </svg>
        <p class="text-gray-500 text-lg">No categories found</p>
        <button @click="openCreateModal" class="btn btn-primary mt-4">Create First Category</button>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Category
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Description
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Products
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Created
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="category in filteredCategories" :key="category.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-900 max-w-xs truncate">
                  {{ category.description || 'No description' }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                  {{ category.products_count || 0 }} products
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="category.is_active 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-gray-100 text-gray-800'"
                >
                  {{ category.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(category.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="viewCategoryProducts(category)"
                    class="text-blue-600 hover:text-blue-900"
                    title="View Products"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                  <button
                    @click="openEditModal(category)"
                    class="text-indigo-600 hover:text-indigo-900"
                    title="Edit"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="deleteCategory(category)"
                    class="text-red-600 hover:text-red-900"
                    title="Delete"
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
    <Modal :show="showModal" @close="closeModal" :title="isEditing ? 'Edit Category' : 'Create Category'">
      <form @submit.prevent="saveCategory" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Category Name *</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="input"
            placeholder="Enter category name"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <textarea
            v-model="form.description"
            rows="3"
            class="input"
            placeholder="Enter category description"
          ></textarea>
        </div>

        <div class="flex items-center">
          <input
            v-model="form.is_active"
            type="checkbox"
            id="is_active"
            class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
          />
          <label for="is_active" class="ml-2 block text-sm text-gray-900">
            Active Category
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
  </div>
</template>

<script setup lang="ts">
import { api } from '~/services/api'
import type { Category } from '~/types'

definePageMeta({
  middleware: 'auth'
})

const notificationStore = useNotificationStore()

// State
const loading = ref(true)
const saving = ref(false)
const categories = ref<Category[]>([])
const filteredCategories = ref<Category[]>([])
const searchQuery = ref('')
const statusFilter = ref('')
const showModal = ref(false)
const isEditing = ref(false)
const currentCategory = ref<Category | null>(null)

const form = ref({
  name: '',
  description: '',
  is_active: true
})

// Computed
const activeCount = computed(() => 
  categories.value.filter(c => c.is_active).length
)

const inactiveCount = computed(() => 
  categories.value.filter(c => !c.is_active).length
)

const mostProductsCategory = computed(() => {
  if (categories.value.length === 0) return null
  return categories.value.reduce((max, cat) => 
    (cat.products_count || 0) > (max.products_count || 0) ? cat : max
  )
})

// Methods
const fetchCategories = async () => {
  try {
    loading.value = true
    const response = await api.categories.getAll()
    categories.value = response.data || []
    filteredCategories.value = categories.value
  } catch (error) {
    console.error('Error fetching categories:', error)
    notificationStore.error('Error', 'Failed to fetch categories')
  } finally {
    loading.value = false
  }
}

const filterCategories = () => {
  let results = categories.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    results = results.filter(c =>
      c.name.toLowerCase().includes(query) ||
      c.description?.toLowerCase().includes(query)
    )
  }

  if (statusFilter.value) {
    const isActive = statusFilter.value === 'active'
    results = results.filter(c => c.is_active === isActive)
  }

  filteredCategories.value = results
}

const openCreateModal = () => {
  isEditing.value = false
  currentCategory.value = null
  form.value = {
    name: '',
    description: '',
    is_active: true
  }
  showModal.value = true
}

const openEditModal = (category: Category) => {
  isEditing.value = true
  currentCategory.value = category
  form.value = {
    name: category.name,
    description: category.description || '',
    is_active: category.is_active
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.value = {
    name: '',
    description: '',
    is_active: true
  }
}

const saveCategory = async () => {
  try {
    saving.value = true
    
    if (isEditing.value && currentCategory.value) {
      await api.categories.update(currentCategory.value.id, form.value)
      notificationStore.success('Success', 'Category updated successfully')
    } else {
      await api.categories.create(form.value)
      notificationStore.success('Success', 'Category created successfully')
    }
    
    closeModal()
    await fetchCategories()
  } catch (error: any) {
    console.error('Error saving category:', error)
    const message = error?.data?.message || 'Failed to save category'
    notificationStore.error('Error', message)
  } finally {
    saving.value = false
  }
}

const deleteCategory = async (category: Category) => {
  if (!confirm(`Are you sure you want to delete "${category.name}"? This action cannot be undone.`)) {
    return
  }

  try {
    await api.categories.delete(category.id)
    notificationStore.success('Success', 'Category deleted successfully')
    await fetchCategories()
  } catch (error: any) {
    console.error('Error deleting category:', error)
    const message = error?.data?.message || 'Failed to delete category'
    notificationStore.error('Error', message)
  }
}

const viewCategoryProducts = (category: Category) => {
  navigateTo(`/products?category=${category.id}`)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Lifecycle
onMounted(() => {
  fetchCategories()
})
</script>
