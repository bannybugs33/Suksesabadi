# Toko Sukses Abadi (Toko Sukses Abadi)

Sistem pencatatan keuangan, monitoring arus kas, serta manajemen utang dan piutang berbasis web. Aplikasi ini dirancang khusus untuk memantau jatuh tempo tagihan pelanggan dan kewajiban supplier secara akurat dan real-time, lengkap dengan riwayat pembayaran bertahap dan pengelolaan bukti kuitansi.

---

## Fitur Utama

### 1. Dashboard Operasional
- Ringkasan arus kas, total piutang aktif pelanggan, dan total utang toko kepada supplier.
- Widget pengingat jatuh tempo terpusat yang mengelompokkan tagihan mendesak (terlambat, jatuh tempo hari ini, dan jatuh tempo besok / H-1).
- Daftar transaksi terkini dengan kalkulasi saldo dan status pembayaran berjalan.

### 2. Manajemen Utang & Piutang (Jatuh Tempo)
- **Pengingat Jatuh Tempo Real-Time**: Tagihan aktif yang masih bersisa diingatkan mulai **H-7** sebelum jatuh tempo, dengan kategori *Mendekati jatuh tempo (H-7 s/d H-1)*, *Jatuh tempo hari ini*, dan *Terlambat (N hari)*. Pengingat hanya tampil di Dashboard dan halaman Jatuh Tempo (tanpa WhatsApp atau email) dan hilang otomatis setelah tagihan lunas.
- **Penyaringan Data Lengkap**: Filter transaksi berdasarkan rentang waktu tempo, jenis kewajiban (`Utang` / `Piutang`), status pembayaran (`Belum Lunas`, `DP`, `Cicilan`), dan pencarian teks nama pihak atau nomor kuitansi.
- **Panel Rincian Transaksi Interaktif**: Tampilan detail transaksi kuitansi dapat dibuka langsung tanpa perlu memuat ulang seluruh halaman (interaksi berbasis komponen client-side).
- **Mengarsipkan Kuitansi**:
  - Tepat **satu foto** per arsip (JPG atau PNG, maksimal 10 MB); PDF dan beberapa lampiran ditolak.
  - **Nomor arsip** dibuat otomatis oleh sistem (unik, tidak diubah); **nomor kuitansi fisik** adalah isian opsional yang terpisah.
  - Owner mengisi **nominal pembayaran**, bukan memilih status. Status dihitung otomatis dengan aturan yang sama seperti pembayaran selanjutnya (belum lunas, DP, cicilan, lunas).
  - Tanggal jatuh tempo **wajib selama masih ada sisa**; transaksi yang langsung lunas boleh tanpa jatuh tempo.
  - Peringatan sebelum menyimpan bila ada arsip yang mirip (nama, tanggal, dan nominal sama, atau nomor kuitansi fisik sama).
- **Pengelolaan Bukti Kuitansi Dinamis**:
  - Foto bukti disimpan di penyimpanan privat dan hanya dapat dibuka oleh owner yang sudah login.
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

### 5. Penyimpanan Foto Kuitansi (Privat)
Foto/berkas bukti kuitansi disimpan di `storage/app/private/kuitansi/` dan **tidak punya URL publik**.
Foto hanya bisa dibuka lewat aplikasi oleh owner yang sudah login, jadi **tidak perlu** menjalankan `php artisan storage:link`.

Jika sebelumnya sudah ada foto lama yang tersimpan di folder publik (`storage/kuitansi/...`), pindahkan ke penyimpanan privat dengan:
```bash
php artisan bukti:amankan
```
(Container Docker menjalankan perintah ini otomatis saat start.)

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
   - `nomor_kuitansi`: **Nomor arsip** yang dibuat sistem, unik, dan tidak diubah (misal: `KWT-240918`, lalu `KWT-240918-2` untuk tanggal yang sama).
   - `nomor_kuitansi_fisik`: Nomor pada dokumen asli, opsional dan tidak harus unik.
   - `nama_pihak`: Nama pihak rekanan (pelanggan atau supplier).
   - `role_pihak`: Keterangan peran pihak (misal: *Pelanggan berutang kepada toko* atau *Supplier harus dibayar toko*).
   - `jenis`: Jenis transaksi (`utang` atau `piutang`).
   - `nominal_transaksi`: Total nilai transaksi awal.
   - `status_pembayaran`: Status pembayaran (`belum_lunas`, `dp`, `cicilan`, `lunas`), dihitung otomatis dari pembayaran.
   - `tanggal_kuitansi`: Tanggal transaksi dibuat.
   - `tanggal_jatuh_tempo`: Batas waktu penyelesaian tagihan (wajib selama masih ada sisa).
   - `bukti_kuitansi`: Lokasi satu foto bukti kuitansi di penyimpanan privat.
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
├── public/                    # Hanya aset web (tidak ada foto kuitansi di sini)
├── resources/
│   └── views/
│       ├── auth/              # Tampilan halaman login
│       ├── dashboard.blade.php# Tampilan ringkasan operasional toko
│       ├── jatuh-tempo/       # Tampilan modul jatuh tempo & rincian bukti
│       └── rekapitulasi/      # Tampilan modul rekapitulasi transaksi
├── routes/
│   └── web.php                # Rute aplikasi web
├── storage/
│   └── app/private/           # Foto bukti kuitansi (privat, hanya lewat login)
└── tests/
    └── Feature/               # Test suite PHPUnit untuk fitur aplikasi
```

---

## Lisensi

Proyek ini dikembangkan secara internal untuk operasional Toko Sukses Abadi.
