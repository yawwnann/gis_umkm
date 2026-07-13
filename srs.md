# SOFTWARE REQUIREMENTS SPECIFICATION (SRS)

# Sistem Informasi Geografis (SIG) UMKM Kuliner Berbasis Web

---

# 1. Pendahuluan

## 1.1 Tujuan

Dokumen Software Requirements Specification (SRS) ini mendefinisikan kebutuhan perangkat lunak untuk Sistem Informasi Geografis (SIG) UMKM Kuliner berbasis web yang digunakan untuk memetakan persebaran UMKM kuliner serta menganalisis potensi ekonomi wilayah di Kecamatan Sungailiat, Kabupaten Bangka.

Dokumen ini digunakan sebagai acuan dalam proses perancangan, pengembangan, pengujian, dan implementasi sistem.

---

## 1.2 Ruang Lingkup

Sistem yang dikembangkan merupakan aplikasi WebGIS yang memungkinkan pengguna untuk:

- Melihat persebaran UMKM kuliner.
- Melihat informasi detail UMKM.
- Melakukan pencarian dan filter data.
- Melihat analisis potensi ekonomi wilayah.
- Mengelola data UMKM.
- Mengelola data pengguna.
- Menampilkan rute menuju lokasi UMKM.

---

## 1.3 Definisi dan Singkatan

| Istilah | Definisi                                |
| ------- | --------------------------------------- |
| SIG     | Sistem Informasi Geografis              |
| UMKM    | Usaha Mikro Kecil dan Menengah          |
| WebGIS  | Sistem Informasi Geografis berbasis Web |
| PostGIS | Ekstensi PostgreSQL untuk data spasial  |
| GeoJSON | Format data spasial berbasis JSON       |
| OSRM    | Open Source Routing Machine             |
| Redis   | In-Memory Data Store untuk caching      |

---

# 2. Deskripsi Umum

## 2.1 Perspektif Produk

Sistem merupakan aplikasi berbasis web yang terdiri dari:

- Frontend (Vue.js)
- Backend API (Laravel)
- Database PostgreSQL + PostGIS
- Redis Cache
- OSRM Routing Engine

---

## 2.2 Karakteristik Pengguna

### Administrator

Memiliki akses penuh terhadap sistem.

### Petugas Lapangan

Melakukan pendataan dan pembaruan data UMKM.

### Masyarakat

Mengakses informasi UMKM dan peta secara publik.

---

## 2.3 Lingkungan Operasional

### Server

- Ubuntu Server 24.04 LTS
- Nginx
- PHP 8.3
- PostgreSQL 16
- PostGIS
- Redis

### Client

- Chrome
- Firefox
- Edge
- Mobile Browser

---

# 3. Arsitektur Sistem

```text
Vue.js + Tailwind
        │
        ▼
Laravel REST API
        │
 ┌──────┴───────┐
 ▼              ▼
Redis      PostgreSQL
Cache      + PostGIS
                │
                ▼
          Spatial Database
```

---

# 4. Kebutuhan Fungsional

## FR-001 Login

### Deskripsi

Sistem harus menyediakan mekanisme autentikasi pengguna.

### Aktor

- Administrator
- Petugas Lapangan

### Input

- Email
- Password

### Output

- Dashboard sesuai role

---

## FR-002 Logout

### Deskripsi

Sistem harus memungkinkan pengguna keluar dari sistem.

### Aktor

- Administrator
- Petugas Lapangan

---

## FR-003 Kelola Data UMKM

### Deskripsi

Administrator dapat mengelola data UMKM.

### Aktor

Administrator

### Operasi

- Tambah
- Edit
- Hapus
- Detail

---

## FR-004 Input Data UMKM

### Deskripsi

Petugas lapangan dapat menambahkan UMKM baru.

### Data

- Nama usaha
- Nama pemilik
- Kategori usaha
- Alamat
- Latitude
- Longitude
- Foto usaha

---

## FR-005 Edit Data UMKM

### Deskripsi

Petugas dapat memperbarui data UMKM yang telah ada.

---

## FR-006 Kelola Pengguna

### Deskripsi

Administrator dapat mengelola akun petugas.

### Operasi

- Tambah akun
- Edit akun
- Hapus akun
- Reset password

---

## FR-007 Menampilkan Peta Persebaran UMKM

### Deskripsi

Sistem harus menampilkan seluruh UMKM dalam bentuk marker pada peta.

### Output

- Marker lokasi
- Popup informasi

---

## FR-008 Menampilkan Detail UMKM

### Deskripsi

Pengguna dapat melihat informasi detail UMKM.

### Informasi

- Nama usaha
- Pemilik
- Kategori
- Alamat
- Foto
- Potensi ekonomi

---

## FR-009 Pencarian UMKM

### Deskripsi

Pengguna dapat mencari UMKM berdasarkan kata kunci.

### Kriteria

- Nama usaha
- Nama pemilik
- Kategori

---

## FR-010 Filter UMKM

### Deskripsi

Pengguna dapat memfilter data berdasarkan kategori.

### Filter

- Kategori usaha
- Kelurahan
- Potensi ekonomi

---

## FR-011 Menampilkan Batas Wilayah

### Deskripsi

Sistem harus menampilkan layer batas administrasi wilayah.

### Layer

- Kecamatan
- Kelurahan

---

## FR-012 Menampilkan Layer Jalan

### Deskripsi

Sistem harus menampilkan jaringan jalan pada peta.

---

## FR-013 Routing Menuju UMKM

### Deskripsi

Sistem harus mampu menampilkan rute menuju UMKM.

### Input

- Lokasi pengguna
- Lokasi UMKM

### Output

- Jalur
- Jarak
- Estimasi waktu

---

## FR-014 Analisis Potensi Ekonomi

### Deskripsi

Sistem harus mampu menampilkan hasil analisis potensi ekonomi UMKM.

### Faktor

- Kedekatan jalan utama
- Kedekatan pasar
- Kepadatan usaha sejenis
- Kedekatan pusat aktivitas

### Output

- Tinggi
- Sedang
- Rendah

---

## FR-015 Dashboard Statistik

### Deskripsi

Sistem harus menyediakan dashboard statistik.

### Informasi

- Total UMKM
- UMKM per kategori
- UMKM per kelurahan
- UMKM berdasarkan potensi

---

## FR-016 Import Data UMKM

### Deskripsi

Administrator dapat mengimpor data UMKM.

### Format

- XLSX
- CSV

---

## FR-017 Import Data Spasial

### Deskripsi

Administrator dapat mengimpor data spasial.

### Format

- Shapefile
- GeoJSON

---

# 5. Kebutuhan Non Fungsional

## NFR-001 Performance

- Response API ≤ 2 detik
- Load peta ≤ 3 detik
- Query spasial ≤ 5 detik

---

## NFR-002 Security

- JWT Authentication
- Password Hashing
- Role Based Access Control
- HTTPS

---

## NFR-003 Reliability

- Uptime minimum 99%
- Auto Backup Database

---

## NFR-004 Availability

Sistem dapat diakses 24 jam.

---

## NFR-005 Scalability

Sistem harus mampu menangani minimal:

- 10.000 data UMKM
- 100 pengguna aktif bersamaan

---

## NFR-006 Compatibility

Mendukung:

- Chrome
- Firefox
- Edge
- Android Browser

---

# 6. Kebutuhan Basis Data

## Tabel Users

| Field    | Tipe    |
| -------- | ------- |
| id       | bigint  |
| name     | varchar |
| email    | varchar |
| password | varchar |
| role     | enum    |

---

## Tabel UMKM

| Field        | Tipe                 |
| ------------ | -------------------- |
| id           | bigint               |
| nama_usaha   | varchar              |
| nama_pemilik | varchar              |
| kategori     | varchar              |
| alamat       | text                 |
| latitude     | decimal              |
| longitude    | decimal              |
| geom         | geometry(Point,4326) |

---

## Tabel Kelurahan

| Field | Tipe                   |
| ----- | ---------------------- |
| id    | bigint                 |
| nama  | varchar                |
| geom  | geometry(Polygon,4326) |

---

## Tabel Roads

| Field      | Tipe                      |
| ---------- | ------------------------- |
| id         | bigint                    |
| nama_jalan | varchar                   |
| geom       | geometry(LineString,4326) |

---

## Tabel Facilities

| Field | Tipe                 |
| ----- | -------------------- |
| id    | bigint               |
| nama  | varchar              |
| jenis | varchar              |
| geom  | geometry(Point,4326) |

---

# 7. Kebutuhan API

## Authentication

### POST /api/login

### POST /api/logout

---

## UMKM

### GET /api/umkms

### GET /api/umkms/{id}

### POST /api/umkms

### PUT /api/umkms/{id}

### DELETE /api/umkms/{id}

---

## Spatial

### GET /api/map/umkms

### GET /api/map/kelurahans

### GET /api/map/roads

### GET /api/map/facilities

---

## Routing

### GET /api/routing

Parameter:

- start_lat
- start_lng
- end_lat
- end_lng

---

# 8. Kriteria Penerimaan Sistem

Sistem dinyatakan berhasil apabila:

1. Seluruh data UMKM dapat ditampilkan pada peta.
2. Routing berjalan dengan benar.
3. Layer wilayah dan jalan tampil dengan baik.
4. Analisis potensi ekonomi dapat ditampilkan.
5. Dashboard statistik berjalan sesuai kebutuhan.
6. Seluruh fitur CRUD berfungsi tanpa error.
7. Sistem dapat digunakan oleh administrator, petugas, dan masyarakat.
