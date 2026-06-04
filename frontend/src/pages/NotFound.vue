<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#111315] flex flex-col items-center justify-center text-center p-8 font-sans">
    <div class="text-7xl font-extrabold text-[#F59E0B] mb-4">404</div>
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Halaman Tidak Ditemukan</h1>
    <p class="text-slate-500 dark:text-[#6F767E] text-sm mb-8 max-w-md">Halaman yang Anda cari mungkin sudah dipindahkan, dihapus, atau alamat URL tidak valid.</p>
    <div class="flex space-x-3">
      <RouterLink to="/" class="px-5 py-2.5 bg-[#F59E0B] hover:bg-[#D97706] text-[#111315] font-bold text-sm rounded-xl transition-colors shadow-lg shadow-[#F59E0B]/20">
        Kembali ke Peta
      </RouterLink>
      <RouterLink to="/dashboard" class="px-5 py-2.5 border border-slate-200 dark:border-[#2A2E33] text-slate-700 dark:text-[#F0F0F0] bg-white dark:bg-[#1A1D1F] hover:bg-slate-50 dark:hover:bg-[#22262A] font-semibold text-sm rounded-xl transition-colors">
        Dashboard
      </RouterLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router';
</script>
