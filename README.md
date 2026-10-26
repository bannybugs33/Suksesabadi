# Toko Sukses Abadi (Toko Tempo)

Sistem pencatatan keuangan, monitoring arus kas, serta manajemen utang dan piutang berbasis web. Aplikasi ini dirancang khusus untuk memantau jatuh tempo tagihan pelanggan dan kewajiban supplier secara akurat dan real-time, lengkap dengan riwayat pembayaran bertahap dan pengelolaan bukti kuitansi.

---

## Fitur Utama

### 1. Dashboard Operasional
- Ringkasan arus kas, total piutang aktif pelanggan, dan total utang toko kepada supplier.
- Widget pengingat jatuh tempo terpusat yang mengelompokkan tagihan mendesak (terlambat, jatuh tempo hari ini, dan jatuh tempo besok / H-1).
- Daftar transaksi terkini dengan kalkulasi saldo dan status pembayaran berjalan.

### 2. Manajemen Utang & Piutang (Jatuh Tempo)
- **Pengingat Jatuh Tempo Real-Time**: Status tagihan dihitung otomatis berdasarkan tanggal acuan sistem, mencakup kategori *Terlambat*, *Hari Ini*, *H-1 (Besok)*, *H-2*, dan *H-3*.
- **Penyaringan Data Lengkap**: Filter transaksi berdasarkan rentang waktu tempo, jenis kewajiban (`Utang` / `Piutang`), status pembayaran (`Belum Lunas`, `DP`, `Cicilan`), dan pencarian teks nama pihak atau nomor kuitansi.
- **Panel Rincian Transaksi Interaktif**: Tampilan detail transaksi kuitansi dapat dibuka langsung tanpa perlu memuat ulang seluruh halaman (interaksi berbasis komponen client-side).
- **Pengelolaan Bukti Kuitansi Dinamis**:
  - Mendukung resolusi berkas bukti dari Laravel Storage (`public`), folder aset publik (`images/`), maupun tautan eksternal.
  - Menampilkan nama berkas asli, ekstensi, dan kalkulasi ukuran berkas aktual dari penyimpanan disk.
  - Tampilan penampung (*empty-state*) informatif dan bersih apabila transaksi belum memiliki lampiran berkas fisik.
  - Tombol pratinjau (*lihat bukti*) dan unduhan (*download*) langsung tertaut ke sumber berkas.
- **Riwayat Pembayaran Terperinci**: Pencatatan riwayat pembayaran bertahap (uang muka/DP, cicilan berkala, pelunasan), metode pembayaran (tunai atau transfer bank), tanggal pencatatan, dan operator/kasir yang mencatat.

### 3. Rekapitulasi Keuangan
- Laporan berkala berdasarkan filter periode bulan dan tahun.
- Analisis akumulasi nominal transaksi, total terbayar, dan sisa saldo kewajiban per kategori.

### 4. Autentikasi Pengguna
- Pembatasan akses berbasis sesi login untuk keamanan data operasional toko.
- Pengelolaan multi-akun kasir/administrator toko.

---

## Teknologi yang Digunakan

- **Backend**: PHP 8.4, Laravel Framework 12/13
- **Database**: SQLite (default pada lingkungan lokal) / kompatibel dengan MySQL dan PostgreSQL
- **Frontend**: Laravel Blade, Vanilla JavaScript (DOM manipulation tanpa full page refresh), CSS kustom terstruktur
- **Peralatan Uji & Standar Kode**: PHPUnit 12, Laravel Pint (PSR-12 code style)

---

## Persyaratan Sistem

- PHP >= 8.3 (disarankan PHP 8.4)
- Ekstensi PHP: `pdo_sqlite` (atau `pdo_mysql`), `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`
- Composer >= 2.x
- Node.js >= 18.x dan NPM

---

## Panduan Instalasi

### 1. Salin Repositori dan Masuk ke Direktori Proyek
```bash
git clone <url-repositori>
cd Suksesabadi
```

### 2. Pasang Dependensi Backend (Composer)
```bash
composer install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file konfigurasi contoh dan buat application key:
```bash
cp .env.example .env
php artisan key:generate
```

Secara default, aplikasi menggunakan SQLite. Buat berkas database lokal jika belum tersedia:
```bash
touch database/database.sqlite
```

> **Catatan**: Jika menggunakan MySQL, sesuaikan baris `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada file `.env`.

### 4. Jalankan Migrasi dan Seeder Database
Inisialisasi tabel dan data awal operasional toko:
```bash
php artisan migrate --seed
```

### 5. Hubungkan Direktori Penyimpanan Publik
Wajib dijalankan agar berkas bukti kuitansi yang diunggah ke storage dapat diakses via web:
```bash
php artisan storage:link
```

### 6. Pasang Dependensi Frontend dan Build Aset
```bash
npm install
npm run build
```

Untuk proses pengembangan dengan *hot-reload*:
```bash
npm run dev
```

### 7. Jalankan Server Pengembangan
```bash
php artisan serve
```
Akses aplikasi melalui browser di: `http://localhost:8000`

---

## Kredensial Akun Uji Coba

Data awal (seeder) menyediakan beberapa akun pengguna bawaan:

| Nama Pengguna | Alamat Email | Password | Peran |
| :--- | :--- | :--- | :--- |
| Andi Saputra | `andi@suksesabadi.com` | `password` | Kasir / Operator |
| Safira Alifiya | `safira@suksesabadi.com` | `password` | Kasir / Operator |
| Admin Sukses Abadi | `admin@suksesabadi.com` | `password` | Administrator |

---

## Struktur Entitas Data Utama

1. **`users`**: Menyimpan identitas kasir dan pengelola toko untuk autentikasi.
2. **`kuitansi`**: Menyimpan data induk transaksi tagihan:
   - `nomor_kuitansi`: Nomor kuitansi unik (misal: `KWT-240918`).
   - `nama_pihak`: Nama pihak rekanan (pelanggan atau supplier).
   - `role_pihak`: Keterangan peran pihak (misal: *Pelanggan berutang kepada toko* atau *Supplier harus dibayar toko*).
   - `jenis`: Jenis transaksi (`utang` atau `piutang`).
   - `nominal_transaksi`: Total nilai transaksi awal.
   - `status_pembayaran`: Status pembayaran (`belum_lunas`, `dp`, `cicilan`, `lunas`).
   - `tanggal_kuitansi`: Tanggal transaksi dibuat.
   - `tanggal_jatuh_tempo`: Batas waktu penyelesaian tagihan.
   - `bukti_kuitansi`: Lokasi berkas fisik bukti kuitansi (bisa berupa path storage, public asset, atau URL).
   - `keterangan`: Catatan deskripsi transaksi.
3. **`pembayaran`**: Menyimpan riwayat pembayaran parsial/lunas yang terhubung ke kuitansi:
   - `kuitansi_id`: Relasi ke tabel `kuitansi`.
   - `nominal`: Besaran nominal pembayaran yang disetorkan.
   - `tanggal_pembayaran`: Waktu transaksi pembayaran dilakukan.
   - `metode_pembayaran`: Metode pembayaran (misal: `Transfer bank`, `Tunai`).
   - `dicatat_oleh`: Nama operator yang menerima/mencatat transaksi.
   - `catatan`: Keterangan tambahan transaksi pembayaran.

---

## Pengujian dan Standar Kode

### Menjalankan Pengujian Otomatis (PHPUnit)
Aplikasi dilengkapi pengujian fitur komprehensif yang mencakup alur autentikasi, kalkulasi metrik dashboard, penyaringan jatuh tempo, serta validasi berkas bukti kuitansi:
```bash
php artisan test --compact
```

### Memeriksa dan Memformat Gaya Kode (Laravel Pint)
Proyek mengikuti standar gaya penulisan kode PHP Laravel:
```bash
vendor/bin/pint --format agent
```

---

## Struktur Direktori Utama

```
Suksesabadi/
├── app/
│   ├── Http/Controllers/      # Controller (Dashboard, JatuhTempo, Rekapitulasi, Auth)
│   └── Models/                # Model Eloquent (User, Kuitansi, Pembayaran)
├── database/
│   ├── migrations/            # Skema tabel database
│   └── seeders/               # Data awal untuk pengujian dan demo
├── public/
│   ├── images/                # Aset gambar statis
│   └── storage/               # Symlink ke storage/app/public untuk file upload
├── resources/
│   └── views/
│       ├── auth/              # Tampilan halaman login
│       ├── dashboard.blade.php# Tampilan ringkasan operasional toko
│       ├── jatuh-tempo/       # Tampilan modul jatuh tempo & rincian bukti
│       └── rekapitulasi/      # Tampilan modul rekapitulasi transaksi
├── routes/
│   └── web.php                # Rute aplikasi web
├── storage/
│   └── app/public/            # Penyimpanan dokumen dan bukti kuitansi
└── tests/
    └── Feature/               # Test suite PHPUnit untuk fitur aplikasi
```

---

## Lisensi

Proyek ini dikembangkan secara internal untuk operasional Toko Sukses Abadi.
