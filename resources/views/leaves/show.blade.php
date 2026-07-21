@extends('layouts.app')

@section('page-title', 'Detail Pengajuan')

@section('content')
    @php
        $isAdmin = Auth::user()->isAdmin();
        $statusColors = [
            'pending'  => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
            'approved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        ];
        $typeIcons = [
            'sakit' => 'pill',
            'izin'  => 'file-check',
        ];
    @endphp

    <div class="fade-in">

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ $isAdmin ? route('leaves.index') : route('teacher.leaves') }}"
                    class="group flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600">
                    <i data-lucide="arrow-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
                    <span>Kembali</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Detail Pengajuan</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        @if($isAdmin)
                            {{ $leave->user->name }}
                        @else
                            Pengajuan Anda
                        @endif
                    </p>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="hidden sm:flex items-center gap-2">
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                    <i data-lucide="{{ $typeIcons[$leave->type] ?? 'file' }}" class="w-3.5 h-3.5"></i>
                    {{ $leave->type }}
                </span>
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusColors[$leave->status] ?? '' }}">
                    {{ $leave->status === 'pending' ? 'Menunggu' : ($leave->status === 'approved' ? 'Disetujui' : 'Ditolak') }}
                </span>
            </div>
        </div>

        <!-- Row 1: Pengaju & Periode Izin -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            <!-- Pengaju Card (Admin View) -->
            @if($isAdmin)
                <div class="card p-6 animate-slide-up" style="animation-delay: 0.1s">
                    <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-400/30">
                            <i data-lucide="user" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-navy-800 dark:text-white">Pengaju</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Informasi pengaju</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all group">
                        <img src="{{ $leave->user->photo_url }}" alt="{{ $leave->user->name }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-slate-200 dark:border-slate-700 group-hover:scale-105 transition-transform">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-navy-800 dark:text-white truncate">{{ $leave->user->name }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400 truncate">{{ $leave->user->email }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $leave->user->phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Periode Izin Card -->
            <div class="card p-6 animate-slide-up" style="animation-delay: 0.15s">
                <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                        <i data-lucide="calendar-range" class="w-5 h-5 text-white dark:text-navy-900"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Periode Izin</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Tanggal mulai dan selesai</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all group hover:shadow-md">
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Tanggal Mulai</p>
                        <p class="text-base font-bold text-navy-800 dark:text-white group-hover:scale-105 transition-transform">{{ $leave->start_date->locale('id')->isoFormat('dddd') }}</p>
                        <p class="text-sm text-slate-600 dark:text-slate-300">{{ $leave->start_date->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all group hover:shadow-md">
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Tanggal Selesai</p>
                        <p class="text-base font-bold text-navy-800 dark:text-white group-hover:scale-105 transition-transform">{{ $leave->end_date->locale('id')->isoFormat('dddd') }}</p>
                        <p class="text-sm text-slate-600 dark:text-slate-300">{{ $leave->end_date->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl flex items-center justify-between">
                    <span class="text-xs text-slate-500 dark:text-slate-400">Durasi</span>
                    <span class="text-sm font-bold text-navy-800 dark:text-white">{{ $leave->duration }} hari</span>
                </div>
            </div>
        </div>

        <!-- Row 2: Alasan & Timeline -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            <!-- Alasan Pengajuan Card -->
            <div class="lg:col-span-2 card p-6 animate-slide-up" style="animation-delay: 0.2s">
                <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                        <i data-lucide="align-left" class="w-5 h-5 text-white dark:text-navy-900"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Alasan Pengajuan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Penjelasan detail</p>
                    </div>
                </div>

                <p class="text-sm text-slate-700 dark:text-slate-200 leading-relaxed whitespace-pre-wrap">{{ $leave->reason }}</p>
            </div>

            <!-- Timeline Card -->
            <div class="lg:col-span-1 card p-6 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-800/50 animate-slide-up" style="animation-delay: 0.25s">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-gold-400 to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-gold-400/30">
                        <i data-lucide="clock" class="w-5 h-5 text-navy-900"></i>
                    </div>
                    <h3 class="text-base font-bold text-navy-800 dark:text-white">Timeline</h3>
                </div>

                <div class="relative pl-4 border-l-2 border-slate-200 dark:border-slate-700 space-y-5">
                    <div class="relative group">
                        <div class="absolute -left-[21px] w-4 h-4 bg-gold-400 rounded-full border-2 border-white dark:border-slate-800 shadow-lg group-hover:scale-125 transition-transform"></div>
                        <p class="text-xs font-semibold text-navy-800 dark:text-white">Diajukan</p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ $leave->created_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}</p>
                    </div>
                    <div class="relative group">
                        <div class="absolute -left-[21px] w-4 h-4 {{ $leave->status !== 'pending' ? 'bg-green-500' : 'bg-slate-300' }} rounded-full border-2 border-white dark:border-slate-800 shadow-lg {{ $leave->status !== 'pending' ? 'group-hover:scale-125' : '' }} transition-transform"></div>
                        <p class="text-xs font-medium {{ $leave->status !== 'pending' ? 'text-green-600 dark:text-green-400' : 'text-slate-500 dark:text-slate-400' }}">
                            {{ $leave->status === 'approved' ? 'Disetujui' : ($leave->status === 'rejected' ? 'Ditolak' : 'Ditinjau') }}
                        </p>
                        <p class="text-[10px] text-slate-400">{{ $leave->approved_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Lampiran & Status Info -->
        @if($leave->attachment)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="card p-6 animate-slide-up" style="animation-delay: 0.3s">
                    <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                        <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                            <i data-lucide="paperclip" class="w-5 h-5 text-white dark:text-navy-900"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-navy-800 dark:text-white">Lampiran Dokumen</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">File pendukung</p>
                        </div>
                    </div>

                    <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" rel="noopener" 
                       class="flex items-center gap-4 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all group hover:shadow-md">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i data-lucide="file" class="w-6 h-6 text-blue-600 dark:text-blue-400 group-hover:rotate-6 transition-transform"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-navy-800 dark:text-white truncate">{{ basename($leave->attachment) }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Klik untuk membuka file</p>
                        </div>
                        <i data-lucide="external-link" class="w-4 h-4 text-slate-400 group-hover:text-navy-600 dark:group-hover:text-gold-400 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all"></i>
                    </a>
                </div>

                <!-- Status Summary Card -->
                <div class="card p-6 animate-slide-up" style="animation-delay: 0.35s">
                    <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                        <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                            <i data-lucide="clipboard-list" class="w-5 h-5 text-white dark:text-navy-900"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-navy-800 dark:text-white">Status Pengajuan</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Informasi terkini</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Jenis</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                                <i data-lucide="{{ $typeIcons[$leave->type] ?? 'file' }}" class="w-3 h-3"></i>
                                {{ $leave->type }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Status</span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $statusColors[$leave->status] ?? '' }}">
                                {{ $leave->status === 'pending' ? 'Menunggu' : ($leave->status === 'approved' ? 'Disetujui' : 'Ditolak') }}
                            </span>
                        </div>

                        @if($leave->approvedBy)
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                                <span class="text-xs text-slate-500 dark:text-slate-400">Diproses oleh</span>
                                <span class="text-sm font-semibold text-navy-800 dark:text-white">{{ $leave->approvedBy->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Admin Notes -->
        @if($leave->admin_notes)
            <div class="card p-6 mb-6 animate-slide-up" style="animation-delay: 0.4s">
                <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-400/30">
                        <i data-lucide="message-square" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Catatan Admin</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Feedback dari peninjau</p>
                    </div>
                </div>
                <p class="text-sm text-slate-700 dark:text-slate-200">{{ $leave->admin_notes }}</p>
            </div>
        @endif

        <!-- Tindakan Admin (Separate Card) -->
        @if($isAdmin && $leave->status === 'pending')
            <div class="card p-6 animate-slide-up" style="animation-delay: 0.45s">
                <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-400/30">
                        <i data-lucide="shield-check" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Tindakan Admin</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Setujui atau tolak pengajuan ini</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Approve Form -->
                    <div class="p-5 bg-green-50 dark:bg-green-900/10 rounded-xl border border-green-200 dark:border-green-800">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                            </div>
                            <h4 class="text-sm font-bold text-green-800 dark:text-green-300">Setujui Pengajuan</h4>
                        </div>

                        <form action="{{ route('leaves.approve', $leave) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-green-700 dark:text-green-400 mb-1.5">
                                    Catatan Persetujuan <span class="text-green-600/60 dark:text-green-400/60 font-normal">(Opsional)</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="message-circle" class="absolute left-3 top-3 w-4 h-4 text-green-400"></i>
                                    <textarea name="admin_notes" rows="2" maxlength="500"
                                              class="w-full pl-10 pr-3 py-2.5 bg-white dark:bg-slate-800 border border-green-200 dark:border-green-800 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all resize-none"
                                              placeholder="Catatan untuk pengaju..."></textarea>
                                </div>
                            </div>
                            <button type="submit" 
                                    class="w-full px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg text-sm font-semibold transition-all shadow-lg shadow-green-500/30 hover:shadow-xl hover:shadow-green-500/40 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                Setujui Pengajuan
                            </button>
                        </form>
                    </div>

                    <!-- Reject Form -->
                    <div class="p-5 bg-red-50 dark:bg-red-900/10 rounded-xl border border-red-200 dark:border-red-800">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                <i data-lucide="x-circle" class="w-4 h-4 text-red-600 dark:text-red-400"></i>
                            </div>
                            <h4 class="text-sm font-bold text-red-800 dark:text-red-300">Tolak Pengajuan</h4>
                        </div>

                        <form action="{{ route('leaves.reject', $leave) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-red-700 dark:text-red-400 mb-1.5">
                                    Alasan Penolakan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="message-square-x" class="absolute left-3 top-3 w-4 h-4 text-red-400"></i>
                                    <textarea name="admin_notes" rows="3" required maxlength="500"
                                              class="w-full pl-10 pr-3 py-2.5 bg-white dark:bg-slate-800 border border-red-200 dark:border-red-800 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all resize-none"
                                              placeholder="Jelaskan alasan penolakan..."></textarea>
                                </div>
                            </div>
                            <button type="submit" 
                                    class="w-full px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg text-sm font-semibold transition-all shadow-lg shadow-red-500/30 hover:shadow-xl hover:shadow-red-500/40 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                Tolak Pengajuan
                            </button>
                        </form>
                    </div>
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

        .animate-slide-up {
            animation: slideUp 0.5s ease-out forwards;
            opacity: 0;
        }

        @keyframes slideUp {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        [x-cloak] { display: none !important; }
    </style>
@endsection
