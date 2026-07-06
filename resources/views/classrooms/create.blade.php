@extends('layouts.app')

@section('page-title', 'Tambah Kelas')

@section('content')
    <div class="fade-in max-w-7xl mx-auto">

        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('classrooms.index') }}"
                class="p-2.5 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-xl transition-all hover:-translate-x-1 group">
                <i data-lucide="arrow-left"
                    class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-navy-800 dark:group-hover:text-gold-400 transition-colors"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Tambah Kelas Baru</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Isi data kelas untuk generate QR Code</p>
            </div>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div
                class="card p-4 mb-6 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800">
                <div class="flex items-start gap-3">
                    <div
                        class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0 text-red-600 dark:text-red-400">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-red-800 dark:text-red-300">Terjadi Kesalahan Validasi</h4>
                        <ul class="list-disc list-inside text-xs text-red-700 dark:text-red-400 mt-1 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('classrooms.store') }}" method="POST" class="card p-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-5">
                    <!-- Nama Kelas -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Nama Kelas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" required placeholder="Contoh: XI FARMASI, XII RPL 1"
                            value="{{ old('name') }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Nama lengkap kelas yang akan
                            ditampilkan</p>
                    </div>

                    <!-- Kode Kelas -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Kode Kelas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" required placeholder="Contoh: XI-FAR-01, XII-RPL-1"
                            value="{{ old('code') }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent uppercase @error('code') border-red-500 @enderror">
                        @error('code')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Kode unik untuk kelas (tanpa spasi,
                            gunakan tanda hubung)</p>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-5">
                    <!-- Status Card -->
                    <div
                        class="p-5 bg-gradient-to-br from-slate-50 to-white dark:from-slate-700/30 dark:to-slate-700/10 rounded-xl border-2 border-slate-200 dark:border-slate-700">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="w-10 h-10 rounded-lg bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center">
                                <i data-lucide="toggle-left" class="w-5 h-5 text-white dark:text-navy-900"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-navy-800 dark:text-white">Status Kelas</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Aktifkan atau nonaktifkan</p>
                            </div>
                        </div>

                        <label
                            class="flex items-start gap-3 p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                class="w-5 h-5 mt-0.5 rounded border-slate-300 text-navy-600 focus:ring-navy-500">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Kelas Aktif</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelas yang tidak aktif tidak
                                    akan muncul dalam jadwal</p>
                            </div>
                        </label>
                    </div>

                    <!-- Info Card -->
                    <div
                        class="p-5 bg-blue-50 dark:bg-blue-900/20 rounded-xl border-2 border-blue-200 dark:border-blue-800">
                        <div class="flex items-start gap-3">
                            <i data-lucide="info" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5"></i>
                            <div>
                                <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-1">Informasi</h4>
                                <ul class="text-xs text-blue-700 dark:text-blue-400 space-y-1.5">
                                    <li>• Pastikan kode kelas unik</li>
                                    <li>• Nama kelas harus jelas</li>
                                    <li>• QR Code akan digenerate otomatis setelah disimpan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('classrooms.index') }}"
                    class="flex-1 px-6 py-3.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all hover:-translate-y-0.5 text-center flex items-center justify-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    <span>Batal</span>
                </a>
                <button type="submit"
                    class="flex-1 px-6 py-3.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan & Generate QR</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>

    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection