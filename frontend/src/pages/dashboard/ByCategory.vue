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

      <!-- Ringkasan yang Dirombak dan Diperkaya -->
      <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl flex flex-col justify-between">
        <div>
          <h3 class="font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-[#2A2E33] pb-4 mb-5 flex items-center space-x-2">
            <PieChart class="w-4 h-4 text-[#F59E0B]" />
            <span>Ringkasan Komprehensif</span>
          </h3>
          
          <div v-if="loading" class="space-y-4">
            <div class="h-16 bg-slate-100 dark:bg-[#22262A] animate-pulse rounded-xl" v-for="i in 3" :key="i"></div>
          </div>

          <div v-else class="space-y-4">
            <!-- Card 1: Kategori Terbanyak -->
            <div class="p-3.5 bg-gradient-to-br from-amber-500/10 to-transparent border border-amber-500/20 rounded-xl">
              <span class="block text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Kategori Terbanyak</span>
              <h4 class="text-base font-extrabold text-slate-800 dark:text-white mt-1">{{ topCategory?.category || '-' }}</h4>
              <div class="flex justify-between items-center mt-2 text-xs">
                <span class="text-slate-500 dark:text-[#6F767E] font-medium">Jumlah Unit Usaha</span>
                <span class="font-bold text-slate-700 dark:text-white">{{ topCategory?.count || 0 }} UMKM ({{ getPercentage(topCategory?.count || 0) }}%)</span>
              </div>
            </div>

            <!-- Card 2: Total Kategori & Diversifikasi -->
            <div class="p-3.5 bg-slate-50 dark:bg-[#111315] border border-slate-200/50 dark:border-[#2A2E33] rounded-xl">
              <span class="block text-[10px] font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Total Ragam Kategori</span>
              <div class="flex items-baseline justify-between mt-1">
                <h4 class="text-lg font-extrabold text-slate-800 dark:text-white">{{ items.length }} Kategori</h4>
                <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-0.5 rounded">Diversifikasi Baik</span>
              </div>
            </div>

            <!-- Card 3: Rata-rata per Kategori -->
            <div class="p-3.5 bg-slate-50 dark:bg-[#111315] border border-slate-200/50 dark:border-[#2A2E33] rounded-xl">
              <span class="block text-[10px] font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Rata-rata Usaha per Kategori</span>
              <div class="flex items-baseline justify-between mt-1">
                <h4 class="text-lg font-extrabold text-slate-800 dark:text-white">{{ averagePerCategory }} UMKM</h4>
                <span class="text-[11px] text-slate-500 dark:text-[#6F767E]">Dari total {{ totalCount }} UMKM</span>
              </div>
            </div>

            <!-- Card 4: Kategori Terendah / Minoritas -->
            <div v-if="bottomCategory" class="p-3.5 bg-slate-50 dark:bg-[#111315] border border-slate-200/50 dark:border-[#2A2E33] rounded-xl">
              <span class="block text-[10px] font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Kategori Minoritas</span>
              <div class="flex items-baseline justify-between mt-1">
                <h4 class="text-sm font-bold text-slate-800 dark:text-white truncate max-w-[140px]">{{ bottomCategory.category }}</h4>
                <span class="text-xs font-bold text-slate-700 dark:text-white">{{ bottomCategory.count }} UMKM</span>
              </div>
            </div>
          </div>
        </div>

      
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { RouterLink } from 'vue-router';
import { PieChart } from 'lucide-vue-next';
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

const bottomCategory = computed(() => {
  if (sortedItems.value.length < 2) return null;
  return sortedItems.value[sortedItems.value.length - 1] || null;
});

const averagePerCategory = computed(() => {
  if (items.value.length === 0) return 0;
  return (totalCount.value / items.value.length).toFixed(1);
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
