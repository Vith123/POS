<!--
  Products Page
  
  Display, create, edit, and delete products
  Full CRUD example with image upload
-->

<template>
  <div>
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Products</h1>
        <p class="text-gray-600">Manage your product inventory</p>
      </div>
      <button @click="openCreateModal" class="btn btn-primary">
        + Add Product
      </button>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Search products..."
            class="input"
            @input="fetchProducts"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
          <select v-model="filters.category" class="input" @change="fetchProducts">
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select v-model="filters.status" class="input" @change="fetchProducts">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
          <select v-model="filters.stock" class="input" @change="fetchProducts">
            <option value="">All Stock</option>
            <option value="low">Low Stock</option>
            <option value="out">Out of Stock</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Products Table -->
    <DataTable
      :columns="columns"
      :data="products"
      :loading="loading"
    >
      <!-- Image Column -->
      <template #cell-image_url="{ row }">
        <img
          v-if="row.image_url"
          :src="row.image_url"
          :alt="row.name"
          class="w-12 h-12 object-cover rounded"
        />
        <div v-else class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
          <span class="text-gray-400 text-xs">No image</span>
        </div>
      </template>

      <!-- Stock Column with Badge -->
      <template #cell-stock_quantity="{ row }">
        <span
          class="px-2 py-1 rounded-full text-xs font-semibold"
          :class="getStockBadgeClass(row)"
        >
          {{ row.stock_quantity }}
        </span>
      </template>

      <template #cell-category="{ row }">
        {{ row.category?.name || '—' }}
      </template>

      <!-- Price Column -->
      <template #cell-price="{ row }">
        ${{ Number(row.price || 0).toFixed(2) }}
      </template>

      <!-- Status Column -->
      <template #cell-is_active="{ row }">
        <span
          class="px-2 py-1 rounded-full text-xs font-semibold"
          :class="row.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
        >
          {{ row.is_active ? 'Active' : 'Inactive' }}
        </span>
      </template>

      <!-- Actions Column -->
      <template #actions="{ row }">
        <div class="flex items-center gap-2">
          <button
            @click="openEditModal(row)"
            class="text-blue-600 hover:text-blue-800 font-medium"
          >
            Edit
          </button>
          <button
            @click="confirmDelete(row)"
            class="text-red-600 hover:text-red-800 font-medium"
          >
            Delete
          </button>
        </div>
      </template>
    </DataTable>

    <!-- Create/Edit Modal -->
    <Modal
      :show="showModal"
      :title="editingProduct ? 'Edit Product' : 'Add New Product'"
      size="lg"
      @close="closeModal"
    >
      <form @submit.prevent="saveProduct" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Product Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="input"
              placeholder="Enter product name"
            />
          </div>

          <!-- SKU -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">SKU *</label>
            <input
              v-model="form.sku"
              type="text"
              required
              class="input"
              placeholder="e.g., PRD-001"
            />
          </div>

          <!-- Category -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
            <select v-model="form.category_id" required class="input">
              <option value="">Select category</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.name }}
              </option>
            </select>
          </div>

          <!-- Barcode -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Barcode</label>
            <input
              v-model="form.barcode"
              type="text"
              class="input"
              placeholder="Optional"
            />
          </div>

          <!-- Price -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
            <input
              v-model.number="form.price"
              type="number"
              step="0.01"
              required
              class="input"
              placeholder="0.00"
            />
          </div>

          <!-- Cost -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cost *</label>
            <input
              v-model.number="form.cost"
              type="number"
              step="0.01"
              required
              class="input"
              placeholder="0.00"
            />
          </div>

          <!-- Stock Quantity -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity *</label>
            <input
              v-model.number="form.stock_quantity"
              type="number"
              required
              class="input"
              placeholder="0"
            />
          </div>

          <!-- Min Stock Level -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Min Stock Level *</label>
            <input
              v-model.number="form.min_stock_level"
              type="number"
              required
              class="input"
              placeholder="0"
            />
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
          <textarea
            v-model="form.description"
            rows="3"
            class="input"
            placeholder="Product description..."
          ></textarea>
        </div>

        <!-- Image Upload -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
          <input
            type="file"
            accept="image/*"
            @change="handleImageUpload"
            class="input"
          />
          <img v-if="imagePreview" :src="imagePreview" alt="Preview" class="mt-2 w-32 h-32 object-cover rounded" />
        </div>

        <!-- Active Status -->
        <div class="flex items-center gap-2">
          <input
            v-model="form.is_active"
            type="checkbox"
            id="is_active"
            class="w-4 h-4 text-primary-600 rounded"
          />
          <label for="is_active" class="text-sm font-medium text-gray-700">
            Active
          </label>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-3 pt-4">
          <button type="button" @click="closeModal" class="btn btn-secondary" :disabled="saving">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary" :disabled="saving">
            <LoadingSpinner v-if="saving" size="sm" class="mr-2" />
            {{ saving ? 'Saving...' : 'Save Product' }}
          </button>
        </div>
      </form>
    </Modal>

    <!-- Delete Confirmation -->
    <ConfirmDialog
      :show="showDeleteConfirm"
      title="Delete Product"
      :message="`Are you sure you want to delete '${deletingProduct?.name}'? This action cannot be undone.`"
      confirm-text="Delete"
      :loading="deleting"
      @confirm="deleteProduct"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>

<script setup lang="ts">
import { api } from '~/services/api'
import type { Product, Category, TableColumn } from '~/types'

definePageMeta({
  middleware: 'auth'
})

const notificationStore = useNotificationStore()

// State
const loading = ref(true)
const products = ref<Product[]>([])
const categories = ref<Category[]>([])
const showModal = ref(false)
const editingProduct = ref<Product | null>(null)
const saving = ref(false)
const showDeleteConfirm = ref(false)
const deletingProduct = ref<Product | null>(null)
const deleting = ref(false)
const imageFile = ref<File | null>(null)
const imagePreview = ref('')

// Filters
const filters = reactive({
  search: '',
  category: '',
  status: '',
  stock: ''
})

// Form data
const form = reactive({
  name: '',
  sku: '',
  barcode: '',
  description: '',
  category_id: '',
  price: 0,
  cost: 0,
  stock_quantity: 0,
  min_stock_level: 0,
  is_active: true
})

// Table columns
const columns: TableColumn[] = [
  { key: 'image_url', label: 'Image' },
  { key: 'name', label: 'Product Name' },
  { key: 'sku', label: 'SKU' },
  { key: 'category', label: 'Category' },
  { key: 'price', label: 'Price' },
  { key: 'stock_quantity', label: 'Stock' },
  { key: 'is_active', label: 'Status' },
]

const validationMessage = (error: any, fallback: string) => {
  const errors = error?.data?.errors
  if (errors) {
    const first = Object.values(errors).flat()[0]
    if (typeof first === 'string') return first
  }
  return error?.data?.message || fallback
}

// Get stock badge class
const getStockBadgeClass = (product: Product) => {
  if (product.stock_quantity === 0) {
    return 'bg-red-100 text-red-800'
  } else if (product.stock_quantity <= product.min_stock_level) {
    return 'bg-yellow-100 text-yellow-800'
  }
  return 'bg-green-100 text-green-800'
}

// Fetch products from API
const fetchProducts = async () => {
  try {
    loading.value = true
    const params: any = { per_page: 50 }
    
    if (filters.search) params.search = filters.search
    if (filters.category) params.category_id = filters.category
    if (filters.status) params.is_active = filters.status === 'active'
    if (filters.stock) params.stock = filters.stock
    
    const response = await api.products.getAll(params)
    // Laravel returns paginated data: { data: [...], links: {...}, meta: {...} }
    products.value = response.data || []
  } catch (error) {
    console.error('Error fetching products:', error)
    notificationStore.error('Error', 'Failed to fetch products')
  } finally {
    loading.value = false
  }
}

// Fetch categories
const fetchCategories = async () => {
  try {
    const response = await api.categories.getAll()
    // Laravel returns paginated data
    categories.value = response.data || []
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

// Open create modal
const openCreateModal = () => {
  resetForm()
  editingProduct.value = null
  showModal.value = true
}

// Open edit modal
const openEditModal = (product: Product) => {
  editingProduct.value = product
  Object.assign(form, {
    name: product.name,
    sku: product.sku,
    barcode: product.barcode || '',
    description: product.description || '',
    category_id: product.category_id,
    price: product.price,
    cost: product.cost,
    stock_quantity: product.stock_quantity,
    min_stock_level: product.min_stock_level,
    is_active: product.is_active
  })
  imagePreview.value = product.image_url || ''
  showModal.value = true
}

// Close modal
const closeModal = () => {
  showModal.value = false
  resetForm()
}

// Reset form
const resetForm = () => {
  Object.assign(form, {
    name: '',
    sku: '',
    barcode: '',
    description: '',
    category_id: '',
    price: 0,
    cost: 0,
    stock_quantity: 0,
    min_stock_level: 0,
    is_active: true
  })
  imageFile.value = null
  imagePreview.value = ''
}

// Handle image upload
const handleImageUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    imageFile.value = target.files[0]
    
    // Show preview
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target?.result as string
    }
    reader.readAsDataURL(target.files[0])
  }
}

// Save product (create or update)
const saveProduct = async () => {
  try {
    saving.value = true

    // Create FormData for file upload
    const formData = new FormData()
    ;(Object.keys(form) as Array<keyof typeof form>).forEach(key => {
      const value = form[key]
      if (key === 'is_active') {
        formData.append(key, value ? '1' : '0')
        return
      }
      if (value === '' || value === null || value === undefined) return
      formData.append(key, String(value))
    })

    if (imageFile.value) {
      formData.append('image', imageFile.value)
    }

    if (editingProduct.value) {
      // Update existing product
      await api.products.update(editingProduct.value.id, formData)
      notificationStore.success('Success', 'Product updated successfully')
    } else {
      // Create new product
      await api.products.create(formData)
      notificationStore.success('Success', 'Product created successfully')
    }

    closeModal()
    fetchProducts()
  } catch (error: any) {
    notificationStore.error('Error', validationMessage(error, 'Failed to save product'))
  } finally {
    saving.value = false
  }
}

// Confirm delete
const confirmDelete = (product: Product) => {
  deletingProduct.value = product
  showDeleteConfirm.value = true
}

// Delete product
const deleteProduct = async () => {
  if (!deletingProduct.value) return

  try {
    deleting.value = true
    await api.products.delete(deletingProduct.value.id)
    notificationStore.success('Success', 'Product deleted successfully')
    showDeleteConfirm.value = false
    fetchProducts()
  } catch (error: any) {
    notificationStore.error('Error', error?.data?.message || 'Failed to delete product')
  } finally {
    deleting.value = false
  }
}

// Load data on mount
onMounted(() => {
  fetchProducts()
  fetchCategories()
})
</script>
