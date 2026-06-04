<template>
  <div class="space-y-5 font-sans">
    
    <!-- Controls Header -->
    <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-4 rounded-2xl shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex flex-wrap items-center gap-3">
        <!-- Search -->
        <div class="relative w-full md:w-64">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search class="h-4 w-4 text-slate-400 dark:text-[#6F767E]" />
          </span>
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Cari UMKM / Pemilik..."
            class="block w-full pl-9 pr-3 py-2 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs bg-white dark:bg-[#111315] text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-[#6F767E] focus:outline-none focus:ring-1 focus:ring-[#F59E0B]"
            @input="debounceSearch"
          />
        </div>

        <!-- Village filter -->
        <select 
          v-model="selectedVillage" 
          class="px-3 py-2 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs bg-white dark:bg-[#111315] text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#F59E0B]"
          @change="fetchUmkms(1)"
        >
          <option value="">Semua Kelurahan</option>
          <option v-for="v in villages" :key="v.id" :value="v.id">{{ v.name }}</option>
        </select>

        <!-- Kategori filter -->
        <select 
          v-model="selectedCategory" 
          class="px-3 py-2 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs bg-white dark:bg-[#111315] text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#F59E0B]"
          @change="fetchUmkms(1)"
        >
          <option value="">Semua Kategori</option>
          <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
        </select>

        <!-- Potensi filter -->
        <select 
          v-model="selectedPotential" 
          class="px-3 py-2 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs bg-white dark:bg-[#111315] text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#F59E0B]"
          @change="fetchUmkms(1)"
        >
          <option value="">Semua Potensi</option>
          <option value="tinggi">Tinggi</option>
          <option value="sedang">Sedang</option>
          <option value="rendah">Rendah</option>
        </select>
      </div>

      <div>
        <RouterLink 
          to="/umkm/create" 
          class="inline-flex items-center space-x-1.5 px-4 py-2 bg-[#F59E0B] hover:bg-[#D97706] text-[#111315] rounded-xl text-xs font-bold transition-colors cursor-pointer shadow-lg shadow-[#F59E0B]/20"
        >
          <Plus class="h-3.5 w-3.5" />
          <span>Tambah UMKM</span>
        </RouterLink>
      </div>
    </div>

    <!-- Error -->
    <div v-if="error" class="p-4 bg-red-50 dark:bg-red-500/10 border-l-4 border-red-500 text-red-700 dark:text-red-400 text-sm rounded-lg">
      {{ error }}
    </div>

    <!-- Data Table -->
    <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 rounded-2xl overflow-hidden shadow-xl">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50 dark:bg-[#111315]/50 border-b border-slate-200 dark:border-[#2A2E33] text-slate-400 dark:text-[#6F767E] font-bold uppercase tracking-wider">
              <th class="p-4 w-12 text-center">No</th>
              <th class="p-4">Nama Usaha</th>
              <th class="p-4">Pemilik</th>
              <th class="p-4">Kategori</th>
              <th class="p-4">Kelurahan</th>
              <th class="p-4 text-center">Skor Spasial</th>
              <th class="p-4 text-center">Tingkat Potensi</th>
              <th class="p-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-[#2A2E33]">
            <tr v-if="loading" class="text-center">
              <td colspan="8" class="p-8 text-slate-400 dark:text-[#6F767E]">Memuat data UMKM...</td>
            </tr>
            <tr v-else-if="umkms.length === 0" class="text-center">
              <td colspan="8" class="p-8 text-slate-400 dark:text-[#6F767E]">Tidak ada data UMKM yang cocok.</td>
            </tr>
            <tr 
              v-else 
              v-for="(umkm, idx) in umkms" 
              :key="umkm.id"
              class="hover:bg-slate-50/50 dark:hover:bg-[#22262A]/50 transition-colors"
            >
              <td class="p-4 text-center text-slate-400 dark:text-[#6F767E] font-medium">
                {{ (pagination.current_page - 1) * pagination.per_page + idx + 1 }}
              </td>
              <td class="p-4 font-bold text-slate-800 dark:text-white">{{ umkm.name }}</td>
              <td class="p-4 text-slate-600 dark:text-[#F0F0F0]">{{ umkm.owner }}</td>
              <td class="p-4 text-slate-600 dark:text-[#F0F0F0]">{{ umkm.category }}</td>
              <td class="p-4 text-slate-600 dark:text-[#F0F0F0]">{{ umkm.village_name }}</td>
              <td class="p-4 text-center font-semibold text-slate-700 dark:text-white">
                {{ umkm.potential_score ? Number(umkm.potential_score).toFixed(1) : '-' }}
              </td>
              <td class="p-4 text-center">
                <span 
                  :class="[
                    'px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider inline-block text-white',
                    getPotentialColor(umkm.potential_level)
                  ]"
                >
                  {{ umkm.potential_level }}
                </span>
              </td>
              <td class="p-4 text-right space-x-1.5">
                <RouterLink 
                  :to="'/umkm/' + umkm.id" 
                  class="inline-flex items-center justify-center p-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-lg hover:bg-slate-50 dark:hover:bg-[#22262A] text-slate-500 dark:text-[#6F767E] hover:text-slate-800 dark:hover:text-white transition-colors"
                  title="Lihat Detail"
                >
                  <Eye class="h-3.5 w-3.5" />
                </RouterLink>
                <RouterLink 
                  :to="'/umkm/' + umkm.id + '/edit'" 
                  class="inline-flex items-center justify-center p-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-lg hover:bg-slate-50 dark:hover:bg-[#22262A] text-slate-500 dark:text-[#6F767E] hover:text-slate-800 dark:hover:text-white transition-colors"
                  title="Edit Data"
                >
                  <Edit class="h-3.5 w-3.5" />
                </RouterLink>
                <button 
                  @click="confirmDelete(umkm)" 
                  class="inline-flex items-center justify-center p-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 text-slate-500 dark:text-[#6F767E] hover:text-red-600 dark:hover:text-red-400 transition-colors cursor-pointer"
                  title="Hapus Data"
                >
                  <Trash2 class="h-3.5 w-3.5" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div 
        v-if="pagination.total > 0" 
        class="border-t border-slate-200 dark:border-[#2A2E33] p-4 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs"
      >
        <span class="text-slate-500 dark:text-[#6F767E]">
          Menampilkan {{ (pagination.current_page - 1) * pagination.per_page + 1 }} sampai 
          {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} dari 
          {{ pagination.total }} baris data
        </span>
        
        <div class="flex items-center space-x-1">
          <button 
            @click="fetchUmkms(pagination.current_page - 1)" 
            :disabled="pagination.current_page === 1"
            class="px-2.5 py-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-lg hover:bg-slate-50 dark:hover:bg-[#22262A] text-slate-600 dark:text-[#F0F0F0] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
          >
            Prev
          </button>
          
          <button 
            v-for="p in totalPages" 
            :key="p"
            @click="fetchUmkms(p)"
            :class="[
              'px-3 py-1.5 border rounded-lg cursor-pointer transition-colors',
              pagination.current_page === p ? 'bg-[#F59E0B] text-[#111315] border-[#F59E0B] font-bold' : 'bg-white dark:bg-[#111315] text-slate-600 dark:text-[#F0F0F0] border-slate-200 dark:border-[#2A2E33] hover:bg-slate-50 dark:hover:bg-[#22262A]'
            ]"
          >
            {{ p }}
          </button>

          <button 
            @click="fetchUmkms(pagination.current_page + 1)" 
            :disabled="pagination.current_page === totalPages"
            class="px-2.5 py-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-lg hover:bg-slate-50 dark:hover:bg-[#22262A] text-slate-600 dark:text-[#F0F0F0] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { RouterLink } from 'vue-router';
import { Search, Plus, Eye, Edit, Trash2 } from 'lucide-vue-next';
import api from '../../services/api';

const umkms = ref<any[]>([]);
const villages = ref<any[]>([]);
const categories = ref<string[]>([]);

const searchQuery = ref('');
const selectedVillage = ref('');
const selectedCategory = ref('');
const selectedPotential = ref('');

const loading = ref(true);
const error = ref('');

const pagination = ref({
  current_page: 1,
  per_page: 15,
  total: 0
});

const totalPages = computed(() => {
  return Math.ceil(pagination.value.total / pagination.value.per_page) || 1;
});

let debounceTimer: any = null;
function debounceSearch() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    fetchUmkms(1);
  }, 300);
}

async function fetchFilterOptions() {
  try {
    const villRes = await api.get('/villages');
    villages.value = villRes.data.data || [];

    const catRes = await api.get('/umkms/categories');
    categories.value = catRes.data.data || [];
  } catch (err) {
    console.error('Error fetching filters option lists:', err);
  }
}

async function fetchUmkms(page = 1) {
  loading.value = true;
  error.value = '';
  try {
    const response = await api.get('/umkms', {
      params: {
        page,
        search: searchQuery.value,
        village_id: selectedVillage.value,
        category: selectedCategory.value,
        potential_level: selectedPotential.value,
        per_page: pagination.value.per_page
      }
    });

    umkms.value = response.data.data || [];
    
    if (response.data.meta) {
      pagination.value = {
        current_page: response.data.meta.current_page,
        per_page: response.data.meta.per_page,
        total: response.data.meta.total
      };
    }
  } catch (err: any) {
    console.error('Error fetching UMKMs list:', err);
    error.value = 'Gagal memuat daftar UMKM. Hubungi administrator jika terus terjadi.';
  } finally {
    loading.value = false;
  }
}

async function confirmDelete(umkm: any) {
  if (confirm(`Apakah Anda yakin ingin menghapus data UMKM "${umkm.name}"?`)) {
    try {
      await api.delete(`/umkms/${umkm.id}`);
      fetchUmkms(pagination.value.current_page);
    } catch (err: any) {
      console.error('Error deleting UMKM:', err);
      alert('Gagal menghapus data UMKM.');
    }
  }
}

function getPotentialColor(level: string): string {
  const norm = String(level).toLowerCase();
  if (norm === 'tinggi') return 'bg-emerald-500';
  if (norm === 'sedang') return 'bg-amber-500';
  return 'bg-rose-500';
}

onMounted(() => {
  fetchFilterOptions();
  fetchUmkms();
});
</script>
