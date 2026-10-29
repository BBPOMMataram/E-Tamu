# Release Note — E-Tamu v2.0 (SI MANDALIKA BBPOM di Mataram)

**Tanggal Rilis:** 29 Oktober 2026  
**Periode Pengembangan:** 22 September 2026 – 29 Oktober 2026  
**Modul:** Elektronik Penerimaan Tamu (**E-Tamu v2.0**)  
**Repositori:** `E-tamu` (Backend API) & `percepat-fe-main` (Frontend Next.js)

---

## 1. Ringkasan Pembaruan (Executive Summary)

**E-Tamu v2.0** merupakan pembaruan menyeluruh (*major upgrade*) dari **E-Tamu v1.0**. Pada versi v2.0, sistem buku tamu BBPOM di Mataram dimigrasikan dari arsitektur monolitik berbasis Laravel Blade menjadi arsitektur modern **Single Page Application (Next.js)** yang terintegrasi penuh di dalam ekosistem portal **SI MANDALIKA** serta didukung oleh **Laravel REST API**.

Pembaruan ini menghadirkan peningkatan signifikan pada kecepatan antarmuka, keamanan validasi lokasi pengunjung (*Double-Layer Geofencing*), kualitas tangkapan kamera swafoto di berbagai perangkat, integrasi autentikasi satu pintu (*Single Sign-On*), serta fitur analisis perbandingan tahunan pada Dashboard Admin.

---

## 2. Perbandingan E-Tamu v1.0 vs E-Tamu v2.0

| Aspek / Fitur | E-Tamu v1.0 (Versi Lama) | E-Tamu v2.0 (Versi Baru) |
| :--- | :--- | :--- |
| **Arsitektur Sistem** | Monolitik Laravel Blade (`welcome.blade.php` & `dashboard.blade.php`), setiap aksi memuat ulang (*reload*) halaman. | **Next.js Frontend + Laravel REST API** (`/api/e-tamu/*`), interaktif dan cepat tanpa *reload* halaman, terintegrasi dengan portal **SI MANDALIKA**. |
| **Autentikasi Admin** | Login terpisah menggunakan tabel user lokal E-Tamu. | Terintegrasi dengan **SSO Auth-BPOM** (`admin` modul E-Tamu & `superadmin` SI MANDALIKA). |
| **Validasi Lokasi (Geofencing)** | Hanya menghitung jarak di browser (Leaflet.js) dan mengirimkannya sebagai *hidden input* tanpa memblokir form atau memvalidasi ulang di server. | **Double-Layer Geofencing (Radius ≤ 50m)**:<br>1. **Client-side**: Pengecekan akurasi GPS rekursif (hingga 5x percobaan jika akurasi > 100m) serta layar pemblokiran otomatis jika GPS mati atau di luar area.<br>2. **Server-side**: Validasi ulang koordinat asli dengan rumus **Haversine** di backend (`HTTP 403` jika di luar radius). |
| **Kamera Swafoto (Selfie)** | Menggunakan pustaka lawas `webcam.min.js` (`320x240px`), rasio gambar sering terdistorsi pada layar HP *portrait*. | Menggunakan **Native WebRTC + HTML5 Canvas** dengan *auto aspect-ratio cropping* (`object-cover` hingga `640px`), efek cermin (*mirror*), dan pemilih sumber kamera. |
| **Formulir & Autofill Tamu** | Tampilan gradien lama; *autofill* hanya terpicu saat fokus keluar dari kolom nama (`onchange`). | Desain formulir modern & responsif; **Autofill Real-Time** saat mengetik nama (> 2 karakter) dilengkapi notifikasi *toast*. |
| **Survei Kepuasan Layanan** | Mengirim form dengan *redirect* halaman penuh. | **Popup Modal Interaktif** dengan 3 tingkat kepuasan (*Tidak Puas, Puas, Sangat Puas*) secara *asynchronous*. |
| **Grafik Statistik Tahunan** | Grafik batang hanya mengikuti 1 filter tahun/bulan aktif; tidak dapat membandingkan dua tahun secara berdampingan. | **Grafik Batang Perbandingan Multi-Tahun**: Dilengkapi **Checkbox Dropdown Tahun** (hanya menampilkan tahun yang memiliki data + opsi *Semua Tahun*) di mana batang tiap keperluan layanan ditampilkan **bersebelahan** antar tahun yang dipilih. |
| **Tabel & Foto Tamu (Admin)** | Paginasi dan filter me-*reload* halaman penuh; foto tampil besar di dalam baris tabel. | **Server-Side Pagination** tanpa *reload*, *thumbnail avatar* rapi dengan fitur **Popup Zoom Foto**, serta unduh **Excel (`.xlsx`)** sesuai filter. |
| **Deployment** | Deployment manual ke server. | **CI/CD Otomatis** menggunakan GitHub Actions (`.github/workflows/deploy.yml`). |

---

## 3. Riwayat Perubahan Berdasarkan Commit (22 September – 29 Oktober 2026)

### A. Backend (`E-tamu`)
- **`cdefd63` (01 Okt 2026) — *Menyesuaikan API Controller untuk Frontend***:
  - Menambahkan endpoint `/api/e-tamu/init`, `/api/e-tamu/store`, dan `/api/e-tamu/survey` pada `PresensiController`.
  - Menambahkan validasi jarak server-side menggunakan algoritma *Haversine* pada `PresensiController::storeApi`.
  - Menambahkan endpoint `/api/e-tamu/dashboard` pada `DashboardController`.
- **`42a9615` & `02b609a` (02 & 05 Okt 2026) — *Konfigurasi CORS***:
  - Menyesuaikan `config/cors.php` untuk mengizinkan komunikasi lintas domain (CORS) dan kredensial dari frontend Next.js.
- **`6b2cc69` (05 Okt 2026) — *GitHub Actions Automated Deployment***:
  - Menambahkan workflow `.github/workflows/deploy.yml` untuk otomatisasi deployment ke server produksi.
- **`fab936b` (14 Okt 2026) — *Fitur Export Excel & Paginasi API Dashboard***:
  - Menambahkan endpoint `/api/e-tamu/download-excel` (`GuestsExport`) yang mendukung filter tahun dan bulan.
  - Menambahkan dukungan paginasi data tamu dan statistik layanan pada `DashboardController::getDashboardApi`.
- **`466aaa1` (29 Okt 2026) — *Dukungan Perbandingan Multi-Tahun pada Grafik Batang Dashboard***:
  - Menambahkan dukungan parameter `chart_years` pada `/api/e-tamu/dashboard` agar admin dapat membandingkan jumlah tamu per keperluan antar tahun secara berdampingan.

### B. Frontend (`percepat-fe-main`)
- **`056e636` & `eb62da6` (29 Sep 2026) — *Implementasi Awal Modul E-Tamu & Pembaruan UI Dashboard***:
  - Membangun halaman buku tamu (`/e-tamu`) dan dashboard (`/e-tamu/dashboard`) berbasis Next.js.
- **`6682aa5`, `71681d0`, `b3a2571` (29 Sep – 02 Okt 2026) — *Integrasi Environment Produksi***:
  - Menambahkan konfigurasi `NEXT_PUBLIC_BACKEND_URL_ETAMU` untuk koneksi API produksi.
- **`8a3fcb1` (12 Okt 2026) — *Penyempurnaan Buku Tamu & Pemisahan Halaman Admin E-Tamu***:
  - Memisahkan halaman khusus Admin (`/admin/e-tamu`) yang dilindungi autentikasi role `admin`/`superadmin`.
  - Menyempurnakan tangkapan kamera WebRTC proporsional dan pengecekan lokasi GPS rekursif.
- **`8e4c603` (14 Okt 2026) — *Komponen Statistik, Filter, Paginasi & Zoom Foto Dashboard***:
  - Menambahkan filter Tahun & Bulan, unduh Excel (`.xlsx`), unduh gambar grafik (`.png`), paginasi tabel, dan popup zoom foto tamu.
- **`d110046` (29 Okt 2026) — *Checkbox Dropdown Perbandingan Tahun Grafik Batang***:
  - Menambahkan *Checkbox Dropdown* pilihan tahun pada grafik batang dashboard admin untuk membandingkan statistik kunjungan per keperluan secara berdampingan antar tahun.

