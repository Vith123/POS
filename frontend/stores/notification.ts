/**
 * Notification Store
 * 
 * Manages application notifications/toasts:
 * - Success messages
 * - Error messages
 * - Warning messages
 * - Info messages
 * 
 * Usage:
 * const notificationStore = useNotificationStore()
 * notificationStore.success('Product created successfully!')
 */

import { defineStore } from 'pinia'
import type { Notification } from '~/types'

interface NotificationState {
  notifications: Notification[]
}

export const useNotificationStore = defineStore('notification', {
  state: (): NotificationState => ({
    notifications: [],
  }),

  actions: {
    /**
     * Add a notification
     */
    add(notification: Omit<Notification, 'id'>) {
      const id = Date.now().toString()
      const newNotification: Notification = {
        id,
        duration: 5000, // Default 5 seconds
        ...notification,
      }
      
      this.notifications.push(newNotification)
      
      // Auto remove after duration
      if (newNotification.duration) {
        setTimeout(() => {
          this.remove(id)
        }, newNotification.duration)
      }
    },

    /**
     * Remove a notification by ID
     */
    remove(id: string) {
      const index = this.notifications.findIndex(n => n.id === id)
      if (index > -1) {
        this.notifications.splice(index, 1)
      }
    },

    /**
     * Success notification
     */
    success(title: string, message: string = '') {
      this.add({ type: 'success', title, message })
    },

    /**
     * Error notification
     */
    error(title: string, message: string = '') {
      this.add({ type: 'error', title, message })
    },

    /**
     * Warning notification
     */
    warning(title: string, message: string = '') {
      this.add({ type: 'warning', title, message })
    },

    /**
     * Info notification
     */
    info(title: string, message: string = '') {
      this.add({ type: 'info', title, message })
    },

    /**
     * Clear all notifications
     */
    clear() {
      this.notifications = []
    },
  },
})
