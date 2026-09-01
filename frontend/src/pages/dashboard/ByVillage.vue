<template>
  <div class="space-y-5 font-sans !pointer-events-none">
    <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl pointer-events-auto">
      <h1 class="text-lg font-bold text-slate-900 dark:text-white">Distribusi UMKM per Kelurahan</h1>
      <p class="text-slate-500 dark:text-[#6F767E] text-sm mt-1">Perbandingan jumlah usaha kuliner aktif di setiap kelurahan Sungailiat.</p>
    </div>

    <div v-if="error" class="p-4 bg-red-50 dark:bg-red-500/10 border-l-4 border-red-500 text-red-700 dark:text-red-400 text-sm rounded-lg pointer-events-auto">
      {{ error }}
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 pointer-events-auto">
      <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl lg:col-span-2">
        <h3 class="font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-[#2A2E33] pb-4 mb-5">Grafik Distribusi</h3>
        
        <div v-if="loading" class="space-y-4 py-8">
          <div class="h-6 bg-slate-100 dark:bg-[#22262A] animate-pulse rounded" v-for="i in 5" :key="i"></div>
        </div>

        <div v-else-if="items.length === 0" class="text-center py-12 text-slate-400 dark:text-[#6F767E]">
          Tidak ada data wilayah tersedia.
        </div>

        <div v-else class="h-[420px]">
          <Bar :data="chartData" :options="chartOptions" />
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl">
        <h3 class="font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-[#2A2E33] pb-4 mb-5">Tabel Kontribusi</h3>
        
        <div v-if="loading" class="space-y-4">
          <div class="h-10 bg-slate-100 dark:bg-[#22262A] animate-pulse rounded" v-for="i in 3" :key="i"></div>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-slate-200 dark:border-[#2A2E33] text-slate-400 dark:text-[#6F767E] font-bold">
                <th class="pb-2">Kelurahan</th>
                <th class="pb-2 text-right">Jumlah</th>
                <th class="pb-2 text-right">Persen</th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="item in sortedItems" 
                :key="item.id"
                class="border-b border-slate-100 dark:border-[#2A2E33] hover:bg-slate-50 dark:hover:bg-[#22262A] transition-colors"
              >
                <td class="py-3 font-medium text-slate-800 dark:text-white">
                  <RouterLink :to="'/villages/' + item.id" class="text-[#D97706] dark:text-[#F59E0B] hover:underline">
                    {{ item.name }}
                  </RouterLink>
                </td>
                <td class="py-3 text-right font-bold text-slate-900 dark:text-white">{{ item.umkm_count }}</td>
                <td class="py-3 text-right text-slate-500 dark:text-[#6F767E]">{{ getPercentage(item.umkm_count) }}%</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { RouterLink } from 'vue-router';
import { Bar } from 'vue-chartjs';
import {
  BarElement,
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  Tooltip,
} from 'chart.js';
import api from '../../services/api';

ChartJS.register(BarElement, CategoryScale, LinearScale, Tooltip, Legend);

interface VillageStatItem {
  id: number;
  name: string;
  umkm_count: number;
  population?: number;
}

const items = ref<VillageStatItem[]>([]);
const loading = ref(true);
const error = ref('');

const sortedItems = computed(() => {
  return [...items.value].sort((a, b) => b.umkm_count - a.umkm_count);
});

const totalCount = computed(() => {
  return items.value.reduce((sum, item) => sum + item.umkm_count, 0);
});

const chartData = computed(() => ({
  labels: sortedItems.value.map(item => item.name),
  datasets: [{
    label: 'Jumlah UMKM',
    data: sortedItems.value.map(item => item.umkm_count),
    backgroundColor: '#F59E0B',
    hoverBackgroundColor: '#D97706',
    borderRadius: 8,
    borderSkipped: false,
    barPercentage: 0.7,
    categoryPercentage: 0.8,
  }],
}));

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      callbacks: {
        label: (context: any) => ` ${context.parsed.y} UMKM`,
      },
    },
  },
  scales: {
    x: {
      grid: { display: false },
      ticks: {
        autoSkip: false,
        maxRotation: 45,
        minRotation: 45,
      },
    },
    y: {
      beginAtZero: true,
      ticks: {
        precision: 0,
      },
      grid: {
        color: 'rgba(148, 163, 184, 0.15)',
      },
    },
  },
}));

async function fetchData() {
  loading.value = true;
  error.value = '';
  try {
    const response = await api.get('/dashboard/by-village');
    items.value = response.data.data || [];
  } catch (err: any) {
    console.error('Error fetching village statistics:', err);
    error.value = 'Gagal memuat data kelurahan. Pastikan server backend berjalan.';
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
