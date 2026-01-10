<!--
  Auth Layout
  
  Simple centered layout for authentication pages (login, register)
-->

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">☕ Coffee POS</h1>
        <p class="text-gray-600">Point of Sale System</p>
      </div>

      <!-- Content -->
      <div class="bg-white rounded-lg shadow-md p-8">
        <slot />
      </div>

      <!-- Footer -->
      <p class="text-center mt-6 text-sm text-gray-500">
        © 2026 Coffee POS
      </p>
    </div>
  </div>
</template>
