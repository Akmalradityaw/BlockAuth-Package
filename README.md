# BlockAuth Package

Modul autentikasi, manajemen profil, direktori pengguna, dan sistem **RBAC granular dinamis** berbasis matriks untuk ekosistem **Laravel 11 / 12** dan **Inertia.js + React**.

---

## Fitur Utama

- **Autentikasi Lengkap:** Registrasi akun, Login berbasis session, Remember Me, Lupa & Reset Password via email.
- **Login Sosial:** OAuth Google dan GitHub melalui Laravel Socialite, dengan kemampuan di-toggle ON/OFF secara dinamis dari menu Pengaturan SuperAdmin.
- **Manajemen Profil:** Perubahan biodata, update password, dan upload avatar dengan auto-crop 300x300 px (Intervention Image).
- **RBAC Granular Dinamis (Spatie):** 5 role hierarkis (`SuperAdmin`, `Manager`, `Editor`, `Staff`, `Viewer`) dengan izin berbasis aksi (`users:create/read/update/delete`, `roles:manage`, `settings:manage`). Matriks izin dapat diubah kapan saja langsung dari antarmuka web.
- **Dashboard & Direktori Pengguna:** Visualisasi grafik pendaftaran 14 hari terakhir (Chart.js), direktori pengguna responsif dengan paginasi.
- **Design System Solid v2:** Tampilan modern profesional tanpa CSS gradient berbasis Tailwind CSS dan daisyUI.

---

## Persyaratan Sistem

- PHP `^8.2`
- Laravel `^11.0` atau `^12.0`
- Node.js `^18.0` atau `^20.0`
- Database MySQL / SQLite / PostgreSQL

---

## Panduan Instalasi

### 1. Pasang Package via Composer

Tambahkan package ke proyek Laravel Anda:

```bash
composer require blockauth/blockauth --dev
```

*(Atau via local path repository jika dalam tahap pengembangan lokal).*

### 2. Jalankan Installer BlockAuth

Jalankan perintah artisan untuk menyalin semua Action, DTO, Controller, Migrasi, Komponen React, dan Konfigurasi:

```bash
php artisan blockauth:install
```

Jika ingin menimpa file yang sudah ada:

```bash
php artisan blockauth:install --force
```

### 3. Pasang Dependensi Frontend & Migrasi Database

```bash
# 1. Pasang dependensi JavaScript baru
npm install

# 2. Hubungkan disk storage untuk avatar
php artisan storage:link

# 3. Jalankan migrasi dan seeder awal
php artisan migrate --seed

# 4. Jalankan Vite dev server
npm run dev
```

---

## Kredensial Bawaan Seeder

Setelah menjalankan `php artisan migrate --seed`, Anda dapat langsung login menggunakan:

- **Email:** `admin@blockauth.test`
- **Password:** `password`
- **Role:** `SuperAdmin`

---

## Lisensi

Lisensi di bawah [MIT License](LICENSE.md).

