# EMR Pinilih (Electronic Medical Record)

Sistem rekam medis elektronik berbasis web (PHP Native) untuk **Rumah Kebugaran Difabel (RKD) Pinilih**. Aplikasi ini digunakan untuk mengelola data pasien difabel, mencatat riwayat rekam medis, serta mengatur jadwal program layanan kesehatan (Fisioterapi, Kinesioterapi, Konsultasi, Screening, dan Edukasi) bagi tiga peran pengguna: **Admin**, **Manajemen**, dan **Terapis**.

## Fitur Utama

- **Manajemen Data Master** — Data Pasien, Tenaga Medis/Terapis, Pengguna, Disabilitas, dan Peserta, dengan tabel ber-paginasi *server-side* (DataTables + AJAX) sehingga tetap responsif meski data berjumlah ribuan baris.
- **Rekam Medis per Program** — pencatatan riwayat Fisioterapi, Kinesioterapi, Konsultasi, Screening, dan Edukasi per pasien, termasuk unggah foto perkembangan/dokumen pendukung.
- **Dasbor & Statistik** — grafik distribusi pasien dan disabilitas (Chart.js) di panel Admin dan Manajemen.
- **Cetak & Ekspor Laporan** — PDF (TCPDF) dan Excel (PhpSpreadsheet) untuk laporan rangkuman maupun detail per pasien/program.
- **Cadangan Database** — unduh salinan `.sql` langsung dari browser dengan deteksi otomatis lokasi `mysqldump` (Windows/Linux). Endpoint `backupData.php` tersedia di ketiga panel (Admin, Manajemen, Terapis), namun tombolnya di UI Atur Profil hanya tampil untuk Admin dan Manajemen — di panel Terapis tombolnya sengaja disembunyikan (kode dinonaktifkan), meski endpoint tetap bisa diakses langsung lewat URL oleh role Terapis.
- **Lupa Password via OTP** — kode OTP 6 digit dikirim ke email (PHPMailer + SMTP Brevo), berlaku 10 menit.
- **Kontrol Akses Berbasis Peran** — setiap peran (Admin, Manajemen, Terapis) memiliki menu dan hak akses yang berbeda sesuai tanggung jawabnya.

## Teknologi yang Digunakan

| Kategori | Teknologi |
|---|---|
| Bahasa Utama | PHP 8.2 (Native, tanpa framework) |
| Database | MySQL — diakses via PDO (utama) dan MySQLi (fungsi CRUD warisan) |
| Tampilan | HTML5, CSS3, Bootstrap 5.3.x |
| Interaktivitas | JavaScript, jQuery, DataTables, Chart.js, SweetAlert2 |
| Cetak PDF | TCPDF (`_tcpdf/`) |
| Cetak Excel | PhpSpreadsheet, PhpWord (via Composer) |
| Email | PHPMailer + SMTP Brevo (Sendinblue) |
| Containerization | Docker (`Dockerfile`, `compose.yaml`) |

## Prasyarat

- PHP 8.2 atau lebih baru, dengan ekstensi `pdo_mysql` dan `mysqli` aktif
- MySQL/MariaDB (disarankan via XAMPP untuk pengembangan lokal)
- Composer (untuk dependensi PHP)
- Node.js/NPM (opsional, untuk dependensi JavaScript seperti Chart.js)

## Cara Menjalankan (Quick Start)

1. Clone repository ini dan masuk ke direktori proyek.
2. Buat database baru di MySQL bernama `emr_pinilih`. Repositori ini tidak menyertakan file dump/skema `.sql` — struktur tabel perlu dibuat manual atau diminta terpisah dari pengelola sistem, lalu diimport ke database yang baru dibuat.
3. Install dependensi PHP:
   ```bash
   composer install
   ```
4. (Opsional) Install dependensi JavaScript:
   ```bash
   npm install
   ```
5. Konfigurasi koneksi database via *environment variable* (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`) atau biarkan menggunakan nilai fallback bawaan di `config.php` (`127.0.0.1` / `emr_pinilih` / `root` / password kosong).
6. Jalankan server PHP built-in dari root proyek:
   ```bash
   php -S localhost:8000
   ```
7. Buka browser ke **http://localhost:8000** dan login menggunakan akun yang tersedia di tabel `user`.

### Menjalankan via Docker

Proyek ini juga menyediakan `Dockerfile` dan `compose.yaml` untuk containerization. `compose.yaml` membaca kredensial database dari file `.env` (tidak ikut ter-commit ke Git) — salin dulu dari template sebelum menjalankan:

```bash
cp .env.example .env
# lalu sesuaikan DB_PASS dan variabel lain di .env sesuai environment Anda
docker compose up -d
```

## Struktur Folder

```
├── admin/         # Panel Admin (akses penuh ke seluruh modul)
├── manajemen/     # Panel Manajemen (akses lihat-saja/read-only ke data program)
├── terapis/       # Panel Terapis (akses ke jadwal & rekam medis miliknya)
├── _function_i/   # Fungsi inti CRUD, koneksi database, dan komponen form bersama
├── _img/          # Aset gambar statis (logo, ikon, maskot)
├── _tcpdf/        # Library TCPDF untuk cetak PDF
├── vendor/        # Dependensi Composer
├── config.php     # Konfigurasi koneksi database (PDO)
└── index.php      # Halaman login
```

## Peran & Hak Akses

| Modul | Admin | Manajemen | Terapis |
|---|:---:|:---:|:---:|
| Data Pasien / Tenaga Medis / Pengguna / Disabilitas / Peserta | CRUD penuh | Lihat saja | – |
| Jadwal Program (Fisio/Kinesio/Konsultasi/Screening/Edukasi) | CRUD penuh | Lihat saja | CRUD sesuai jadwal sendiri |
| Grafik Statistik | ✓ | ✓ | – |
| Ekspor Laporan (PDF/Excel) | ✓ | ✓ | – |
| Cadangan Database (tombol tampil di UI) | ✓ | ✓ | – |

---
*Dibuat & dikembangkan untuk keperluan operasional Rumah Kebugaran Difabel (RKD) Pinilih.*