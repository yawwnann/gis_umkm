<template>
  <div class="space-y-6">

    <div class="bg-white dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200 dark:border-[#2A2E33]/60 p-6 rounded-2xl shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl font-bold text-slate-900 dark:text-white">Kelola Akun Petugas Lapangan</h1>
        <p class="text-slate-500 dark:text-[#6F767E] text-sm mt-1">Administrator dapat menambah, mengedit, menghapus, atau mengatur ulang kata sandi akun petugas.</p>
      </div>
      <div>
        <button 
          @click="openCreateModal" 
          class="inline-flex items-center space-x-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition-colors cursor-pointer"
        >
          <Plus class="h-3.5 w-3.5" />
          <span>Tambah Akun Petugas</span>
        </button>
      </div>
    </div>

    <!-- Error/Success States -->
    <div v-if="error" class="p-4 bg-red-50 dark:bg-red-500/10 border-l-4 border-red-500 text-red-700 dark:text-red-400 text-xs rounded-lg">
      {{ error }}
    </div>
    <div v-if="successMsg" class="p-4 bg-emerald-50 dark:bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-700 dark:text-emerald-400 text-xs rounded-lg">
      {{ successMsg }}
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200 dark:border-[#2A2E33]/60 rounded-2xl shadow-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50 dark:bg-[#111315] border-b border-slate-200 dark:border-[#2A2E33] text-slate-400 dark:text-[#6F767E] font-bold uppercase tracking-wider">
              <th class="p-4 w-12 text-center">No</th>
              <th class="p-4">Nama Lengkap</th>
              <th class="p-4">Alamat Email</th>
              <th class="p-4">Hak Akses</th>
              <th class="p-4">Tanggal Dibuat</th>
              <th class="p-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-[#2A2E33]">
            <tr v-if="loading" class="text-center">
              <td colspan="6" class="p-8 text-slate-400 dark:text-[#6F767E]">Memuat data pengguna...</td>
            </tr>
            <tr v-else-if="users.length === 0" class="text-center">
              <td colspan="6" class="p-8 text-slate-400 dark:text-[#6F767E]">Belum ada akun terdaftar.</td>
            </tr>
            <tr 
              v-else 
              v-for="(u, idx) in users" 
              :key="u.id"
              class="hover:bg-slate-50/50 dark:hover:bg-[#22262A] transition-colors"
            >
              <td class="p-4 text-center text-slate-400 dark:text-[#6F767E] font-medium">{{ idx + 1 }}</td>
              <td class="p-4 font-bold text-slate-800 dark:text-white">{{ u.name }}</td>
              <td class="p-4 text-slate-600 dark:text-[#F0F0F0] font-medium">{{ u.email }}</td>
              <td class="p-4">
                <span 
                  :class="[
                    'px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider text-white inline-block',
                    u.role === 'admin' ? 'bg-indigo-600' : 'bg-slate-500'
                  ]"
                >
                  {{ u.role_label || u.role }}
                </span>
              </td>
              <td class="p-4 text-slate-500 dark:text-[#6F767E]">{{ formatDate(u.created_at) }}</td>
              <td class="p-4 text-right space-x-1.5">
                <button 
                  @click="openEditModal(u)" 
                  class="inline-flex items-center justify-center p-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-lg hover:bg-slate-50 dark:hover:bg-[#22262A] text-slate-500 dark:text-[#6F767E] hover:text-slate-800 dark:hover:text-white transition-colors cursor-pointer"
                  title="Ubah Profil"
                >
                  <Edit class="h-3.5 w-3.5" />
                </button>
                <button 
                  @click="openResetModal(u)" 
                  class="inline-flex items-center justify-center p-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-lg hover:bg-slate-50 dark:hover:bg-[#22262A] text-slate-500 dark:text-[#6F767E] hover:text-slate-800 dark:hover:text-white transition-colors cursor-pointer"
                  title="Reset Password"
                >
                  <KeyRound class="h-3.5 w-3.5" />
                </button>
                <button 
                  @click="confirmDelete(u)" 
                  :disabled="isCurrentUser(u)"
                  class="inline-flex items-center justify-center p-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 text-slate-500 dark:text-[#6F767E] hover:text-red-600 dark:hover:text-red-400 transition-colors disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer"
                  title="Hapus Akun"
                >
                  <Trash2 class="h-3.5 w-3.5" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create / Edit User Modal -->
    <div 
      v-if="modalOpen" 
      class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4"
    >
      <div class="bg-white dark:bg-[#1A1D1F] border border-slate-200 dark:border-[#2A2E33] rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-xl">
        <h3 class="font-bold text-slate-900 dark:text-white text-sm border-b border-slate-100 dark:border-[#2A2E33] pb-2">
          {{ isEditMode ? 'Ubah Informasi Akun' : 'Tambah Petugas Baru' }}
        </h3>

        <form @submit.prevent="saveUser" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-wider mb-1">Nama Lengkap</label>
            <input 
              v-model="modalForm.name" 
              type="text" 
              required
              class="block w-full px-3 py-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-white dark:bg-[#111315] text-slate-900 dark:text-white"
            />
          </div>

          <div v-if="!isEditMode">
            <label class="block text-xs font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-wider mb-1">Alamat Email</label>
            <input 
              v-model="modalForm.email" 
              type="email" 
              required
              placeholder="nama@gisumkm.test"
              class="block w-full px-3 py-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-white dark:bg-[#111315] text-slate-900 dark:text-white"
            />
          </div>

          <div v-if="!isEditMode">
            <label class="block text-xs font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-wider mb-1">Kata Sandi Awal</label>
            <input 
              v-model="modalForm.password" 
              type="password" 
              required
              placeholder="Minimal 8 karakter"
              class="block w-full px-3 py-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-white dark:bg-[#111315] text-slate-900 dark:text-white"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-wider mb-1">Role / Hak Akses</label>
            <select 
              v-model="modalForm.role" 
              required
              class="block w-full px-3 py-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-white dark:bg-[#111315] text-slate-900 dark:text-white"
            >
              <option value="field_officer">Petugas Lapangan</option>
              <option value="admin">Administrator</option>
            </select>
          </div>

          <div class="pt-4 border-t border-slate-100 dark:border-[#2A2E33] flex justify-end space-x-2">
            <button 
              type="button" 
              @click="closeModal" 
              class="px-3 py-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs font-semibold hover:bg-slate-50 dark:hover:bg-[#22262A] text-slate-600 dark:text-[#F0F0F0] bg-white dark:bg-[#1A1D1F] transition-colors cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit" 
              class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer"
            >
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Reset Password Modal -->
    <div 
      v-if="resetModalOpen" 
      class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4"
    >
      <div class="bg-white dark:bg-[#1A1D1F] border border-slate-200 dark:border-[#2A2E33] rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-xl">
        <h3 class="font-bold text-slate-900 dark:text-white text-sm border-b border-slate-100 dark:border-[#2A2E33] pb-2">
          Reset Kata Sandi Petugas
        </h3>
        <p class="text-xs text-slate-500 dark:text-[#6F767E]">Anda akan mengganti kata sandi untuk akun <strong class="text-slate-800 dark:text-white">{{ activeUser?.name }}</strong>.</p>

        <form @submit.prevent="resetPassword" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-wider mb-1">Kata Sandi Baru</label>
            <input 
              v-model="resetForm.password" 
              type="password" 
              required
              placeholder="Minimal 8 karakter"
              class="block w-full px-3 py-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-white dark:bg-[#111315] text-slate-900 dark:text-white"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-500 dark:text-[#6F767E] uppercase tracking-wider mb-1">Konfirmasi Sandi Baru</label>
            <input 
              v-model="resetForm.password_confirmation" 
              type="password" 
              required
              placeholder="Ketik ulang sandi"
              class="block w-full px-3 py-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-white dark:bg-[#111315] text-slate-900 dark:text-white"
            />
          </div>

          <div class="pt-4 border-t border-slate-100 dark:border-[#2A2E33] flex justify-end space-x-2">
            <button 
              type="button" 
              @click="closeResetModal" 
              class="px-3 py-1.5 border border-slate-200 dark:border-[#2A2E33] rounded-xl text-xs font-semibold hover:bg-slate-50 dark:hover:bg-[#22262A] text-slate-600 dark:text-[#F0F0F0] bg-white dark:bg-[#1A1D1F] transition-colors cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit" 
              class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer"
            >
              Ubah Sandi
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Plus, Edit, KeyRound, Trash2 } from 'lucide-vue-next';
import api from '../../services/api';

const users = ref<any[]>([]);
const loading = ref(true);
const error = ref('');
const successMsg = ref('');

// Modal controls
const modalOpen = ref(false);
const isEditMode = ref(false);
const activeUser = ref<any>(null);
const modalForm = ref({
  name: '',
  email: '',
  password: '',
  role: 'field_officer'
});

// Reset Password Modal
const resetModalOpen = ref(false);
const resetForm = ref({
  password: '',
  password_confirmation: ''
});

async function fetchUsers() {
  loading.value = true;
  error.value = '';
  try {
    const response = await api.get('/users');
    users.value = response.data.data || [];
  } catch (err: any) {
    console.error('Error fetching users:', err);
    error.value = 'Gagal memuat daftar user. Harap pastikan hak akses Anda admin.';
  } finally {
    loading.value = false;
  }
}

function isCurrentUser(user: any): boolean {
  const currentStr = localStorage.getItem('user_info');
  if (currentStr) {
    const current = JSON.parse(currentStr);
    return current.id === user.id;
  }
  return false;
}

// Modal open handlers
function openCreateModal() {
  isEditMode.value = false;
  activeUser.value = null;
  modalForm.value = {
    name: '',
    email: '',
    password: '',
    role: 'field_officer'
  };
  modalOpen.value = true;
}

function openEditModal(user: any) {
  isEditMode.value = true;
  activeUser.value = user;
  modalForm.value = {
    name: user.name,
    email: user.email,
    password: '',
    role: user.role
  };
  modalOpen.value = true;
}

function closeModal() {
  modalOpen.value = false;
}

// Save User
async function saveUser() {
  error.value = '';
  successMsg.value = '';
  try {
    if (isEditMode.value && activeUser.value) {
      await api.put(`/users/${activeUser.value.id}`, {
        name: modalForm.value.name,
        role: modalForm.value.role
      });
      successMsg.value = 'Data user berhasil diperbarui.';
    } else {
      await api.post('/users', modalForm.value);
      successMsg.value = 'Akun petugas baru berhasil didaftarkan.';
    }
    closeModal();
    fetchUsers();
  } catch (err: any) {
    console.error('Error saving user profile:', err);
    if (err.response && err.response.data && err.response.data.message) {
      error.value = err.response.data.message;
    } else {
      error.value = 'Gagal menyimpan data user.';
    }
  }
}

// Reset Password
function openResetModal(user: any) {
  activeUser.value = user;
  resetForm.value = {
    password: '',
    password_confirmation: ''
  };
  resetModalOpen.value = true;
}

function closeResetModal() {
  resetModalOpen.value = false;
}

async function resetPassword() {
  error.value = '';
  successMsg.value = '';

  if (resetForm.value.password !== resetForm.value.password_confirmation) {
    error.value = 'Konfirmasi kata sandi tidak cocok.';
    return;
  }

  try {
    await api.post(`/users/${activeUser.value.id}/reset-password`, resetForm.value);
    successMsg.value = `Kata sandi untuk ${activeUser.value.name} berhasil diganti.`;
    closeResetModal();
  } catch (err: any) {
    console.error('Error resetting password:', err);
    error.value = 'Gagal mengganti kata sandi. Pastikan minimal 8 karakter.';
  }
}

// Delete user
async function confirmDelete(user: any) {
  if (confirm(`Apakah Anda yakin ingin menghapus petugas "${user.name}"? Tindakan ini tidak dapat dibatalkan.`)) {
    error.value = '';
    successMsg.value = '';
    try {
      await api.delete(`/users/${user.id}`);
      successMsg.value = 'Akun petugas berhasil dihapus.';
      fetchUsers();
    } catch (err: any) {
      console.error('Error deleting user:', err);
      error.value = 'Gagal menghapus user.';
    }
  }
}

function formatDate(dateStr: string) {
  if (!dateStr) return '-';
  const date = new Date(dateStr);
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
}

onMounted(() => {
  fetchUsers();
});
</script>
