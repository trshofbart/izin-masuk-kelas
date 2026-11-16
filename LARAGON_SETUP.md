# Setup Laragon — Izin Masuk Kelas SMKN 5 Surakarta

Panduan instalasi di laptop/server Windows memakai Laragon (Apache + MySQL + PHP 8.3).

## 1. Salin proyek

1. Ekstrak `izin-masuk-kelas-laragon.zip` ke `C:\laragon\www\izin-masuk-kelas`.
   (Zip sudah berisi `vendor/` dan hasil build Vite, jadi **tidak perlu**
   `composer install` / `npm install`.)
2. Salin `.env.laragon` menjadi `.env` di folder proyek.
3. Sesuaikan `APP_URL` di `.env` dengan alamat akses Anda (lihat cara akses di bawah).
4. Buat application key (sekali saja, di terminal Laragon / di folder proyek):

```bash
php artisan key:generate
```

## 2. Buat database MySQL

1. Jalankan Laragon → klik **Database** (HeidiSQL) atau buka phpMyAdmin.
2. Buat database baru bernama **`izin_masuk_kelas`** (collation `utf8mb4_unicode_ci`).
3. Pastikan `.env` berisi:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=izin_masuk_kelas
DB_USERNAME=root
DB_PASSWORD=
```

> Di Laragon standar, user MySQL `root` tanpa password. Jika MySQL Anda memakai
> password, isi `DB_PASSWORD` sesuai.

## 3. Migrasi + seed

Di terminal Laragon, dari folder proyek:

```bash
php artisan migrate --seed
```

Perintah ini membuat semua tabel dan mengisi data awal: 8 jurusan, 50 kelas,
1 user admin, dan 1 QR statis "Lobi Utama".

## 4. Akses aplikasi

- **Form siswa:** alamat root aplikasi (lihat cara akses)
- **Login admin:** `/admin/login` — default `admin@smkn5.sch.id` / `admin12345`
  (**wajib ganti password** setelah instalasi; tidak ada menu ganti password,
  ganti lewat `php artisan tinker`: `User::first()->update(['password' => bcrypt('baru')])`)
- **Cetak QR lobi:** login admin → menu **QR Lobi** → **Cetak QR** → tempel hasil cetak di lobi.

### Cara A — VirtualHost port 8002 (disarankan)

Port 8002 dipilih supaya tidak bentrok dengan Buku Induk (8000) dan Izin Keluar (8001).

1. Pastikan Apache mendengarkan port 8002: di `C:\laragon\bin\apache\httpd-*\conf\httpd.conf`
   harus ada baris `Listen 8002` (tambahkan bila belum ada).
2. Salin file `laragon/izin-masuk-kelas-vhost.conf` menjadi
   `C:\laragon\etc\apache2\sites-enabled\00-izin-masuk-kelas.conf`.
3. Restart Apache di Laragon (Stop lalu Start).
4. Di `.env`: `APP_URL=http://localhost:8002`, lalu `php artisan config:clear`.
5. Buka `http://localhost:8002/` (admin: `http://localhost:8002/admin`).

### Cara B — `php artisan serve` (paling cepat untuk tes)

```bash
php artisan serve --port=8002
```

Untuk akses dari HP di WiFi yang sama:
`php artisan serve --host=0.0.0.0 --port=8002` lalu buka
`http://IP-LAN-laptop:8002/` di HP. Scan QR kamera membutuhkan HTTPS atau
localhost — di jaringan LAN biasa, gunakan cara alternatif: scan QR cetak
dengan aplikasi kamera (halaman lobi terbuka) lalu masukkan kode izin,
atau buka aplikasi lewat alamat yang mendukung HTTPS.

## 5. Alur pakai sehari-hari

1. Siswa terlambat / izin masuk membuka form, mengisi data, submit.
2. Siswa menuju lobi, menekan **Scan QR Izin Masuk Kelas** di halaman
   statusnya dan men-scan QR cetak — status berubah menjadi **Diizinkan**.
3. Guru piket / admin membuka **Rekap Harian** untuk memeriksa daftar hari itu.
