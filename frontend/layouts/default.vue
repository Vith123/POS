<!--
  Default Layout
  
  Main application layout with sidebar and navbar
  Used for all authenticated pages
-->

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Mobile Overlay -->
    <div
      v-if="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 bg-black/50 z-20 lg:hidden"
    ></div>

    <!-- Sidebar -->
    <aside
      class="fixed left-0 top-0 h-full w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-transform duration-300 z-30"
      :class="{ 
        '-translate-x-full lg:translate-x-0': !sidebarOpen,
        'translate-x-0': sidebarOpen
      }"
    >
      <!-- Logo -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-primary-600 dark:text-primary-400">POS System</h1>
        <button
          @click="sidebarOpen = false"
          class="lg:hidden text-gray-500 hover:text-gray-700"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Navigation -->
      <nav class="p-4 space-y-1">
        <NuxtLink
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          @click="closeSidebarOnMobile"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors"
          :class="isActive(item.path) 
            ? 'bg-primary-50 dark:bg-primary-900 text-primary-600 dark:text-primary-400' 
            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
        >
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="item.icon"></svg>
          <span class="font-medium">{{ item.label }}</span>
        </NuxtLink>
      </nav>

      <!-- User Info -->
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-gray-700">
        <ClientOnly>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
              <span class="text-primary-600 dark:text-primary-400 font-semibold">{{ userInitials }}</span>
            </div>
            <div class="flex-1">
              <p class="font-medium text-gray-900 dark:text-white">{{ authStore.user?.name }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ authStore.userRole }}</p>
            </div>
            <button
              @click="handleLogout"
              class="text-gray-400 hover:text-red-600 transition-colors"
              title="Logout"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
            </button>
          </div>
        </ClientOnly>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:ml-64">
      <!-- Top Navbar -->
      <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10">
        <div class="flex items-center justify-between px-6 py-4">
          <!-- Mobile Menu Button -->
          <button
            @click="sidebarOpen = true"
            class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>

          <h2 class="text-xl font-semibold text-gray-800 dark:text-white">{{ pageTitle }}</h2>

          <!-- Right side actions -->
          <div class="flex items-center gap-3">
            <!-- Language Switcher -->
            <select
              v-model="currentLocale"
              @change="changeLocale"
              class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="en">English</option>
              <option value="km">ខ្មែរ</option>
              <option value="zh">中文</option>
            </select>

            <!-- Dark Mode Toggle -->
            <button
              @click="toggleDarkMode"
              class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
              :title="colorMode === 'dark' ? 'Light Mode' : 'Dark Mode'"
            >
              <svg v-if="colorMode === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
              </svg>
            </button>

            <!-- Notifications -->
            <button class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 relative rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-6">
        <slot />
      </main>
    </div>

    <!-- Notifications Toast -->
    <NotificationToast />
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()
const route = useRoute()
const { locale } = useI18n()
const colorMode = useColorMode()

// Sidebar state - open by default on desktop
const sidebarOpen = ref(false)

// Language state
const currentLocale = ref(locale.value)

// Methods
const changeLocale = () => {
  locale.value = currentLocale.value
}

const toggleDarkMode = () => {
  colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark'
}

// Menu items with SVG icons
const menuItems = [
  { 
    path: '/', 
    label: 'Dashboard', 
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />'
  },
  { 
    path: '/pos', 
    label: 'Point of Sale', 
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />'
  },
  { 
    path: '/products', 
    label: 'Products', 
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />'
  },
  { 
    path: '/categories', 
    label: 'Categories', 
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />'
  },
  { 
    path: '/inventory', 
    label: 'Inventory', 
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />'
  },
  { 
    path: '/sales', 
    label: 'Sales History', 
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />'
  },
  { 
    path: '/customers', 
    label: 'Customers', 
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />'
  },
  { 
    path: '/returns', 
    label: 'Returns', 
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />'
  },
  { 
    path: '/reports', 
    label: 'Reports', 
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />'
  },
]

// Check if route is active
const isActive = (path: string) => {
  if (path === '/') {
    return route.path === '/'
  }
  return route.path.startsWith(path)
}

// Get page title from route
const pageTitle = computed(() => {
  const item = menuItems.find(item => isActive(item.path))
  return item?.label || 'Dashboard'
})

// Get user initials
const userInitials = computed(() => {
  const name = authStore.user?.name || 'U'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

// Logout handler
const handleLogout = async () => {
  await authStore.logout()
  navigateTo('/login')
}

// Close sidebar on mobile after navigation
const closeSidebarOnMobile = () => {
  if (window.innerWidth < 1024) {
    sidebarOpen.value = false
  }
}

// Watch route changes to close sidebar on mobile
watch(() => route.path, () => {
  closeSidebarOnMobile()
})
</script>
