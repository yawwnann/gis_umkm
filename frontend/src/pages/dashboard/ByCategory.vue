<template>
  <div class="space-y-5 font-sans !pointer-events-none">
    <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl pointer-events-auto">
      <h1 class="text-lg font-bold text-slate-900 dark:text-white">Statistik Kategori Usaha</h1>
      <p class="text-slate-500 dark:text-[#6F767E] text-sm mt-1">Distribusi dan komposisi kategori UMKM kuliner di wilayah Sungailiat.</p>
    </div>

    <div v-if="error" class="p-4 bg-red-50 dark:bg-red-500/10 border-l-4 border-red-500 text-red-700 dark:text-red-400 text-sm rounded-lg pointer-events-auto">{{ error }}</div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 pointer-events-auto">
      <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl lg:col-span-2">
        <h3 class="font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-[#2A2E33] pb-4 mb-5">Distribusi Kategori</h3>
        
        <div v-if="loading" class="space-y-4 py-8">
          <div class="h-6 bg-slate-100 dark:bg-[#22262A] animate-pulse rounded" v-for="i in 5" :key="i"></div>
        </div>

        <div v-else-if="items.length === 0" class="text-center py-12 text-slate-400 dark:text-[#6F767E]">Tidak ada data kategori tersedia.</div>

        <div v-else class="space-y-4">
          <div v-for="item in sortedItems" :key="item.category" class="space-y-1.5">
            <div class="flex justify-between text-sm">
              <span class="font-medium text-slate-700 dark:text-[#F0F0F0]">{{ item.category }}</span>
              <span class="font-bold text-slate-900 dark:text-white">{{ item.count }} UMKM</span>
            </div>
            <div class="flex items-center space-x-3">
              <div class="flex-1 bg-slate-100 dark:bg-[#111315] h-5 rounded-lg overflow-hidden">
                <div class="bg-[#F59E0B] h-full transition-all duration-500 rounded-lg" :style="{ width: getPercentage(item.count) + '%' }"></div>
              </div>
              <span class="text-xs text-slate-400 dark:text-[#6F767E] font-medium w-8 text-right">{{ getPercentage(item.count) }}%</span>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl flex flex-col justify-between">
        <div>
          <h3 class="font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-[#2A2E33] pb-4 mb-5">Ringkasan</h3>
          
          <div v-if="loading" class="space-y-4">
            <div class="h-12 bg-slate-100 dark:bg-[#22262A] animate-pulse rounded" v-for="i in 2" :key="i"></div>
          </div>

          <div v-else class="space-y-5">
            <div>
              <span class="text-[10px] font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Kategori Terbanyak</span>
              <h4 class="text-lg font-bold text-slate-800 dark:text-white mt-1">{{ topCategory?.category || '-' }}</h4>
              <p class="text-xs text-slate-500 dark:text-[#6F767E] mt-0.5">{{ topCategory?.count || 0 }} pelaku usaha kuliner.</p>
            </div>
            <div>
              <span class="text-[10px] font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Total Kategori</span>
              <h4 class="text-lg font-bold text-slate-800 dark:text-white mt-1">{{ items.length }} Kategori</h4>
            </div>
          </div>
        </div>

        <div class="pt-5 border-t border-slate-100 dark:border-[#2A2E33] mt-5">
          <RouterLink 
            to="/umkm" 
            class="w-full flex items-center justify-center px-4 py-2 border border-slate-200 dark:border-[#2A2E33] text-xs font-semibold rounded-xl text-slate-700 dark:text-[#F0F0F0] bg-white dark:bg-[#111315] hover:bg-slate-50 dark:hover:bg-[#22262A] transition-colors"
          >
            Filter UMKM Kategori &rarr;
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { RouterLink } from 'vue-router';
import api from '../../services/api';

interface CategoryStatItem {
  category: string;
  count: number;
}

const items = ref<CategoryStatItem[]>([]);
const loading = ref(true);
const error = ref('');

const sortedItems = computed(() => {
  return [...items.value].sort((a, b) => b.count - a.count);
});

const totalCount = computed(() => {
  return items.value.reduce((sum, item) => sum + item.count, 0);
});

const topCategory = computed(() => {
  return sortedItems.value[0] || null;
});

async function fetchData() {
  loading.value = true;
  error.value = '';
  try {
    const response = await api.get('/dashboard/by-category');
    items.value = response.data.data || [];
  } catch (err: any) {
    console.error('Error fetching category statistics:', err);
    error.value = 'Gagal memuat data kategori.';
  } finally {
    loading.value = false;
  }
}

function getPercentage(count: number): number {
  if (totalCount.value === 0) return 0;
  return Math.round((count / totalCount.value) * 100);
}

onMounted(() => {
  fetchData();
});
</script>
