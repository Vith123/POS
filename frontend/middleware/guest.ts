/**
 * Guest Middleware
 * 
 * Redirects authenticated users away from guest-only pages (like login)
 * 
 * Usage: Add definePageMeta({ middleware: 'guest' }) to login/register pages
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

  // If user is authenticated, redirect to dashboard
  if (authStore.isAuthenticated) {
    return navigateTo('/')
  }
})
