/**
 * TypeScript Type Definitions
 * 
 * Define all data types used in the application
 * This helps with type safety and IDE autocomplete
 */

// ============================================
// USER & AUTHENTICATION
// ============================================

export interface User {
  id: number
  name: string
  email: string
  role_id: number
  role?: Role
  is_active: boolean
  created_at: string
  updated_at: string
}

export interface Role {
  id: number
  name: string
  description: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface AuthResponse {
  success: boolean
  data: {
    user: User
    token: string
  }
  message: string
}

// ============================================
// PRODUCTS & CATEGORIES
// ============================================

export interface Product {
  id: number
  name: string
  sku: string
  barcode?: string
  description?: string
  category_id: number
  category?: Category
  price: number
  cost: number
  stock_quantity: number
  min_stock_level: number
  image_url?: string
  is_active: boolean
  created_at: string
  updated_at: string
}

export interface Category {
  id: number
  name: string
  description?: string
  is_active: boolean
  products_count?: number
  created_at: string
  updated_at: string
}

// ============================================
// SALES
// ============================================

export interface Sale {
  id: number
  sale_number: string
  customer_id?: number
  customer?: Customer
  user_id: number
  user?: User
  total_amount: number
  tax_amount: number
  discount_amount: number
  final_amount: number
  payment_method: 'cash' | 'card' | 'mobile_money' | 'bank_transfer'
  status: 'completed' | 'pending' | 'cancelled'
  notes?: string
  sale_items?: SaleItem[]
  created_at: string
  updated_at: string
}

export interface SaleItem {
  id: number
  sale_id: number
  product_id: number
  product?: Product
  quantity: number
  unit_price: number
  subtotal: number
  discount: number
  tax: number
  total: number
}

// ============================================
// CUSTOMERS
// ============================================

export interface Customer {
  id: number
  name: string
  email: string
  phone: string
  address?: string
  loyalty_points: number
  total_purchases?: number
  last_purchase_date?: string
  is_vip: boolean
  is_active: boolean
  created_at: string
  updated_at: string
}

// ============================================
// INVENTORY
// ============================================

export interface Inventory {
  id: number
  product_id: number
  product?: Product
  quantity: number
  min_stock_level: number
  last_restock_date?: string
  created_at: string
  updated_at: string
}

export interface StockMovement {
  id: number
  product_id: number
  product?: Product
  type: 'in' | 'out' | 'adjustment'
  quantity: number
  reference_type?: string
  reference_id?: number
  notes?: string
  user_id: number
  user?: User
  created_at: string
}

// ============================================
// RETURNS
// ============================================

export interface ProductReturn {
  id: number
  return_number: string
  sale_id: number
  sale?: Sale
  product_id: number
  product?: Product
  quantity: number
  reason: string
  refund_amount: number
  status: 'pending' | 'approved' | 'rejected'
  processed_by?: number
  processed_at?: string
  notes?: string
  created_at: string
  updated_at: string
}

// Type alias for convenience
export type Return = ProductReturn

// ============================================
// REPORTS
// ============================================

export interface DashboardStats {
  today: {
    sales_count: number
    revenue: number
    tax: number
    discount: number
  }
  week: {
    sales_count: number
    revenue: number
    tax: number
    discount: number
  }
  month: {
    sales_count: number
    revenue: number
    tax: number
    discount: number
  }
  inventory: {
    total_products: number
    low_stock: number
    out_of_stock: number
  }
  customers: {
    total: number
    vip: number
    regular: number
  }
  pending_actions: {
    returns_to_process: number
  }
}

export interface Report {
  [key: string]: any
}

// ============================================
// UI TYPES
// ============================================

export interface TableColumn {
  key: string
  label: string
  sortable?: boolean
  class?: string
}

export interface Notification {
  id: string
  type: 'success' | 'error' | 'warning' | 'info'
  title: string
  message: string
  duration?: number
}

export interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}
