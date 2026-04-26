import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig(({ command }) => ({
  plugins: [vue()],
  root: '.',
  base: command === 'build' ? '/build/' : '/',
  publicDir: false,
  build: {
    outDir: 'public/build',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: resolve(__dirname, 'assets/app.ts'),
    },
  },
  server: {
    port: 5173,
    strictPort: true,
    cors: true,
    host: true,
  },
  resolve: {
    alias: {
      '@': resolve(__dirname, 'assets'),
    },
  },
  test: {
    environment: 'jsdom',
    globals: true,
  },
}))
