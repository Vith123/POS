<!--
  Notification Toast Component
  
  Displays notification messages at the top-right of the screen
  Auto-dismisses after a set duration
-->

<template>
  <div class="fixed top-4 right-4 z-50 space-y-2 max-w-sm">
    <TransitionGroup name="notification">
      <div
        v-for="notification in notificationStore.notifications"
        :key="notification.id"
        class="bg-white rounded-lg shadow-lg border-l-4 p-4 flex items-start gap-3"
        :class="borderColor(notification.type)"
      >
        <!-- Icon -->
        <div class="flex-shrink-0">
          <svg
            v-if="notification.type === 'success'"
            class="w-6 h-6 text-green-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <svg
            v-else-if="notification.type === 'error'"
            class="w-6 h-6 text-red-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <svg
            v-else-if="notification.type === 'warning'"
            class="w-6 h-6 text-yellow-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <svg
            v-else
            class="w-6 h-6 text-blue-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>

        <!-- Content -->
        <div class="flex-1">
          <h4 class="font-semibold text-gray-900">{{ notification.title }}</h4>
          <p v-if="notification.message" class="text-sm text-gray-600 mt-1">
            {{ notification.message }}
          </p>
        </div>

        <!-- Close Button -->
        <button
          @click="notificationStore.remove(notification.id)"
          class="flex-shrink-0 text-gray-400 hover:text-gray-600"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup lang="ts">
const notificationStore = useNotificationStore()

const borderColor = (type: string) => {
  switch (type) {
    case 'success': return 'border-green-500'
    case 'error': return 'border-red-500'
    case 'warning': return 'border-yellow-500'
    default: return 'border-blue-500'
  }
}
</script>

<style scoped>
.notification-enter-active,
.notification-leave-active {
  transition: all 0.3s ease;
}

.notification-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.notification-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>
