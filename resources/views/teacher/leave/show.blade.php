@extends('layouts.teacher')

@section('page-title', 'Detail Pengajuan')

@section('content')
<div class="fade-in space-y-6 px-4 sm:px-6 lg:px-8 py-6">

    <!-- Header -->
    <div class="flex items-center justify-between mb-4 sm:mb-6">
        <div class="flex items-center gap-3 sm:gap-4">
            <a href="{{ route('teacher.leave') }}" 
               class="p-2 sm:p-2.5 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-xl transition-all hover:-translate-x-1 group">
                <i data-lucide="arrow-left" class="w-4 h-4 sm:w-5 sm:h-5 text-slate-600 dark:text-slate-400 group-hover:text-navy-800 dark:group-hover:text-gold-400 transition-colors"></i>
            </a>
            <div>
                <h1 class="text-lg sm:text-2xl font-bold text-navy-800 dark:text-white">Detail Pengajuan</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Status terkini pengajuan Anda</p>
            </div>
        </div>
    </div>

    <!-- Status Card -->
    <div class="card p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center
                    {{ $leaveRequest->status === 'approved' ? 'bg-green-100 dark:bg-green-900/30' : '' }}
                    {{ $leaveRequest->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/30' : '' }}
                    {{ $leaveRequest->status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/30' : '' }}">
                    @if($leaveRequest->status === 'approved')
                        <i data-lucide="check-circle" class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-400"></i>
                    @elseif($leaveRequest->status === 'rejected')
                        <i data-lucide="x-circle" class="w-5 h-5 sm:w-6 sm:h-6 text-red-600 dark:text-red-400"></i>
                    @else
                        <i data-lucide="clock" class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600 dark:text-yellow-400"></i>
                    @endif
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-navy-800 dark:text-white capitalize">
                        {{ $leaveRequest->status === 'approved' ? 'Disetujui' : '' }}
                        {{ $leaveRequest->status === 'rejected' ? 'Ditolak' : '' }}
                        {{ $leaveRequest->status === 'pending' ? 'Menunggu Persetujuan' : '' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                        @if($leaveRequest->status === 'approved')
                            Pengajuan Anda telah disetujui oleh admin
                        @elseif($leaveRequest->status === 'rejected')
                            Pengajuan Anda ditolak oleh admin
                        @else
                            Pengajuan sedang ditinjau oleh admin
                        @endif
                    </p>
                </div>
            </div>
            <span class="px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-xs sm:text-sm font-bold self-start sm:self-auto
                {{ $leaveRequest->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                {{ $leaveRequest->status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                {{ $leaveRequest->status === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}">
                {{ ucfirst($leaveRequest->status) }}
            </span>
        </div>
    </div>

    <!-- Main Grid: Proses Pengajuan + Detail Pengajuan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 items-stretch">
        
        <!-- Card Proses Pengajuan -->
        <div class="card p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-gold-400 to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-gold-400/30">
                    <i data-lucide="activity" class="w-5 h-5 text-navy-900"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-navy-800 dark:text-white">Proses Pengajuan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Status terkini pengajuan Anda</p>
                </div>
            </div>

            <div class="relative pl-4 border-l-2 border-slate-200 dark:border-slate-700 space-y-5 flex-1">
                <div class="relative">
                    <div class="absolute -left-[21px] w-4 h-4 bg-gold-400 rounded-full border-2 border-white dark:border-slate-800 shadow-lg"></div>
                    <div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <i data-lucide="send" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                                </div>
                                <p class="text-sm font-bold text-navy-800 dark:text-white">Diajukan</p>
                            </div>
                            <span class="text-xs font-mono text-slate-500 dark:text-slate-400">
                                {{ $leaveRequest->created_at->format('H:i') }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Form pengajuan dikirim</p>
                        <p class="text-xs text-slate-400">
                            {{ $leaveRequest->created_at->locale('id')->isoFormat('D MMMM YYYY') }}
                        </p>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -left-[21px] w-4 h-4 rounded-full border-2 border-white dark:border-slate-800 shadow-lg transition-all duration-500
                        {{ $leaveRequest->status !== 'pending' ? 'bg-blue-500' : 'bg-slate-300 dark:bg-slate-600' }}">
                    </div>
                    <div class="p-4 rounded-xl border transition-all duration-500
                        {{ $leaveRequest->status !== 'pending' 
                            ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' 
                            : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700' }}">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg {{ $leaveRequest->status !== 'pending' ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-slate-200 dark:bg-slate-700' }} flex items-center justify-center">
                                <i data-lucide="eye" class="w-4 h-4 {{ $leaveRequest->status !== 'pending' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400' }}"></i>
                            </div>
                            <p class="text-sm font-bold {{ $leaveRequest->status !== 'pending' ? 'text-blue-800 dark:text-blue-300' : 'text-slate-500 dark:text-slate-400' }}">
                                Ditinjau
                            </p>
                        </div>
                        <p class="text-xs {{ $leaveRequest->status !== 'pending' ? 'text-blue-700 dark:text-blue-300' : 'text-slate-400' }}">
                            {{ $leaveRequest->status !== 'pending' 
                                ? 'Admin telah memverifikasi pengajuan Anda' 
                                : 'Admin sedang memverifikasi' }}
                        </p>
                        @if($leaveRequest->status !== 'pending' && $leaveRequest->approved_at)
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1 font-mono">
                            {{ $leaveRequest->approved_at->format('H:i') }} • {{ $leaveRequest->approved_at->locale('id')->diffForHumans() }}
                        </p>
                        @endif
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -left-[21px] w-4 h-4 rounded-full border-2 border-white dark:border-slate-800 shadow-lg transition-all duration-500
                        {{ $leaveRequest->status === 'approved' ? 'bg-green-500' : '' }}
                        {{ $leaveRequest->status === 'rejected' ? 'bg-red-500' : '' }}
                        {{ $leaveRequest->status === 'pending' ? 'bg-slate-300 dark:bg-slate-600' : '' }}">
                    </div>
                    <div class="p-4 rounded-xl border transition-all duration-500
                        {{ $leaveRequest->status === 'approved' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : '' }}
                        {{ $leaveRequest->status === 'rejected' ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : '' }}
                        {{ $leaveRequest->status === 'pending' ? 'bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700' : '' }}">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg 
                                {{ $leaveRequest->status === 'approved' ? 'bg-green-100 dark:bg-green-900/30' : '' }}
                                {{ $leaveRequest->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/30' : '' }}
                                {{ $leaveRequest->status === 'pending' ? 'bg-slate-200 dark:bg-slate-700' : '' }} 
                                flex items-center justify-center">
                                @if($leaveRequest->status === 'approved')
                                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                                @elseif($leaveRequest->status === 'rejected')
                                    <i data-lucide="x-circle" class="w-4 h-4 text-red-600 dark:text-red-400"></i>
                                @else
                                    <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                @endif
                            </div>
                            <p class="text-sm font-bold 
                                {{ $leaveRequest->status === 'approved' ? 'text-green-800 dark:text-green-300' : '' }}
                                {{ $leaveRequest->status === 'rejected' ? 'text-red-800 dark:text-red-300' : '' }}
                                {{ $leaveRequest->status === 'pending' ? 'text-slate-500 dark:text-slate-400' : '' }}">
                                {{ $leaveRequest->status === 'approved' ? 'Disetujui' : '' }}
                                {{ $leaveRequest->status === 'rejected' ? 'Ditolak' : '' }}
                                {{ $leaveRequest->status === 'pending' ? 'Menunggu Keputusan' : '' }}
                            </p>
                        </div>
                        
                        @if($leaveRequest->status === 'approved')
                            <p class="text-xs text-green-700 dark:text-green-300">Pengajuan Anda telah disetujui</p>
                            @if($leaveRequest->approved_at)
                            <p class="text-xs text-green-600 dark:text-green-400 mt-1 font-mono">
                                {{ $leaveRequest->approved_at->format('H:i') }} • Notifikasi dikirim
                            </p>
                            @endif
                        @elseif($leaveRequest->status === 'rejected')
                            <p class="text-xs text-red-700 dark:text-red-300">Pengajuan Anda ditolak</p>
                            @if($leaveRequest->admin_notes)
                            <div class="mt-2 p-3 bg-red-100 dark:bg-red-900/30 rounded-lg border border-red-200 dark:border-red-800">
                                <p class="text-xs text-red-700 dark:text-red-300">
                                    <strong>Alasan:</strong> {{ $leaveRequest->admin_notes }}
                                </p>
                            </div>
                            @endif
                            @if($leaveRequest->approved_at)
                            <p class="text-xs text-red-600 dark:text-red-400 mt-2 font-mono">
                                {{ $leaveRequest->approved_at->format('H:i') }} • Notifikasi dikirim
                            </p>
                            @endif
                        @else
                            <p class="text-xs text-slate-400">Notifikasi akan dikirim setelah keputusan</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Detail Pengajuan -->
        <div class="card p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="file-text" class="w-5 h-5 text-white dark:text-navy-900"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-navy-800 dark:text-white">Detail Pengajuan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Informasi pengajuan</p>
                </div>
            </div>

            <div class="space-y-4 flex-1">
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-slate-50 dark:bg-slate-700/30 rounded-xl">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Jenis Pengajuan</p>
                        <p class="text-sm font-bold text-navy-800 dark:text-white capitalize">
                            {{ $leaveRequest->type === 'izin' ? 'Izin' : 'Sakit' }}
                        </p>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-700/30 rounded-xl">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Durasi</p>
                        <p class="text-sm font-bold text-navy-800 dark:text-white">{{ $leaveRequest->duration }} hari</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="p-3 bg-slate-50 dark:bg-slate-700/30 rounded-xl">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Tanggal Mulai</p>
                        <p class="text-sm font-bold text-navy-800 dark:text-white">
                            {{ $leaveRequest->start_date->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </p>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-700/30 rounded-xl">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Tanggal Selesai</p>
                        <p class="text-sm font-bold text-navy-800 dark:text-white">
                            {{ $leaveRequest->end_date->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </p>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Alasan Pengajuan</p>
                    <div class="p-4 bg-slate-50 dark:bg-slate-700/30 rounded-xl">
                        <p class="text-sm text-navy-800 dark:text-white leading-relaxed">{{ $leaveRequest->reason }}</p>
                    </div>
                </div>

                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 mt-auto">
                    <div class="flex items-start gap-3">
                        <i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0"></i>
                        <div class="text-xs text-blue-700 dark:text-blue-300">
                            <p class="font-semibold mb-1">Informasi:</p>
                            <p>Diajukan pada {{ $leaveRequest->created_at->locale('id')->isoFormat('D MMMM Y [pukul] H.i') }}</p>
                            @if($leaveRequest->status !== 'pending' && $leaveRequest->approved_at)
                            <p>Diproses pada {{ $leaveRequest->approved_at->locale('id')->isoFormat('D MMMM Y [pukul] H.i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($leaveRequest->status === 'pending')
            <form action="{{ route('teacher.leave.destroy', $leaveRequest) }}" method="POST" 
                  onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini?')" class="mt-4">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="w-full px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Batalkan Pengajuan
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Card Lampiran Dokumen (TERPISAH) -->
@if($leaveRequest->attachment)
@php
    $attachmentPath = $leaveRequest->attachment;
    $attachmentName = basename($attachmentPath);
    $extension = strtolower(pathinfo($attachmentName, PATHINFO_EXTENSION));
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    $isPdf = $extension === 'pdf';
    $isWord = in_array($extension, ['doc', 'docx']);
    $isExcel = in_array($extension, ['xls', 'xlsx']);
    $isPowerpoint = in_array($extension, ['ppt', 'pptx']);
    $attachmentExists = \Storage::disk('public')->exists($attachmentPath);
    
    $fileSizeFormatted = '-';
    if ($attachmentExists) {
        $fileSize = \Storage::disk('public')->size($attachmentPath);
        $fileSizeFormatted = $fileSize >= 1048576 
            ? round($fileSize / 1048576, 2) . ' MB' 
            : round($fileSize / 1024, 2) . ' KB';
    }
@endphp
<div class="card p-4 sm:p-6" x-data="{ showPreview: false }">
    <!-- Header dengan posisi di kanan -->
    <div class="flex items-center justify-between mb-4 sm:mb-6 pb-4 sm:pb-5 border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                <i data-lucide="paperclip" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
            </div>
            <div>
                <h3 class="text-sm sm:text-base font-bold text-navy-800 dark:text-white">Lampiran Dokumen</h3>
                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400">Dokumen pendukung pengajuan</p>
            </div>
        </div>
        
        <!-- Tombol Tutul di pojok kanan (hanya muncul saat preview aktif) -->
        <button x-show="showPreview" 
                @click="showPreview = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90"
                class="px-3 py-1.5 sm:px-4 sm:py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg sm:rounded-xl text-xs font-bold transition-all shadow-lg hover:shadow-xl flex items-center gap-1.5"
                x-cloak>
            <i data-lucide="x" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
            <span class="hidden sm:inline">Tutup</span>
        </button>
    </div>

    <div class="space-y-4">
        <!-- File Info Card (Muncul saat preview ditutup) -->
        <div x-show="!showPreview"
             x-transition:enter="transition ease-out duration-400"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="p-3 sm:p-4 bg-slate-50 dark:bg-slate-700/30 rounded-xl border-2 border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3 sm:gap-4">
                    @if($isImage)
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="image" class="w-6 h-6 sm:w-7 sm:h-7 text-green-600 dark:text-green-400"></i>
                        </div>
                    @elseif($isPdf)
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="file-text" class="w-6 h-6 sm:w-7 sm:h-7 text-red-600 dark:text-red-400"></i>
                        </div>
                    @elseif($isWord)
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="file-text" class="w-6 h-6 sm:w-7 sm:h-7 text-blue-600 dark:text-blue-400"></i>
                        </div>
                    @elseif($isExcel)
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="file-spreadsheet" class="w-6 h-6 sm:w-7 sm:h-7 text-emerald-600 dark:text-emerald-400"></i>
                        </div>
                    @elseif($isPowerpoint)
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="presentation" class="w-6 h-6 sm:w-7 sm:h-7 text-orange-600 dark:text-orange-400"></i>
                        </div>
                    @else
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-slate-200 dark:bg-slate-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="file" class="w-6 h-6 sm:w-7 sm:h-7 text-slate-600 dark:text-slate-400"></i>
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-bold text-navy-800 dark:text-white break-all leading-snug">{{ $attachmentName }}</p>
                        <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 uppercase mt-0.5">
                            {{ $extension }} • {{ $fileSizeFormatted }}
                        </p>
                        <!-- Tombol Lihat: muncul di bawah nama file di mobile -->
                        <button @click="showPreview = true" 
                                class="mt-2 sm:hidden w-full px-3 py-2 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            Lihat Lampiran
                        </button>
                    </div>

                    <!-- Tombol Lihat Lampiran (hanya di sm+) -->
                    <button @click="showPreview = true" 
                            class="hidden sm:flex px-4 py-2.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-xs font-bold hover:shadow-lg transition-all items-center gap-1.5 flex-shrink-0">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                        Lihat Lampiran
                    </button>
                </div>
            </div>
        </div>

        <!-- Preview Container -->
        @if(!$attachmentExists)
        <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800">
            <div class="flex items-start gap-3">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 dark:text-red-400 mt-0.5 flex-shrink-0"></i>
                <p class="text-xs sm:text-sm text-red-700 dark:text-red-300">File lampiran tidak ditemukan di storage.</p>
            </div>
        </div>
        @elseif($isImage)
        <div x-show="showPreview"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 scale-90 -translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90 -translate-y-2"
             x-cloak>
            <div class="rounded-2xl overflow-hidden border-2 border-slate-200 dark:border-slate-700 shadow-2xl bg-slate-100 dark:bg-slate-900">
                <img src="{{ asset('storage/' . $attachmentPath) }}" 
                     alt="Lampiran Preview" 
                     class="w-full max-h-[400px] sm:max-h-[500px] object-contain">
            </div>
        </div>
        @else
        <div x-show="showPreview"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 scale-90 -translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90 -translate-y-2"
             x-cloak>
            @if($isPdf)
                <div class="rounded-2xl overflow-hidden border-2 border-slate-200 dark:border-slate-700 shadow-2xl">
                    <iframe src="{{ asset('storage/' . $attachmentPath) }}" 
                            class="w-full h-[400px] sm:h-[500px]" 
                            frameborder="0"></iframe>
                </div>
            @else
                <div class="p-6 sm:p-8 bg-slate-50 dark:bg-slate-700/30 rounded-2xl border-2 border-slate-200 dark:border-slate-700 text-center">
                    <i data-lucide="file" class="w-12 h-12 sm:w-16 sm:h-16 text-slate-400 mx-auto mb-3"></i>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mb-4">Preview tidak tersedia untuk file ini</p>
                    <a href="{{ asset('storage/' . $attachmentPath) }}" target="_blank" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-lg text-xs font-bold">
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        Buka di Tab Baru
                    </a>
                </div>
            @endif
        </div>
        @endif

        @if($attachmentExists)
        <!-- Tombol Download -->
        <a href="{{ asset('storage/' . $attachmentPath) }}" 
           download="{{ $attachmentName }}"
           class="w-full px-4 py-3 sm:py-3.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2">
            <i data-lucide="download" class="w-4 h-4"></i>
            Download Lampiran
        </a>
        @endif
    </div>
</div>
@endif
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
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    [x-cloak] { display: none !important; }
</style>
@endsection
