/**
 * Authentication Store
 * 
 * Manages user authentication state:
 * - Login/Logout
 * - Token management (stored in localStorage)
 * - Current user information
 * - Authentication status
 * 
 * Usage:
 * const authStore = useAuthStore()
 * await authStore.login(email, password)
 */

import { defineStore } from 'pinia'
import { api } from '~/services/api'
import type { User } from '~/types'

interface AuthState {
  user: User | null
  token: string | null
  isLoading: boolean
}

export const useAuthStore = defineStore('auth', {
  // State - Initial values
  state: (): AuthState => ({
    user: null,
    token: null,
    isLoading: false,
  }),

  // Getters - Computed properties
  getters: {
    /**
     * Check if user is authenticated
     */
    isAuthenticated: (state) => !!state.token && !!state.user,

    /**
     * Check if user is admin
     */
    isAdmin: (state) => state.user?.role_id === 1,

    /**
     * Get user's role name
     */
    userRole: (state) => state.user?.role?.name || 'Unknown',
  },

  // Actions - Methods that can modify state
  actions: {
    /**
     * Login with email and password
     */
    async login(email: string, password: string) {
      try {
        this.isLoading = true
        
        // Call API login endpoint
        const response = await api.auth.login({ email, password })
        
        if (response.success) {
          // Save user and token
          this.user = response.data.user
          this.token = response.data.token
          
          // Store token in localStorage for persistence
          if (process.client) {
            localStorage.setItem('auth_token', response.data.token)
            localStorage.setItem('user', JSON.stringify(response.data.user))
          }
          
          return { success: true }
        }
        
        return { success: false, message: 'Login failed' }
      } catch (error: any) {
        console.error('Login error:', error)
        return { 
          success: false, 
          message: error?.data?.message || 'Invalid credentials' 
        }
      } finally {
        this.isLoading = false
      }
    },

    /**
     * Logout current user
     */
    async logout() {
      try {
        // Call API logout endpoint
        await api.auth.logout()
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        // Clear state and localStorage
        this.user = null
        this.token = null
        
        if (process.client) {
          localStorage.removeItem('auth_token')
          localStorage.removeItem('user')
        }
      }
    },

    /**
     * Restore auth state from localStorage
     * Called on app initialization
     */
    restoreAuth() {
      if (process.client) {
        const token = localStorage.getItem('auth_token')
        const userStr = localStorage.getItem('user')
        
        if (token && userStr) {
          this.token = token
          this.user = JSON.parse(userStr)
        }
      }
    },

    /**
     * Fetch current user details from API
     * Useful for refreshing user data
     */
    async fetchUser() {
      try {
        const response = await api.auth.me()
        if (response.success) {
          this.user = response.data
          
          // Update localStorage
          if (process.client) {
            localStorage.setItem('user', JSON.stringify(response.data))
          }
        }
      } catch (error) {
        console.error('Fetch user error:', error)
        // If fetch fails, clear auth (token might be invalid)
        this.logout()
      }
    },
  },
})
