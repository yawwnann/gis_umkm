# Product Requirements Document (PRD)

# Sistem Informasi Geografis (SIG) UMKM Kuliner Kecamatan Sungailiat

## 1. Ringkasan Produk

Sistem Informasi Geografis (SIG) UMKM Kuliner Kecamatan Sungailiat merupakan aplikasi berbasis web yang digunakan untuk memetakan persebaran UMKM kuliner, menampilkan informasi spasial pendukung, serta melakukan analisis potensi ekonomi berdasarkan kondisi lingkungan sekitar UMKM.

Sistem dikembangkan menggunakan Laravel, Vue.js, Tailwind CSS, PostgreSQL, PostGIS, dan Redis.

---

# 2. Tujuan Produk

## Tujuan Utama

Menyediakan informasi persebaran UMKM kuliner secara visual dan interaktif serta membantu mengidentifikasi tingkat potensi ekonomi lokasi UMKM berdasarkan faktor spasial.

## Tujuan Khusus

- Menampilkan persebaran UMKM kuliner.
- Menampilkan batas wilayah administrasi Sungailiat.
- Menampilkan jaringan jalan.
- Menampilkan fasilitas pendukung aktivitas ekonomi.
- Menghitung skor potensi ekonomi UMKM.
- Menyediakan rute menuju lokasi UMKM.

---

# 3. Pengguna Sistem

## Masyarakat

- Melihat peta UMKM.
- Mencari UMKM.
- Melihat detail UMKM.
- Melihat rute menuju UMKM.

## Petugas Lapangan

- Menambah data UMKM.
- Memperbarui data UMKM.
- Mengelola foto UMKM.

## Administrator

- Mengelola seluruh data.
- Mengelola akun petugas.
- Mengelola parameter analisis.
- Melihat statistik dan dashboard.

---

# 4. Data Spasial

## Layer Utama

### Batas Wilayah

Tipe:

```text
Polygon
```

Data:

- Desa Rebo
- Bukit Betung
- Jelitik
- Kenanga
- Kudai
- Lubuk Kelik
- Matras
- Parit Padang
- Sinar Baru
- Sinar Jaya Jelutung
- Sri Menanti
- Sungailiat
- Surya Timur

---

### UMKM Kuliner

Tipe:

```text
Point
```

Data:

- Nama usaha
- Pemilik
- Alamat
- Kategori
- Foto
- Koordinat

---

### Jaringan Jalan

Tipe:

```text
LineString
```

Data:

- Nama jalan
- Jenis jalan
- Surface
- Oneway

---

### Kawasan Pemukiman

Tipe:

```text
Polygon
```

Digunakan sebagai indikator konsentrasi calon pelanggan.

---

### Fasilitas Niaga

Tipe:

```text
Point
```

Contoh:

- Pasar
- Pertokoan
- Pusat perdagangan

---

### Fasilitas Pendidikan

Tipe:

```text
Point
```

Contoh:

- SD
- SMP
- SMA
- Perguruan Tinggi

---

### Fasilitas Pemerintahan

Tipe:

```text
Point
```

Contoh:

- Kantor Kecamatan
- Kantor Kelurahan
- Kantor Pemerintah

---

### Objek Wisata

Tipe:

```text
Point
```

Contoh:

- Pantai
- Destinasi Wisata

---

# 5. Fitur Utama

## F01 Peta Interaktif

Menampilkan seluruh layer spasial pada peta.

Fitur:

- Zoom
- Pan
- Layer Control
- Basemap Switcher

---

## F02 Manajemen UMKM

CRUD data UMKM.

Fitur:

- Tambah UMKM
- Edit UMKM
- Hapus UMKM
- Upload Foto

---

## F03 Pencarian UMKM

Pencarian berdasarkan:

- Nama usaha
- Pemilik
- Kelurahan

---

## F04 Filter Data

Filter berdasarkan:

- Kelurahan
- Kategori usaha
- Potensi ekonomi

---

## F05 Detail UMKM

Menampilkan:

- Informasi usaha
- Foto usaha
- Lokasi
- Potensi ekonomi

---

## F06 Routing

Menampilkan:

- Jalur menuju UMKM
- Jarak tempuh
- Estimasi perjalanan

---

## F07 Dashboard Statistik

Menampilkan:

- Total UMKM
- UMKM per kelurahan
- UMKM per kategori
- UMKM berdasarkan tingkat potensi

---

## F08 Analisis Potensi Ekonomi

Menghasilkan klasifikasi:

```text
Tinggi
Sedang
Rendah
```

berdasarkan faktor spasial.

---

# 6. Model Analisis Potensi Ekonomi

## Faktor Penilaian

### Akses Jalan

Bobot:

```text
40%
```

Semakin dekat dengan jalan utama maka skor semakin tinggi.

---

### Kedekatan Fasilitas Niaga

Bobot:

```text
30%
```

Semakin dekat dengan pasar atau pusat perdagangan maka skor semakin tinggi.

---

### Kawasan Pemukiman

Bobot:

```text
20%
```

Semakin dekat dengan kawasan pemukiman maka skor semakin tinggi.

---

### Kepadatan Penduduk

Bobot:

```text
10%
```

Berdasarkan data jumlah penduduk dan luas wilayah kelurahan.

---

## Rumus Potensi

```text
Potensi =
(0.4 × Skor Jalan)
+
(0.3 × Skor Niaga)
+
(0.2 × Skor Pemukiman)
+
(0.1 × Skor Kepadatan Penduduk)
```

---

# 7. Arsitektur Sistem

```text
Vue.js + Tailwind
        │
        ▼
Laravel REST API
        │
 ┌──────┴──────┐
 ▼             ▼
Redis      PostgreSQL
Cache      + PostGIS
```

---

# 8. Struktur Data Utama

## villages

- id
- name
- population
- area_km2
- density
- geom

## umkms

- id
- name
- owner
- category
- address
- latitude
- longitude
- geom

## roads

- id
- osm_id
- name
- highway
- surface
- geom

## settlements

- id
- name
- geom

## trading_centers

- id
- name
- type
- geom

## schools

- id
- name
- geom

## government_facilities

- id
- name
- geom

## tourisms

- id
- name
- geom

---

# 9. Teknologi

Backend:

- Laravel 12

Frontend:

- Vue 3
- Tailwind CSS

Database:

- PostgreSQL 16
- PostGIS

Cache:

- Redis

Map:

- Leaflet

Deployment:

- Ubuntu Server
- Nginx
