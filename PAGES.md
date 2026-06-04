# Halaman Frontend - SIG UMKM Kuliner

## Struktur Pages

```
src/
├── pages/
│   ├── auth/
│   │   ├── Login.vue
│   │   └── Register.vue (optional)
│   ├── dashboard/
│   │   ├── DashboardLayout.vue
│   │   ├── index.vue (overview dashboard)
│   │   ├── Statistics.vue
│   │   ├── ByVillage.vue
│   │   ├── ByCategory.vue
│   │   └── ByPotential.vue
│   ├── map/
│   │   ├── MapLayout.vue
│   │   ├── MapView.vue (main public map)
│   │   └── components/
│   │       ├── MapLegend.vue
│   │       ├── LayerControl.vue
│   │       ├── BasemapSwitcher.vue
│   │       ├── MapFilter.vue
│   │       ├── MapSearch.vue
│   │       └── RoutePanel.vue
│   ├── umkm/
│   │   ├── UmkmList.vue
│   │   ├── UmkmDetail.vue
│   │   ├── UmkmForm.vue (create/edit)
│   │   └── UmkmPhotoUpload.vue
│   ├── village/
│   │   ├── VillageList.vue
│   │   └── VillageDetail.vue
│   ├── user/
│   │   ├── UserList.vue
│   │   ├── UserForm.vue
│   │   └── UserProfile.vue
│   └── NotFound.vue
├── components/
│   ├── common/
│   │   ├── AppNavbar.vue
│   │   ├── AppSidebar.vue
│   │   ├── AppBreadcrumb.vue
│   │   ├── AppFooter.vue
│   │   ├── AppPagination.vue
│   │   ├── AppModal.vue
│   │   ├── AppAlert.vue
│   │   ├── AppLoading.vue
│   │   └── AppEmpty.vue
│   ├── map/
│   │   ├── LeafletMap.vue
│   │   ├── MarkerPopup.vue
│   │   ├── RoutingControl.vue
│   │   └── PotentialMarker.vue
│   ├── forms/
│   │   ├── FormInput.vue
│   │   ├── FormSelect.vue
│   │   ├── FormTextarea.vue
│   │   ├── FormFile.vue
│   │   └── FormCheckbox.vue
│   └── charts/
│       ├── BarChart.vue
│       ├── PieChart.vue
│       └── DonutChart.vue
└── layouts/
    ├── PublicLayout.vue
    ├── DashboardLayout.vue
    └── AuthLayout.vue
```

---

## Deskripsi Halaman

### 1. Authentication

#### `/login` - Halaman Login
**Purpose:** Autentikasi pengguna

**Elements:**
- Email input
- Password input
- Remember me checkbox
- Login button
- Forgot password link

**API:** `POST /api/auth/login`

---

### 2. Public Map Pages

#### `/` - Home / Peta Utama
**Purpose:** Halaman utama menampilkan peta interaktif

**Features:**
- Fullscreen Leaflet map
- Layer visibility toggle
- Basemap switcher (OpenStreetMap, Satellite, etc.)
- Search bar (cari UMKM)
- Filter panel (kategori, kelurahan, potensi)
- Cluster markers untuk UMKM
- Popup info saat marker diklik

**Components:**
- `MapLegend` - Legenda layer
- `LayerControl` - Toggle visibility setiap layer
- `BasemapSwitcher` - Ganti basemap
- `MapSearch` - Pencarian UMKM
- `MapFilter` - Filter data

---

#### `/umkm/{id}` - Detail UMKM
**Purpose:** Menampilkan informasi lengkap UMKM

**Elements:**
- Foto carousel
- Info usaha (nama, pemilik, kategori, alamat)
- Lokasi di map
- Skor potensi ekonomi
- Indikator potensi (warna)
- Tombol navigasi ke lokasi
- Tombol hubungi (jika ada)
- Gallery foto

---

### 3. Dashboard Pages (Admin & Officer)

#### `/dashboard` - Dashboard Overview
**Purpose:** Overview statistik sistem

**Elements:**
- Total UMKM card
- Total Kategori card
- Total Kelurahan card
- Distribusi potensi (bar chart)
- UMKM per kelurahan (pie chart)
- Recent activity log

**API:** `GET /api/dashboard/stats`

---

#### `/dashboard/by-village` - Statistik per Kelurahan
**Purpose:** Visualisasi data UMKM per kelurahan

**Elements:**
- Bar chart: UMKM per kelurahan
- Tabel data per kelurahan
- Sorting by count
- Filter tahun (jika ada)

**API:** `GET /api/dashboard/by-village`

---

#### `/dashboard/by-category` - Statistik per Kategori
**Purpose:** Visualisasi data UMKM per kategori

**Elements:**
- Pie chart: distribusi kategori
- List kategori dengan count
- Top 10 kategori

**API:** `GET /api/dashboard/by-category`

---

#### `/dashboard/by-potential` - Statistik per Potensi
**Purpose:** Visualisasi distribusi potensi ekonomi

**Elements:**
- Donut chart: tinggi/sedang/rendah
- Map highlight potensi
- Color-coded markers

**API:** `GET /api/dashboard/by-potential`

---

### 4. UMKM Management Pages (Admin & Officer)

#### `/umkm` - Daftar UMKM
**Purpose:** List semua UMKM dengan fitur CRUD

**Features:**
- Data table dengan pagination
- Kolom: Nama, Pemilik, Kategori, Kelurahan, Potensi
- Search functionality
- Filter (kategori, kelurahan, potensi)
- Sort by any column
- Bulk delete
- Action buttons (view, edit, delete)

**API:**
- `GET /api/umkms` - List
- `GET /api/umkms/categories` - Categories for filter

---

#### `/umkm/create` - Tambah UMKM
**Purpose:** Form tambah UMKM baru

**Fields:**
- Nama usaha (required)
- Nama pemilik (required)
- Kategori (select dropdown)
- Alamat (textarea)
- Latitude (number)
- Longitude (number)
- Village (select dropdown)
- Map picker untuk koordinat

**API:** `POST /api/umkms`

---

#### `/umkm/{id}/edit` - Edit UMKM
**Purpose:** Form edit UMKM

**Same fields as create, pre-filled with data**

**API:** `PUT /api/umkms/{id}`

---

#### `/umkm/{id}` - Detail UMKM (Admin)
**Purpose:** Detail lengkap untuk admin/officer

**Elements:**
- Semua info UMKM
- Edit button
- Delete button
- Photo management
- History perubahan

**API:** `GET /api/umkms/{id}`

---

### 5. Village Pages (Admin)

#### `/villages` - Daftar Kelurahan
**Purpose:** List semua kelurahan

**Elements:**
- Tabel kelurahan
- Kolom: Nama, Populasi, Luas, Kepadatan, Jumlah UMKM
- Click untuk detail

**API:** `GET /api/villages`

---

#### `/villages/{id}` - Detail Kelurahan
**Purpose:** Detail kelurahan dengan UMKM-nya

**Elements:**
- Info kelurahan
- Peta highlighting wilayah
- List UMKM di kelurahan ini
- Statistik lokal

**API:** `GET /api/villages/{id}`

---

### 6. User Management (Admin Only)

#### `/users` - Daftar User
**Purpose:** Kelola akun petugas

**Elements:**
- Data table user
- Kolom: Nama, Email, Role, Tanggal dibuat
- Create new user
- Edit user
- Delete user
- Reset password

**API:**
- `GET /api/users`
- `POST /api/users`
- `PUT /api/users/{id}`
- `DELETE /api/users/{id}`
- `POST /api/users/{id}/reset-password`

---

#### `/users/create` - Tambah User
#### `/users/{id}/edit` - Edit User

---

## Komponen Shared

### Common Components
| Component | Description |
|-----------|-------------|
| `AppNavbar` | Navigation bar dengan user menu |
| `AppSidebar` | Sidebar navigation (dashboard) |
| `AppBreadcrumb` | Breadcrumb navigation |
| `AppPagination` | Reusable pagination |
| `AppModal` | Reusable modal dialog |
| `AppAlert` | Alert notification |
| `AppLoading` | Loading spinner |
| `AppEmpty` | Empty state placeholder |

### Map Components
| Component | Description |
|-----------|-------------|
| `LeafletMap` | Wrapper komponen Leaflet |
| `MarkerPopup` | Popup content untuk marker |
| `RoutingControl` | Panel routing sidebar |
| `PotentialMarker` | Custom marker dengan warna potensi |

### Form Components
| Component | Description |
|-----------|-------------|
| `FormInput` | Reusable input dengan validasi |
| `FormSelect` | Reusable select dropdown |
| `FormTextarea` | Reusable textarea |
| `FormFile` | File upload dengan preview |
| `FormCheckbox` | Checkbox dengan label |

### Chart Components
| Component | Description |
|-----------|-------------|
| `BarChart` | Bar chart menggunakan Chart.js |
| `PieChart` | Pie chart |
| `DonutChart` | Donut chart |

---

## Route Structure

```javascript
const routes = [
  // Public
  { path: '/', component: MapView, name: 'home' },
  { path: '/login', component: Login, name: 'login' },
  { path: '/umkm/:id', component: UmkmDetail, name: 'umkm.show' },

  // Protected - Officer & Admin
  {
    path: '/dashboard',
    component: DashboardLayout,
    children: [
      { path: '', component: DashboardIndex, name: 'dashboard' },
      { path: 'by-village', component: ByVillage, name: 'dashboard.by-village' },
      { path: 'by-category', component: ByCategory, name: 'dashboard.by-category' },
      { path: 'by-potential', component: ByPotential, name: 'dashboard.by-potential' },
    ]
  },

  // Protected - Officer & Admin
  {
    path: '/umkm',
    children: [
      { path: '', component: UmkmList, name: 'umkm.index' },
      { path: 'create', component: UmkmForm, name: 'umkm.create' },
      { path: ':id', component: UmkmDetail, name: 'umkm.show' },
      { path: ':id/edit', component: UmkmForm, name: 'umkm.edit' },
    ]
  },

  // Protected - Admin Only
  {
    path: '/users',
    meta: { role: 'admin' },
    children: [
      { path: '', component: UserList, name: 'users.index' },
      { path: 'create', component: UserForm, name: 'users.create' },
      { path: ':id/edit', component: UserForm, name: 'users.edit' },
    ]
  },

  // Catch all
  { path: '/:pathMatch(.*)', component: NotFound }
]
```

---

## API Integration Notes

### Authentication Flow
1. User login → `POST /api/auth/login`
2. Store JWT token in localStorage/cookie
3. Add `Authorization: Bearer {token}` to protected requests
4. Handle 401 → redirect to login

### Map Layer Integration
1. Fetch GeoJSON from `/api/map/{layer}`
2. Parse and add to Leaflet layers
3. Use cluster plugin for dense areas
4. Cache responses (optional)

### Error Handling
- 401 → Redirect to login, clear token
- 403 → Show "Access denied" message
- 404 → Show not found page
- 422 → Show validation errors
- 500 → Show generic error

---

## Mockups / Wireframes Priority

### Phase 1 - MVP
1. `/login` - Simple login form
2. `/` - Full map with basic layers
3. `/dashboard` - Stats overview
4. `/umkm` - List view
5. `/umkm/create` - Add form
6. `/umkm/:id` - Detail view

### Phase 2 - Full Feature
1. `/umkm/:id/edit` - Edit form
2. `/dashboard/by-village` - Chart
3. `/dashboard/by-category` - Chart
4. `/dashboard/by-potential` - Chart
5. `/villages` - List
6. `/villages/:id` - Detail

### Phase 3 - Admin Feature
1. `/users` - User management
2. `/users/create` - Add user
3. `/users/:id/edit` - Edit user
4. Routing panel in map
5. Photo gallery in umkm detail