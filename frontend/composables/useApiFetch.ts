/**
 * API Fetch Helper
 * 
 * This composable provides a wrapper around $fetch with:
 * - Automatic token management
 * - Error handling
 * - Base URL configuration
 * 
 * Usage:
 * const { data, error } = await useApiFetch('/products')
 */

export const useApiFetch = <T>(url: string, options: any = {}) => {
  const config = useRuntimeConfig()
  
  // Get auth store only if on client side
  let authStore: any = null
  if (process.client) {
    authStore = useAuthStore()
  }

  // Merge default options with provided options
  const defaultOptions = {
    baseURL: config.public.apiBase,
    headers: {
      'Accept': 'application/json',
      ...(options.body instanceof FormData ? {} : { 'Content-Type': 'application/json' }),
    },
    // Add authentication token if user is logged in
    onRequest({ options }: any) {
      if (authStore.token) {
        options.headers = {
          ...options.headers,
          Authorization: `Bearer ${authStore.token}`,
        }
      }
    },
    // Handle response errors
    onResponseError({ response }: any) {
      if (response.status === 401 && authStore) {
        // Unauthorized - clear auth and redirect to login
        authStore.logout()
        navigateTo('/login')
      }
    },
  }

  return $fetch<T>(url, { ...defaultOptions, ...options })
}
