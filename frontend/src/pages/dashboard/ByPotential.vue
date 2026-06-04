<template>
  <div class="space-y-5 font-sans !pointer-events-none">
    <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl pointer-events-auto">
      <h1 class="text-lg font-bold text-slate-900 dark:text-white">Analisis Potensi Ekonomi</h1>
      <p class="text-slate-500 dark:text-[#6F767E] text-sm mt-1">Perbandingan UMKM berdasarkan tingkat potensi spasial dan ekonominya.</p>
    </div>

    <div v-if="error" class="p-4 bg-red-50 dark:bg-red-500/10 border-l-4 border-red-500 text-red-700 dark:text-red-400 text-sm rounded-lg pointer-events-auto">{{ error }}</div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 pointer-events-auto">
      <!-- Tinggi -->
      <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 rounded-2xl p-5 shadow-xl flex flex-col justify-between">
        <div>
          <div class="flex items-center space-x-2">
            <span class="h-4 w-4 bg-emerald-500 rounded-full"></span>
            <h3 class="font-bold text-slate-800 dark:text-white">Potensi Tinggi</h3>
          </div>
          <p class="text-xs text-slate-500 dark:text-[#6F767E] mt-2">Dekat jalan utama dan pusat kegiatan ekonomi.</p>
          <div class="text-3xl font-extrabold text-slate-900 dark:text-white mt-5">
            {{ loading ? '...' : getCount('tinggi', '1') }}
            <span class="text-sm font-normal text-slate-500 dark:text-[#6F767E]">UMKM</span>
          </div>
        </div>
        <div class="pt-5 border-t border-slate-100 dark:border-[#2A2E33] mt-5">
          <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 rounded-lg">Kontribusi: {{ getPercentage(getCount('tinggi', '1')) }}%</span>
        </div>
      </div>

      <!-- Sedang -->
      <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 rounded-2xl p-5 shadow-xl flex flex-col justify-between">
        <div>
          <div class="flex items-center space-x-2">
            <span class="h-4 w-4 bg-amber-500 rounded-full"></span>
            <h3 class="font-bold text-slate-800 dark:text-white">Potensi Sedang</h3>
          </div>
          <p class="text-xs text-slate-500 dark:text-[#6F767E] mt-2">Dekat pemukiman, jarak sedang ke jalan utama.</p>
          <div class="text-3xl font-extrabold text-slate-900 dark:text-white mt-5">
            {{ loading ? '...' : getCount('sedang', '2') }}
            <span class="text-sm font-normal text-slate-500 dark:text-[#6F767E]">UMKM</span>
          </div>
        </div>
        <div class="pt-5 border-t border-slate-100 dark:border-[#2A2E33] mt-5">
          <span class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 px-2.5 py-1 rounded-lg">Kontribusi: {{ getPercentage(getCount('sedang', '2')) }}%</span>
        </div>
      </div>

      <!-- Rendah -->
      <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 rounded-2xl p-5 shadow-xl flex flex-col justify-between">
        <div>
          <div class="flex items-center space-x-2">
            <span class="h-4 w-4 bg-rose-500 rounded-full"></span>
            <h3 class="font-bold text-slate-800 dark:text-white">Potensi Rendah</h3>
          </div>
          <p class="text-xs text-slate-500 dark:text-[#6F767E] mt-2">Jauh dari jalan utama dan pusat perniagaan.</p>
          <div class="text-3xl font-extrabold text-slate-900 dark:text-white mt-5">
            {{ loading ? '...' : getCount('rendah', '3') }}
            <span class="text-sm font-normal text-slate-500 dark:text-[#6F767E]">UMKM</span>
          </div>
        </div>
        <div class="pt-5 border-t border-slate-100 dark:border-[#2A2E33] mt-5">
          <span class="text-xs font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 px-2.5 py-1 rounded-lg">Kontribusi: {{ getPercentage(getCount('rendah', '3')) }}%</span>
        </div>
      </div>
    </div>

    <!-- Formula -->
    <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl">
      <h3 class="font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-100 dark:border-[#2A2E33] pb-3">Model Pembobotan Spasial</h3>
      <div class="bg-slate-50 dark:bg-[#111315] p-4 rounded-xl border border-slate-100 dark:border-[#2A2E33] font-mono text-xs md:text-sm text-slate-700 dark:text-[#F0F0F0] leading-relaxed">
        Skor = (40% × Jalan Utama) + (30% × Pusat Niaga) + (20% × Pemukiman) + (10% × Kepadatan)
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import api from '../../services/api';

interface PotentialItem {
  level: string;
  count: number;
}

const items = ref<PotentialItem[]>([]);
const loading = ref(true);
const error = ref('');

const totalCount = computed(() => {
  return items.value.reduce((sum, item) => sum + item.count, 0);
});

async function fetchData() {
  loading.value = true;
  error.value = '';
  try {
    const response = await api.get('/dashboard/by-potential');
    items.value = response.data.data || [];
  } catch (err: any) {
    console.error('Error fetching potential stats:', err);
    error.value = 'Gagal memuat data potensi ekonomi.';
  } finally {
    loading.value = false;
  }
}

function getCount(keyStr: string, keyNum: string): number {
  const match = items.value.find(
    item => String(item.level).toLowerCase() === keyStr || String(item.level) === keyNum
  );
  return match ? match.count : 0;
}

function getPercentage(count: number): number {
  if (totalCount.value === 0) return 0;
  return Math.round((count / totalCount.value) * 100);
}

onMounted(() => {
  fetchData();
});
</script>
