<template>
  <div class="space-y-6">

    <div class="bg-white dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200 dark:border-[#2A2E33]/60 p-6 rounded-2xl shadow-xl flex items-center justify-between">
      <div>
        <h1 class="text-xl font-bold text-slate-900 dark:text-white">Daftar Kelurahan & Desa Kecamatan Sungailiat</h1>
        <p class="text-slate-500 dark:text-[#6F767E] text-sm mt-1">Daftar batas administrasi wilayah, jumlah populasi penduduk, dan kontribusi persebaran UMKM.</p>
      </div>
    </div>

    <div v-if="error" class="p-4 bg-red-50 dark:bg-red-500/10 border-l-4 border-red-500 text-red-700 dark:text-red-400 text-xs rounded-lg">
      {{ error }}
    </div>

    <div class="bg-white dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200 dark:border-[#2A2E33]/60 rounded-2xl shadow-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50 dark:bg-[#111315] border-b border-slate-200 dark:border-[#2A2E33] text-slate-400 dark:text-[#6F767E] font-bold uppercase tracking-wider">
              <th class="p-4 w-12 text-center">No</th>
              <th class="p-4">Nama Kelurahan</th>
              <th class="p-4 text-right">Populasi (Jiwa)</th>
              <th class="p-4 text-right">Luas Wilayah</th>
              <th class="p-4 text-right">Kepadatan Penduduk</th>
              <th class="p-4 text-center">Total UMKM Kuliner</th>
              <th class="p-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-[#2A2E33]">
            <tr v-if="loading" class="text-center">
              <td colspan="7" class="p-8 text-slate-400 dark:text-[#6F767E]">Memuat data kelurahan...</td>
            </tr>
            <tr v-else-if="villages.length === 0" class="text-center">
              <td colspan="7" class="p-8 text-slate-400 dark:text-[#6F767E]">Tidak ada data kelurahan.</td>
            </tr>
            <tr 
              v-else 
              v-for="(village, idx) in villages" 
              :key="village.id"
              class="hover:bg-slate-50/50 dark:hover:bg-[#22262A] transition-colors"
            >
              <td class="p-4 text-center text-slate-400 dark:text-[#6F767E] font-medium">{{ idx + 1 }}</td>
              <td class="p-4 font-bold text-slate-800 dark:text-white">{{ village.name }}</td>
              <td class="p-4 text-right font-medium text-slate-600 dark:text-[#F0F0F0]">
                {{ village.population ? Number(village.population).toLocaleString('id-ID') : '-' }}
              </td>
              <td class="p-4 text-right font-medium text-slate-600 dark:text-[#F0F0F0]">
                {{ village.area_km2 ? Number(village.area_km2).toFixed(2) + ' km²' : '-' }}
              </td>
              <td class="p-4 text-right font-medium text-slate-600 dark:text-[#F0F0F0]">
                {{ village.density ? Number(village.density).toFixed(1) + ' jiwa/km²' : '-' }}
              </td>
              <td class="p-4 text-center">
                <span class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 font-bold rounded-lg">
                  {{ village.umkm_count || 0 }} UMKM
                </span>
              </td>
              <td class="p-4 text-right">
                <RouterLink 
                  :to="'/villages/' + village.id" 
                  class="inline-flex items-center justify-center px-3 py-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-lg hover:bg-slate-50 dark:hover:bg-[#22262A] text-slate-600 dark:text-[#6F767E] hover:text-slate-800 dark:hover:text-white transition-colors"
                >
                  <Eye class="h-3.5 w-3.5 mr-1" />
                  <span>Lihat Peta Bounds</span>
                </RouterLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import { Eye } from 'lucide-vue-next';
import api from '../../services/api';

const villages = ref<any[]>([]);
const loading = ref(true);
const error = ref('');

async function fetchVillages() {
  loading.value = true;
  error.value = '';
  try {
    const response = await api.get('/villages');
    villages.value = response.data.data || [];
  } catch (err: any) {
    console.error('Error fetching villages list:', err);
    error.value = 'Gagal memuat daftar kelurahan. Silakan periksa kembali server backend Anda.';
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchVillages();
});
</script>
