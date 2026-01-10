<!--
  Point of Sale Page
  
  Main sales interface with product selection and cart
-->

<template>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-8rem)]">
    <!-- Products Section (Left Side - 2 columns) -->
    <div class="lg:col-span-2 flex flex-col h-full">
      <!-- Search and Filters -->
      <div class="card mb-4 flex-shrink-0">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="md:col-span-2">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search products by name, SKU, or barcode..."
              class="input"
              @input="searchProducts"
            />
          </div>
          <div>
            <select v-model="selectedCategory" class="input" @change="filterProducts">
              <option value="">All Categories</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.name }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Products Grid -->
      <div class="flex-1 overflow-y-auto">
        <div v-if="loadingProducts" class="flex items-center justify-center h-64">
          <LoadingSpinner size="lg" text="Loading products..." />
        </div>
        
        <div v-else class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3">
          <button
            v-for="product in filteredProducts"
            :key="product.id"
            @click="addToCart(product)"
            class="bg-white rounded-lg shadow hover:shadow-md transition-all cursor-pointer overflow-hidden border border-gray-100"
            :disabled="product.stock_quantity === 0"
            :class="{ 'opacity-50 cursor-not-allowed': product.stock_quantity === 0 }"
          >
            <div class="relative">
              <img
                v-if="product.image_url"
                :src="product.image_url"
                :alt="product.name"
                class="w-full h-28 object-cover"
              />
              <div v-else class="w-full h-28 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                <span class="text-4xl">☕</span>
              </div>
              <div v-if="product.stock_quantity <= product.min_stock_level" class="absolute top-2 right-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">
                Low
              </div>
            </div>
            <div class="p-3">
              <h3 class="font-semibold text-gray-900 text-sm mb-1 line-clamp-2 h-10">{{ product.name }}</h3>
              <div class="flex items-center justify-between mt-2">
                <p class="text-lg font-bold text-green-600">${{ product.price.toFixed(2) }}</p>
                <p class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ product.stock_quantity }} in stock</p>
              </div>
            </div>
          </button>
        </div>

        <div v-if="!loadingProducts && filteredProducts.length === 0" class="text-center py-12 text-gray-500">
          No products found
        </div>
      </div>
    </div>

    <!-- Cart Section (Right Side - 1 column) -->
    <div class="lg:col-span-1 flex flex-col h-full overflow-hidden">
      <div class="card flex flex-col h-full overflow-hidden">
        <!-- Header -->
        <div class="flex-shrink-0 pb-3 border-b-2 border-green-500 dark:border-green-400">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Current Sale</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ cartStore.itemCount }} item(s)</p>
              </div>
            </div>
            <button
              v-if="!cartStore.isEmpty"
              @click="clearCart"
              class="px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
            >
              Clear
            </button>
          </div>
        </div>

        <!-- Customer Selection -->
        <div class="flex-shrink-0 py-3 border-b border-gray-200 dark:border-gray-700">
          <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Customer</label>
          <select v-model="cartStore.customer_id" class="input text-sm py-2">
            <option :value="null">Walk-in Customer</option>
            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
              {{ customer.name }} - {{ customer.phone }}
            </option>
          </select>
        </div>

        <!-- Cart Items - Scrollable -->
        <div class="flex-1 overflow-y-auto py-3 min-h-0 max-h-[calc(100vh-32rem)]">
          <div v-if="cartStore.isEmpty" class="flex flex-col items-center justify-center h-full text-gray-400 dark:text-gray-500">
            <div class="text-6xl mb-4">🛒</div>
            <p class="text-lg font-semibold mb-1">Cart is empty</p>
            <p class="text-sm">Add products to start a sale</p>
          </div>

          <div v-else class="space-y-2 pr-1">
            <div
              v-for="item in cartStore.items"
              :key="item.product.id"
              class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-3 hover:border-green-500 dark:hover:border-green-400 transition-colors shadow-sm"
            >
              <div class="flex items-start gap-2.5 mb-2.5">
                <div v-if="item.product.image_url" class="flex-shrink-0">
                  <img
                    :src="item.product.image_url"
                    :alt="item.product.name"
                    class="w-14 h-14 object-cover rounded-lg shadow-md"
                  />
                </div>
                <div v-else class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-600 dark:to-gray-700 rounded-lg flex items-center justify-center shadow-md">
                  <span class="text-2xl">📦</span>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-bold text-sm text-gray-900 dark:text-white leading-tight mb-1.5">{{ item.product.name }}</p>
                  <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">${{ item.price.toFixed(2) }} each</span>
                    <span class="text-sm font-bold text-green-600 dark:text-green-400">${{ (item.price * item.quantity).toFixed(2) }}</span>
                  </div>
                </div>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5 bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
                  <button
                    @click="decreaseQuantity(item.product.id)"
                    class="w-8 h-8 flex items-center justify-center bg-white dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors shadow-sm"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                  </button>
                  <span class="w-12 text-center font-bold text-gray-900 dark:text-white">{{ item.quantity }}</span>
                  <button
                    @click="increaseQuantity(item.product.id)"
                    class="w-8 h-8 flex items-center justify-center bg-white dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-green-50 dark:hover:bg-green-900/30 transition-colors shadow-sm"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </button>
                </div>
                <button
                  @click="cartStore.removeItem(item.product.id)"
                  class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                  title="Remove item"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Totals - Fixed at bottom -->
        <div class="flex-shrink-0 pt-3 border-t-2 border-gray-300 dark:border-gray-600">
          <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-lg p-4 mb-3 space-y-2.5 shadow-inner">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-gray-400 font-medium">Subtotal:</span>
              <span class="font-bold text-gray-900 dark:text-white">${{ cartStore.subtotal.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-gray-400 font-medium">Tax ({{ cartStore.tax_rate }}%):</span>
              <span class="font-bold text-gray-900 dark:text-white">${{ cartStore.taxAmount.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-gray-400 font-medium">Discount:</span>
              <span class="font-bold text-red-600 dark:text-red-400">-${{ cartStore.discountAmount.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-2xl font-bold pt-3 border-t-2 border-gray-300 dark:border-gray-600">
              <span class="text-gray-900 dark:text-white">Total:</span>
              <span class="text-green-600 dark:text-green-400">${{ cartStore.total.toFixed(2) }}</span>
            </div>
          </div>

          <!-- Payment Method -->
          <div class="mb-3">
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Payment Method</label>
            <select v-model="paymentMethod" class="input text-sm py-2.5 font-medium">
              <option value="cash">💵 Cash</option>
              <option value="card">💳 Card</option>
              <option value="mobile">📱 Mobile Money</option>
            </select>
          </div>

          <!-- Action Buttons -->
          <div class="space-y-2">
            <button
              @click="completeSale"
              class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-4 px-6 rounded-lg transition-all disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed shadow-lg text-lg transform hover:scale-[1.02] active:scale-[0.98]"
              :disabled="cartStore.isEmpty || processing"
            >
              <div v-if="processing" class="flex items-center justify-center">
                <LoadingSpinner size="sm" class="mr-2" />
                <span>Processing...</span>
              </div>
              <div v-else class="flex items-center justify-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Complete Sale - ${{ cartStore.total.toFixed(2) }}</span>
              </div>
            </button>
            <button
              @click="clearCart"
              class="w-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-2.5 px-6 rounded-lg transition-colors"
              :disabled="cartStore.isEmpty"
            >
              Clear Cart
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { api } from '~/services/api'
import type { Product, Customer, Category } from '~/types'

definePageMeta({
  middleware: 'auth'
})

const cartStore = useCartStore()
const notificationStore = useNotificationStore()

// State
const loadingProducts = ref(true)
const products = ref<Product[]>([])
const customers = ref<Customer[]>([])
const filteredProducts = ref<Product[]>([])
const searchQuery = ref('')
const selectedCategory = ref('')
const categories = ref<Category[]>([])
const paymentMethod = ref('cash')
const processing = ref(false)

// Fetch products
const fetchProducts = async () => {
  try {
    loadingProducts.value = true
    const response = await api.products.getAll({ is_active: true, per_page: 100 })
    console.log('Products API response:', response)
    // Laravel ResourceCollection returns: { data: [...], links: {...}, meta: {...} }
    products.value = Array.isArray(response) ? response : (response.data || [])
    filteredProducts.value = products.value
  } catch (error) {
    console.error('Error fetching products:', error)
    notificationStore.error('Error', 'Failed to fetch products')
  } finally {
    loadingProducts.value = false
  }
}

// Fetch customers
const fetchCustomers = async () => {
  try {
    const response = await api.customers.getAll()
    // Laravel returns paginated data
    customers.value = response.data || []
  } catch (error) {
    console.error('Error fetching customers:', error)
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

// Search products
const searchProducts = () => {
  let results = products.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    results = results.filter(p =>
      p.name.toLowerCase().includes(query) ||
      p.sku.toLowerCase().includes(query) ||
      p.barcode?.toLowerCase().includes(query)
    )
  }

  if (selectedCategory.value) {
    results = results.filter(p => p.category_id === Number(selectedCategory.value))
  }

  filteredProducts.value = results
}

// Filter products by category
const filterProducts = () => {
  searchProducts()
}

// Add product to cart
const addToCart = (product: Product) => {
  if (product.stock_quantity > 0) {
    cartStore.addItem(product)
    notificationStore.success('Added to cart', product.name)
  } else {
    notificationStore.error('Out of stock', 'This product is currently unavailable')
  }
}

// Increase quantity
const increaseQuantity = (productId: number) => {
  const item = cartStore.items.find(i => i.product.id === productId)
  if (item && item.quantity < item.product.stock_quantity) {
    cartStore.updateQuantity(productId, item.quantity + 1)
  } else {
    notificationStore.warning('Stock limit', 'Cannot add more than available stock')
  }
}

// Decrease quantity
const decreaseQuantity = (productId: number) => {
  const item = cartStore.items.find(i => i.product.id === productId)
  if (item) {
    cartStore.updateQuantity(productId, item.quantity - 1)
  }
}

// Clear cart
const clearCart = () => {
  if (confirm('Are you sure you want to clear the cart?')) {
    cartStore.clear()
    notificationStore.info('Cart cleared', 'All items removed')
  }
}

// Complete sale
const completeSale = async () => {
  if (cartStore.isEmpty) {
    notificationStore.warning('Cart is empty', 'Add products to cart first')
    return
  }

  try {
    processing.value = true
    
    const saleData = cartStore.getSaleData(paymentMethod.value)

    console.log('Creating sale with data:', saleData)
    const response = await api.sales.create(saleData) as { success: boolean; data: any }
    console.log('Sale response:', response)
    
    notificationStore.success('Sale Completed! 🎉', `Total: $${cartStore.total.toFixed(2)}`)
    
    // Print receipt
    printReceipt(response.data, saleData)
    
    cartStore.clear()
    paymentMethod.value = 'cash'
    
    // Refresh products to update stock
    await fetchProducts()
  } catch (error: any) {
    console.error('Sale error:', error)
    console.error('Full error data:', JSON.stringify(error?.data, null, 2))
    console.error('Error details:', {
      status: error?.status,
      statusText: error?.statusText,
      data: error?.data,
      message: error?.message
    })
    console.error('Validation errors:', error?.data?.errors)
    
    // Handle validation errors
    if (error?.status === 422) {
      if (error?.data?.errors) {
        const validationErrors = Object.values(error.data.errors).flat().join(', ')
        notificationStore.error('Validation Error', validationErrors)
      } else {
        const errorMessage = error?.data?.message || error?.data?.error || 'Validation failed - check console for details'
        notificationStore.error('Sale Failed', errorMessage)
      }
    } else {
      const errorMessage = error?.data?.message || error?.message || 'Failed to complete sale'
      notificationStore.error('Sale Failed', errorMessage)
    }
  } finally {
    processing.value = false
  }
}

// Print Receipt (80mm thermal printer)
const printReceipt = (sale: any, saleData: any) => {
  const printWindow = window.open('', '_blank', 'width=302,height=600')
  if (!printWindow) return

  const customer = customers.value.find(c => c.id === cartStore.customer_id)
  const authStore = useAuthStore()
  
  const itemsHtml = cartStore.items.map(item => 
    `<div class="item">
      <div class="item-name">${item.product.name}</div>
      <div class="item-details">
        <span>${item.quantity} x $${item.price.toFixed(2)}</span>
        <span class="item-total">$${(item.price * item.quantity).toFixed(2)}</span>
      </div>
    </div>`
  ).join('')
  
  const customerHtml = customer ? `<div class="info-row">Customer: ${customer.name}</div>` : ''
  const discountHtml = cartStore.discountAmount > 0 ? 
    `<div class="total-row">
      <span>Discount</span>
      <span>-$${cartStore.discountAmount.toFixed(2)}</span>
    </div>` : ''
  
  const html = '<!DOCTYPE html>' +
    '<html>' +
    '<head>' +
    '<meta charset="UTF-8">' +
    '<title>Receipt - ' + sale.invoice_number + '</title>' +
    '<style>' +
    '@page { size: 80mm auto; margin: 0; }' +
    '* { margin: 0; padding: 0; box-sizing: border-box; }' +
    'body { font-family: "Courier New", monospace; width: 80mm; padding: 5mm; font-size: 11px; line-height: 1.4; }' +
    '.receipt { width: 100%; }' +
    '.header { text-align: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px dashed #000; }' +
    '.header h1 { font-size: 18px; margin-bottom: 3px; font-weight: bold; }' +
    '.header p { font-size: 10px; }' +
    '.info { margin-bottom: 10px; font-size: 10px; }' +
    '.info-row { margin: 2px 0; }' +
    '.separator { border-top: 1px dashed #000; margin: 8px 0; }' +
    '.items { margin: 8px 0; }' +
    '.item { margin: 6px 0; }' +
    '.item-name { font-weight: bold; margin-bottom: 2px; }' +
    '.item-details { display: flex; justify-content: space-between; font-size: 10px; }' +
    '.item-total { font-weight: bold; }' +
    '.totals { margin-top: 10px; padding-top: 8px; border-top: 1px dashed #000; }' +
    '.total-row { display: flex; justify-content: space-between; margin: 4px 0; font-size: 11px; }' +
    '.total-row.final { font-size: 14px; font-weight: bold; margin-top: 6px; padding-top: 6px; border-top: 1px solid #000; }' +
    '.footer { text-align: center; margin-top: 12px; padding-top: 8px; border-top: 1px dashed #000; font-size: 10px; }' +
    '.footer p { margin: 3px 0; }' +
    '@media print { body { width: 80mm; margin: 0; padding: 5mm; } }' +
    '</style>' +
    '</head>' +
    '<body>' +
    '<div class="receipt">' +
    '<div class="header">' +
    '<h1>POS SYSTEM</h1>' +
    '<p>Sales Receipt</p>' +
    '</div>' +
    '<div class="info">' +
    '<div class="info-row">Invoice: ' + sale.invoice_number + '</div>' +
    '<div class="info-row">Date: ' + new Date().toLocaleString('en-US', { dateStyle: 'short', timeStyle: 'short' }) + '</div>' +
    '<div class="info-row">Cashier: ' + (authStore.user?.name || 'Staff') + '</div>' +
    customerHtml +
    '<div class="info-row">Payment: ' + paymentMethod.value.toUpperCase() + '</div>' +
    '</div>' +
    '<div class="separator"></div>' +
    '<div class="items">' +
    itemsHtml +
    '</div>' +
    '<div class="totals">' +
    '<div class="total-row"><span>Subtotal</span><span>$' + cartStore.subtotal.toFixed(2) + '</span></div>' +
    '<div class="total-row"><span>Tax (' + cartStore.tax_rate + '%)</span><span>$' + cartStore.taxAmount.toFixed(2) + '</span></div>' +
    discountHtml +
    '<div class="total-row final"><span>TOTAL</span><span>$' + cartStore.total.toFixed(2) + '</span></div>' +
    '</div>' +
    '<div class="footer">' +
    '<p>Thank you for your business!</p>' +
    '<p>Please come again</p>' +
    '<div style="margin: 8px 0; border-top: 1px solid #000; padding-top: 4px;">* * * * *</div>' +
    '<p style="font-size: 9px;">Powered by POS System</p>' +
    '</div>' +
    '</div>' +
    '<script>' +
    'window.onload = function() {' +
    'window.print();' +
    'setTimeout(function() { window.close(); }, 100);' +
    '}' +
    '<\/script>' +
    '</body>' +
    '</html>'
  
  printWindow.document.write(html)
  printWindow.document.close()
}

// Load data on mount
onMounted(() => {
  fetchProducts()
  fetchCustomers()
  fetchCategories()
})
</script>
