<template>
  <RouterView />
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { RouterView } from 'vue-router';
import { useTheme } from './composables/useTheme';

const { initTheme } = useTheme();

onMounted(() => {
  initTheme();
});
</script>

<style>
/* Minimal global resets only - everything else is Tailwind */
html, body, #app {
  margin: 0;
  padding: 0;
  width: 100%;
  height: 100%;
  --font-sans: 'Roboto', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
  font-family: var(--font-sans);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Leaflet requires !important overrides since it uses inline styles */
.leaflet-popup-content-wrapper {
  border-radius: 12px !important;
  padding: 0 !important;
  overflow: hidden;
  box-shadow: 0 8px 32px rgb(0 0 0 / 0.25) !important;
}
.leaflet-popup-content {
  margin: 0 !important;
  line-height: inherit !important;
}
.leaflet-popup-tip {
  box-shadow: none !important;
}
.leaflet-bar {
  border: none !important;
  box-shadow: none !important;
  border-radius: 8px !important;
  overflow: hidden;
}
.custom-umkm-icon {
  background: none !important;
  border: none !important;
}

/* Dark mode Leaflet overrides */
.dark .leaflet-popup-content-wrapper {
  background-color: #1A1D1F !important;
  border: 1px solid #2A2E33 !important;
  color: #F0F0F0 !important;
}
.dark .leaflet-popup-tip {
  background: #1A1D1F !important;
  border: 1px solid #2A2E33 !important;
}
.dark .leaflet-bar a {
  background-color: #1A1D1F !important;
  color: #F0F0F0 !important;
  border-bottom: 1px solid #2A2E33 !important;
}
.dark .leaflet-bar a:hover {
  background-color: #22262A !important;
}

/* Scrollbar */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
.dark ::-webkit-scrollbar-thumb { background: #2A2E33; }

.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
