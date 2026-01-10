/**
 * API Service
 * 
 * Centralized API calls for all backend endpoints
 * Each method corresponds to a Laravel API endpoint
 * 
 * Usage Example:
 * import { api } from '~/services/api'
 * const products = await api.products.getAll()
 */

import type { 
  LoginCredentials, 
  User, 
  Product, 
  Category, 
  Sale, 
  Customer,
  Inventory,
  Report 
} from '~/types'

export const api = {
  // ============================================
  // AUTHENTICATION
  // ============================================
  auth: {
    /**
     * Login user and get authentication token
     */
    login: (credentials: LoginCredentials) => 
      useApiFetch<{ success: boolean; data: { user: User; token: string } }>('/login', {
        method: 'POST',
        body: credentials,
      }),

    /**
     * Logout current user
     */
    logout: () => 
      useApiFetch('/logout', { method: 'POST' }),

    /**
     * Get current authenticated user details
     */
    me: () => 
      useApiFetch<{ success: boolean; data: User }>('/me'),
  },

  // ============================================
  // PRODUCTS
  // ============================================
  products: {
    /**
     * Get all products with optional filters
     */
    getAll: (params?: any) => 
      useApiFetch<{ success: boolean; data: Product[] }>('/products', { params }),

    /**
     * Get single product by ID
     */
    getById: (id: number) => 
      useApiFetch<{ success: boolean; data: Product }>(`/products/${id}`),

    /**
     * Get products with low stock
     */
    getLowStock: () => 
      useApiFetch<{ success: boolean; data: Product[] }>('/products/low-stock'),

    /**
     * Get out of stock products
     */
    getOutOfStock: () => 
      useApiFetch<{ success: boolean; data: Product[] }>('/products/out-of-stock'),

    /**
     * Create new product
     */
    create: (data: FormData) => 
      useApiFetch('/products', {
        method: 'POST',
        body: data,
      }),

    /**
     * Update existing product
     */
    update: (id: number, data: FormData) => 
      useApiFetch(`/products/${id}`, {
        method: 'POST',
        body: data,
      }),

    /**
     * Delete product
     */
    delete: (id: number) => 
      useApiFetch(`/products/${id}`, { method: 'DELETE' }),
  },

  // ============================================
  // CATEGORIES
  // ============================================
  categories: {
    /**
     * Get all categories
     */
    getAll: (params?: any) => 
      useApiFetch<{ success: boolean; data: Category[] }>('/categories', { params }),

    /**
     * Get single category by ID
     */
    getById: (id: number) => 
      useApiFetch<{ success: boolean; data: Category }>(`/categories/${id}`),

    /**
     * Get products in a category
     */
    getProducts: (id: number) => 
      useApiFetch<{ success: boolean; data: Product[] }>(`/categories/${id}/products`),

    /**
     * Create new category
     */
    create: (data: any) => 
      useApiFetch('/categories', {
        method: 'POST',
        body: data,
      }),

    /**
     * Update category
     */
    update: (id: number, data: any) => 
      useApiFetch(`/categories/${id}`, {
        method: 'PUT',
        body: data,
      }),

    /**
     * Delete category
     */
    delete: (id: number) => 
      useApiFetch(`/categories/${id}`, { method: 'DELETE' }),
  },

  // ============================================
  // SALES
  // ============================================
  sales: {
    /**
     * Get all sales
     */
    getAll: (params?: any) => 
      useApiFetch<{ success: boolean; data: Sale[] }>('/sales', { params }),

    /**
     * Get single sale by ID
     */
    getById: (id: number) => 
      useApiFetch<{ success: boolean; data: Sale }>(`/sales/${id}`),

    /**
     * Get today's sales summary
     */
    getTodaySummary: () => 
      useApiFetch('/sales/today'),

    /**
     * Get sales statistics
     */
    getStatistics: (params?: any) => 
      useApiFetch('/sales/statistics', { params }),

    /**
     * Create new sale
     */
    create: (data: any) => 
      useApiFetch('/sales', {
        method: 'POST',
        body: data,
      }),

    /**
     * Update sale status
     */
    updateStatus: (id: number, status: string) => 
      useApiFetch(`/sales/${id}/status`, {
        method: 'PUT',
        body: { status },
      }),
  },

  // ============================================
  // CUSTOMERS
  // ============================================
  customers: {
    /**
     * Get all customers
     */
    getAll: (params?: any) => 
      useApiFetch<{ success: boolean; data: Customer[] }>('/customers', { params }),

    /**
     * Get single customer by ID
     */
    getById: (id: number) => 
      useApiFetch<{ success: boolean; data: Customer }>(`/customers/${id}`),

    /**
     * Get VIP customers
     */
    getVIP: () => 
      useApiFetch<{ success: boolean; data: Customer[] }>('/customers/vip'),

    /**
     * Get customer purchase history
     */
    getPurchaseHistory: (id: number) => 
      useApiFetch(`/customers/${id}/purchases`),

    /**
     * Create new customer
     */
    create: (data: any) => 
      useApiFetch('/customers', {
        method: 'POST',
        body: data,
      }),

    /**
     * Update customer
     */
    update: (id: number, data: any) => 
      useApiFetch(`/customers/${id}`, {
        method: 'PUT',
        body: data,
      }),

    /**
     * Delete customer
     */
    delete: (id: number) => 
      useApiFetch(`/customers/${id}`, { method: 'DELETE' }),

    /**
     * Manage customer loyalty points
     */
    manageLoyaltyPoints: (id: number, data: any) => 
      useApiFetch(`/customers/${id}/loyalty-points`, {
        method: 'POST',
        body: data,
      }),
  },

  // ============================================
  // INVENTORY
  // ============================================
  inventory: {
    /**
     * Get all inventory items
     */
    getAll: (params?: any) => 
      useApiFetch<{ success: boolean; data: Inventory[] }>('/inventory', { params }),

    /**
     * Get inventory for specific product
     */
    getByProduct: (productId: number) => 
      useApiFetch<{ success: boolean; data: Inventory }>(`/inventory/${productId}`),

    /**
     * Get stock movements history
     */
    getMovements: (productId: number) => 
      useApiFetch(`/inventory/${productId}/movements`),

    /**
     * Add stock (stock in)
     */
    stockIn: (data: any) => 
      useApiFetch('/inventory/stock-in', {
        method: 'POST',
        body: data,
      }),

    /**
     * Remove stock (stock out)
     */
    stockOut: (data: any) => 
      useApiFetch('/inventory/stock-out', {
        method: 'POST',
        body: data,
      }),

    /**
     * Adjust stock level
     */
    adjust: (productId: number, data: any) => 
      useApiFetch(`/inventory/${productId}/adjust`, {
        method: 'POST',
        body: data,
      }),

    /**
     * Update minimum stock level
     */
    updateMinLevel: (productId: number, minStockLevel: number) => 
      useApiFetch(`/inventory/${productId}/min-level`, {
        method: 'PUT',
        body: { min_stock_level: minStockLevel },
      }),
  },

  // ============================================
  // RETURNS
  // ============================================
  returns: {
    /**
     * Get all returns
     */
    getAll: (params?: any) => 
      useApiFetch('/returns', { params }),

    /**
     * Get single return by ID
     */
    getById: (id: number) => 
      useApiFetch(`/returns/${id}`),

    /**
     * Get pending returns
     */
    getPending: () => 
      useApiFetch('/returns/pending'),

    /**
     * Get return statistics
     */
    getStatistics: (params?: any) => 
      useApiFetch('/returns/statistics', { params }),

    /**
     * Create new return
     */
    create: (data: any) => 
      useApiFetch('/returns', {
        method: 'POST',
        body: data,
      }),

    /**
     * Update return status
     */
    updateStatus: (id: number, status: string) => 
      useApiFetch(`/returns/${id}/status`, {
        method: 'PUT',
        body: { status },
      }),

    /**
     * Update return
     */
    update: (id: number, data: any) => 
      useApiFetch(`/returns/${id}`, {
        method: 'PUT',
        body: data,
      }),

    /**
     * Delete return
     */
    delete: (id: number) => 
      useApiFetch(`/returns/${id}`, { method: 'DELETE' }),
  },

  // ============================================
  // REPORTS
  // ============================================
  reports: {
    /**
     * Get dashboard overview
     */
    getDashboard: () => 
      useApiFetch<{ success: boolean; data: any }>('/reports/dashboard'),

    /**
     * Get sales report
     */
    sales: (params?: any) => 
      useApiFetch('/reports/sales', { params }),

    /**
     * Get top products report
     */
    topProducts: (params?: any) => 
      useApiFetch('/reports/products', { params }),

    /**
     * Get categories report
     */
    categories: (params?: any) => 
      useApiFetch('/reports/sales', { params }),

    /**
     * Get product performance report
     */
    getProducts: (params?: any) => 
      useApiFetch('/reports/products', { params }),

    /**
     * Get customer insights report
     */
    getCustomers: (params?: any) => 
      useApiFetch('/reports/customers', { params }),

    /**
     * Get inventory report
     */
    getInventory: (params?: any) => 
      useApiFetch('/reports/inventory', { params }),

    /**
     * Get financial summary (Admin only)
     */
    getFinancial: (params?: any) => 
      useApiFetch('/reports/financial', { params }),

    /**
     * Get comparative report (Admin only)
     */
    getComparative: (params: any) => 
      useApiFetch('/reports/comparative', { params }),
  },
}
