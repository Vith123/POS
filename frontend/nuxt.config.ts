// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-04-03',
  devtools: { enabled: true },

  // Modules - Tailwind CSS, Pinia, i18n, and Color Mode
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    '@nuxtjs/i18n',
    '@nuxtjs/color-mode',
  ],

  // i18n Configuration
  i18n: {
    locales: ['en', 'km', 'zh'],
    defaultLocale: 'en',
    vueI18n: './i18n.config.ts',
  },

  // Color Mode Configuration
  colorMode: {
    classSuffix: '',
    preference: 'light',
    fallback: 'light',
  },

  // Runtime config - API URL can be changed via environment variables
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api',
    }
  },

  // App configuration
  app: {
    head: {
      title: 'POS System',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'Modern Point of Sale System' }
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
      ]
    }
  },

  // CSS configuration
  css: [
    '~/assets/css/main.css',
  ],

  // Tailwind CSS configuration
  tailwindcss: {
    cssPath: '~/assets/css/main.css',
    configPath: 'tailwind.config.js',
  },
})
