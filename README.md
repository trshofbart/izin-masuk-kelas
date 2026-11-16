# Izin Masuk Kelas — SMKN 5 Surakarta

Aplikasi PWA (Laravel + Tailwind CSS, database MySQL) untuk izin masuk kelas:

1. **Siswa mengisi form** — tanggal (otomatis hari ini), nama, no. absen, kelas
   (dropdown; jurusan terisi otomatis mengikuti kelas), dan keterangan.
2. **Setelah submit**, siswa masuk ke halaman status berisi tombol
   **Scan QR Izin Masuk Kelas**. Siswa men-scan QR statis yang tertempel di
   lobi — begitu scan berhasil, keterangan di halaman siswa otomatis berubah
   menjadi **Diizinkan** (halaman juga mengecek status tiap 3 detik, jadi
   konfirmasi lewat halaman lobi pun langsung terlihat).
   Cara alternatif: scan QR cetak dengan aplikasi kamera HP → halaman lobi
   terbuka → masukkan kode izin → status berubah menjadi Diizinkan.
3. **Admin** login untuk:
   - **Rekap harian**: filter tanggal / pencarian / status / kelas, statistik
     total–diizinkan–menunggu, export CSV, dan tombol *Tandai Diizinkan*
     manual (mis. siswa tidak membawa HP).
   - **Generate QR code statis** untuk masuk kelas di lobi: buat QR bernama
     (mis. "Lobi Utama"), cetak sekali dan tempel di lobi, aktif/nonaktif,
     regenerate kode (QR lama mati), dan hapus.

## Teknologi

- Laravel 13, PHP 8.3, Tailwind CSS 4 (Vite), MySQL
- PWA: `manifest.webmanifest` + service worker (bisa di-install di HP)
- QR: Simple Qrcode (server-side) + html5-qrcode (scan kamera di browser)

## Instalasi singkat (Laragon)

Lihat **LARAGON_SETUP.md**. Ringkasnya:

```bash
# salin .env.laragon menjadi .env, sesuaikan APP_URL, lalu:
php artisan key:generate
# buat database MySQL "izin_masuk_kelas" dulu, lalu:
php artisan migrate --seed
```

Login admin default (GANTI setelah instalasi):

| Email | Password |
|---|---|
| `admin@smkn5.sch.id` | `admin12345` |

Data awal dari seeder: 8 jurusan, 50 kelas, 1 QR statis "Lobi Utama".

## Rute utama

| Rute | Keterangan |
|---|---|
| `GET /` | Form siswa |
| `GET /izin/{kode}` | Halaman status siswa + tombol scan QR |
| `POST /izin/{kode}/scan` | Konfirmasi scan QR dari halaman siswa |
| `GET /lobi/{kode}` | Halaman tujuan QR cetak di lobi |
| `GET /admin` | Rekap harian (login) |
| `GET /admin/qr` | Kelola & generate QR statis lobi (login) |

## Tes

```bash
php artisan test
```

16 tes (46 assertion) mencakup form, scan QR valid/tidak dikenal/nonaktif,
konfirmasi lobi, login admin, rekap, generate QR, dan export CSV.
