<!--
  Confirmation Dialog Component
  
  Simple confirmation dialog for delete actions
  
  Props:
  - show: boolean - Show/hide dialog
  - title: string - Dialog title
  - message: string - Confirmation message
  - confirmText: string - Confirm button text
  - loading: boolean - Show loading state
  
  Emits:
  - confirm: When confirmed
  - cancel: When cancelled
-->

<template>
  <Modal :show="show" :title="title" size="sm" @close="$emit('cancel')">
    <div class="py-4">
      <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
          <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <div>
          <p class="text-gray-700">{{ message }}</p>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end gap-3">
        <button
          @click="$emit('cancel')"
          class="btn btn-secondary"
          :disabled="loading"
        >
          Cancel
        </button>
        <button
          @click="$emit('confirm')"
          class="btn btn-danger"
          :disabled="loading"
        >
          <LoadingSpinner v-if="loading" size="sm" class="mr-2" />
          {{ confirmText }}
        </button>
      </div>
    </template>
  </Modal>
</template>

<script setup lang="ts">
interface Props {
  show: boolean
  title?: string
  message: string
  confirmText?: string
  loading?: boolean
}

withDefaults(defineProps<Props>(), {
  title: 'Confirm Action',
  confirmText: 'Confirm',
  loading: false
})

defineEmits<{
  confirm: []
  cancel: []
}>()
</script>
