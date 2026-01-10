<!--
  Modal Component
  
  Reusable modal dialog for forms and confirmations
  
  Props:
  - show: boolean - Show/hide modal
  - title: string - Modal title
  - size: 'sm' | 'md' | 'lg' | 'xl' - Modal size
  
  Emits:
  - close: When modal is closed
  
  Usage:
  <Modal :show="showModal" title="Add Product" @close="showModal = false">
    <p>Modal content here</p>
  </Modal>
-->

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="$emit('close')"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div
            class="relative bg-white rounded-lg shadow-xl w-full"
            :class="sizeClass"
          >
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
              <button
                @click="$emit('close')"
                class="text-gray-400 hover:text-gray-600 transition-colors"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
              <slot />
            </div>

            <!-- Footer (optional) -->
            <div v-if="$slots.footer" class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
              <slot name="footer" />
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
interface Props {
  show: boolean
  title: string
  size?: 'sm' | 'md' | 'lg' | 'xl'
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md'
})

defineEmits<{
  close: []
}>()

const sizeClass = computed(() => {
  switch (props.size) {
    case 'sm': return 'max-w-md'
    case 'md': return 'max-w-lg'
    case 'lg': return 'max-w-2xl'
    case 'xl': return 'max-w-4xl'
    default: return 'max-w-lg'
  }
})

// Prevent body scroll when modal is open
watch(() => props.show, (show) => {
  if (process.client) {
    document.body.style.overflow = show ? 'hidden' : ''
  }
})
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
  transition: transform 0.3s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
  transform: scale(0.95);
}
</style>
