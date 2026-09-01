<template>
  <div class="space-y-6 font-sans text-slate-900 dark:text-white pb-10 transition-colors">
    <!-- Header panel -->
    <div class="bg-white/80 dark:bg-[#1A1D1F]/80 backdrop-blur-md border border-slate-200/50 dark:border-[#2A2E33]/50 p-6 rounded-3xl flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
      <div>
        <span class="text-[10px] font-bold text-[#D97706] dark:text-[#F59E0B] bg-[#F59E0B]/10 px-3 py-1 rounded-full uppercase tracking-widest border border-[#F59E0B]/20">UMKM KULINER SUNGAILIAT</span>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white mt-3">{{ loading ? 'Memuat Data...' : umkm?.name }}</h1>
        <div class="flex items-center space-x-2 text-slate-500 dark:text-[#8B949E] text-xs font-semibold mt-2">
          <span class="flex items-center"><Store class="w-3.5 h-3.5 mr-1 text-slate-400 dark:text-[#6F767E]"/> {{ umkm?.category }}</span>
          <span>&bull;</span>
          <span class="flex items-center"><MapPin class="w-3.5 h-3.5 mr-1 text-slate-400 dark:text-[#6F767E]"/> {{ umkm?.village?.name || umkm?.village_name }}</span>
        </div>
      </div>

      <div class="flex items-center space-x-3">
        <button 
          @click="goBack" 
          class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1.5"
          :class="isDark ? 'bg-[#22262A] hover:bg-[#2A2E33] text-white border border-[#2A2E33]' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200'"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Kembali</span>
        </button>
        <RouterLink 
          v-if="isLoggedIn"
          :to="`/admin/umkm/${id}/edit`" 
          class="px-5 py-2.5 bg-[#F59E0B] hover:shadow-lg hover:shadow-amber-500/20 text-[#111315] rounded-xl text-xs font-bold transition-all flex items-center space-x-1.5"
        >
          <Edit class="w-4 h-4" />
          <span>Ubah Data</span>
        </RouterLink>
      </div>
    </div>

    <!-- Error State -->
    <div v-if="error" class="p-4 bg-red-50 dark:bg-red-500/10 border-l-4 border-red-500 rounded-r-2xl text-red-700 dark:text-red-400 text-sm font-medium flex items-center space-x-2">
      <AlertCircle class="w-5 h-5 shrink-0" />
      <span>{{ error }}</span>
    </div>

    <div v-if="!loading && umkm" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Left Column: Photos -->
      <div class="space-y-6">
        <div class="bg-white/80 dark:bg-[#1A1D1F]/80 backdrop-blur-md border border-slate-200/50 dark:border-[#2A2E33]/50 rounded-3xl p-6 shadow-sm">
          <h3 class="font-extrabold text-[15px] tracking-wide text-slate-800 dark:text-white border-b border-slate-100 dark:border-[#2A2E33] pb-3 mb-5 flex items-center space-x-2">
            <ImageIcon class="w-4 h-4 text-[#F59E0B]" />
            <span>Galeri Foto Usaha</span>
          </h3>
          
          <!-- Primary photo -->
          <div class="aspect-video bg-slate-100 dark:bg-[#111315] border border-slate-200/50 dark:border-[#2A2E33]/50 rounded-2xl overflow-hidden relative flex items-center justify-center group shadow-inner">
            <img 
              v-if="activePhotoUrl" 
              :src="activePhotoUrl" 
              alt="Foto UMKM" 
              class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105"
            />
            <div v-else class="text-slate-400 dark:text-[#6F767E] text-xs flex flex-col items-center space-x-1.5 font-medium">
              <Camera class="h-8 w-8 mb-2 opacity-50" />
              <span>Belum ada foto</span>
            </div>
          </div>

          <!-- Thumbnails -->
          <div v-if="umkm.photos && umkm.photos.length > 0" class="grid grid-cols-4 gap-3 mt-4">
            <button 
              v-for="photo in umkm.photos" 
              :key="photo.id" 
              @click="activePhotoUrl = photo.url"
              class="aspect-square rounded-xl overflow-hidden bg-slate-50 dark:bg-[#111315] transition-all cursor-pointer border-2"
              :class="activePhotoUrl === photo.url ? 'border-[#F59E0B] shadow-md' : 'border-transparent opacity-70 hover:opacity-100'"
            >
              <img :src="photo.url" alt="Thumb" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>

        <!-- Upload Card -->
        <div v-if="isLoggedIn" class="bg-white/80 dark:bg-[#1A1D1F]/80 backdrop-blur-md border border-slate-200/50 dark:border-[#2A2E33]/50 rounded-3xl p-6 shadow-sm">
          <h3 class="font-extrabold text-[15px] tracking-wide text-slate-800 dark:text-white border-b border-slate-100 dark:border-[#2A2E33] pb-3 mb-5 flex items-center space-x-2">
            <Upload class="w-4 h-4 text-[#F59E0B]" />
            <span>Unggah Foto Baru</span>
          </h3>
          
          <form @submit.prevent="handlePhotoUpload" class="space-y-4">
            <div>
              <label class="block text-[10px] font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider mb-2">Pilih File Foto</label>
              <input 
                type="file" 
                ref="photoInput"
                required
                accept="image/*"
                class="block w-full text-xs text-slate-500 dark:text-[#8B949E] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#F59E0B]/10 file:text-[#D97706] dark:file:text-[#F59E0B] hover:file:bg-[#F59E0B]/20 cursor-pointer"
              />
            </div>
            
            <label class="flex items-center space-x-2 text-xs font-semibold text-slate-600 dark:text-[#8B949E] cursor-pointer group">
              <div class="relative flex items-center justify-center">
                <input type="checkbox" v-model="uploadIsPrimary" class="peer appearance-none h-5 w-5 border-2 border-slate-300 dark:border-[#2A2E33] rounded bg-white dark:bg-[#111315] checked:bg-[#F59E0B] checked:border-[#F59E0B] transition-all cursor-pointer" />
                <Check class="absolute w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 pointer-events-none" />
              </div>
              <span class="group-hover:text-slate-900 dark:group-hover:text-white transition-colors">Jadikan foto utama</span>
            </label>

            <button 
              type="submit" 
              :disabled="uploading"
              class="w-full py-3 bg-slate-900 dark:bg-[#22262A] hover:bg-slate-800 dark:hover:bg-[#2A2E33] text-white rounded-xl text-xs font-bold transition-colors disabled:opacity-50 cursor-pointer border border-transparent dark:border-[#2A2E33] flex justify-center items-center"
            >
              <Loader2 v-if="uploading" class="w-4 h-4 mr-2 animate-spin" />
              {{ uploading ? 'Mengunggah...' : 'Unggah Foto Sekarang' }}
            </button>
          </form>
        </div>
      </div>

      <!-- Middle Column: Info & Potential -->
      <div class="space-y-6">
        <div class="bg-white/80 dark:bg-[#1A1D1F]/80 backdrop-blur-md border border-slate-200/50 dark:border-[#2A2E33]/50 rounded-3xl p-6 shadow-sm">
          <h3 class="font-extrabold text-[15px] tracking-wide text-slate-800 dark:text-white border-b border-slate-100 dark:border-[#2A2E33] pb-3 mb-5 flex items-center space-x-2">
            <Info class="w-4 h-4 text-[#F59E0B]" />
            <span>Informasi Detail</span>
          </h3>
          
          <div class="space-y-5 text-sm">
            <div class="bg-slate-50 dark:bg-[#111315] p-3 rounded-xl border border-slate-100 dark:border-[#22262A]">
              <span class="block text-[10px] font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Nama Pemilik</span>
              <span class="text-slate-800 dark:text-[#F0F0F0] font-bold mt-1 block">{{ umkm.owner }}</span>
            </div>
            <div class="bg-slate-50 dark:bg-[#111315] p-3 rounded-xl border border-slate-100 dark:border-[#22262A]">
              <span class="block text-[10px] font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Kategori Usaha</span>
              <span class="text-slate-800 dark:text-[#F0F0F0] font-bold mt-1 block">{{ umkm.category }}</span>
            </div>
            <div class="bg-slate-50 dark:bg-[#111315] p-3 rounded-xl border border-slate-100 dark:border-[#22262A]">
              <span class="block text-[10px] font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Alamat Lengkap</span>
              <p class="text-slate-600 dark:text-[#8B949E] font-medium mt-1 block leading-relaxed">{{ umkm.address }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-[#111315] p-3 rounded-xl border border-slate-100 dark:border-[#22262A]">
              <span class="block text-[10px] font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Koordinat Spasial</span>
              <div class="flex items-center space-x-2 mt-1">
                <span class="bg-slate-200/50 dark:bg-[#22262A] px-2 py-1 rounded text-slate-600 dark:text-[#F0F0F0] font-mono text-xs">Lat: {{ umkm.latitude }}</span>
                <span class="bg-slate-200/50 dark:bg-[#22262A] px-2 py-1 rounded text-slate-600 dark:text-[#F0F0F0] font-mono text-xs">Lng: {{ umkm.longitude }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/10 rounded-3xl p-6 relative overflow-hidden">
          <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#F59E0B]/20 rounded-full blur-2xl"></div>
          
          <h3 class="font-extrabold text-[15px] tracking-wide text-slate-800 dark:text-white border-b border-slate-200/50 dark:border-[#2A2E33]/50 pb-3 mb-5 flex items-center space-x-2">
            <Activity class="w-4 h-4 text-[#F59E0B]" />
            <span>Hasil Analisis Spasial</span>
          </h3>
          
          <div class="flex items-center justify-between mt-2">
            <div>
              <span class="block text-[10px] font-bold text-slate-500 dark:text-[#8B949E] uppercase tracking-wider">Skor Potensi</span>
              <span class="text-4xl font-black text-slate-900 dark:text-white mt-1 block drop-shadow-sm">{{ umkm.potential_score ? Number(umkm.potential_score).toFixed(1) : '-' }}</span>
            </div>
            <div class="text-right">
              <span class="block text-[10px] font-bold text-slate-500 dark:text-[#8B949E] uppercase tracking-wider">Tingkat Potensi</span>
              <span 
                :class="[
                  'px-4 py-1.5 rounded-lg text-xs font-black uppercase tracking-widest block mt-2 text-center shadow-sm',
                  getPotentialBg(umkm.potential_level)
                ]"
              >
                {{ umkm.potential_level || 'Belum dihitung' }}
              </span>
            </div>
          </div>

          <p class="mt-5 text-[11px] font-medium text-slate-500 dark:text-[#6F767E] leading-relaxed bg-white/50 dark:bg-[#111315]/50 p-3 rounded-xl">
            Skor potensi dihitung secara otomatis menggunakan algoritma spasial berdasarkan jarak terdekat dengan jalan utama, kepadatan pemukiman, dan aksesibilitas ke fasilitas perdagangan.
          </p>
        </div>
      </div>

      <!-- Right Column: Map -->
      <div class="bg-white/80 dark:bg-[#1A1D1F]/80 backdrop-blur-md border border-slate-200/50 dark:border-[#2A2E33]/50 rounded-3xl p-6 flex flex-col min-h-[400px] shadow-sm">
        <h3 class="font-extrabold text-[15px] tracking-wide text-slate-800 dark:text-white border-b border-slate-100 dark:border-[#2A2E33] pb-3 mb-5 flex items-center space-x-2">
          <MapIcon class="w-4 h-4 text-[#F59E0B]" />
          <span>Peta Lokasi Interaktif</span>
        </h3>
        
        <div class="flex-1 border-2 border-slate-200/50 dark:border-[#2A2E33]/50 rounded-2xl overflow-hidden relative shadow-inner">
          <div id="detail-map" class="w-full h-full min-h-[300px] bg-slate-100 dark:bg-[#111315]"></div>
          
          <!-- Map Style Toggle Overlay -->
          <div class="absolute bottom-4 right-4 z-[400]">
            <button @click="toggleTheme" class="bg-white/90 dark:bg-[#1A1D1F]/90 backdrop-blur border border-slate-200 dark:border-[#2A2E33] p-2 rounded-xl shadow-lg hover:scale-105 transition-transform cursor-pointer">
              <Sun v-if="isDark" class="w-4 h-4 text-[#F59E0B]" />
              <Moon v-else class="w-4 h-4 text-slate-700" />
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick, computed, watch, shallowRef } from 'vue';
import { useRoute, useRouter, RouterLink } from 'vue-router';
import { Camera, Image as ImageIcon, Upload, Check, Loader2, Store, MapPin, ArrowLeft, Edit, AlertCircle, Info, Activity, Map as MapIcon, Sun, Moon } from 'lucide-vue-next';
import L from 'leaflet';
import api from '../../services/api';
import { useTheme } from '../../composables/useTheme';

const props = defineProps({
  id: {
    type: String,
    required: true
  }
});

const route = useRoute();
const router = useRouter();
const { isDark, toggleTheme } = useTheme();

const isLoggedIn = ref(!!localStorage.getItem('auth_token'));

const umkm = ref<any>(null);
const loading = ref(true);
const error = ref('');

// Photo Upload states
const photoInput = ref<HTMLInputElement | null>(null);
const uploading = ref(false);
const uploadIsPrimary = ref(false);
const activePhotoUrl = ref('');

// Map
const detailMap = shallowRef<L.Map | null>(null);
let tileLayer: L.TileLayer | null = null;

onMounted(async () => {
  await fetchDetail();
  await nextTick();
  if (umkm.value) {
    initDetailMap();
  }
});

// Watch theme changes to update map tiles
watch(isDark, () => {
  if (detailMap.value && tileLayer) {
    const url = isDark.value
      ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
      : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    tileLayer.setUrl(url);
    if (isDark.value) {
      tileLayer.setUrl(url);
    }
  }
});

async function fetchDetail() {
  loading.value = true;
  error.value = '';
  try {
    if (isLoggedIn.value) {
      const res = await api.get(`/umkms/${props.id}`);
      umkm.value = res.data.data;
    } else {
      const res = await api.get('/map/umkms');
      const match = res.data.features.find((f: any) => f.properties.id == props.id);
      if (match) {
        const props = match.properties;
        const coords = match.geometry.coordinates;
        umkm.value = {
          id: props.id,
          name: props.name,
          owner: props.owner,
          category: props.category,
          address: props.address,
          latitude: coords[1],
          longitude: coords[0],
          potential_score: props.potential_score,
          potential_level: props.potential_level,
          village_name: props.village_name,
          photos: props.primary_photo_url ? [{ id: 1, url: props.primary_photo_url, is_primary: true }] : []
        };
      } else {
        throw new Error('UMKM not found');
      }
    }

    if (umkm.value && umkm.value.photos && umkm.value.photos.length > 0) {
      const primary = umkm.value.photos.find((p: any) => p.is_primary);
      activePhotoUrl.value = primary ? primary.url : umkm.value.photos[0].url;
    } else if (umkm.value && umkm.value.primary_photo_url) {
      activePhotoUrl.value = umkm.value.primary_photo_url;
    }
  } catch (err: any) {
    console.error('Error fetching UMKM details:', err);
    error.value = 'Gagal memuat detail data UMKM. Pastikan ID valid.';
  } finally {
    loading.value = false;
  }
}

function initDetailMap() {
  if (!umkm.value || detailMap.value) return;
  
  const coords: L.LatLngExpression = [umkm.value.latitude, umkm.value.longitude];
  
  detailMap.value = L.map('detail-map', {
    center: coords,
    zoom: 16,
    zoomControl: false,
    attributionControl: false
  });
  
  L.control.zoom({ position: 'topright' }).addTo(detailMap.value);

  // Standard OSM tiles
  const tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

  tileLayer = L.tileLayer(tileUrl, {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    subdomains: 'abc',
  }).addTo(detailMap.value);

  const normLevel = String(umkm.value.potential_level).toLowerCase();
  const color = normLevel === 'tinggi' ? '#10b981' : (normLevel === 'sedang' ? '#f59e0b' : '#ef4444');

  // Custom teardrop SVG marker
  const svgIcon = `
    <svg viewBox="0 0 24 24" fill="${color}" xmlns="http://www.w3.org/2000/svg">
      <path d="M12 0C7.58 0 4 3.58 4 8c0 5.25 8 16 8 16s8-10.75 8-16c0-4.42-3.58-8-8-8zm0 11.5c-1.93 0-3.5-1.57-3.5-3.5S10.07 4.5 12 4.5 15.5 6.07 15.5 8 13.93 11.5 12 11.5z" stroke="white" stroke-width="1.5"/>
    </svg>
  `;

  const customIcon = L.divIcon({
    className: '',
    html: `<div style="width: 36px; height: 36px; transform: translateY(-10px); filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));">${svgIcon}</div>`,
    iconSize: [36, 36],
    iconAnchor: [18, 36]
  });

  L.marker(coords, { icon: customIcon }).addTo(detailMap.value);
}

async function handlePhotoUpload() {
  if (!photoInput.value || !photoInput.value.files || photoInput.value.files.length === 0) return;
  
  const file = photoInput.value.files[0];
  if (!file) return;
  
  uploading.value = true;
  const formData = new FormData();
  formData.append('photo', file);
  formData.append('is_primary', uploadIsPrimary.value ? '1' : '0');
  
  try {
    await api.post(`/umkms/${props.id}/photos`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    if (photoInput.value) photoInput.value.value = '';
    uploadIsPrimary.value = false;
    
    await fetchDetail();
  } catch (err: any) {
    console.error('Error uploading photo:', err);
    alert('Gagal mengunggah foto. Pastikan format file berupa JPEG/PNG dengan ukuran kecil.');
  } finally {
    uploading.value = false;
  }
}

function getPotentialBg(level: string): string {
  const norm = String(level).toLowerCase();
  if (norm === 'tinggi') return 'bg-emerald-500 text-white border border-emerald-400';
  if (norm === 'sedang') return 'bg-amber-500 text-white border border-amber-400';
  return 'bg-rose-500 text-white border border-rose-400';
}

function goBack() {
  if (isLoggedIn.value) {
    router.push('/admin/umkm');
  } else {
    router.push('/');
  }
}
</script>
