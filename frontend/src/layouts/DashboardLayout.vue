<template>
  <div class="relative h-screen w-screen flex bg-slate-50 dark:bg-[#111315] text-slate-900 dark:text-white font-sans overflow-hidden">

    <!-- Sidebar -->
    <aside class="relative z-30 m-4 h-[calc(100vh-2rem)] w-[260px] bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-3xl border border-white/60 dark:border-[#2A2E33]/60 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] dark:shadow-none flex flex-col py-6 shrink-0 overflow-hidden transition-all duration-300">

      <!-- Logo Area -->
      <div class="w-full px-6 mb-8 flex items-center">
        <RouterLink to="/" class="h-11 w-11 shrink-0 rounded-2xl flex items-center justify-center bg-white dark:bg-[#22262A] shadow-md border border-slate-200/50 dark:border-[#2A2E33] hover:scale-105 hover:shadow-lg hover:shadow-amber-500/20 transition-all duration-300 group" title="Peta Utama">
          <img src="/src/assets/logo.png" alt="GIS UMKM" class="h-8 w-8 rounded-xl group-hover:rotate-3 transition-transform" />
        </RouterLink>
        <div class="ml-3.5 flex flex-col justify-center">
          <span class="font-extrabold text-[17px] tracking-tight text-slate-900 dark:text-white leading-none mb-1.5">GIS UMKM</span>
          <span class="text-[9px] font-bold text-amber-500 tracking-widest uppercase leading-none">Sungailiat</span>
        </div>
      </div>

      <!-- Navigation Links -->
      <nav class="flex-1 w-full px-4 flex flex-col space-y-1.5 overflow-y-auto no-scrollbar">
        <span class="px-3 text-[10px] font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-widest mb-1 mt-1">Menu Utama</span>

        <RouterLink
          to="/admin/dashboard"
          class="relative w-full h-12 rounded-2xl flex items-center px-4 transition-all duration-300 group overflow-hidden"
          :class="isRouteActive('/admin/dashboard') ? 'bg-amber-500 text-[#111315]' : 'text-slate-500 dark:text-[#8B949E] hover:bg-slate-100/80 dark:hover:bg-[#22262A] hover:text-slate-900 dark:hover:text-white'"
        >
          <div v-if="isRouteActive('/admin/dashboard')" class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
          <LayoutDashboard class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:scale-110" />
          <span class="ml-3.5 font-semibold text-[13px] tracking-wide">Dashboard</span>
        </RouterLink>
 
        <RouterLink
          to="/admin/dashboard/by-category"
          class="relative w-full h-12 rounded-2xl flex items-center px-4 transition-all duration-300 group overflow-hidden"
          :class="isRouteActive('/admin/dashboard/by-category') ? 'bg-amber-500 text-[#111315]' : 'text-slate-500 dark:text-[#8B949E] hover:bg-slate-100/80 dark:hover:bg-[#22262A] hover:text-slate-900 dark:hover:text-white'"
        >
          <div v-if="isRouteActive('/admin/dashboard/by-category')" class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
          <PieChart class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:scale-110" />
          <span class="ml-3.5 font-semibold text-[13px] tracking-wide">Statistik Kategori</span>
        </RouterLink>

        <div class="h-px w-full bg-slate-200/50 dark:bg-[#2A2E33]/50 my-2"></div>
        <span class="px-3 text-[10px] font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-widest mb-1">Data Master</span>

        <RouterLink
          to="/admin/umkm"
          class="relative w-full h-12 rounded-2xl flex items-center px-4 transition-all duration-300 group overflow-hidden"
          :class="isRouteActive('/admin/umkm') ? 'bg-amber-500 text-[#111315]' : 'text-slate-500 dark:text-[#8B949E] hover:bg-slate-100/80 dark:hover:bg-[#22262A] hover:text-slate-900 dark:hover:text-white'"
        >
          <div v-if="isRouteActive('/admin/umkm')" class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
          <Store class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:scale-110" />
          <span class="ml-3.5 font-semibold text-[13px] tracking-wide">Data UMKM</span>
        </RouterLink>

        <RouterLink
          to="/admin/villages"
          class="relative w-full h-12 rounded-2xl flex items-center px-4 transition-all duration-300 group overflow-hidden"
          :class="isRouteActive('/admin/villages') ? 'bg-amber-500 text-[#111315]' : 'text-slate-500 dark:text-[#8B949E] hover:bg-slate-100/80 dark:hover:bg-[#22262A] hover:text-slate-900 dark:hover:text-white'"
        >
          <div v-if="isRouteActive('/admin/villages')" class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
          <MapPin class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:scale-110" />
          <span class="ml-3.5 font-semibold text-[13px] tracking-wide">Kelurahan</span>
        </RouterLink>

        <RouterLink
          to="/admin/users"
          class="relative w-full h-12 rounded-2xl flex items-center px-4 transition-all duration-300 group overflow-hidden"
          :class="isRouteActive('/admin/users') ? 'bg-amber-500 text-[#111315]' : 'text-slate-500 dark:text-[#8B949E] hover:bg-slate-100/80 dark:hover:bg-[#22262A] hover:text-slate-900 dark:hover:text-white'"
        >
          <div v-if="isRouteActive('/admin/users')" class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
          <Users class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:scale-110" />
          <span class="ml-3.5 font-semibold text-[13px] tracking-wide">Pengguna</span>
        </RouterLink>
      </nav>

      <!-- Footer Actions -->
      <div class="mt-auto w-full px-4 flex flex-col space-y-1.5 pt-4">

        <button
          @click="toggleTheme"
          class="w-full h-11 rounded-xl flex items-center px-4 text-slate-500 dark:text-[#8B949E] hover:bg-slate-100 dark:hover:bg-[#22262A] hover:text-amber-500 dark:hover:text-amber-500 transition-all cursor-pointer group"
        >
          <Moon v-if="!isDark" class="h-4 w-4 shrink-0 transition-transform group-hover:-rotate-12" />
          <Sun v-else class="h-4 w-4 shrink-0 transition-transform group-hover:rotate-45" />
          <span class="ml-3.5 font-medium text-[13px] tracking-wide">Ganti Tema</span>
        </button>

        <button
          @click="showLogoutModal = true"
          class="w-full h-11 rounded-xl flex items-center px-4 text-slate-500 dark:text-[#8B949E] hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 dark:hover:text-red-400 transition-all cursor-pointer group"
        >
          <LogOut class="h-4 w-4 shrink-0 transition-transform group-hover:-translate-x-1" />
          <span class="ml-3.5 font-medium text-[13px] tracking-wide">Keluar</span>
        </button>

        <!-- User Profile Card -->
        <div class="w-full mt-3 rounded-2xl bg-slate-100 dark:bg-[#22262A] border border-slate-200/60 dark:border-[#2A2E33] p-3 flex items-center shadow-sm hover:shadow-md transition-shadow">
          <div class="h-10 w-10 shrink-0 rounded-xl bg-slate-800 dark:bg-amber-500 flex items-center justify-center text-white dark:text-[#111315] font-bold shadow-inner border border-white/10">
            {{ userInitial }}
          </div>
          <div class="ml-3.5 flex flex-col overflow-hidden">
            <span class="text-sm font-bold truncate text-slate-800 dark:text-white leading-tight">{{ userName }}</span>
            <span class="text-[10px] font-bold uppercase tracking-widest text-amber-500 mt-0.5">Administrator</span>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 relative z-10 flex flex-col h-full overflow-hidden">
      <!-- Top Toolbar -->
      <header class="h-14 px-5 flex items-center justify-between shrink-0">
        <div class="flex items-center space-x-2">
          <!-- Page title pill -->
          <div class="bg-white/80 dark:bg-[#1A1D1F]/80 backdrop-blur-md px-4 py-2 rounded-xl border border-slate-200/40 dark:border-[#2A2E33]/40">
            <h2 class="text-xs font-bold text-slate-700 dark:text-[#F0F0F0] tracking-wide">{{ pageTitle }}</h2>
          </div>
        </div>

        <div class="flex items-center space-x-2">
          <!-- Action Buttons -->
          <RouterLink
            to="/"
            class="bg-white/80 dark:bg-[#1A1D1F]/80 backdrop-blur-md border border-slate-200/40 dark:border-[#2A2E33]/40 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-[#F0F0F0] hover:bg-slate-50 dark:hover:bg-[#22262A] transition-colors flex items-center space-x-1.5"
          >
            <Map class="h-3.5 w-3.5 text-amber-500" />
            <span>Peta Utama</span>
          </RouterLink>

          <RouterLink
            to="/admin/umkm/create"
            class="bg-amber-500 hover:bg-amber-600 text-[#111315] px-4 py-2 rounded-xl text-xs font-bold flex items-center space-x-1.5 transition-colors shadow-lg shadow-amber-500/20"
          >
            <Plus class="h-3.5 w-3.5" />
            <span>Tambah UMKM</span>
            <span class="ml-1 bg-[#111315]/10 px-1.5 py-0.5 rounded-md text-xs font-bold">{{ umkmCount }}</span>
          </RouterLink>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-5 overflow-y-auto no-scrollbar">
        <RouterView />
      </main>
    </div>

    <!-- Logout Confirmation Modal -->
    <Teleport to="body">
      <div v-if="showLogoutModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm" @click.self="showLogoutModal = false">
        <div class="bg-white dark:bg-[#1A1D1F] rounded-3xl p-8 max-w-sm w-full mx-4 shadow-2xl border border-slate-200 dark:border-[#2A2E33] transform transition-all">
          <div class="flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center mb-5">
              <LogOut class="w-6 h-6 text-red-500" />
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Konfirmasi Keluar</h3>
            <p class="text-sm text-slate-500 dark:text-[#6F767E] mb-8">Apakah Anda yakin ingin keluar dari sistem?</p>
            <div class="flex space-x-3 w-full">
              <button 
                @click="showLogoutModal = false" 
                class="flex-1 px-4 py-2.5 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs font-semibold text-slate-700 dark:text-[#F0F0F0] bg-white dark:bg-[#111315] hover:bg-slate-50 dark:hover:bg-[#22262A] transition-colors cursor-pointer"
              >
                Batal
              </button>
              <button 
                @click="confirmLogout" 
                class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer"
              >
                Keluar
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter, RouterLink, RouterView } from 'vue-router';
import {
  LayoutDashboard, Map, Store, MapPin, Users, LogOut,
  BarChart3, PieChart, Moon, Sun, Plus
} from 'lucide-vue-next';
import api from '../services/api';
import { useTheme } from '../composables/useTheme';

const route = useRoute();
const router = useRouter();
const { isDark, toggleTheme } = useTheme();
const umkmCount = ref(0);
const showLogoutModal = ref(false);

// User
const userName = computed(() => {
  const s = localStorage.getItem('user_info');
  return s ? JSON.parse(s).name : 'Administrator';
});
const userInitial = computed(() => userName.value.charAt(0).toUpperCase());

const pageTitle = computed(() => {
  switch (route.name) {
    case 'dashboard': return 'Dashboard Overview';
    case 'dashboard.by-village': return 'Statistik per Kelurahan';
    case 'dashboard.by-category': return 'Statistik per Kategori';
    case 'dashboard.by-potential': return 'Distribusi Potensi';
    case 'umkm.index': return 'Manajemen UMKM';
    case 'umkm.create': return 'Tambah UMKM Baru';
    case 'umkm.edit': return 'Ubah Data UMKM';
    case 'umkm.show': return 'Detail UMKM';
    case 'villages.index': return 'Data Kelurahan';
    case 'villages.show': return 'Detail Kelurahan';
    case 'users.index': return 'Kelola Pengguna';
    default: return 'SIG UMKM Sungailiat';
  }
});

function isRouteActive(path: string): boolean {
  if (route.path === path) return true;
  if (route.path.startsWith(path + '/')) {
    if (path === '/admin/dashboard') return false;
    return true;
  }
  return false;
}

function confirmLogout() {
  showLogoutModal.value = false;
  handleLogout();
}

async function handleLogout() {
  try { await api.post('/auth/logout'); } catch (err) {}
  localStorage.removeItem('auth_token');
  localStorage.removeItem('user_info');
  router.push('/login');
}

onMounted(() => {
  loadUmkmCount();
});

async function loadUmkmCount() {
  try {
    const res = await api.get('/dashboard/stats');
    umkmCount.value = res.data.data?.total_umkm || 0;
  } catch (e) {}
}
</script>