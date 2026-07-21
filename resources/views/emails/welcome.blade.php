@component('mail::message')
# Selamat Datang, {{ $user->name }}! 👋

Terima kasih telah bergabung dengan **{{ $appName }}**.

Akun Anda telah berhasil dibuat. Anda sekarang dapat mengakses sistem presensi digital kami.

@component('mail::button', ['url' => $dashboardUrl])
Mulai Menggunakan
@endcomponent

## Informasi Akun Anda:
- **Email:** {{ $user->email }}
- **Role:** {{ ucfirst($user->role) }}
- **Bergabung:** {{ $user->created_at->format('d F Y') }}

## Langkah Selanjutnya:
1. Lengkapi profil Anda di menu **Profil**
2. Periksa **Jadwal Mengajar** Anda
3. Mulai gunakan fitur **Presensi Kelas** dengan scan QR Code

Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi tim support kami.

Terima kasih,<br>
**Tim {{ $appName }}**

@component('mail::subcopy')
Jika Anda tidak merasa mendaftar di {{ $appName }}, abaikan email ini.
@endcomponent
@endcomponent
