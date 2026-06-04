<template>
  <div class="w-full flex flex-col font-sans space-y-4">

    <!-- Header Stats Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
      <div class="bg-amber-500 rounded-xl p-4 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs opacity-80 font-medium">Total UMKM</p>
            <p class="text-2xl font-bold mt-1">{{ loading ? '...' : stats?.total_umkm || 0 }}</p>
          </div>
          <div class="h-10 w-10 bg-white/20 rounded-lg flex items-center justify-center">
            <Store class="h-5 w-5" />
          </div>
        </div>
        <p class="text-[10px] mt-2 opacity-70">UMKM Kuliner Sungailiat</p>
      </div>

      <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 rounded-xl p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[10px] text-slate-400 dark:text-[#6F767E] font-medium uppercase tracking-wider">Kategori</p>
            <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ loading ? '...' : stats?.total_categories || 0 }}</p>
          </div>
          <div class="h-10 w-10 bg-indigo-500/10 rounded-lg flex items-center justify-center">
            <PieChart class="h-5 w-5 text-indigo-500" />
          </div>
        </div>
      </div>

      <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 rounded-xl p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[10px] text-slate-400 dark:text-[#6F767E] font-medium uppercase tracking-wider">Kelurahan</p>
            <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ loading ? '...' : stats?.total_villages || 0 }}</p>
          </div>
          <div class="h-10 w-10 bg-emerald-500/10 rounded-lg flex items-center justify-center">
            <MapPin class="h-5 w-5 text-emerald-500" />
          </div>
        </div>
      </div>

      <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 rounded-xl p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[10px] text-slate-400 dark:text-[#6F767E] font-medium uppercase tracking-wider">Rata-rata Skor</p>
            <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ loading ? '...' : (analysis.summary.avg_potential_score || 0) }}</p>
          </div>
          <div class="h-10 w-10 bg-rose-500/10 rounded-lg flex items-center justify-center">
            <TrendingUp class="h-5 w-5 text-rose-500" />
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

      <!-- Left Column: 2/3 width -->
      <div class="lg:col-span-2 space-y-4">

        <!-- Distribusi Potensi Section -->
        <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 rounded-xl p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center space-x-2">
              <TrendingUp class="h-5 w-5 text-amber-500" />
              <span>Distribusi Potensi UMKM</span>
            </h3>
            <span class="text-xs text-slate-400 dark:text-[#6F767E]">{{ todayLabel }}</span>
          </div>

          <!-- Potential Stats Cards -->
          <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/40 rounded-lg p-3">
              <div class="flex items-center space-x-2 mb-1">
                <span class="h-2.5 w-2.5 bg-emerald-500 rounded-full"></span>
                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">Tinggi</span>
              </div>
              <div class="flex items-baseline justify-between">
                <span class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ stats?.by_potential?.tinggi || 0 }}</span>
                <span class="text-xs text-emerald-600 dark:text-emerald-500">{{ getPercentage(stats?.by_potential?.tinggi) }}%</span>
              </div>
            </div>
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/40 rounded-lg p-3">
              <div class="flex items-center space-x-2 mb-1">
                <span class="h-2.5 w-2.5 bg-amber-500 rounded-full"></span>
                <span class="text-xs font-semibold text-amber-700 dark:text-amber-400">Sedang</span>
              </div>
              <div class="flex items-baseline justify-between">
                <span class="text-xl font-extrabold text-amber-600 dark:text-amber-400">{{ stats?.by_potential?.sedang || 0 }}</span>
                <span class="text-xs text-amber-600 dark:text-amber-500">{{ getPercentage(stats?.by_potential?.sedang) }}%</span>
              </div>
            </div>
            <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/40 rounded-lg p-3">
              <div class="flex items-center space-x-2 mb-1">
                <span class="h-2.5 w-2.5 bg-rose-500 rounded-full"></span>
                <span class="text-xs font-semibold text-rose-700 dark:text-rose-400">Rendah</span>
              </div>
              <div class="flex items-baseline justify-between">
                <span class="text-xl font-extrabold text-rose-600 dark:text-rose-400">{{ stats?.by_potential?.rendah || 0 }}</span>
                <span class="text-xs text-rose-600 dark:text-rose-500">{{ getPercentage(stats?.by_potential?.rendah) }}%</span>
              </div>
            </div>
          </div>

          <!-- Progress Bars -->
          <div class="space-y-2">
            <div class="flex items-center space-x-3">
              <span class="text-xs font-medium text-slate-600 dark:text-[#F0F0F0] w-16">Tinggi</span>
              <div class="flex-1 bg-slate-100 dark:bg-[#111315] h-3 rounded-full overflow-hidden">
                <div class="bg-emerald-500 h-full rounded-full transition-all duration-700" :style="{ width: getPercentage(stats?.by_potential?.tinggi) + '%' }"></div>
              </div>
              <span class="text-xs font-bold text-slate-700 dark:text-white w-20 text-right">{{ stats?.by_potential?.tinggi || 0 }} ({{ getPercentage(stats?.by_potential?.tinggi) }}%)</span>
            </div>
            <div class="flex items-center space-x-3">
              <span class="text-xs font-medium text-slate-600 dark:text-[#F0F0F0] w-16">Sedang</span>
              <div class="flex-1 bg-slate-100 dark:bg-[#111315] h-3 rounded-full overflow-hidden">
                <div class="bg-amber-500 h-full rounded-full transition-all duration-700" :style="{ width: getPercentage(stats?.by_potential?.sedang) + '%' }"></div>
              </div>
              <span class="text-xs font-bold text-slate-700 dark:text-white w-20 text-right">{{ stats?.by_potential?.sedang || 0 }} ({{ getPercentage(stats?.by_potential?.sedang) }}%)</span>
            </div>
            <div class="flex items-center space-x-3">
              <span class="text-xs font-medium text-slate-600 dark:text-[#F0F0F0] w-16">Rendah</span>
              <div class="flex-1 bg-slate-100 dark:bg-[#111315] h-3 rounded-full overflow-hidden">
                <div class="bg-rose-500 h-full rounded-full transition-all duration-700" :style="{ width: getPercentage(stats?.by_potential?.rendah) + '%' }"></div>
              </div>
              <span class="text-xs font-bold text-slate-700 dark:text-white w-20 text-right">{{ stats?.by_potential?.rendah || 0 }} ({{ getPercentage(stats?.by_potential?.rendah) }}%)</span>
            </div>
          </div>
        </div>

        <!-- Analisis per Kelurahan -->
        <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 rounded-xl p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center space-x-2">
              <MapPin class="h-5 w-5 text-amber-500" />
              <span>Analisis per Kelurahan</span>
            </h3>
            <RouterLink to="/dashboard/by-village" class="text-xs text-amber-500 hover:text-amber-600 font-medium flex items-center space-x-1">
              <span>Lihat Detail</span>
              <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </RouterLink>
          </div>

          <div class="space-y-3">
            <div v-for="(item, idx) in sortedVillages.slice(0, 8)" :key="item.id" class="group">
              <div class="flex justify-between items-center mb-1">
                <div class="flex items-center space-x-2">
                  <span class="w-5 text-xs font-bold text-slate-400 dark:text-[#6F767E] text-right">{{ idx + 1 }}</span>
                  <span class="text-sm font-medium text-slate-700 dark:text-[#F0F0F0]">{{ item.name }}</span>
                  <span v-if="hasMaxUmkm(item.umkm_count)" class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">TERBANYAK</span>
                </div>
                <span class="text-sm font-bold text-slate-800 dark:text-white">{{ item.umkm_count }}</span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="flex-1 bg-slate-100 dark:bg-[#111315] h-2 rounded-full overflow-hidden">
                  <div class="h-full rounded-full transition-all duration-700" :class="getVillageBarColor(item.umkm_count)" :style="{ width: getVillagePercent(item.umkm_count) + '%' }"></div>
                </div>
                <span class="text-xs font-medium text-slate-500 dark:text-[#6F767E] w-12 text-right">{{ getVillagePercent(item.umkm_count) }}%</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Kategori vs Potensi Table -->
        <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 rounded-xl p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center space-x-2">
              <BarChart3 class="h-5 w-5 text-amber-500" />
              <span>Kategori vs Potensi</span>
            </h3>
            <RouterLink to="/dashboard/by-category" class="text-xs text-amber-500 hover:text-amber-600 font-medium flex items-center space-x-1">
              <span>Lihat Detail</span>
              <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </RouterLink>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-xs">
              <thead>
                <tr class="text-slate-400 dark:text-[#6F767E] font-bold uppercase tracking-wider border-b border-slate-100 dark:border-[#2A2E33]">
                  <th class="pb-2 pr-4 text-left">Kategori</th>
                  <th class="pb-2 pr-4 text-right">Total</th>
                  <th class="pb-2 pr-4 text-right">Rata Skor</th>
                  <th class="pb-2 pr-4 text-right text-emerald-600">Tinggi</th>
                  <th class="pb-2 pr-4 text-right text-amber-600">Sedang</th>
                  <th class="pb-2 text-right text-rose-600">Rendah</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in categoryPotential" :key="item.category" class="border-b border-slate-50 dark:border-[#2A2E33]/50 hover:bg-slate-50 dark:hover:bg-[#22262A]/50 transition-colors">
                  <td class="py-2.5 pr-4 font-medium text-slate-700 dark:text-[#F0F0F0]">{{ item.category }}</td>
                  <td class="py-2.5 pr-4 text-right font-bold text-slate-800 dark:text-white">{{ item.total }}</td>
                  <td class="py-2.5 pr-4 text-right font-bold" :class="getScoreTextClass(item.avg_score)">{{ item.avg_score }}</td>
                  <td class="py-2.5 pr-4 text-right font-semibold text-emerald-600 dark:text-emerald-400">{{ item.tinggi }}</td>
                  <td class="py-2.5 pr-4 text-right font-semibold text-amber-600 dark:text-amber-400">{{ item.sedang }}</td>
                  <td class="py-2.5 text-right font-semibold text-rose-600 dark:text-rose-400">{{ item.rendah }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Right Column: 1/3 width -->
      <div class="space-y-4">

        <!-- Chart Section -->
        <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 rounded-xl p-4">
          <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center space-x-2 mb-3">
            <Activity class="h-4 w-4 text-amber-500" />
            <span>Registrasi per Bulan</span>
          </h3>
          <div class="h-40">
            <Line
              v-if="monthlyRegistrations.length"
              :data="chartData"
              :options="chartOptions"
              :key="isDark ? 'dark' : 'light'"
            />
          </div>
        </div>

        <!-- Sebaran Skor -->
        <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 rounded-xl p-4">
          <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center space-x-2 mb-3">
            <BarChart3 class="h-4 w-4 text-amber-500" />
            <span>Sebaran Skor Potensi</span>
          </h3>
          <div class="space-y-3">
            <div v-for="bucket in analysis.score_distribution" :key="bucket.range" class="space-y-1">
              <div class="flex justify-between items-center">
                <span class="text-xs font-medium text-slate-600 dark:text-[#F0F0F0]">{{ bucket.range }}</span>
                <span class="text-xs font-bold text-slate-700 dark:text-white">{{ bucket.count }} <span class="font-normal text-slate-400 dark:text-[#6F767E]">UMKM</span></span>
              </div>
              <div class="w-full bg-slate-100 dark:bg-[#111315] h-2 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700" :class="getScoreColor(bucket.range)" :style="{ width: getScorePercent(bucket.count) + '%' }"></div>
              </div>
            </div>
          </div>
          <div class="mt-4 pt-3 border-t border-slate-100 dark:border-[#2A2E33] flex justify-between items-center">
            <span class="text-xs font-medium text-slate-500 dark:text-[#6F767E]">Rata-rata</span>
            <span class="text-lg font-extrabold text-amber-500">{{ analysis.summary.avg_potential_score }}</span>
          </div>
        </div>

        <!-- Distribusi Kategori -->
        <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 rounded-xl p-4">
          <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center space-x-2 mb-3">
            <PieChart class="h-4 w-4 text-amber-500" />
            <span>Distribusi Kategori</span>
          </h3>
          <div class="space-y-2">
            <div v-for="item in sortedCategories.slice(0, 6)" :key="item.category" class="flex items-center justify-between">
              <span class="text-xs font-medium text-slate-600 dark:text-[#F0F0F0] truncate max-w-[120px]">{{ item.category }}</span>
              <div class="flex items-center space-x-2">
                <div class="w-20 bg-slate-100 dark:bg-[#111315] h-1.5 rounded-full overflow-hidden">
                  <div class="bg-amber-500 h-full rounded-full" :style="{ width: getCatPercent(item.count) + '%' }"></div>
                </div>
                <span class="text-xs font-bold text-slate-700 dark:text-white w-6 text-right">{{ item.count }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Top 5 UMKM -->
        <div class="bg-amber-500 rounded-xl p-4 text-white shadow-lg">
          <h3 class="text-base font-bold flex items-center space-x-2 mb-3">
            <TrendingUp class="h-4 w-4" />
            <span>Top 5 Potensi Tertinggi</span>
          </h3>
          <div class="space-y-3">
            <div v-for="(umkm, idx) in analysis.top_umkm" :key="umkm.id" class="flex items-start space-x-3">
              <div class="h-6 w-6 rounded-full flex items-center justify-center text-xs font-bold" :class="idx === 0 ? 'bg-white/30' : 'bg-white/20'">
                {{ idx + 1 }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-bold truncate">{{ umkm.name }}</p>
                <p class="text-[10px] opacity-70 truncate">{{ umkm.category }} • {{ umkm.village_name }}</p>
              </div>
              <span class="text-lg font-extrabold" :class="idx === 0 ? 'text-white' : 'text-white/80'">{{ umkm.potential_score }}</span>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="space-y-4">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div v-for="i in 4" :key="i" class="h-24 bg-slate-100 dark:bg-[#22262A] rounded-xl animate-pulse"></div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 h-64 bg-slate-100 dark:bg-[#22262A] rounded-xl animate-pulse"></div>
        <div class="space-y-4">
          <div class="h-40 bg-slate-100 dark:bg-[#22262A] rounded-xl animate-pulse"></div>
          <div class="h-40 bg-slate-100 dark:bg-[#22262A] rounded-xl animate-pulse"></div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { Store, TrendingUp, Activity, MapPin, BarChart3, PieChart } from 'lucide-vue-next';
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS,
  LineElement,
  PointElement,
  LineController,
  CategoryScale,
  LinearScale,
  Filler,
  Tooltip,
} from 'chart.js';
import { useTheme } from '../../composables/useTheme';
import api from '../../services/api';

ChartJS.register(LineElement, PointElement, LineController, CategoryScale, LinearScale, Filler, Tooltip);

const { isDark } = useTheme();

interface PotentialStats {
  tinggi: number;
  sedang: number;
  rendah: number;
}

interface StatsData {
  total_umkm: number;
  total_categories: number;
  total_villages: number;
  by_potential: PotentialStats;
}

interface VillageItem {
  id: number;
  name: string;
  umkm_count: number;
  population: number | null;
  density: number | null;
}

interface CategoryItem {
  category: string;
  count: number;
}

interface BucketItem {
  range: string;
  min: number;
  max: number;
  count: number;
}

interface TopUmkm {
  id: number;
  name: string;
  owner: string;
  category: string;
  potential_score: number;
  potential_level: string;
  village_name: string;
}

interface CategoryPotential {
  category: string;
  total: number;
  avg_score: number;
  tinggi: number;
  sedang: number;
  rendah: number;
}

interface AnalysisData {
  summary: {
    total_umkm: number;
    total_categories: number;
    total_villages: number;
    avg_potential_score: number;
    scored_umkm: number;
  };
  village_analysis: VillageItem[];
  category_analysis: CategoryItem[];
  potential_distribution: { level: string; count: number }[];
  score_distribution: BucketItem[];
  top_umkm: TopUmkm[];
  category_potential: CategoryPotential[];
}

const stats = ref<StatsData | null>(null);
const loading = ref(true);

const analysis = ref<AnalysisData>({
  summary: { total_umkm: 0, total_categories: 0, total_villages: 0, avg_potential_score: 0, scored_umkm: 0 },
  village_analysis: [],
  category_analysis: [],
  potential_distribution: [],
  score_distribution: [],
  top_umkm: [],
  category_potential: [],
});

const monthlyRegistrations = ref<number[]>([]);

const chartData = computed(() => ({
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
  datasets: [{
    data: monthlyRegistrations.value,
    borderColor: '#F59E0B',
    backgroundColor: (ctx: any) => {
      if (!ctx.chart.chartArea) return 'transparent';
      const { ctx: c, chartArea: { top, bottom } } = ctx.chart;
      const grad = c.createLinearGradient(0, top, 0, bottom);
      grad.addColorStop(0, 'rgba(245, 158, 11, 0.3)');
      grad.addColorStop(1, 'rgba(245, 158, 11, 0)');
      return grad;
    },
    fill: true,
    tension: 0.4,
    pointRadius: 2,
    pointHitRadius: 6,
    borderWidth: 2,
  }]
}));

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      enabled: true,
      backgroundColor: isDark.value ? '#1A1D1F' : '#ffffff',
      titleColor: isDark.value ? '#F0F0F0' : '#1e293b',
      bodyColor: isDark.value ? '#F0F0F0' : '#1e293b',
      borderColor: isDark.value ? '#2A2E33' : '#e2e8f0',
      borderWidth: 1,
      padding: 8,
      displayColors: false,
    },
  },
  scales: {
    x: {
      display: true,
      grid: { display: false },
      ticks: {
        color: isDark.value ? '#6F767E' : '#94a3b8',
        font: { size: 9 },
        maxRotation: 0,
      },
    },
    y: {
      display: true,
      grid: { color: isDark.value ? 'rgba(111, 118, 126, 0.15)' : 'rgba(148, 163, 184, 0.15)' },
      ticks: {
        color: isDark.value ? '#6F767E' : '#94a3b8',
        font: { size: 9 },
        stepSize: 1,
      },
      beginAtZero: true,
    },
  },
  elements: { point: { radius: 2, hoverRadius: 4 } },
}));

const todayLabel = computed(() => {
  const d = new Date();
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
});

const sortedVillages = computed(() =>
  [...analysis.value.village_analysis].sort((a, b) => b.umkm_count - a.umkm_count)
);

const maxVillageCount = computed(() =>
  Math.max(...analysis.value.village_analysis.map(v => v.umkm_count), 1)
);

const sortedCategories = computed(() =>
  [...analysis.value.category_analysis].sort((a, b) => b.count - a.count)
);

const totalCatCount = computed(() =>
  analysis.value.category_analysis.reduce((s, c) => s + c.count, 0)
);

const categoryPotential = computed(() =>
  [...analysis.value.category_potential].sort((a, b) => b.total - a.total)
);

const totalPotentialCount = computed(() =>
  analysis.value.score_distribution.reduce((s, b) => s + b.count, 0)
);

function hasMaxUmkm(count: number): boolean {
  return count === maxVillageCount.value && count > 0;
}

function getVillagePercent(count: number): number {
  if (maxVillageCount.value === 0) return 0;
  return Math.round((count / maxVillageCount.value) * 100);
}

function getVillageBarColor(count: number): string {
  if (maxVillageCount.value === 0) return 'bg-slate-300';
  const ratio = count / maxVillageCount.value;
  if (ratio >= 0.8) return 'bg-emerald-500';
  if (ratio >= 0.5) return 'bg-amber-500';
  return 'bg-slate-300 dark:bg-slate-600';
}

function getScorePercent(count: number): number {
  if (totalPotentialCount.value === 0) return 0;
  return Math.round((count / totalPotentialCount.value) * 100);
}

function getScoreColor(range: string): string {
  if (range.startsWith('0') || range.startsWith('21')) return 'bg-rose-500';
  if (range.startsWith('41')) return 'bg-amber-400';
  if (range.startsWith('61')) return 'bg-amber-500';
  return 'bg-emerald-500';
}

function getCatPercent(count: number): number {
  if (totalCatCount.value === 0) return 0;
  return Math.round((count / totalCatCount.value) * 100);
}

function getScoreTextClass(score: number): string {
  if (score >= 80) return 'text-emerald-600 dark:text-emerald-400';
  if (score >= 60) return 'text-amber-600 dark:text-amber-400';
  return 'text-rose-600 dark:text-rose-400';
}

function getPercentage(value: number | undefined): number {
  if (!value || !stats.value) return 0;
  const total = stats.value.total_umkm;
  if (total === 0) return 0;
  return Math.round((value / total) * 100);
}

async function fetchStats() {
  loading.value = true;
  try {
    const response = await api.get('/dashboard/stats');
    stats.value = response.data.data;

    const regRes = await api.get('/dashboard/registrations');
    const regData = regRes.data.data || [];

    if (regData.length > 0) {
      const monthlyCounts = new Array(12).fill(0);
      regData.forEach((item: any) => {
        const monthIdx = item.month - 1;
        monthlyCounts[monthIdx] = item.count;
      });
      monthlyRegistrations.value = monthlyCounts;
    }
  } catch (err: any) {
    console.error('Error fetching dashboard stats:', err);
  } finally {
    loading.value = false;
  }
}

async function fetchAnalysis() {
  try {
    const res = await api.get('/dashboard/analysis');
    analysis.value = res.data.data;
  } catch (err: any) {
    console.error('Error fetching analysis:', err);
  }
}

onMounted(() => {
  fetchStats();
  fetchAnalysis();
});
</script>