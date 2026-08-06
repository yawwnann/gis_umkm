<template>
  <div
    class="h-screen w-screen relative overflow-hidden font-sans bg-slate-50 dark:bg-[#111315] text-slate-900 dark:text-[#F0F0F0] transition-colors duration-200"
  >
    <!-- Fullscreen Map Container -->
    <main class="absolute inset-0 z-0">
      <div id="map" class="h-full w-full bg-slate-100 dark:bg-[#111315]"></div>
    </main>

    <!-- Floating Dropdown for Layers Settings -->
    <div
      :class="[
        'absolute z-20 transition-all duration-300 ease-out flex flex-col',
        sidebarOpen ? 'max-md:hidden md:left-[460px] top-6' : 'left-16 top-4',
      ]"
    >
      <button
        @click="showLayerSettings = !showLayerSettings"
        class="bg-white/90 dark:bg-[#1A1D1F]/90 backdrop-blur-md p-3 rounded-2xl shadow-lg border border-slate-200/50 dark:border-[#2A2E33]/50 hover:scale-105 transition-all group focus:outline-none"
        title="Pengaturan Layer Peta"
      >
        <Layers
          class="w-5 h-5 text-slate-700 dark:text-[#F0F0F0] group-hover:text-[#F59E0B] transition-colors"
        />
      </button>

      <!-- Dropdown Menu -->
      <div
        v-if="showLayerSettings"
        class="absolute top-14 left-0 mt-2 w-[280px] bg-white/95 dark:bg-[#1A1D1F]/95 backdrop-blur-xl border border-slate-200/50 dark:border-[#2A2E33]/50 rounded-2xl shadow-2xl p-5 max-h-[75vh] overflow-y-auto no-scrollbar flex flex-col space-y-5"
      >


        <!-- Layers Toggle -->
        <div class="space-y-4">
          <label
            class="block text-[10px] font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-widest"
            >Layer Spasial</label
          >

          <div class="grid grid-cols-1 gap-2.5">
            <label
              class="flex items-center space-x-3 text-sm text-slate-700 dark:text-[#F0F0F0] cursor-pointer p-2 hover:bg-slate-50 dark:hover:bg-[#22262A] rounded-lg transition-colors"
            >
              <input
                type="checkbox"
                v-model="visibleLayers.umkm"
                @change="toggleLayer('umkm')"
                class="h-4 w-4 text-[#F59E0B] rounded border-slate-300 dark:border-[#2A2E33] bg-white dark:bg-[#111315] focus:ring-[#F59E0B] focus:ring-offset-[#1A1D1F]"
              />
              <span class="font-medium">UMKM Kuliner</span>
            </label>
            <label
              class="flex items-center space-x-3 text-sm text-slate-700 dark:text-[#F0F0F0] cursor-pointer p-2 hover:bg-slate-50 dark:hover:bg-[#22262A] rounded-lg transition-colors"
            >
              <input
                type="checkbox"
                v-model="visibleLayers.village"
                @change="toggleLayer('village')"
                class="h-4 w-4 text-[#F59E0B] rounded border-slate-300 dark:border-[#2A2E33] bg-white dark:bg-[#111315] focus:ring-[#F59E0B] focus:ring-offset-[#1A1D1F]"
              />
              <span class="font-medium">Peta Potensi Kelurahan</span>
            </label>

            <label
              class="flex items-center space-x-3 text-sm text-slate-700 dark:text-[#F0F0F0] cursor-pointer p-2 hover:bg-slate-50 dark:hover:bg-[#22262A] rounded-lg transition-colors"
            >
              <input
                type="checkbox"
                v-model="visibleLayers.settlement"
                @change="toggleLayer('settlement')"
                class="h-4 w-4 text-[#F59E0B] rounded border-slate-300 dark:border-[#2A2E33] bg-white dark:bg-[#111315] focus:ring-[#F59E0B] focus:ring-offset-[#1A1D1F]"
              />
              <span class="font-medium">Kawasan Pemukiman</span>
            </label>
            <label
              class="flex items-center space-x-3 text-sm text-slate-700 dark:text-[#F0F0F0] cursor-pointer p-2 hover:bg-slate-50 dark:hover:bg-[#22262A] rounded-lg transition-colors"
            >
              <input
                type="checkbox"
                v-model="visibleLayers.trading"
                @change="toggleLayer('trading')"
                class="h-4 w-4 text-[#F59E0B] rounded border-slate-300 dark:border-[#2A2E33] bg-white dark:bg-[#111315] focus:ring-[#F59E0B] focus:ring-offset-[#1A1D1F]"
              />
              <span class="font-medium">Fasilitas Niaga</span>
            </label>

            <label
              class="flex items-center space-x-3 text-sm text-slate-700 dark:text-[#F0F0F0] cursor-pointer p-2 hover:bg-slate-50 dark:hover:bg-[#22262A] rounded-lg transition-colors"
            >
              <input
                type="checkbox"
                v-model="visibleLayers.school"
                @change="toggleLayer('school')"
                class="h-4 w-4 text-[#F59E0B] rounded border-slate-300 dark:border-[#2A2E33] bg-white dark:bg-[#111315] focus:ring-[#F59E0B] focus:ring-offset-[#1A1D1F]"
              />
              <span class="font-medium">Fasilitas Pendidikan</span>
            </label>
            <label
              class="flex items-center space-x-3 text-sm text-slate-700 dark:text-[#F0F0F0] cursor-pointer p-2 hover:bg-slate-50 dark:hover:bg-[#22262A] rounded-lg transition-colors"
            >
              <input
                type="checkbox"
                v-model="visibleLayers.gov"
                @change="toggleLayer('gov')"
                class="h-4 w-4 text-[#F59E0B] rounded border-slate-300 dark:border-[#2A2E33] bg-white dark:bg-[#111315] focus:ring-[#F59E0B] focus:ring-offset-[#1A1D1F]"
              />
              <span class="font-medium">Fasilitas Pemerintahan</span>
            </label>
            </label>
          </div>
        </div>

        <!-- Basemap Selector -->
        <div class="space-y-3">
          <label
            class="block text-[10px] font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-widest"
            >Basemap</label
          >
          <div
            class="flex bg-slate-100 dark:bg-[#111315] p-1 rounded-xl border border-slate-200 dark:border-[#2A2E33] transition-colors"
          >
            <button
              @click="changeBasemap('streets')"
              :class="[
                'flex-1 py-2 text-xs font-bold rounded-lg transition-all',
                basemap === 'streets'
                  ? 'bg-[#F59E0B] text-[#111315] shadow-md'
                  : 'text-slate-500 dark:text-[#6F767E] hover:text-slate-900 dark:hover:text-white',
              ]"
            >
              Streets
            </button>
            <button
              @click="changeBasemap('satellite')"
              :class="[
                'flex-1 py-2 text-xs font-bold rounded-lg transition-all',
                basemap === 'satellite'
                  ? 'bg-[#F59E0B] text-[#111315] shadow-md'
                  : 'text-slate-500 dark:text-[#6F767E] hover:text-slate-900 dark:hover:text-white',
              ]"
            >
              Satellite
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Current Location Button -->
    <button
      @click="getCurrentLocation"
      class="absolute bottom-6 right-6 z-20 h-14 w-14 bg-[#F59E0B] hover:bg-[#D97706] text-[#111315] rounded-2xl flex items-center justify-center shadow-[0_8px_30px_rgb(245,158,11,0.3)] hover:shadow-[0_8px_30px_rgb(245,158,11,0.5)] transition-all hover:scale-105 active:scale-95"
      title="Temukan Lokasi Saya"
    >
      <LocateFixed class="h-6 w-6" />
    </button>

    <!-- Toggle Sidebar Button (visible when sidebar is collapsed) -->
    <button
      v-if="!sidebarOpen"
      @click="sidebarOpen = true"
      class="absolute left-4 top-4 z-30 h-11 w-11 bg-[#F59E0B] hover:bg-[#D97706] text-[#111315] rounded-xl flex items-center justify-center shadow-lg shadow-[#F59E0B]/30 transition-all hover:scale-105 active:scale-95 md:hidden"
      title="Buka Panel"
    >
      <Menu class="h-5 w-5" />
    </button>

    <!-- Desktop Toggle Sidebar Button -->
    <button
      v-if="!sidebarOpen"
      @click="sidebarOpen = true"
      class="hidden md:flex absolute left-4 top-4 z-30 h-11 w-11 bg-[#F59E0B] hover:bg-[#D97706] text-[#111315] rounded-xl items-center justify-center shadow-lg shadow-[#F59E0B]/30 transition-all hover:scale-105 active:scale-95"
      title="Buka Panel"
    >
      <ChevronRight class="h-5 w-5" />
    </button>

    <!-- Mobile Overlay -->
    <div
      v-if="sidebarOpen && !isDesktop"
      class="md:hidden fixed inset-0 z-10 bg-black/40"
      @click="sidebarOpen = false"
    ></div>

    <!-- Modern Floating Sidebar Panel -->
    <aside
      :class="[
        'absolute left-0 top-0 bottom-0 md:left-6 md:top-6 md:bottom-6 w-full md:w-[420px] flex flex-col z-20 shadow-[0_0_50px_rgba(0,0,0,0.1)] dark:shadow-[0_0_50px_rgba(0,0,0,0.5)] md:rounded-3xl bg-white/85 dark:bg-[#1A1D1F]/85 backdrop-blur-2xl border border-white/60 dark:border-white/5 overflow-hidden transition-transform duration-300 ease-out',
        sidebarOpen
          ? 'translate-x-0'
          : '-translate-x-full max-md:-translate-x-full md:translate-x-0',
      ]"
    >
      <!-- Header -->
      <div
        class="p-6 border-b border-slate-200/50 dark:border-white/5 bg-white/40 dark:bg-black/20 flex items-center justify-between"
      >
        <div class="flex items-center space-x-3">
          <div class="h-10 w-10 rounded-xl flex items-center justify-center">
            <img
              src="/src/assets/logo.png"
              alt="GIS UMKM"
              class="h-10 w-10 rounded-xl"
            />
          </div>
          <h1
            class="font-extrabold text-lg tracking-tight text-slate-900 dark:text-white"
          >
            GIS UMKM
          </h1>
        </div>

        <div class="flex items-center space-x-2">
          <button
            v-if="!isDesktop"
            @click="sidebarOpen = false"
            class="md:hidden p-2 rounded-xl bg-slate-100 dark:bg-[#22262A] text-slate-500 dark:text-[#6F767E] hover:text-slate-800"
          >
            <X class="h-4 w-4" />
          </button>
          <button
            @click="toggleTheme"
            class="p-2 rounded-xl bg-white dark:bg-[#111315] border border-slate-200 dark:border-[#2A2E33] text-slate-500 dark:text-[#6F767E] hover:text-[#F59E0B] dark:hover:text-[#F59E0B] transition-colors"
            :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
          >
            <Sun v-if="isDark" class="h-4 w-4" />
            <Moon v-else class="h-4 w-4" />
          </button>

          <RouterLink
            v-if="!isLoggedIn"
            to="/login"
            class="inline-flex items-center space-x-1.5 px-4 py-2 bg-[#F59E0B] hover:bg-[#D97706] text-[#111315] rounded-xl text-xs font-bold transition-all shadow-lg shadow-[#F59E0B]/20"
          >
            <LogIn class="h-4 w-4" />
            <span>Login</span>
          </RouterLink>
          <RouterLink
            v-else
            :to="dashboardRoute"
            class="inline-flex items-center space-x-1.5 px-4 py-2 bg-[#F59E0B] hover:bg-[#D97706] text-[#111315] rounded-xl text-xs font-bold transition-all shadow-lg shadow-[#F59E0B]/20"
          >
            <LayoutDashboard class="h-4 w-4" />
            <span>Dashboard</span>
          </RouterLink>
        </div>
      </div>

      <!-- Scrollable Content -->
      <div class="flex-1 overflow-y-auto p-6 space-y-8 custom-scrollbar">
        <!-- Search -->
        <div class="space-y-3">
          <label
            class="block text-[10px] font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-widest"
            >Pencarian</label
          >
          <div class="relative">
            <span
              class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"
            >
              <Search class="h-4 w-4 text-slate-400 dark:text-[#6F767E]" />
            </span>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari nama usaha atau pemilik..."
              class="block w-full pl-10 pr-4 py-3 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-sm bg-white dark:bg-[#111315] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-[#6F767E] focus:outline-none focus:ring-1 focus:ring-[#F59E0B] focus:border-[#F59E0B] transition-colors"
              @input="handleSearch"
            />
            <button
              v-if="searchQuery"
              @click="clearSearch"
              class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 dark:text-[#6F767E] hover:text-slate-900 dark:hover:text-white"
            >
              <X class="h-4 w-4" />
            </button>
          </div>

          <!-- Search Results Dropdown -->
          <div
            v-if="searchResults.length > 0"
            class="border border-slate-200 dark:border-[#2A2E33] bg-white dark:bg-[#1A1D1F] rounded-xl max-h-56 overflow-y-auto divide-y divide-slate-100 dark:divide-[#2A2E33] mt-2 shadow-2xl absolute w-[calc(100%-3rem)] z-50"
          >
            <button
              v-for="umkm in searchResults"
              :key="umkm.id"
              @click="focusUmkm(umkm)"
              class="w-full text-left px-4 py-3 text-sm hover:bg-slate-50 dark:hover:bg-[#22262A] transition-colors flex justify-between items-center group cursor-pointer"
            >
              <div>
                <div
                  class="font-bold text-slate-900 dark:text-white group-hover:text-[#D97706] dark:group-hover:text-[#F59E0B] transition-colors"
                >
                  {{ umkm.name }}
                </div>
                <div class="text-slate-500 dark:text-[#6F767E] text-xs mt-0.5">
                  {{ umkm.category }} - {{ umkm.village_name }}
                </div>
              </div>
              <ChevronRight
                class="h-4 w-4 text-slate-400 dark:text-[#6F767E] group-hover:text-[#D97706] dark:group-hover:text-[#F59E0B] transition-colors"
              />
            </button>
          </div>
        </div>

        <!-- Filters -->
        <div class="space-y-4">
          <label
            class="block text-[10px] font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-widest"
            >Filter Kriteria</label
          >

          <div class="space-y-1.5">
            <span
              class="text-xs font-semibold text-slate-700 dark:text-[#F0F0F0]"
              >Wilayah Kelurahan</span
            >
            <select
              v-model="selectedVillage"
              class="block w-full px-4 py-2.5 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-sm bg-white dark:bg-[#111315] text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#F59E0B] cursor-pointer transition-colors"
              @change="applyFilters"
            >
              <option value="">Semua Kelurahan</option>
              <option v-for="v in villages" :key="v.id" :value="v.name">
                {{ v.name }}
              </option>
            </select>
          </div>

          <div class="space-y-1.5">
            <span
              class="text-xs font-semibold text-slate-700 dark:text-[#F0F0F0]"
              >Kategori Kuliner</span
            >
            <select
              v-model="selectedCategory"
              class="block w-full px-4 py-2.5 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-sm bg-white dark:bg-[#111315] text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#F59E0B] cursor-pointer transition-colors"
              @change="applyFilters"
            >
              <option value="">Semua Kategori</option>
              <option v-for="c in categories" :key="c" :value="c">
                {{ c }}
              </option>
            </select>
          </div>
        </div>

        <!-- Papan Statistik Pemantauan (Menggantikan Layer Setting) -->
        <div class="space-y-4 pt-2">
          <label
            class="block text-[10px] font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-widest border-b border-slate-200/50 dark:border-[#2A2E33]/50 pb-2"
            >Statistik Area Ini</label
          >

          <div class="grid grid-cols-2 gap-3">
            <div
              class="bg-gradient-to-br from-slate-100 to-slate-50 dark:from-[#22262A] dark:to-[#1A1D1F] p-3 rounded-2xl border border-slate-200/50 dark:border-[#2A2E33]/50 flex flex-col justify-between shadow-sm"
            >
              <span
                class="text-[10px] font-bold text-slate-400 dark:text-[#6F767E] uppercase"
                >Total UMKM</span
              >
              <span
                class="text-2xl font-black text-slate-800 dark:text-white mt-1"
                >{{ mapStats.total }}</span
              >
            </div>

            <div
              class="bg-gradient-to-br from-[#F59E0B]/10 to-transparent p-3 rounded-2xl border border-[#F59E0B]/20 flex flex-col justify-between shadow-sm relative overflow-hidden"
            >
              <Activity
                class="absolute right-[-10px] bottom-[-10px] w-12 h-12 text-[#F59E0B]/10"
              />
              <span
                class="text-[10px] font-bold text-[#D97706] dark:text-[#F59E0B] uppercase"
                >Rata-rata Potensi</span
              >
              <span
                class="text-2xl font-black text-slate-800 dark:text-white mt-1 z-10"
                >{{ mapStats.avgPotential }}</span
              >
            </div>
          </div>

          <div
            class="bg-slate-100 dark:bg-[#22262A] p-3.5 rounded-2xl border border-slate-200/50 dark:border-[#2A2E33]/50 shadow-sm flex items-center justify-between"
          >
            <div class="flex items-center space-x-2.5 w-full">
              <div
                class="p-2 bg-white dark:bg-[#1A1D1F] rounded-xl shadow-sm shrink-0"
              >
                <Store class="w-4 h-4 text-[#F59E0B]" />
              </div>
              <div class="flex flex-col min-w-0 flex-1">
                <span
                  class="text-[10px] font-bold text-slate-400 dark:text-[#6F767E] uppercase"
                  >Kategori Dominan</span
                >
                <span
                  class="text-sm font-bold text-slate-800 dark:text-white truncate"
                  >{{ mapStats.topCategory }}</span
                >
              </div>
            </div>
          </div>

          <!-- Informasi Kelurahan -->
          <div
            v-if="selectedVillage"
            class="bg-white/50 dark:bg-[#1A1D1F]/50 p-4 rounded-2xl border border-slate-200/50 dark:border-[#2A2E33]/50 shadow-sm space-y-3"
          >
            <span
              class="block text-[10px] font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-widest mb-1"
              >Batas/Potensi Kelurahan {{ selectedVillage }}</span
            >
            <div class="space-y-2.5">
              <div class="flex items-center justify-between text-xs font-bold">
                <span
                  class="flex items-center text-slate-600 dark:text-[#6F767E]"
                  ><Users class="w-3.5 h-3.5 mr-2" /> Penduduk</span
                >
                <span class="text-slate-800 dark:text-white"
                  >{{ mapStats.population.toLocaleString("id-ID") }} Jiwa</span
                >
              </div>
              <div class="flex items-center justify-between text-xs font-bold">
                <span
                  class="flex items-center text-slate-600 dark:text-[#6F767E]"
                  ><GraduationCap class="w-3.5 h-3.5 mr-2" /> Sekolah</span
                >
                <span class="text-slate-800 dark:text-white"
                  >{{ mapStats.schoolsCount }} Unit</span
                >
              </div>
              <div class="flex items-center justify-between text-xs font-bold">
                <span
                  class="flex items-center text-slate-600 dark:text-[#6F767E]"
                  ><Landmark class="w-3.5 h-3.5 mr-2" /> Pemerintahan</span
                >
                <span class="text-slate-800 dark:text-white"
                  >{{ mapStats.govCount }} Unit</span
                >
              </div>
            </div>
          </div>

          <!-- Indikator Potensi -->
          <div
            class="bg-white/50 dark:bg-[#1A1D1F]/50 p-4 rounded-2xl border border-slate-200/50 dark:border-[#2A2E33]/50 shadow-sm space-y-3"
          >
            <span
              class="block text-[10px] font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-widest mb-1"
              >Sebaran Potensi Ekonomi</span
            >

            <div class="space-y-2.5">
              <div class="flex items-center justify-between text-xs font-bold">
                <span
                  class="flex items-center text-rose-600 dark:text-rose-400"
                  ><span
                    class="w-2 h-2 rounded-full bg-rose-500 mr-2 shadow-[0_0_8px_rgba(225,29,72,0.5)]"
                  ></span
                  >Tinggi</span
                >
                <span
                  class="text-slate-800 dark:text-white bg-slate-100 dark:bg-[#22262A] px-2 py-0.5 rounded-md"
                  >{{ mapStats.levelTinggi }}</span
                >
              </div>
              <div class="flex items-center justify-between text-xs font-bold">
                <span
                  class="flex items-center text-orange-600 dark:text-orange-400"
                  ><span
                    class="w-2 h-2 rounded-full bg-orange-500 mr-2 shadow-[0_0_8px_rgba(249,115,22,0.5)]"
                  ></span
                  >Sedang</span
                >
                <span
                  class="text-slate-800 dark:text-white bg-slate-100 dark:bg-[#22262A] px-2 py-0.5 rounded-md"
                  >{{ mapStats.levelSedang }}</span
                >
              </div>
              <div class="flex items-center justify-between text-xs font-bold">
                <span class="flex items-center text-amber-600 dark:text-amber-400"
                  ><span
                    class="w-2 h-2 rounded-full bg-amber-500 mr-2 shadow-[0_0_8px_rgba(245,158,11,0.5)]"
                  ></span
                  >Rendah</span
                >
                <span
                  class="text-slate-800 dark:text-white bg-slate-100 dark:bg-[#22262A] px-2 py-0.5 rounded-md"
                  >{{ mapStats.levelRendah }}</span
                >
              </div>
            </div>
          </div>
        </div>


      </div>
    </aside>

    <!-- Loading Overlay -->
    <div
      v-if="isLoading"
      class="absolute inset-0 z-[1000] bg-slate-900/60 backdrop-blur-sm flex flex-col items-center justify-center transition-all duration-300"
    >
      <div
        class="bg-white dark:bg-[#1A1D1F] p-8 rounded-3xl shadow-2xl flex flex-col items-center max-w-sm text-center border border-slate-200 dark:border-[#2A2E33]"
      >
        <div class="relative w-16 h-16 mb-6">
          <div
            class="absolute inset-0 border-4 border-slate-200 dark:border-[#2A2E33] rounded-full"
          ></div>
          <div
            class="absolute inset-0 border-4 border-[#F59E0B] rounded-full border-t-transparent animate-spin"
          ></div>
          <LocateFixed
            class="absolute inset-0 m-auto h-6 w-6 text-[#F59E0B] animate-pulse"
          />
        </div>
        <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">
          {{ loadingMessage }}
        </h3>
        <p class="text-sm text-slate-500 dark:text-[#6F767E]">
          Mohon tunggu sebentar, sistem sedang memproses permintaan Anda.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {
  ref,
  shallowRef,
  onMounted,
  onBeforeUnmount,
  watch,
  computed,
} from "vue";
import { RouterLink, useRouter } from "vue-router";
import {
  Map as MapIcon,
  Search,
  X,
  ChevronRight,
  ChevronLeft,

  LayoutDashboard,
  LogIn,
  LocateFixed,
  Sun,
  Moon,
  Layers,
  Activity,
  Store,
  Tag,
  Users,
  GraduationCap,
  Landmark,
  Menu,
} from "lucide-vue-next";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import api from "../../services/api";
import { useTheme } from "../../composables/useTheme";

// Declare global L for plugins
declare global {
  interface Window {
    L: any;
    router: any;
  }
}

const router = useRouter();
const { isDark, toggleTheme } = useTheme();

// State
const isLoggedIn = ref(!!localStorage.getItem("auth_token"));
const isLoading = ref(false);
const loadingMessage = ref("");
const sidebarOpen = ref(true);
const isDesktop = ref(window.innerWidth >= 768);

window.addEventListener("resize", () => {
  isDesktop.value = window.innerWidth >= 768;
});

// Use shallowRef for Leaflet objects to prevent massive performance drops during map animations!
const map = shallowRef<L.Map | null>(null);
const umkmLayer = shallowRef<L.LayerGroup | null>(null);


// Layers state
const villageLayer = shallowRef<L.GeoJSON | null>(null);
const settlementLayer = shallowRef<L.GeoJSON | null>(null);
const tradingLayer = shallowRef<L.GeoJSON | null>(null);
const schoolLayer = shallowRef<L.GeoJSON | null>(null);
const govLayer = shallowRef<L.GeoJSON | null>(null);

const basemap = ref("streets");
let tileLayer: L.TileLayer | null = null;

// Search & Filter state
const searchQuery = ref("");
const searchResults = ref<any[]>([]);
const allUmkms = ref<any[]>([]);
const filteredUmkms = ref<any[]>([]);
const villages = ref<any[]>([]);
const categories = ref<string[]>([]);

const selectedVillage = ref("");
const selectedCategory = ref("");

const visibleLayers = ref({
  umkm: true,
  village: false,
  settlement: false,
  trading: false,
  school: false,
  gov: false,
});

const showLayerSettings = ref(false);

const villageStats = ref<Record<string, any>>({});

const dashboardRoute = computed(() => {
  try {
    const userInfo = JSON.parse(localStorage.getItem("user_info") || "{}");
    if (userInfo && userInfo.role) {
      const urlRole =
        userInfo.role === "field_officer" ? "officer" : userInfo.role;
      return `/${urlRole}/dashboard`;
    }
  } catch (e) {
    // ignore
  }
  return "/login";
});

const mapStats = computed(() => {
  const data = filteredUmkms.value;
  const total = data.length;

  // Calculate specific village stats if one is selected
  let population = 0;
  let schoolsCount = 0;
  let govCount = 0;

  if (selectedVillage.value && villageStats.value[selectedVillage.value]) {
    const vStats = villageStats.value[selectedVillage.value];
    population = vStats.population || 0;
    schoolsCount = vStats.schools_count || 0;
    govCount = vStats.gov_count || 0;
  }

  if (total === 0)
    return {
      total: 0,
      avgPotential: "0.0",
      topCategory: "-",
      levelTinggi: 0,
      levelSedang: 0,
      levelRendah: 0,
      population,
      schoolsCount,
      govCount,
    };

  let totalScore = 0;
  const catCount: Record<string, number> = {};
  let levelTinggi = 0;
  let levelSedang = 0;
  let levelRendah = 0;

  data.forEach((u) => {
    totalScore += parseFloat(u.potential_score || "0");
    catCount[u.category] = (catCount[u.category] || 0) + 1;

    const lvl = (u.potential_level || "").toLowerCase();
    if (lvl === "tinggi") levelTinggi++;
    else if (lvl === "sedang") levelSedang++;
    else if (lvl === "rendah") levelRendah++;
  });

  let topCategory = "-";
  let maxCount = 0;
  for (const [cat, count] of Object.entries(catCount)) {
    if (count > maxCount) {
      maxCount = count;
      topCategory = cat;
    }
  }

  return {
    total,
    avgPotential: (totalScore / total).toFixed(1),
    topCategory,
    levelTinggi,
    levelSedang,
    levelRendah,
    population,
    schoolsCount,
    govCount,
  };
});



// Icons setup
const createUmkmIcon = (color: string) => {
  const shopSvg =
    '<path d="M3 7v4a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V7M2 7l2-4h16l2 4M12 12v9M4 12v9h16v-9"/>';
  return L.divIcon({
    className: "",
    html: `
      <div class="umkm-marker-icon" style="position: relative; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transform: translateY(-8px); cursor: pointer;">
        <div style="position: absolute; background-color: ${color}; width: 28px; height: 28px; border-radius: 50% 50% 50% 0px; transform: rotate(-45deg); border: 2px solid white; box-shadow: 2px 2px 6px rgba(0,0,0,0.3);"></div>
        <div style="position: absolute; z-index: 10; display: flex; align-items: center; justify-content: center; margin-top: -4px; color: white;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            ${shopSvg}
          </svg>
        </div>
      </div>
    `,
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32],
  });
};

const markerIcons = {
  tinggi: createUmkmIcon("#dc2626"), // Merah
  sedang: createUmkmIcon("#f97316"), // Oranye
  rendah: createUmkmIcon("#facc15"), // Kuning
  default: createUmkmIcon("#f97316"),
};

// Watch for authentication state changes
window.addEventListener("storage", () => {
  isLoggedIn.value = !!localStorage.getItem("auth_token");
});

// Watch for theme changes to update Stadia Maps
watch(isDark, () => {
  if (basemap.value === "streets") {
    setTileLayer("streets"); // Re-trigger to apply light/dark URL
  }
  // Re-render markers to update popup theme if needed
  renderUmkmMarkers();
});

function initMap() {
  map.value = L.map("map", {
    zoomControl: false,
    maxZoom: 20,
    preferCanvas: true, // Menggunakan Canvas rendering untuk performa super mulus pada poligon/marker
  }).setView([-1.8841, 106.1136], 13);

  L.control
    .zoom({
      position: "bottomright",
    })
    .addTo(map.value);


  // Create custom pane for polygons so they render below the overlayPane (which is used by Leaflet heat)
  map.value.createPane("polygonsPane");
  const polygonsPane = map.value.getPane("polygonsPane");
  if (polygonsPane) {
    polygonsPane.style.zIndex = "390";
  }

  umkmLayer.value = (L as any).markerClusterGroup({
    spiderfyOnMaxZoom: true,
    showCoverageOnHover: false,
    zoomToBoundsOnClick: true,
    maxClusterRadius: 50,
  }).addTo(map.value);

  setTileLayer("streets");

  const polygonStyle = {
    color: "#6F767E",
    weight: 2,
    opacity: 0.8,
    fillColor: "#64748b",
    fillOpacity: 0.1,
  };

  const settlementStyle = {
    color: "#a855f7",
    weight: 1,
    fillColor: "#a855f7",
    fillOpacity: 0.2,
  };

  const tradingStyle = {
    color: "#3b82f6",
    weight: 1,
    fillColor: "#3b82f6",
    fillOpacity: 0.2,
  };

  const getChoroplethColor = (score: number | null) => {
    if (!score) return "#94a3b8"; // slate-400 (Belum ada data)
    if (score >= 70) return "#dc2626"; // red-600 (Tinggi)
    if (score >= 40) return "#f97316"; // orange-500 (Sedang)
    return "#facc15"; // yellow-400 (Rendah)
  };

  const getChoroplethBorder = (score: number | null) => {
    if (!score) return "#64748b"; // slate-500
    if (score >= 70) return "#991b1b"; // red-800
    if (score >= 40) return "#c2410c"; // orange-700
    return "#ca8a04"; // yellow-600
  };

  const defaultVillageStyle = (feature: any) => {
    const score = feature?.properties?.avg_potential_score;
    return {
      color: getChoroplethBorder(score),
      weight: 2.5,
      opacity: 1.0,
      dashArray: "",
      fillColor: getChoroplethColor(score),
      fillOpacity: 0.35,
    };
  };

  const highlightVillageStyle = {
    weight: 4,
    fillOpacity: 0.6,
  };

  villageLayer.value = L.geoJSON(undefined, {
    pane: "polygonsPane",
    style: defaultVillageStyle,
    onEachFeature: (feature, layer) => {
      if (feature.properties && feature.properties.name) {
        const score = feature.properties.avg_potential_score
          ? Number(feature.properties.avg_potential_score).toFixed(2)
          : "Belum ada data";

        const tooltipHtml = `
          <div class="bg-white/95 dark:bg-[#1A1D1F]/95 border border-slate-200/50 dark:border-[#2A2E33]/50 rounded-xl shadow-xl p-3 flex flex-col items-center">
            <span class="text-[9px] font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-widest mb-0.5">Kelurahan</span>
            <span class="font-extrabold text-sm text-slate-800 dark:text-white">${feature.properties.name}</span>
            <div class="mt-1.5 px-2 py-0.5 bg-slate-100 dark:bg-[#111315] rounded-md border border-slate-200 dark:border-[#2A2E33]">
              <span class="text-[10px] font-semibold text-[#F59E0B]">Skor: ${score}</span>
            </div>
          </div>
        `;

        layer.bindTooltip(tooltipHtml, {
          permanent: false,
          direction: "center",
          className: "empty-tooltip",
        });
      }

      layer.on("mouseover", function (e) {
        const target = e.target;
        target.setStyle(highlightVillageStyle);
        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
          target.bringToFront();
        }
      });

      layer.on("mouseout", function (e) {
        if (villageLayer.value) {
          (villageLayer.value as L.GeoJSON).resetStyle(e.target);
          if (selectedVillage.value === feature.properties.name) {
            e.target.setStyle(highlightVillageStyle);
          } else if (selectedVillage.value) {
            e.target.setStyle({ fillOpacity: 0.1, opacity: 0.3 });
          }
        }
      });

      layer.on("click", () => {
        if (map.value && typeof (layer as any).getBounds === "function") {
          map.value.fitBounds((layer as any).getBounds(), {
            padding: [50, 50],
            animate: true,
            duration: 1,
          });
        }
        // Update selection and filter
        if (selectedVillage.value === feature.properties.name) {
          selectedVillage.value = ""; // toggle off
        } else {
          selectedVillage.value = feature.properties.name;
        }
        applyFilters();
      });
    },
  });

  // Watcher to handle dynamic dimming for village boundaries
  watch(selectedVillage, (newVal) => {
    if (!villageLayer.value) return;

    (villageLayer.value as L.GeoJSON).eachLayer((layer: any) => {
      (villageLayer.value as L.GeoJSON).resetStyle(layer);

      if (newVal && layer.feature.properties.name === newVal) {
        layer.setStyle(highlightVillageStyle);
      } else if (newVal) {
        layer.setStyle({ fillOpacity: 0.1, opacity: 0.3 });
      }
    });
  });

  settlementLayer.value = L.geoJSON(undefined, {
    pane: "polygonsPane",
    style: settlementStyle,
  });
  const pointToLayerMarker =
    (color: string, svgPath: string) => (feature: any, latlng: L.LatLng) => {
      return L.marker(latlng, {
        icon: L.divIcon({
          className: "",
          html: `
          <div style="position: relative; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transform: translateY(-8px); cursor: pointer;">
            <div style="position: absolute; background-color: ${color}; width: 28px; height: 28px; border-radius: 50% 50% 50% 0px; transform: rotate(-45deg); border: 2px solid white; box-shadow: 2px 2px 6px rgba(0,0,0,0.3);"></div>
            <div style="position: absolute; z-index: 10; display: flex; align-items: center; justify-content: center; margin-top: -4px; color: white;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                ${svgPath}
              </svg>
            </div>
          </div>
        `,
          iconSize: [32, 32],
          iconAnchor: [16, 32],
        }),
      });
    };

  tradingLayer.value = L.geoJSON(undefined, {
    pointToLayer: pointToLayerMarker("#f59e0b", '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>'),
    onEachFeature: (f, l) =>
      l.bindTooltip(`<div class="bg-white dark:bg-[#1A1D1F] text-[#f59e0b] font-bold border-l-4 border-[#f59e0b] border-y border-r border-y-slate-200 border-r-slate-200 dark:border-y-[#2A2E33] dark:border-r-[#2A2E33] px-3 py-1.5 rounded-lg shadow-lg text-xs whitespace-nowrap">${f.properties?.name || "Pusat Niaga"}</div>`, {
        className: "empty-tooltip",
        direction: "top",
        offset: [0, -32],
      }),
  });

  const schoolSvg =
    '<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>';
  const govSvg =
    '<path d="M3 21h18"/><path d="M9 8h1"/><path d="M9 12h1"/><path d="M9 16h1"/><path d="M14 8h1"/><path d="M14 12h1"/><path d="M14 16h1"/><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/>';

  schoolLayer.value = L.geoJSON(undefined, {
    pointToLayer: pointToLayerMarker("#10b981", schoolSvg),
    onEachFeature: (f, l) =>
      l.bindTooltip(`<div class="bg-white dark:bg-[#1A1D1F] text-[#10b981] font-bold border-l-4 border-[#10b981] border-y border-r border-y-slate-200 border-r-slate-200 dark:border-y-[#2A2E33] dark:border-r-[#2A2E33] px-3 py-1.5 rounded-lg shadow-lg text-xs whitespace-nowrap">${f.properties?.name || "Sekolah"}</div>`, {
        className: "empty-tooltip",
        direction: "top",
        offset: [0, -32],
      }),
  });

  govLayer.value = L.geoJSON(undefined, {
    pointToLayer: pointToLayerMarker("#10b981", govSvg),
    onEachFeature: (f, l) =>
      l.bindTooltip(`<div class="bg-white dark:bg-[#1A1D1F] text-[#10b981] font-bold border-l-4 border-[#10b981] border-y border-r border-y-slate-200 border-r-slate-200 dark:border-y-[#2A2E33] dark:border-r-[#2A2E33] px-3 py-1.5 rounded-lg shadow-lg text-xs whitespace-nowrap">${f.properties?.name || "Fasilitas Pemerintahan"}</div>`, {
        className: "empty-tooltip",
        direction: "top",
        offset: [0, -32],
      }),
  });
}

function setTileLayer(type: string) {
  if (tileLayer && map.value) {
    map.value.removeLayer(tileLayer);
  }

  basemap.value = type;

  if (type === "streets") {
    // OSM / CartoDB tiles based on theme
    const url = isDark.value
      ? "https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png"
      : "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";

    tileLayer = L.tileLayer(url, {
      maxZoom: 19,
      attribution: isDark.value
        ? '&copy; <a href="https://carto.com/">CARTO</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        : '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      subdomains: isDark.value ? 'abcd' : 'a',
    });
  } else {
    // Satellite - Esri World Imagery (free)
    tileLayer = L.tileLayer(
      "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
      {
        maxZoom: 19,
        attribution: "Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community",
      }
    );
  }

  if (map.value && tileLayer) {
    tileLayer.addTo(map.value);
    tileLayer.bringToBack();
  }
}

function changeBasemap(type: string) {
  setTileLayer(type);
}

async function fetchInitialData() {
  try {
    // Use the correct public map endpoint for UMKMs
    const umkmRes = await api.get("/map/umkms");

    if (umkmRes.data && umkmRes.data.features) {
      allUmkms.value = umkmRes.data.features.map((f: any) => ({
        id: f.properties.id,
        name: f.properties.name,
        owner: f.properties.owner,
        category: f.properties.category,
        village_id: f.properties.village_id,
        village_name: f.properties.village_name,
        potential_level: f.properties.potential_level,
        potential_score: f.properties.potential_score,
        geometry: f.geometry,
      }));

      // Extract unique villages and categories safely from the public geojson data
      // This avoids calling protected API endpoints (/villages, /umkms/categories) when unauthenticated
      const uniqueCats = new Set<string>();
      const uniqueVills = new Map<number, { id: number; name: string }>();

      allUmkms.value.forEach((u) => {
        if (u.category) uniqueCats.add(u.category);
        if (u.village_id && u.village_name) {
          uniqueVills.set(u.village_id, {
            id: u.village_id,
            name: u.village_name,
          });
        }
      });

      categories.value = Array.from(uniqueCats).sort();
      villages.value = Array.from(uniqueVills.values()).sort(
        (a: { name: string }, b: { name: string }) =>
          a.name.localeCompare(b.name),
      );

      filteredUmkms.value = [...allUmkms.value];

      // Fetch village stats to display in sidebar
      try {
        const vRes = await api.get("/dashboard/by-village");
        if (vRes.data && vRes.data.data) {
          const statsMap: Record<string, any> = {};
          vRes.data.data.forEach((v: any) => {
            statsMap[v.name] = v;
          });
          villageStats.value = statsMap;
        }
      } catch (e) {
        console.error("Failed fetching village stats", e);
      }

      renderUmkmMarkers();
    }
  } catch (err) {
    console.error("Error fetching map initial data:", err);
  }
}

async function fetchSpatialLayer(layerName: string, layer: any) {
  try {
    // Correct public endpoints defined in routes/api.php
    const endpointMap: Record<string, string> = {
      village: "/map/villages",
      settlement: "/map/settlements",
      trading: "/map/trading-centers",
      school: "/map/schools",
      gov: "/map/government-facilities",
    };

    const endpoint = endpointMap[layerName];
    if (!endpoint) return;

    const res = await api.get(endpoint);
    if (res.data && res.data.features && layer) {
      layer.clearLayers();
      layer.addData(res.data);
    }
  } catch (err) {
    console.error(`Error fetching spatial layer ${layerName}:`, err);
  }
}


function renderUmkmMarkers() {
  if (!umkmLayer.value) return;

  umkmLayer.value.clearLayers();

  if (!visibleLayers.value.umkm) return;

  filteredUmkms.value.forEach((umkm) => {
    if (!umkm.geometry || !umkm.geometry.coordinates) return;

    const lat = umkm.geometry.coordinates[1];
    const lng = umkm.geometry.coordinates[0];

    let icon = markerIcons.default;
    const level = umkm.potential_level
      ? String(umkm.potential_level).toLowerCase()
      : "";

    if (level === "tinggi") icon = markerIcons.tinggi;
    else if (level === "sedang") icon = markerIcons.sedang;
    else if (level === "rendah") icon = markerIcons.rendah;

    const marker = L.marker([lat, lng], { icon });

    // Dynamic popup styling based on theme
    const popupBg = isDark.value ? "#1A1D1F" : "#ffffff";
    const popupText = isDark.value ? "#ffffff" : "#0f172a";
    const popupBorder = isDark.value ? "#2A2E33" : "#e2e8f0";
    const popupMuted = isDark.value ? "#6F767E" : "#64748b";
    const btnBg = isDark.value ? "#22262A" : "#f1f5f9";
    const btnHover = isDark.value ? "#2A2E33" : "#e2e8f0";

    const popupContent = `
      <div class="p-4 w-64 rounded-xl" style="background-color: ${popupBg}; color: ${popupText};">
        <h3 class="font-bold text-[#D97706] dark:text-[#F59E0B] text-base mb-1 border-b pb-2" style="border-color: ${popupBorder}">${umkm.name}</h3>
        <div class="space-y-1.5 mt-2 text-xs">
          <p><span style="color: ${popupMuted}">Pemilik:</span> ${umkm.owner}</p>
          <p><span style="color: ${popupMuted}">Kategori:</span> ${umkm.category}</p>
          <p><span style="color: ${popupMuted}">Kelurahan:</span> ${umkm.village_name}</p>
          <p class="pt-1"><span style="color: ${popupMuted}">Potensi:</span> <span class="font-bold uppercase tracking-wide text-${level === "tinggi" ? "rose" : level === "sedang" ? "orange" : "amber"}-500">${umkm.potential_level}</span> (${Number(umkm.potential_score).toFixed(2)})</p>
        </div>
        <div class="mt-4">
          <button onclick="window.router.push('/umkm/${umkm.id}')" class="w-full text-xs py-2 rounded-lg font-medium transition-colors border" style="background-color: ${btnBg}; border-color: ${popupBorder}; color: ${popupText}" onmouseover="this.style.backgroundColor='${btnHover}'" onmouseout="this.style.backgroundColor='${btnBg}'">Detail</button>
        </div>
      </div>
    `;

    marker.bindPopup(popupContent, {
      className: isDark.value ? "dark-popup" : "light-popup",
    });
    umkmLayer.value?.addLayer(marker);
  });
}

function handleSearch() {
  if (!searchQuery.value || searchQuery.value.length < 2) {
    searchResults.value = [];
    applyFilters();
    return;
  }

  const q = searchQuery.value.toLowerCase();
  searchResults.value = allUmkms.value
    .filter(
      (u) =>
        u.name.toLowerCase().includes(q) || u.owner.toLowerCase().includes(q),
    )
    .slice(0, 5);

  filteredUmkms.value = searchResults.value;
  renderUmkmMarkers();
}

function clearSearch() {
  searchQuery.value = "";
  searchResults.value = [];
  applyFilters();
}

function focusUmkm(umkm: any) {
  searchQuery.value = umkm.name;
  searchResults.value = [];
  filteredUmkms.value = [umkm];
  renderUmkmMarkers();

  if (map.value && umkm.geometry) {
    map.value.flyTo(
      [umkm.geometry.coordinates[1], umkm.geometry.coordinates[0]],
      18,
      {
        duration: 1.5,
      },
    );
  }
}

function applyFilters() {
  filteredUmkms.value = allUmkms.value.filter((u) => {
    let match = true;

    if (searchQuery.value && !searchResults.value.some((r) => r.id === u.id)) {
      if (
        !u.name.toLowerCase().includes(searchQuery.value.toLowerCase()) &&
        !u.owner.toLowerCase().includes(searchQuery.value.toLowerCase())
      ) {
        match = false;
      }
    }

    if (selectedVillage.value && u.village_name !== selectedVillage.value) {
      match = false;
    }

    if (selectedCategory.value && u.category !== selectedCategory.value) {
      match = false;
    }

    return match;
  });

  renderUmkmMarkers();
}
async function toggleLayer(layerName: string) {
  if (!map.value) return;

  if (layerName === "umkm") {
    if (visibleLayers.value.umkm) {
      map.value.addLayer(umkmLayer.value as L.LayerGroup);
      renderUmkmMarkers();
    } else {
      if (umkmLayer.value) {
        map.value.removeLayer(umkmLayer.value as L.LayerGroup);
      }
    }
    return;
  }

  const layerMap: Record<string, L.GeoJSON | null> = {
    village: villageLayer.value,
    settlement: settlementLayer.value,
    trading: tradingLayer.value,
    school: schoolLayer.value,
    gov: govLayer.value,
  };

  const layer = layerMap[layerName];

  if (visibleLayers.value[layerName as keyof typeof visibleLayers.value]) {
    try {
      if (!layer || Object.keys(layer.getLayers()).length === 0) {
        await fetchSpatialLayer(layerName, layerMap[layerName]);
      }
      if (layerMap[layerName]) {
        map.value.addLayer(layerMap[layerName] as L.LayerGroup);
      }
    } catch (err) {
      console.error(`Error toggling layer ${layerName}:`, err);
      visibleLayers.value[layerName as keyof typeof visibleLayers.value] =
        false;
    }
  } else {
    if (layer) {
      map.value.removeLayer(layer);
    }
  }
}

function getCurrentLocation() {
  if (!navigator.geolocation) {
    alert("Geolocation tidak didukung oleh browser ini.");
    return;
  }

  isLoading.value = true;
  loadingMessage.value = "Mendapatkan Lokasi Anda...";

  navigator.geolocation.getCurrentPosition(
    (position) => {
      let lat = position.coords.latitude;
      let lng = position.coords.longitude;

      // MOCK LOCATION LOGIC FOR TESTING
      if (lat > -1.7 || lat < -2.1 || lng < 105.9 || lng > 106.3) {
        console.warn(
          "User berada di luar Sungailiat, menggunakan lokasi simulasi (Pusat Kota).",
        );
        lat = -1.8841;
        lng = 106.1136;
      }

      if (map.value) {
        map.value.flyTo(L.latLng(lat, lng), 16);
      }

      isLoading.value = false;
    },
    (error) => {
      console.warn("Error geolocation:", error);
      loadingMessage.value = "Menyiapkan Lokasi Simulasi...";

      setTimeout(() => {
        if (map.value) {
          map.value.flyTo([-1.8841, 106.1136], 16);
        }

        isLoading.value = false;
      }, 1000);
    },
  );
}


onMounted(async () => {
  window.router = router;

  // Initialize Leaflet globals before loading plugins
  window.L = window.L || L;

  try {
    // Dynamically import Leaflet plugins so they can access window.L
    await import("leaflet.markercluster");
    await import("leaflet.markercluster/dist/MarkerCluster.css");
    await import("leaflet.markercluster/dist/MarkerCluster.Default.css");
    await import("leaflet.heat");
  } catch (err) {
    console.error("Failed to load Leaflet plugins:", err);
  }

  initMap();
  fetchInitialData();

  // Fix Leaflet tile loading issue by invalidating size after layout is fully rendered
  setTimeout(() => {
    if (map.value) {
      map.value.invalidateSize();
    }
  }, 100);
});

onBeforeUnmount(() => {
  if (map.value) {
    map.value.remove();
  }
  delete window.router;
});
</script>

<style>
/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #2a2e33;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #f59e0b;
}

/* Dynamic Popup Theme Override */
.light-popup .leaflet-popup-content-wrapper {
  background-color: #ffffff !important;
  color: #0f172a !important;
  border: 1px solid #e2e8f0 !important;
}
.light-popup .leaflet-popup-tip {
  background-color: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
}
.dark-popup .leaflet-popup-content-wrapper {
  background-color: #1a1d1f !important;
  color: #f0f0f0 !important;
  border: 1px solid #2a2e33 !important;
}
.dark-popup .leaflet-popup-tip {
  background-color: #1a1d1f !important;
  border: 1px solid #2a2e33 !important;
}

/* Tooltip Theme Fix */
.empty-tooltip {
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  padding: 0 !important;
}
.empty-tooltip::before {
  display: none !important;
}
</style>
