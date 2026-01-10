<!--
  Login Page
  
  User authentication page with email and password
-->

<template>
  <div>
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome Back</h2>
    <p class="text-gray-600 mb-6">Sign in to your account to continue</p>

    <!-- Error Message -->
    <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
      {{ error }}
    </div>

    <!-- Login Form -->
    <form @submit.prevent="handleLogin" class="space-y-4">
      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
          Email Address
        </label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          required
          class="input"
          placeholder="Enter your email"
          :disabled="loading"
        />
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
          Password
        </label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          required
          class="input"
          placeholder="Enter your password"
          :disabled="loading"
        />
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        class="w-full btn btn-primary"
        :disabled="loading"
      >
        <LoadingSpinner v-if="loading" size="sm" class="mr-2" />
        {{ loading ? 'Signing in...' : 'Sign In' }}
      </button>
    </form>

    <!-- Demo Credentials -->
    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
      <p class="text-sm font-medium text-blue-900 mb-2">Demo Credentials:</p>
      <p class="text-sm text-blue-700">Email: admin@pos.com</p>
      <p class="text-sm text-blue-700">Password: password</p>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'auth',
  middleware: 'guest'
})

const authStore = useAuthStore()
const notificationStore = useNotificationStore()

// Form state
const form = reactive({
  email: 'admin@pos.com',
  password: 'password'
})

const loading = ref(false)
const error = ref('')

// Handle login
const handleLogin = async () => {
  try {
    loading.value = true
    error.value = ''

    console.log('Attempting login with:', form.email)

    const result = await authStore.login(form.email, form.password)

    if (result.success) {
      notificationStore.success('Login successful', 'Welcome back!')
      navigateTo('/')
    } else {
      error.value = result.message || 'Invalid credentials'
    }
  } catch (err: any) {
    console.error('Login error:', err)
    
    // Handle different error types
    if (err?.statusCode === 422 && err?.data?.errors) {
      // Validation errors
      const validationErrors = Object.values(err.data.errors).flat().join(', ')
      error.value = validationErrors
    } else if (err?.cause?.code === 'ECONNREFUSED') {
      error.value = 'Cannot connect to server. Please make sure the backend is running at http://localhost:8000'
    } else {
      error.value = err?.data?.message || err?.message || 'An error occurred during login'
    }
  } finally {
    loading.value = false
  }
}
</script>
