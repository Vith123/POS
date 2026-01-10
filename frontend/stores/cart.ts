/**
 * Cart Store
 * 
 * Manages shopping cart for POS sales:
 * - Add/remove items
 * - Update quantities
 * - Calculate totals
 * - Apply discounts
 * 
 * Usage:
 * const cartStore = useCartStore()
 * cartStore.addItem(product)
 */

import { defineStore } from 'pinia'
import type { Product } from '~/types'

interface CartItem {
  product: Product
  quantity: number
  price: number
  discount: number
  subtotal: number
}

interface CartState {
  items: CartItem[]
  customer_id: number | null
  discount: number
  tax_rate: number
}

export const useCartStore = defineStore('cart', {
  state: (): CartState => ({
    items: [],
    customer_id: null,
    discount: 0,
    tax_rate: 10, // 10% default tax rate
  }),

  getters: {
    /**
     * Calculate subtotal (before tax and discount)
     */
    subtotal: (state): number => {
      return state.items.reduce((sum, item) => sum + item.subtotal, 0)
    },

    /**
     * Calculate total discount amount
     */
    discountAmount(): number {
      const itemDiscounts = this.items.reduce((sum, item) => sum + item.discount, 0)
      const cartDiscount = (this.subtotal * this.discount) / 100
      return itemDiscounts + cartDiscount
    },

    /**
     * Calculate tax amount
     */
    taxAmount(): number {
      const taxableAmount = this.subtotal - this.discountAmount
      return (taxableAmount * this.tax_rate) / 100
    },

    /**
     * Calculate final total
     */
    total(): number {
      return this.subtotal - this.discountAmount + this.taxAmount
    },

    /**
     * Get total items count
     */
    itemCount: (state): number => {
      return state.items.reduce((sum, item) => sum + item.quantity, 0)
    },

    /**
     * Check if cart is empty
     */
    isEmpty: (state): boolean => {
      return state.items.length === 0
    },
  },

  actions: {
    /**
     * Add product to cart
     */
    addItem(product: Product, quantity: number = 1) {
      // Check if product already in cart
      const existingItem = this.items.find(item => item.product.id === product.id)
      
      if (existingItem) {
        // Update quantity
        this.updateQuantity(product.id, existingItem.quantity + quantity)
      } else {
        // Add new item
        this.items.push({
          product,
          quantity,
          price: product.price,
          discount: 0,
          subtotal: product.price * quantity,
        })
      }
    },

    /**
     * Remove item from cart
     */
    removeItem(productId: number) {
      const index = this.items.findIndex(item => item.product.id === productId)
      if (index > -1) {
        this.items.splice(index, 1)
      }
    },

    /**
     * Update item quantity
     */
    updateQuantity(productId: number, quantity: number) {
      const item = this.items.find(item => item.product.id === productId)
      if (item) {
        if (quantity <= 0) {
          this.removeItem(productId)
        } else {
          item.quantity = quantity
          item.subtotal = item.price * quantity
        }
      }
    },

    /**
     * Update item discount
     */
    updateItemDiscount(productId: number, discount: number) {
      const item = this.items.find(item => item.product.id === productId)
      if (item) {
        item.discount = discount
      }
    },

    /**
     * Set customer for the sale
     */
    setCustomer(customerId: number | null) {
      this.customer_id = customerId
    },

    /**
     * Set cart-wide discount percentage
     */
    setDiscount(discount: number) {
      this.discount = discount
    },

    /**
     * Set tax rate
     */
    setTaxRate(rate: number) {
      this.tax_rate = rate
    },

    /**
     * Clear entire cart
     */
    clear() {
      this.items = []
      this.customer_id = null
      this.discount = 0
    },

    /**
     * Get cart data for sale creation
     */
    getSaleData(paymentMethod: string = 'cash') {
      return {
        customer_id: this.customer_id,
        items: this.items.map(item => ({
          product_id: item.product.id,
          quantity: item.quantity,
          price: item.price, // Backend expects 'price' not 'unit_price'
        })),
        discount_type: this.discount > 0 ? 'percentage' : null,
        discount_value: this.discount,
        tax_rate: this.tax_rate,
        payments: [{
          method: paymentMethod,
          amount: this.total,
        }],
      }
    },
  },
})
