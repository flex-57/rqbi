// vite.config.ts
import { defineConfig } from "file:///C:/Users/flex5/source/repos/RQBI/node_modules/vite/dist/node/index.js";
import vue from "file:///C:/Users/flex5/source/repos/RQBI/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import { resolve } from "path";
var __vite_injected_original_dirname = "C:\\Users\\flex5\\source\\repos\\RQBI";
var vite_config_default = defineConfig(({ command }) => ({
  plugins: [vue()],
  root: ".",
  base: command === "build" ? "/build/" : "/",
  publicDir: false,
  build: {
    outDir: "public/build",
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: resolve(__vite_injected_original_dirname, "assets/app.ts")
    }
  },
  server: {
    port: 5173,
    strictPort: true,
    cors: true
  },
  resolve: {
    alias: {
      "@": resolve(__vite_injected_original_dirname, "assets")
    }
  },
  test: {
    environment: "jsdom",
    globals: true
  }
}));
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcudHMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxVc2Vyc1xcXFxmbGV4NVxcXFxzb3VyY2VcXFxccmVwb3NcXFxcUlFCSVwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiQzpcXFxcVXNlcnNcXFxcZmxleDVcXFxcc291cmNlXFxcXHJlcG9zXFxcXFJRQklcXFxcdml0ZS5jb25maWcudHNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL0M6L1VzZXJzL2ZsZXg1L3NvdXJjZS9yZXBvcy9SUUJJL3ZpdGUuY29uZmlnLnRzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSdcbmltcG9ydCB2dWUgZnJvbSAnQHZpdGVqcy9wbHVnaW4tdnVlJ1xuaW1wb3J0IHsgcmVzb2x2ZSB9IGZyb20gJ3BhdGgnXG5cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZygoeyBjb21tYW5kIH0pID0+ICh7XG4gIHBsdWdpbnM6IFt2dWUoKV0sXG4gIHJvb3Q6ICcuJyxcbiAgYmFzZTogY29tbWFuZCA9PT0gJ2J1aWxkJyA/ICcvYnVpbGQvJyA6ICcvJyxcbiAgcHVibGljRGlyOiBmYWxzZSxcbiAgYnVpbGQ6IHtcbiAgICBvdXREaXI6ICdwdWJsaWMvYnVpbGQnLFxuICAgIGVtcHR5T3V0RGlyOiB0cnVlLFxuICAgIG1hbmlmZXN0OiB0cnVlLFxuICAgIHJvbGx1cE9wdGlvbnM6IHtcbiAgICAgIGlucHV0OiByZXNvbHZlKF9fZGlybmFtZSwgJ2Fzc2V0cy9hcHAudHMnKSxcbiAgICB9LFxuICB9LFxuICBzZXJ2ZXI6IHtcbiAgICBwb3J0OiA1MTczLFxuICAgIHN0cmljdFBvcnQ6IHRydWUsXG4gICAgY29yczogdHJ1ZSxcbiAgfSxcbiAgcmVzb2x2ZToge1xuICAgIGFsaWFzOiB7XG4gICAgICAnQCc6IHJlc29sdmUoX19kaXJuYW1lLCAnYXNzZXRzJyksXG4gICAgfSxcbiAgfSxcbiAgdGVzdDoge1xuICAgIGVudmlyb25tZW50OiAnanNkb20nLFxuICAgIGdsb2JhbHM6IHRydWUsXG4gIH0sXG59KSlcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBOFIsU0FBUyxvQkFBb0I7QUFDM1QsT0FBTyxTQUFTO0FBQ2hCLFNBQVMsZUFBZTtBQUZ4QixJQUFNLG1DQUFtQztBQUl6QyxJQUFPLHNCQUFRLGFBQWEsQ0FBQyxFQUFFLFFBQVEsT0FBTztBQUFBLEVBQzVDLFNBQVMsQ0FBQyxJQUFJLENBQUM7QUFBQSxFQUNmLE1BQU07QUFBQSxFQUNOLE1BQU0sWUFBWSxVQUFVLFlBQVk7QUFBQSxFQUN4QyxXQUFXO0FBQUEsRUFDWCxPQUFPO0FBQUEsSUFDTCxRQUFRO0FBQUEsSUFDUixhQUFhO0FBQUEsSUFDYixVQUFVO0FBQUEsSUFDVixlQUFlO0FBQUEsTUFDYixPQUFPLFFBQVEsa0NBQVcsZUFBZTtBQUFBLElBQzNDO0FBQUEsRUFDRjtBQUFBLEVBQ0EsUUFBUTtBQUFBLElBQ04sTUFBTTtBQUFBLElBQ04sWUFBWTtBQUFBLElBQ1osTUFBTTtBQUFBLEVBQ1I7QUFBQSxFQUNBLFNBQVM7QUFBQSxJQUNQLE9BQU87QUFBQSxNQUNMLEtBQUssUUFBUSxrQ0FBVyxRQUFRO0FBQUEsSUFDbEM7QUFBQSxFQUNGO0FBQUEsRUFDQSxNQUFNO0FBQUEsSUFDSixhQUFhO0FBQUEsSUFDYixTQUFTO0FBQUEsRUFDWDtBQUNGLEVBQUU7IiwKICAibmFtZXMiOiBbXQp9Cg==
