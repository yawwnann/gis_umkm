<template>
  <div class="space-y-6 font-sans text-slate-900 dark:text-white transition-colors">
    <div class="bg-white/80 dark:bg-[#1A1D1F]/80 backdrop-blur-md border border-slate-200/50 dark:border-[#2A2E33]/50 p-6 rounded-3xl flex items-center justify-between shadow-sm">
      <div>
        <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ isEdit ? 'Ubah Data UMKM' : 'Tambah UMKM Baru' }}</h1>
        <p class="text-slate-500 dark:text-[#6F767E] text-sm mt-1">Formulir untuk memasukkan data atribut dan koordinat lokasi spasial UMKM kuliner.</p>
      </div>
      <button 
        @click="$router.push('/umkm')" 
        class="px-4 py-2 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs font-semibold hover:bg-slate-50 dark:hover:bg-[#22262A] text-slate-700 dark:text-[#F0F0F0] bg-white dark:bg-[#111315] transition-colors cursor-pointer"
      >
        Kembali
      </button>
    </div>

    <!-- Validation / Connection Errors -->
    <div v-if="error" class="p-4 bg-red-50 dark:bg-red-500/10 border-l-4 border-red-500 text-red-700 dark:text-red-400 text-xs rounded-lg">
      {{ error }}
    </div>

    <form @submit.prevent="handleSubmit" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Fields card -->
      <div class="bg-white/80 dark:bg-[#1A1D1F]/80 backdrop-blur-md border border-slate-200/50 dark:border-[#2A2E33]/50 rounded-3xl p-6 space-y-5 shadow-sm">
        <h3 class="font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-[#2A2E33] pb-3 mb-4">Informasi Atribut Usaha</h3>
        
        <!-- Nama Usaha -->
        <div>
          <label for="name" class="block text-xs font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-wider mb-1">Nama Usaha</label>
          <input 
            id="name" 
            v-model="form.name" 
            type="text" 
            required 
            placeholder="Contoh: Warung Makan Barokah"
            class="block w-full px-3.5 py-2 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs bg-white dark:bg-[#111315] text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-[#6F767E] focus:outline-none focus:ring-1 focus:ring-[#F59E0B] focus:border-[#F59E0B]"
          />
        </div>

        <!-- Nama Pemilik -->
        <div>
          <label for="owner" class="block text-xs font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-wider mb-1">Nama Pemilik</label>
          <input 
            id="owner" 
            v-model="form.owner" 
            type="text" 
            required 
            placeholder="Contoh: Budi Santoso"
            class="block w-full px-3.5 py-2 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs bg-white dark:bg-[#111315] text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-[#6F767E] focus:outline-none focus:ring-1 focus:ring-[#F59E0B] focus:border-[#F59E0B]"
          />
        </div>

        <!-- Kategori -->
        <div>
          <label for="category" class="block text-xs font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-wider mb-1">Kategori Usaha</label>
          <select 
            id="category" 
            v-model="form.category" 
            required
            class="block w-full px-3 py-2 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs bg-white dark:bg-[#111315] text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#F59E0B]"
          >
            <option value="" disabled>Pilih Kategori</option>
            <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
          </select>
        </div>

        <!-- Kelurahan -->
        <div>
          <label for="village_id" class="block text-xs font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-wider mb-1">Kelurahan / Desa</label>
          <select 
            id="village_id" 
            v-model="form.village_id" 
            required
            class="block w-full px-3 py-2 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs bg-white dark:bg-[#111315] text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#F59E0B]"
          >
            <option value="" disabled>Pilih Kelurahan</option>
            <option v-for="v in villages" :key="v.id" :value="v.id">{{ v.name }}</option>
          </select>
        </div>

        <!-- Alamat -->
        <div>
          <label for="address" class="block text-xs font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-wider mb-1">Alamat Lengkap</label>
          <textarea 
            id="address" 
            v-model="form.address" 
            rows="3" 
            required
            placeholder="Nama jalan, nomor, RT/RW..."
            class="block w-full px-3.5 py-2 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs bg-white dark:bg-[#111315] text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-[#6F767E] focus:outline-none focus:ring-1 focus:ring-[#F59E0B] focus:border-[#F59E0B]"
          ></textarea>
        </div>

        <!-- Coordinates inputs -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="latitude" class="block text-xs font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-wider mb-1">Latitude</label>
            <input 
              id="latitude" 
              v-model.number="form.latitude" 
              type="number" 
              step="any"
              required 
              placeholder="-1.8889"
              class="block w-full px-3.5 py-2 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs bg-white dark:bg-[#111315] text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-[#6F767E] focus:outline-none focus:ring-1 focus:ring-[#F59E0B]"
              @input="updateMarkerFromInputs"
            />
          </div>
          <div>
            <label for="longitude" class="block text-xs font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-wider mb-1">Longitude</label>
            <input 
              id="longitude" 
              v-model.number="form.longitude" 
              type="number" 
              step="any"
              required 
              placeholder="106.1038"
              class="block w-full px-3.5 py-2 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs bg-white dark:bg-[#111315] text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-[#6F767E] focus:outline-none focus:ring-1 focus:ring-[#F59E0B]"
              @input="updateMarkerFromInputs"
            />
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="pt-4 border-t border-slate-100 dark:border-[#2A2E33] flex items-center justify-end space-x-3">
          <button 
            type="button" 
            @click="$router.push('/umkm')"
            class="px-4 py-2 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs font-semibold hover:bg-slate-50 dark:hover:bg-[#22262A] text-slate-700 dark:text-[#F0F0F0] bg-white dark:bg-[#111315] transition-colors cursor-pointer"
          >
            Batal
          </button>
          <button 
            type="submit" 
            :disabled="submitting"
            class="px-5 py-2.5 bg-[#F59E0B] hover:bg-[#D97706] text-[#111315] rounded-xl text-xs font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer shadow-lg shadow-[#F59E0B]/20"
          >
            {{ submitting ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Tambah UMKM') }}
          </button>
        </div>
      </div>

      <!-- Map Picker card -->
      <div class="bg-white/80 dark:bg-[#1A1D1F]/80 backdrop-blur-md border border-slate-200/50 dark:border-[#2A2E33]/50 rounded-3xl p-6 flex flex-col min-h-[400px] shadow-sm">
        <h3 class="font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-[#2A2E33] pb-3 mb-4 flex items-center space-x-1.5">
          <Map class="h-4.5 w-4.5 text-[#F59E0B]" />
          <span>Pilih Koordinat pada Peta</span>
        </h3>
        <p class="text-xs text-slate-500 dark:text-[#6F767E] mb-3">Klik di area peta mana saja untuk meletakkan pin koordinat secara presisi, atau seret pin yang ada.</p>
        
        <div class="flex-1 border border-slate-200 dark:border-[#2A2E33] rounded-2xl overflow-hidden relative bg-slate-100 dark:bg-[#111315]">
          <div id="form-map" class="w-full h-full min-h-[300px]"></div>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { Map } from 'lucide-vue-next';
import L from 'leaflet';
import api from '../../services/api';
import { useTheme } from '../../composables/useTheme';

const props = defineProps({
  id: {
    type: String,
    required: false
  }
});

const router = useRouter();
const route = useRoute();
const { isDark } = useTheme();

const isEdit = ref(!!props.id);
const submitting = ref(false);
const error = ref('');

const categories = ref<string[]>([]);
const villages = ref<any[]>([]);

// Form state
const form = ref({
  name: '',
  owner: '',
  category: '',
  village_id: '',
  address: '',
  latitude: -1.88898,
  longitude: 106.10388
});

// Map variables
let formMap: L.Map | null = null;
let pickerMarker: L.Marker | null = null;
let formTileLayer: L.TileLayer | null = null;
const defaultCenter: L.LatLngExpression = [-1.8889, 106.1038];

onMounted(async () => {
  await fetchOptions();
  
  if (isEdit.value && props.id) {
    await fetchUmkmDetail(props.id);
  }
  
  await nextTick();
  initFormMap();
});

watch(isDark, () => {
  if (formMap && formTileLayer) {
    const url = isDark.value 
      ? 'https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png'
      : 'https://tiles.stadiamaps.com/tiles/alidade_smooth/{z}/{x}/{y}{r}.png';
    formTileLayer.setUrl(url);
  }
});

async function fetchOptions() {
  try {
    const villRes = await api.get('/villages');
    villages.value = villRes.data.data || [];

    const catRes = await api.get('/umkms/categories');
    categories.value = catRes.data.data || [];
  } catch (err) {
    console.error('Error fetching categories/villages for form dropdowns:', err);
  }
}

async function fetchUmkmDetail(id: string) {
  try {
    const res = await api.get(`/umkms/${id}`);
    const data = res.data.data;
    form.value = {
      name: data.name,
      owner: data.owner,
      category: data.category,
      village_id: data.village ? data.village.id : '',
      address: data.address,
      latitude: Number(data.latitude),
      longitude: Number(data.longitude)
    };
  } catch (err: any) {
    console.error('Error loading UMKM for edit prefill:', err);
    error.value = 'Gagal memuat detail data UMKM.';
  }
}

function initFormMap() {
  const initialCoords: L.LatLngExpression = [form.value.latitude, form.value.longitude];

  formMap = L.map('form-map', {
    center: initialCoords,
    zoom: 14
  });

  const tileUrl = isDark.value 
    ? 'https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png'
    : 'https://tiles.stadiamaps.com/tiles/alidade_smooth/{z}/{x}/{y}{r}.png';

  formTileLayer = L.tileLayer(tileUrl, {
    maxZoom: 20
  }).addTo(formMap);

  // Set up coordinate picking marker
  const pinIcon = L.divIcon({
    html: '<div style="background-color: #f59e0b; width: 16px; height: 16px; border: 2.5px solid white; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3)"></div>',
    iconSize: [16, 16],
    iconAnchor: [8, 8]
  });

  pickerMarker = L.marker(initialCoords, {
    icon: pinIcon,
    draggable: true
  }).addTo(formMap);

  // Bind marker drag events
  pickerMarker.on('dragend', (event) => {
    const marker = event.target;
    const position = marker.getLatLng();
    form.value.latitude = Number(position.lat.toFixed(6));
    form.value.longitude = Number(position.lng.toFixed(6));
  });

  // Bind map click events
  formMap.on('click', (event) => {
    const coords = event.latlng;
    form.value.latitude = Number(coords.lat.toFixed(6));
    form.value.longitude = Number(coords.lng.toFixed(6));
    pickerMarker?.setLatLng(coords);
  });
}

function updateMarkerFromInputs() {
  if (pickerMarker && formMap && form.value.latitude && form.value.longitude) {
    const coords: L.LatLngExpression = [form.value.latitude, form.value.longitude];
    pickerMarker.setLatLng(coords);
    formMap.setView(coords);
  }
}

async function handleSubmit() {
  submitting.value = true;
  error.value = '';
  
  try {
    if (isEdit.value && props.id) {
      await api.put(`/umkms/${props.id}`, form.value);
    } else {
      await api.post('/umkms', form.value);
    }
    router.push('/umkm');
  } catch (err: any) {
    console.error('Error submitting UMKM form:', err);
    if (err.response && err.response.data && err.response.data.message) {
      error.value = err.response.data.message;
    } else if (err.response && err.response.status === 422) {
      error.value = 'Formulir tidak valid. Pastikan semua field terisi dengan benar.';
    } else {
      error.value = 'Terjadi kesalahan sistem. Silakan hubungi administrator.';
    }
  } finally {
    submitting.value = false;
  }
}
</script>
