<template>
  <div class="space-y-6">

    <!-- Header panel -->
    <div class="bg-white dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200 dark:border-[#2A2E33]/60 p-6 rounded-2xl shadow-xl flex items-center justify-between">
      <div>
        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-500/20 px-2.5 py-1 rounded-lg">DETAIL KELURAHAN SUNGAILIAT</span>
        <h1 class="text-xl font-bold text-slate-900 dark:text-white mt-2">{{ loading ? 'Memuat Kelurahan...' : village?.name }}</h1>
        <p class="text-slate-500 dark:text-[#6F767E] text-sm mt-1">Informasi atribut spasial dan demografi penduduk kelurahan.</p>
      </div>
      <button 
        @click="$router.push('/admin/villages')" 
        class="px-4 py-2 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs font-semibold hover:bg-slate-50 dark:hover:bg-[#22262A] text-slate-700 dark:text-[#F0F0F0] bg-white dark:bg-[#1A1D1F] transition-colors cursor-pointer"
      >
        Kembali
      </button>
    </div>

    <!-- Error State -->
    <div v-if="error" class="p-4 bg-red-50 dark:bg-red-500/10 border-l-4 border-red-500 text-red-700 dark:text-red-400 text-xs rounded-lg">
      {{ error }}
    </div>

    <div v-if="!loading && village" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- Info and list (Left 2 cols) -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Stats Row -->
        <div class="grid grid-cols-3 gap-4">
          <div class="bg-white dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl text-center">
            <span class="block text-xs font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Populasi Penduduk</span>
            <span class="text-lg font-bold text-slate-800 dark:text-white mt-2 block">
              {{ village.population ? Number(village.population).toLocaleString('id-ID') : '-' }} Jiwa
            </span>
          </div>
          <div class="bg-white dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl text-center">
            <span class="block text-xs font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Luas Wilayah</span>
            <span class="text-lg font-bold text-slate-800 dark:text-white mt-2 block">
              {{ village.area_km2 ? Number(village.area_km2).toFixed(2) + ' km²' : '-' }}
            </span>
          </div>
          <div class="bg-white dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl text-center">
            <span class="block text-xs font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Kepadatan Penduduk</span>
            <span class="text-lg font-bold text-slate-800 dark:text-white mt-2 block">
              {{ village.density ? Number(village.density).toFixed(1) + ' jiwa/km²' : '-' }}
            </span>
          </div>
        </div>

        <!-- UMKMs in this village table -->
        <div class="bg-white dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200 dark:border-[#2A2E33]/60 rounded-2xl shadow-xl p-6">
          <h3 class="font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-[#2A2E33] pb-3 mb-4 flex items-center justify-between">
            <span>Daftar UMKM Kuliner Aktif ({{ localUmkms.length }})</span>
          </h3>

          <div v-if="localUmkms.length === 0" class="text-center py-8 text-slate-400 dark:text-[#6F767E] text-xs">
            Belum ada UMKM kuliner terdaftar di wilayah kelurahan ini.
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
              <thead>
                <tr class="border-b border-slate-200 dark:border-[#2A2E33] text-slate-400 dark:text-[#6F767E] font-bold uppercase">
                  <th class="pb-2">Nama Usaha</th>
                  <th class="pb-2">Pemilik</th>
                  <th class="pb-2">Kategori</th>
                  <th class="pb-2 text-center">Potensi</th>
                  <th class="pb-2 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr 
                  v-for="umkm in localUmkms" 
                  :key="umkm.id"
                  class="border-b border-slate-100 dark:border-[#2A2E33] hover:bg-slate-50/50 dark:hover:bg-[#22262A] transition-colors"
                >
                  <td class="py-3 font-bold text-slate-800 dark:text-white">{{ umkm.name }}</td>
                  <td class="py-3 text-slate-600 dark:text-[#F0F0F0]">{{ umkm.owner }}</td>
                  <td class="py-3 text-slate-600 dark:text-[#F0F0F0]">{{ umkm.category }}</td>
                  <td class="py-3 text-center">
                    <span 
                      :class="[
                        'px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider inline-block text-white',
                        getPotentialColor(umkm.potential_level)
                      ]"
                    >
                      {{ umkm.potential_level }}
                    </span>
                  </td>
                  <td class="py-3 text-right">
                    <RouterLink 
                      :to="'/umkm/' + umkm.id" 
                      class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold"
                    >
                      Detail &rarr;
                    </RouterLink>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <!-- Leaflet map (Right 1 col) -->
      <div class="bg-white dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200 dark:border-[#2A2E33]/60 rounded-2xl shadow-xl p-6 flex flex-col min-h-[400px]">
        <h3 class="font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-[#2A2E33] pb-3 mb-4">Peta Batas Wilayah Kelurahan</h3>
        <p class="text-xs text-slate-500 dark:text-[#6F767E] mb-3">Wilayah kelurahan ditandai dengan garis batas biru tebal, dan titik UMKM kuliner di dalamnya.</p>

        <div class="flex-1 border border-slate-200 dark:border-[#2A2E33] rounded-xl overflow-hidden relative">
          <div id="village-map" class="w-full h-full min-h-[300px]"></div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import L from 'leaflet';
import api from '../../services/api';

const props = defineProps({
  id: {
    type: String,
    required: true
  }
});

const route = useRoute();
const village = ref<any>(null);
const localUmkms = ref<any[]>([]);
const loading = ref(true);
const error = ref('');

// Map
let villageMap: L.Map | null = null;
let boundariesLayer: L.GeoJSON | null = null;
let umkmsLayer = L.featureGroup();

onMounted(async () => {
  await fetchVillageDetail();
  await nextTick();
  if (village.value) {
    initVillageMap();
  }
});

async function fetchVillageDetail() {
  loading.value = true;
  error.value = '';
  try {
    const res = await api.get(`/villages/${props.id}`);
    village.value = res.data.data;

    const umkmsRes = await api.get('/umkms', {
      params: {
        village_id: props.id,
        per_page: 100
      }
    });
    localUmkms.value = umkmsRes.data.data || [];
  } catch (err: any) {
    console.error('Error fetching village detail:', err);
    error.value = 'Gagal memuat detail kelurahan. Hubungi administrator.';
  } finally {
    loading.value = false;
  }
}

async function initVillageMap() {
  if (!village.value) return;

  // Wait for DOM to be ready
  await nextTick();
  const mapContainer = document.getElementById('village-map');
  if (!mapContainer) {
    console.error('Map container not found');
    return;
  }

  // Center on Sungailiat coordinates
  const centerLat = village.value.geom?.coordinates ?
    (Array.isArray(village.value.geom.coordinates[0][0]) ?
      village.value.geom.coordinates[0][0][1] :
      village.value.geom.coordinates[1]) :
    -1.8889;
  const centerLng = village.value.geom?.coordinates ?
    (Array.isArray(village.value.geom.coordinates[0][0]) ?
      village.value.geom.coordinates[0][0][0] :
      village.value.geom.coordinates[0]) :
    106.1038;

  villageMap = L.map('village-map', {
    center: [centerLat, centerLng],
    zoom: 14,
    zoomControl: true
  });

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
  }).addTo(villageMap);

  umkmsLayer.addTo(villageMap);

  try {
    const boundariesRes = await api.get('/map/villages');
    const features = boundariesRes.data.features || [];
    const villageId = parseInt(props.id);
    const matchFeature = features.find((f: any) => f.properties?.id === villageId);

    if (matchFeature) {
      // Fix GeoJSON if coordinates are in wrong order
      let geoJson = matchFeature;
      if (matchFeature.geometry?.coordinates) {
        // Check if it's actually a Point instead of Polygon
        if (matchFeature.geometry.type === 'Point') {
          console.warn('Village geometry is Point, not Polygon');
          // Center map on point instead
          villageMap.setView([matchFeature.geometry.coordinates[1], matchFeature.geometry.coordinates[0]], 15);
        } else {
          boundariesLayer = L.geoJSON(geoJson, {
            style: {
              color: '#4f46e5',
              weight: 3,
              fillColor: '#4f46e5',
              fillOpacity: 0.15
            }
          }).addTo(villageMap);
          villageMap.fitBounds(boundariesLayer.getBounds(), { padding: [30, 30] });
        }
      }
    } else {
      console.warn('Village boundary not found for ID:', villageId);
      // Show message on map
      L.popup()
        .setLatLng([centerLat, centerLng])
        .setContent('<div style="padding: 10px; text-align: center;">Batas wilayah tidak tersedia</div>')
        .openOn(villageMap);
    }

    // Add UMKM markers
    localUmkms.value.forEach(umkmItem => {
      if (!umkmItem.latitude || !umkmItem.longitude) return;

      const coords: L.LatLngExpression = [Number(umkmItem.latitude), Number(umkmItem.longitude)];
      const normLevel = String(umkmItem.potential_level || '').toLowerCase();
      const color = normLevel === 'tinggi' ? '#10b981' : (normLevel === 'sedang' ? '#f59e0b' : '#ef4444');

      const customIcon = L.divIcon({
        html: `<div style="background-color: ${color}; width: 12px; height: 12px; border: 2px solid white; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3)"></div>`,
        iconSize: [12, 12],
        iconAnchor: [6, 6]
      });

      const marker = L.marker(coords, { icon: customIcon });
      marker.bindPopup(`
        <div style="min-width: 150px;">
          <strong style="font-size: 12px;">${umkmItem.name}</strong><br>
          <span style="font-size: 11px; color: #666;">${umkmItem.category}</span>
        </div>
      `);
      marker.addTo(umkmsLayer);
    });

  } catch (err) {
    console.error('Error drawing village bounds/markers:', err);
  }
}

function getPotentialColor(level: string): string {
  const norm = String(level).toLowerCase();
  if (norm === 'tinggi') return 'bg-emerald-500';
  if (norm === 'sedang') return 'bg-amber-500';
  return 'bg-rose-500';
}
</script>
