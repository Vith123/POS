/**
 * Authentication Middleware
 * 
 * Protects routes that require authentication
 * Redirects to login if user is not authenticated
 * 
 * Usage: Add definePageMeta({ middleware: 'auth' }) to protected pages
 */

export default defineNuxtRouteMiddleware((to, from) => {
  // Only run on client side to avoid hydration mismatch
  if (process.server) {
    return
  }

  const authStore = useAuthStore()

  // Restore auth from localStorage on first load
  if (!authStore.token) {
    authStore.restoreAuth()
  }

  // Check if user is authenticated
  if (!authStore.isAuthenticated) {
    // Redirect to login page
    return navigateTo('/login')
  }
})
