<p align="center">
  <img src="public/images/logo-readme.png" width="200" alt="Absensi SMK Logo">
</p>

<h1 align="center">Absensi SMK - Sistem Presensi Guru Modern</h1>

<p align="center">
  <em>Solusi cerdas manajemen kehadiran guru dengan teknologi QR Code dan Geo-Location.</em>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-00758F?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
</p>

---

## 🌟 Tentang Proyek

**Absensi SMK** adalah platform manajemen kehadiran yang dirancang khusus untuk lingkungan sekolah menengah kejuruan. Sistem ini memudahkan guru untuk melakukan presensi secara cepat menggunakan QR Code, serta memberikan kontrol penuh kepada admin untuk memantau kedisiplinan secara real-time.

Dikembangkan dengan fokus pada **pengalaman pengguna (UX)** yang premium dan **keamanan data**, platform ini mengintegrasikan validasi lokasi (GPS) untuk memastikan presensi dilakukan di area sekolah.

## 🚀 Fitur Utama

- 📱 **QR Code Attendance**: Presensi instan hanya dengan memindai kode QR unik yang di-generate sistem.
- 📍 **Geo-Location Validation**: Memastikan guru melakukan presensi di lokasi yang telah ditentukan.
- 👨‍🏫 **Manajemen Guru & Mapel**: Pengelolaan data guru, status aktif, dan penugasan mata pelajaran yang komprehensif.
- 📊 **Statistik & Laporan**: Dashboard interaktif yang menyajikan data kehadiran harian, keterlambatan, dan izin.
- ✉️ **System Notifications**: Notifikasi otomatis kepada admin setiap kali ada aktivitas presensi atau perubahan data.
- 📄 **Izin & Cuti**: Sistem pengajuan izin dan cuti yang terintegrasi dengan persetujuan admin.
- 🔐 **Secure Authentication**: Sistem login yang aman didukung oleh Laravel Breeze dan Socialite.

## 🛠️ Tech Stack

- **Backend**: Laravel 12 (Modern PHP Framework)
- **Frontend**: Blade Templating + Tailwind CSS
- **Database**: MySQL / PostgreSQL
- **Security**: Laravel Sanctum / Breeze
- **Packages**: Simple-QRCode, Laravel Socialite, Carbon

## 💻 Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek di lokal Anda:

1. **Clone Repositori**
   ```bash
   git clone https://github.com/vexalyn-dev/Absensi-SMK.git
   cd Absensi-SMK
   ```

2. **Instal Dependensi**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database Anda.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi & Seeding**
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   # Di terminal terpisah
   npm run dev
   ```

## 📸 Screenshots

| Dashboard Admin | QR Scanner | Profil Guru |
| :---: | :---: | :---: |
| _(Coming Soon)_ | _(Coming Soon)_ | _(Coming Soon)_ |

---

## 👨‍💻 Developer

Proyek ini dikembangkan dan dikelola dengan bangga oleh:

**Vexalyn Dev**
- 🌐 Website: [vexalyn.com](https://vexalyn.com)
- 📧 Email: contact@vexalyn.com
- 🐙 GitHub: [@vexalyn-dev](https://github.com/vexalyn-dev)

## 📄 Lisensi & Hak Cipta

Copyright © 2026 **Vexalyn Dev**. Seluruh Hak Cipta Dilindungi Undang-Undang.

Aplikasi ini dilisensikan di bawah **Lisensi MIT**. Anda bebas menggunakan, memodifikasi, dan mendistribusikan aplikasi ini sesuai dengan ketentuan lisensi yang berlaku.

---

<p align="center">
  Dibuat dengan ❤️ oleh <b>Vexalyn Dev</b>
</p>
