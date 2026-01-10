# 🛍️ POS Frontend - Nuxt.js 3

Modern Point of Sale system frontend built with Nuxt.js 3, Vue 3, and Tailwind CSS.

## 📋 Features

- ✅ **Modern Tech Stack** - Nuxt.js 3, Vue 3 Composition API, TypeScript
- ✅ **Beautiful UI** - Tailwind CSS with custom components
- ✅ **State Management** - Pinia stores for clean data flow
- ✅ **Authentication** - JWT token-based auth with Laravel Sanctum
- ✅ **Real-time POS** - Fast product search and cart management
- ✅ **Full CRUD** - Complete product, category, customer management
- ✅ **Dashboard** - Business analytics and insights
- ✅ **Responsive** - Mobile-friendly design
- ✅ **Easy to Learn** - Well-commented code for beginners

## 🚀 Quick Start

### 1. Install Dependencies

```bash
npm install
```

### 2. Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` and set your API URL:

```env
NUXT_PUBLIC_API_BASE=http://localhost:8000/api
```

### 3. Start Development Server

```bash
npm run dev
```

The app will be available at `http://localhost:3000`

## 📁 Project Structure

```
frontend/
├── assets/              # CSS and static assets
│   └── css/
│       └── main.css    # Tailwind CSS + custom styles
│
├── components/          # Reusable Vue components
│   ├── DataTable.vue          # Table component
│   ├── Modal.vue              # Modal dialog
│   ├── NotificationToast.vue  # Toast notifications
│   ├── LoadingSpinner.vue     # Loading indicator
│   ├── ConfirmDialog.vue      # Confirmation dialog
│   └── StatsCard.vue          # Statistics card
│
├── composables/         # Reusable composition functions
│   └── useApiFetch.ts  # API fetch wrapper with auth
│
├── layouts/            # Page layouts
│   ├── default.vue    # Main app layout with sidebar
│   └── auth.vue       # Auth pages layout (login)
│
├── middleware/         # Route middleware
│   ├── auth.ts       # Protect authenticated routes
│   └── guest.ts      # Redirect authenticated users
│
├── pages/             # Application pages (auto-routed)
│   ├── index.vue     # Dashboard
│   ├── login.vue     # Login page
│   ├── pos.vue       # Point of Sale interface
│   ├── products/
│   │   └── index.vue # Products management
│   └── sales/
│       └── index.vue # Sales history
│
├── services/          # API service layer
│   └── api.ts        # All API endpoints
│
├── stores/           # Pinia stores (state management)
│   ├── auth.ts      # Authentication state
│   ├── cart.ts      # Shopping cart state
│   └── notification.ts # Notifications state
│
├── types/            # TypeScript type definitions
│   └── index.ts     # All application types
│
├── nuxt.config.ts   # Nuxt configuration
├── tailwind.config.js # Tailwind CSS configuration
└── package.json     # Dependencies
```

## 🎨 Key Components

### DataTable
Reusable table with sorting and custom slots:
```vue
<DataTable :columns="columns" :data="products" :loading="loading">
  <template #cell-price="{ row }">
    ${{ row.price }}
  </template>
  <template #actions="{ row }">
    <button @click="edit(row)">Edit</button>
  </template>
</DataTable>
```

### Modal
Flexible modal dialog:
```vue
<Modal :show="showModal" title="Add Product" @close="showModal = false">
  <p>Your content here</p>
  <template #footer>
    <button>Save</button>
  </template>
</Modal>
```

### Notifications
Toast notifications:
```ts
const notificationStore = useNotificationStore()
notificationStore.success('Success!', 'Product saved')
notificationStore.error('Error', 'Something went wrong')
```

## 🔐 Authentication

The app uses token-based authentication:

1. **Login** - User logs in with email/password
2. **Token Storage** - Token saved in localStorage
3. **Auto-restore** - Token restored on page reload
4. **Protected Routes** - Middleware checks authentication
5. **Auto-logout** - 401 responses clear auth and redirect

### Using Auth Store

```ts
const authStore = useAuthStore()

// Login
await authStore.login(email, password)

// Check if authenticated
if (authStore.isAuthenticated) {
  // User is logged in
}

// Get current user
console.log(authStore.user)

// Logout
await authStore.logout()
```

## 🛒 Cart Management

The cart store manages POS sales:

```ts
const cartStore = useCartStore()

// Add product to cart
cartStore.addItem(product, quantity)

// Update quantity
cartStore.updateQuantity(productId, newQuantity)

// Remove item
cartStore.removeItem(productId)

// Get totals
console.log(cartStore.subtotal)
console.log(cartStore.taxAmount)
console.log(cartStore.total)

// Clear cart
cartStore.clear()

// Get sale data for API
const saleData = cartStore.getSaleData()
```

## 🌐 API Integration

All API calls go through the service layer:

```ts
import { api } from '~/services/api'

// Products
const products = await api.products.getAll()
const product = await api.products.getById(1)
await api.products.create(data)
await api.products.update(id, data)
await api.products.delete(id)

// Sales
const sales = await api.sales.getAll()
await api.sales.create(saleData)

// Dashboard
const stats = await api.reports.getDashboard()
```

## 🎯 Creating New Pages

1. **Create a page file** in `pages/` directory:
```vue
<!-- pages/customers/index.vue -->
<template>
  <div>
    <h1>Customers</h1>
    <!-- Your content -->
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth' // Protect route
})

// Your component logic
</script>
```

2. **Access automatically** at `/customers`

## 🔧 Customization

### Colors
Edit `tailwind.config.js` to change the primary color:
```js
theme: {
  extend: {
    colors: {
      primary: {
        // Your custom colors
      }
    }
  }
}
```

### API URL
Change in `.env`:
```env
NUXT_PUBLIC_API_BASE=https://your-api.com/api
```

### Add New Menu Item
Edit `layouts/default.vue`:
```ts
const menuItems = [
  // ... existing items
  { path: '/your-page', label: 'Your Page', icon: 'IconName' }
]
```

## 📚 Learn More

### Key Concepts

1. **Composition API** - Modern Vue 3 syntax
2. **Reactivity** - `ref()`, `reactive()`, `computed()`
3. **Lifecycle Hooks** - `onMounted()`, `watch()`
4. **Stores** - Centralized state with Pinia
5. **Auto-imports** - Components and composables auto-imported

### Example: Creating a Feature

```vue
<template>
  <div>
    <h1>{{ title }}</h1>
    <button @click="increment">Count: {{ count }}</button>
    <DataTable :data="items" :loading="loading" />
  </div>
</template>

<script setup lang="ts">
import { api } from '~/services/api'

// Define page meta
definePageMeta({
  middleware: 'auth'
})

// Reactive state
const title = ref('My Feature')
const count = ref(0)
const items = ref([])
const loading = ref(false)

// Computed property
const doubled = computed(() => count.value * 2)

// Methods
const increment = () => {
  count.value++
}

const fetchData = async () => {
  loading.value = true
  try {
    const response = await api.products.getAll()
    items.value = response.data
  } finally {
    loading.value = false
  }
}

// Lifecycle - runs when component mounts
onMounted(() => {
  fetchData()
})

// Watch for changes
watch(count, (newValue) => {
  console.log('Count changed to:', newValue)
})
</script>
```

## 🚀 Production Build

```bash
# Build for production
npm run build

# Preview production build
npm run preview

# Generate static site
npm run generate
```

## 🐛 Troubleshooting

### API Connection Issues
- Check if backend is running on `http://localhost:8000`
- Verify CORS is enabled in Laravel backend
- Check `.env` has correct `NUXT_PUBLIC_API_BASE`

### Authentication Not Working
- Clear browser localStorage
- Check token in browser DevTools > Application > Local Storage
- Verify API returns correct token format

### Components Not Found
- Nuxt auto-imports components from `components/` directory
- Make sure component files are named with PascalCase
- Restart dev server after adding new components

## 📖 Resources

- [Nuxt 3 Documentation](https://nuxt.com)
- [Vue 3 Documentation](https://vuejs.org)
- [Tailwind CSS](https://tailwindcss.com)
- [Pinia Documentation](https://pinia.vuejs.org)

## 💡 Tips for Learning

1. **Start with pages/login.vue** - See authentication flow
2. **Study pages/products/index.vue** - Complete CRUD example
3. **Check stores/** - Understand state management
4. **Look at components/** - Reusable UI patterns
5. **Read services/api.ts** - API integration patterns

## 🤝 Contributing

Feel free to modify and extend this codebase! The code is well-commented to help you learn.

---

Built with ❤️ using Nuxt.js 3
