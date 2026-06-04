<template>
  <div class="min-h-screen bg-slate-100 dark:bg-[#111315] flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans transition-colors">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <!-- Logo -->
      <div class="flex justify-center">
        <div class="h-12 w-12 bg-[#F59E0B] rounded-2xl flex items-center justify-center text-[#111315]">
          <Map class="h-7 w-7" />
        </div>
      </div>
      <h2 class="mt-6 text-center text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
        Sistem Informasi Geografis
      </h2>
      <p class="mt-2 text-center text-sm text-slate-500 dark:text-[#6F767E]">
        Pemetaan &amp; Analisis Spasial UMKM Kuliner Sungailiat
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white dark:bg-[#1A1D1F] py-8 px-4 border border-slate-200 dark:border-[#2A2E33] sm:rounded-2xl sm:px-10 shadow-xl">
        <!-- Error -->
        <div 
          v-if="errorMessage" 
          class="mb-4 p-3 bg-red-50 dark:bg-red-500/10 border-l-4 border-red-500 text-red-700 dark:text-red-400 text-sm flex items-start space-x-2"
        >
          <AlertCircle class="h-5 w-5 shrink-0" />
          <span>{{ errorMessage }}</span>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
          <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-[#F0F0F0]">Alamat Email</label>
            <div class="mt-1">
              <input 
                id="email" 
                v-model="email" 
                type="email" 
                required 
                placeholder="admin@gisumkm.test"
                class="appearance-none block w-full px-3 py-2.5 border border-slate-300 dark:border-[#2A2E33] rounded-xl bg-white dark:bg-[#111315] text-slate-900 dark:text-white shadow-none placeholder-slate-400 dark:placeholder-[#6F767E] focus:outline-none focus:ring-2 focus:ring-[#F59E0B]/50 focus:border-[#F59E0B] text-sm transition-colors"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-[#F0F0F0]">Kata Sandi</label>
            <div class="mt-1">
              <input 
                id="password" 
                v-model="password" 
                type="password" 
                required 
                placeholder="••••••••"
                class="appearance-none block w-full px-3 py-2.5 border border-slate-300 dark:border-[#2A2E33] rounded-xl bg-white dark:bg-[#111315] text-slate-900 dark:text-white shadow-none placeholder-slate-400 dark:placeholder-[#6F767E] focus:outline-none focus:ring-2 focus:ring-[#F59E0B]/50 focus:border-[#F59E0B] text-sm transition-colors"
              />
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input 
                id="remember-me" 
                v-model="rememberMe" 
                type="checkbox" 
                class="h-4 w-4 text-[#F59E0B] focus:ring-[#F59E0B] border-slate-300 dark:border-[#2A2E33] rounded"
              />
              <label for="remember-me" class="ml-2 block text-sm text-slate-700 dark:text-[#F0F0F0]">Ingat Saya</label>
            </div>
            
            <RouterLink to="/" class="text-sm font-medium text-[#D97706] dark:text-[#F59E0B] hover:opacity-80">
              Lihat Peta Publik
            </RouterLink>
          </div>

          <div>
            <button 
              type="submit" 
              :disabled="loading"
              class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl text-sm font-bold text-[#111315] bg-[#F59E0B] hover:bg-[#D97706] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F59E0B] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer transition-colors shadow-lg shadow-[#F59E0B]/20"
            >
              <span v-if="loading">Mengautentikasi...</span>
              <span v-else>Masuk ke Akun</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import { Map, AlertCircle } from 'lucide-vue-next';
import api from '../../services/api';

const router = useRouter();
const email = ref('');
const password = ref('');
const rememberMe = ref(false);
const loading = ref(false);
const errorMessage = ref('');

async function handleLogin() {
  loading.value = true;
  errorMessage.value = '';
  
  try {
    const response = await api.post('/auth/login', {
      email: email.value,
      password: password.value,
    });
    
    const { access_token, user } = response.data;
    
    localStorage.setItem('auth_token', access_token);
    localStorage.setItem('user_info', JSON.stringify(user));
    
    const urlRole = user.role === 'field_officer' ? 'officer' : user.role;
    router.push({ name: 'dashboard', params: { role: urlRole } });
  } catch (error: any) {
    console.error('Login error details:', error);
    if (error.response && error.response.data && error.response.data.message) {
      errorMessage.value = error.response.data.message;
    } else if (error.response && error.response.status === 422) {
      errorMessage.value = 'Email atau password yang Anda masukkan salah.';
    } else {
      errorMessage.value = 'Terjadi kesalahan koneksi. Silakan periksa server backend Anda.';
    }
  } finally {
    loading.value = false;
  }
}
</script>
