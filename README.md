# Ecomm Laravel — Toko Online Multi-Level

Aplikasi e-commerce berbasis **Laravel 12** (PHP 8.2, MySQL/TiDB) dengan peran Superadmin, Pemilik Toko, Admin, dan Pelanggan. Termasuk fitur cart, checkout, ongkir (RajaOngkir), pembayaran Midtrans, flash sale, kupon, chat, ulasan, dan laporan.

---

## Deploy Gratis (Render + TiDB Cloud)

Arsitektur deploy **gratis permanen**:

- **Render** — Web Service (Docker), plan `free`
- **TiDB Cloud Starter** — database MySQL-compatible (`MySQL 8`), 5 GiB storage gratis

> Mengapa TiDB? Database utama diaplikasi ini aslinya MySQL. TiDB 100% kompatibel MySQL sehingga **tidak perlu konversi SQL**. Alternatif PostgreSQL (Neon/Supabase) butuh konversi besar dan tidak disarankan untuk project ini.

### 1. Siapkan Database TiDB

1. Daftar di [tidbcloud.com](https://tidbcloud.com) → buat *TiDB Cloud Starter* cluster (pilih region **Singapore**).
2. Di halaman cluster, buka **SQL Editor**, jalankan:
   ```sql
   CREATE DATABASE eshop;
   ```
3. Kita import database dari MySQL lokal (lihat langkah 2).

### 2. Import Database MySQL Lokal → TiDB

Tabel inti (produk, pembayaran, pelanggan, dll.) **tidak ada di migration** — database dibangun langsung di phpMyAdmin. Maka jangan pakai `php artisan migrate`. Import dump full:

```bash
# Windows (XAMPP): export dulu database lokal
cd C:\xampp\mysql\bin
mysqldump -u root eshop_laravel --no-tablespaces > C:\Temp\eshop_backup.sql

# Import ke TiDB (ganti host/user/password dari dashboard TiDB)
mysql --ssl-mode=REQUIRED -h <host>.tidbcloud.com -P 4000 -u <user> -p -D eshop < C:\Temp\eshop_backup.sql
```

> Alternatif: buka file `.sql` di MySQL Workbench / DBeaver yang tersambung ke TiDB dan jalankan.
>
> **Catatan migration**: folder `database/migrations` berisi migration tambahan (kolom/index) yang **MySQL-spesifik**. Jangan jalankan `php artisan migrate` di produksi — schema sudah terbawa oleh dump di atas.

### 3. Push ke GitHub

```bash
git init
git add -A
git commit -m "init: siap deploy Render + TiDB"
git remote add origin https://github.com/<username>/ecomm_laravel.git
git push -u origin main
```

> `.env` dan folder `vendor/` sudah di-ignore — jangan pernah commit.

### 4. Deploy ke Render (Blueprint)

1. Daftar di [render.com](https://render.com) → hubungkan akun **GitHub**.
2. **New → Blueprint** → pilih repo `ecomm_laravel`.
3. Render membaca `render.yaml` dan membuat service `ecomm-laravel` otomatis.
4. Sebelum/sesudah deploy, isi **secrets** yang `sync: false` di dashboard service → **Environment**:
   - `APP_KEY` → salin dari `.env` lokal: `base64:...` (jangan ubah)
   - `DB_HOST` → host TiDB Anda (dengan `-` disensor, ganti di `render.yaml` atau dashboard)
   - `DB_DATABASE=eshop`, `DB_USERNAME`, `DB_PASSWORD` → dari TiDB
   - `APP_URL` → `https://ecomm-laravel.onrender.com` (atau custom domain)
   - `MIDTRANS_CLIENT_KEY`, `MIDTRANS_SERVER_KEY`, `RAJAONGKIR_API_KEY`, `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`
5. Set `MIDTRANS_CALLBACK_URL` di dashboard Midtrans: `https://ecomm-laravel.onrender.com/midtrans/callback`
6. Atur `GOOGLE_REDIRECT_URI` di Google Cloud Console ke `https://ecomm-laravel.onrender.com/auth/google/callback`.

Deploy berhasil jika halaman `https://ecomm-laravel.onrender.com/home_toko/index` terbuka.

### 5. Batasan Plan Gratis (Wajib Tahu)

| Hal | Penjelasan |
|---|---|
| **Cold start** | Service "tidur" setelah 15 menit idle; request pertama butuh 30–60 detik. |
| **File upload** | Folder `public/fotoproduk` dkk. bersifat ephemeral — **hilang saat redeploy**. Untuk produksi serius, pindahkan storage ke layanan eksternal (Supabase Storage / Cloudinary / B2). |
| **Disk** | Free tier tidak mendukung persistent disk. |
| **Queue/worker** | Free tier mematikan background worker; project ini set `QUEUE_CONNECTION=sync` sehingga job berjalan inline. |
| **TiDB quota** | 5 GiB storage + 50M RUs/bulan. Setelah kuota habis, koneksi baru ditolak sampai bulan berikutnya. |

### 6. Struktur File Deployment

```
Dockerfile                    # nginx + php-fpm (PHP 8.2), composer install saat build
.dockerignore
render.yaml                   # Konfigurasi Blueprint Render
conf/nginx/nginx-site.conf    # Konfigurasi nginx (webroot public/, gzip, upload 30M)
scripts/00-laravel-deploy.sh  # config:cache, view:cache, buat folder upload
```

### Local Development

```bash
cp .env.example .env
php artisan key:generate
# atur koneksi MySQL di .env (DB_DATABASE=eshop_laravel)
php artisan serve
```

---

## License

MIT