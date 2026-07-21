@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    
    <!-- Welcome Card with Enhanced Animation -->
    <div class="card-hover p-6 bg-gradient-to-r from-navy-800 via-navy-900 to-slate-900 dark:from-slate-800 dark:via-slate-900 dark:to-navy-950 rounded-2xl text-white relative overflow-hidden group animate-fade-in-up">
        <!-- Animated Background Elements -->
        <div class="absolute top-0 right-0 w-80 h-80 bg-gold-400/10 rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl group-hover:bg-gold-400/15 transition-all duration-700"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-gold-400/5 rounded-full translate-y-1/3 -translate-x-1/4 blur-2xl group-hover:bg-gold-400/10 transition-all duration-700"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-navy-700/10 rounded-full blur-3xl"></div>
        
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <img src="{{ Auth::user()->photo_url }}" 
                         class="w-16 h-16 rounded-full object-cover border-3 border-gold-400/50 shadow-lg shadow-gold-400/20 hover:scale-105 transition-transform duration-300">
                    
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-transparent">Selamat Datang, {{ Auth::user()->name }}! <span class="bg-clip-text-none text-white">👋</span></h2>
                            <span class="w-2.5 h-2.5 bg-green-500 rounded-full pulse-dot flex-shrink-0" style="display:inline-block!important; animation:pulse-green 2s infinite!important;"></span>
                        </div>
                        <p class="text-slate-300 text-sm">Kelola presensi guru dengan mudah dan efisien</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-xs text-slate-400">Hari Ini</p>
                        <p class="text-lg font-bold realtime-date">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                    </div>
                    <div class="w-14 h-14 bg-gold-400/20 backdrop-blur-sm rounded-xl flex items-center justify-center border border-gold-400/30 shadow-lg shadow-gold-400/10">
                        <i data-lucide="calendar" class="w-7 h-7 text-gold-400"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Guru -->
        <a href="{{ route('teachers.index') }}" class="card-hover card p-5 group animate-stagger-1 block cursor-pointer">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Guru</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1">{{ $totalGuru }}</h3>
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                        <span class="text-[10px] text-green-600 dark:text-green-400 font-medium">Aktif</span>
                    </div>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-blue-500 group-hover:translate-x-0.5 transition-all shrink-0"></i>
            </div>
        </a>

        <!-- Hadir Hari Ini -->
        <a href="{{ route('attendance.history', ['start_date' => now()->toDateString(), 'end_date' => now()->toDateString(), 'status' => 'Hadir']) }}" class="card-hover card p-5 group animate-stagger-2 block cursor-pointer">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <i data-lucide="circle-check" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Hadir Hari Ini</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1">{{ $hadirHariIni }}</h3>
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                        <span class="text-[10px] text-green-600 dark:text-green-400 font-medium">{{ now()->locale('id')->format('d M') }}</span>
                    </div>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-green-500 group-hover:translate-x-0.5 transition-all shrink-0"></i>
            </div>
        </a>

        <!-- Terlambat -->
        <a href="{{ route('attendance.history', ['start_date' => now()->toDateString(), 'end_date' => now()->toDateString(), 'status' => 'Terlambat']) }}" class="card-hover card p-5 group animate-stagger-3 block cursor-pointer">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/30 dark:to-yellow-800/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Terlambat</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1">{{ $terlambat }}</h3>
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse"></span>
                        <span class="text-[10px] text-yellow-600 dark:text-yellow-400 font-medium">Perlu perhatian</span>
                    </div>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-yellow-500 group-hover:translate-x-0.5 transition-all shrink-0"></i>
            </div>
        </a>

        <!-- Tidak Hadir -->
        <a href="{{ route('attendance.history', ['start_date' => now()->toDateString(), 'end_date' => now()->toDateString(), 'status' => 'Alpha']) }}" class="card-hover card p-5 group animate-stagger-4 block cursor-pointer">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <i data-lucide="circle-x" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Tidak Hadir</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1">{{ $tidakHadir }}</h3>
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                        <span class="text-[10px] text-red-600 dark:text-red-400 font-medium">Alpha</span>
                    </div>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-red-500 group-hover:translate-x-0.5 transition-all shrink-0"></i>
            </div>
        </a>

        <!-- Izin/Cuti -->
        <a href="{{ route('attendance.history', ['start_date' => now()->toDateString(), 'end_date' => now()->toDateString(), 'status' => 'Izin']) }}" class="card-hover card p-5 group animate-stagger-5 block cursor-pointer">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <i data-lucide="file-text" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Izin/Cuti</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1">{{ $izinCuti }}</h3>
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse" style="display:inline-block;min-width:6px;min-height:6px;"></span>
                        <span class="text-[10px] text-blue-600 dark:text-blue-400 font-medium">Izin/Sakit/Cuti</span>
                    </div>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-blue-500 group-hover:translate-x-0.5 transition-all shrink-0"></i>
            </div>
        </a>
    </div>

    <!-- Chart & Attendance Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Chart Section -->
        <div class="lg:col-span-2 animate-fade-in-left">
            <div class="chart-premium-card rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-700/80 bg-white dark:bg-slate-800/90 shadow-[0_1px_0_rgba(15,23,42,0.06),0_12px_40px_-12px_rgba(15,23,42,0.12)] dark:shadow-[0_1px_0_rgba(255,255,255,0.05),0_20px_50px_-20px_rgba(0,0,0,0.45)]">
                <div class="relative px-6 pt-6 pb-4">
                    <div class="absolute inset-0 bg-gradient-to-br from-gold-400/[0.07] via-transparent to-navy-900/[0.06] dark:from-gold-400/[0.06] dark:to-slate-900/40 pointer-events-none"></div>
                    <div class="relative flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-semibold uppercase tracking-wider bg-slate-100 dark:bg-slate-700/80 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-600/60">
                                    <i data-lucide="activity" class="w-3.5 h-3.5 text-gold-500"></i>
                                    Ringkasan
                                </span>
                            </div>
                            <h3 class="text-2xl font-bold text-navy-900 dark:text-white tracking-tight leading-tight">Grafik Kehadiran</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2" id="chart-period">7 hari terakhir</p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <div class="hidden sm:flex items-center gap-4 pr-1">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 shadow-sm shadow-green-500/40"></span>
                                    <span class="text-[11px] font-medium text-slate-600 dark:text-slate-300">Hadir</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-yellow-500 to-amber-600 shadow-sm shadow-yellow-500/40"></span>
                                    <span class="text-[11px] font-medium text-slate-600 dark:text-slate-300">Terlambat</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-red-500 to-rose-600 shadow-sm shadow-red-500/40"></span>
                                    <span class="text-[11px] font-medium text-slate-600 dark:text-slate-300">Tidak Hadir</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 shadow-sm shadow-blue-500/40"></span>
                                    <span class="text-[11px] font-medium text-slate-600 dark:text-slate-300">Izin/Cuti</span>
                                </div>
                            </div>
                            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                <button type="button" @click="open = !open; animateClick(this)"
                                        :class="open ? 'bg-slate-200 dark:bg-slate-600 text-slate-800 dark:text-white' : 'bg-slate-100/90 dark:bg-slate-700/80 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600'"
                                        class="p-2.5 rounded-xl transition-all duration-300 shadow-sm border border-slate-200/60 dark:border-slate-600/50 icon-click"
                                        aria-label="Opsi periode grafik">
                                    <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                                </button>
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                     x-cloak
                                     class="absolute right-0 mt-2 w-52 bg-white dark:bg-navy-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden z-20">
                                    <p class="px-4 py-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Periode</p>
                                    <div class="py-1 border-t border-slate-100 dark:border-slate-700/80">
                                        <button type="button" @click="updateChartPeriod(3); open = false" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors text-left">
                                            <i data-lucide="calendar" class="w-4 h-4 text-gold-500"></i>
                                            <span>3 hari</span>
                                        </button>
                                        <button type="button" @click="updateChartPeriod(7); open = false" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors text-left">
                                            <i data-lucide="calendar" class="w-4 h-4 text-gold-500"></i>
                                            <span>7 hari</span>
                                        </button>
                                        <button type="button" @click="updateChartPeriod(14); open = false" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors text-left">
                                            <i data-lucide="calendar" class="w-4 h-4 text-gold-500"></i>
                                            <span>14 hari</span>
                                        </button>
                                        <button type="button" @click="updateChartPeriod(30); open = false" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors text-left">
                                            <i data-lucide="calendar" class="w-4 h-4 text-gold-500"></i>
                                            <span>30 hari</span>
                                        </button>
                                    </div>
                                    <div class="border-t border-slate-200 dark:border-slate-700">
                                        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gold-600 dark:text-gold-400 hover:bg-gold-50/80 dark:hover:bg-gold-900/20 transition-colors font-medium">
                                            <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                                            <span>Laporan lengkap</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 pb-4 sm:px-6 sm:pb-6">
                    <div class="relative rounded-xl border border-slate-200/70 dark:border-slate-600/50 bg-gradient-to-b from-slate-50/90 to-white dark:from-slate-900/40 dark:to-slate-800/30 min-h-[320px] sm:min-h-[360px] overflow-hidden">
                        <!-- Modern Premium Loading Overlay -->
                        <div id="chart-loading-overlay" class="hidden absolute inset-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm z-50" style="display:none;">
                            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                                <div style="text-align:center;">
                                    <!-- Double Rotating Rings -->
                                    <div style="position:relative;width:80px;height:80px;margin:0 auto;">
                                        <div style="position:absolute;inset:0;border-radius:50%;border:4px solid #e2e8f0;border-top-color:#0f172a;animation:spin 1s linear infinite;"></div>
                                        <div style="position:absolute;top:8px;left:8px;right:8px;bottom:8px;border-radius:50%;border:4px solid transparent;border-bottom-color:#facc15;animation:spinReverse 0.8s linear infinite;"></div>
                                    </div>

                                    <!-- Bouncing Dots -->
                                    <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-top:20px;">
                                        <div style="width:8px;height:8px;background:#0f172a;border-radius:50%;animation:bounce 0.6s ease-in-out infinite;" class="dark-dot"></div>
                                        <div style="width:8px;height:8px;background:#0f172a;border-radius:50%;animation:bounce 0.6s ease-in-out 0.15s infinite;" class="dark-dot"></div>
                                        <div style="width:8px;height:8px;background:#0f172a;border-radius:50%;animation:bounce 0.6s ease-in-out 0.3s infinite;" class="dark-dot"></div>
                                    </div>

                                    <!-- Text -->
                                    <div style="margin-top:20px;">
                                        <p style="font-size:15px;font-weight:700;color:#000000;margin:0 0 4px;">Memuat Grafik</p>
                                        <p style="font-size:13px;color:#64748b;margin:0;">Mohon tunggu sebentar...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 sm:p-5 h-[320px] sm:h-[360px]">
                            <canvas id="attendanceChart" aria-label="Grafik kehadiran harian"></canvas>
                        </div>
                    </div>
                    <div class="flex sm:hidden items-center justify-center gap-4 flex-wrap mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/80">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-green-500 to-emerald-600"></span>
                            <span class="text-[11px] font-medium text-slate-600 dark:text-slate-300">Hadir</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-yellow-500 to-amber-600"></span>
                            <span class="text-[11px] font-medium text-slate-600 dark:text-slate-300">Terlambat</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-red-500 to-rose-600"></span>
                            <span class="text-[11px] font-medium text-slate-600 dark:text-slate-300">Tidak Hadir</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600"></span>
                            <span class="text-[11px] font-medium text-slate-600 dark:text-slate-300">Izin/Cuti</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Info Card -->
        <div class="card p-5 animate-fade-in-right">
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-semibold text-navy-800 dark:text-white">Info Presensi</h3>
                    </div>
                    <span class="px-2.5 py-1 bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 text-green-600 dark:text-green-400 text-[9px] font-semibold rounded-full flex items-center gap-1.5 shadow-sm">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full pulse-dot" style="display:inline-block!important; animation:pulse-green 2s infinite!important;"></span>
                        Live
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Waktu dan lokasi Anda</p>
            </div>

            <!-- Real-time Clock -->
            <div class="p-4 bg-gradient-to-br from-navy-800 via-navy-900 to-slate-900 dark:from-slate-700 dark:via-slate-800 dark:to-slate-900 rounded-xl text-white mb-3 shadow-lg shadow-navy-800/20 border border-navy-700/30">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 bg-gradient-to-br from-gold-400/20 to-gold-500/20 rounded-lg flex items-center justify-center border border-gold-400/30">
                            <i data-lucide="clock" class="w-4 h-4 text-gold-400"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-300">Waktu Sekarang</span>
                    </div>
                    <span class="text-2xl font-bold font-mono tracking-wider text-white realtime-clock tabular-nums">00:00:00</span>
                </div>
            </div>

            <!-- Jam Masuk Info -->
            <div class="p-3.5 bg-gradient-to-r from-gold-50 via-amber-50 to-yellow-50 dark:from-gold-900/20 dark:via-amber-900/20 dark:to-yellow-900/20 rounded-xl border border-gold-200 dark:border-gold-800 mb-3 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="sunrise" class="w-4 h-4 text-gold-600 dark:text-gold-400"></i>
                        <span class="text-xs font-medium text-gold-800 dark:text-gold-300">Waktu Masuk</span>
                    </div>
                    <span class="text-xs font-bold text-gold-800 dark:text-gold-300">
                        {{ $appSettings->attendance_start_time ?? '07:30' }} - {{ $appSettings->attendance_end_time ?? '08:00' }} WIB
                    </span>
                </div>
                <!-- Status indicator removed per user request -->
            </div>

            <!-- Location Status -->
            <div class="p-4 bg-gradient-to-br from-slate-50 via-slate-50 to-slate-100 dark:from-slate-700/50 dark:via-slate-700/30 dark:to-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600 mb-3 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="relative flex-shrink-0">
                        <div class="w-11 h-11 bg-gradient-to-br from-navy-800 via-navy-900 to-slate-900 dark:from-gold-400 dark:via-gold-500 dark:to-gold-600 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                            <i data-lucide="map-pin" class="w-5 h-5 text-white dark:text-navy-900"></i>
                        </div>
                        <!-- PERMANENT GREEN DOT - FIXED -->
                        <span class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 border-2 border-white dark:border-navy-800 rounded-full pulse-dot shadow-lg shadow-green-500/50" style="display:inline-block!important; animation:pulse-green 2s infinite!important;"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Lokasi Anda</span>
                            <!-- Status dot: PERMANENT (never use innerHTML) -->
                            <span id="locationStatusDot" class="w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse flex-shrink-0" style="display:inline-block!important;"></span>
                            <!-- Status text: DYNAMIC (use textContent/className) -->
                            <span id="locationStatus" class="px-2 py-0.5 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 text-[9px] font-medium rounded-full">Mendeteksi...</span>
                        </div>
                        <p id="locationNameDisplay" class="text-sm font-bold text-navy-800 dark:text-white truncate">
                            <span class="inline-flex items-center gap-1.5 text-slate-400">
                                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full pulse-dot" style="display:inline-block!important; animation:pulse-green 2s infinite!important;"></span>
                                Mengambil koordinat...
                            </span>
                        </p>
                        <p id="locationAddressDisplay" class="text-[10px] text-slate-500 dark:text-slate-400 truncate mt-0.5">
                            Mohon tunggu sebentar
                        </p>
                        <div class="flex items-center gap-3 mt-2 text-[9px] text-slate-400">
                            <span class="flex items-center gap-1">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                <span id="lastUpdate">--:--</span>
                            </span>
                            <span class="flex items-center gap-1">
                                <i data-lucide="target" class="w-3 h-3"></i>
                                <span id="accuracyInfo">--m</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('attendance.history') }}" class="group flex items-center justify-center gap-2 py-2.5 px-4 bg-gradient-to-r from-navy-800 via-navy-900 to-slate-900 hover:from-navy-900 hover:via-slate-900 hover:to-navy-800 text-white rounded-xl text-xs font-medium transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-navy-800/30 hover:-translate-y-0.5 icon-click">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 group-hover:scale-110 transition-transform"></i>
                    Riwayat
                </a>
                <a href="{{ route('reports.index') }}" class="group flex items-center justify-center gap-2 py-2.5 px-4 bg-gradient-to-r from-slate-100 via-slate-200 to-slate-100 dark:from-slate-700 dark:via-slate-600 dark:to-slate-700 hover:from-slate-200 hover:via-slate-300 hover:to-slate-200 dark:hover:from-slate-600 dark:hover:to-slate-500 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-medium transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5 icon-click">
                    <i data-lucide="file-bar-chart" class="w-3.5 h-3.5 group-hover:scale-110 transition-transform"></i>
                    Laporan
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity Table -->
    <div class="card overflow-hidden animate-fade-in-up">
        <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-white dark:from-slate-800/50 dark:to-slate-800/30">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div>
                        <h3 class="text-base font-semibold text-navy-800 dark:text-white">Aktivitas Terakhir</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">5 Presensi terbaru dari semua guru</p>
                    </div>
                </div>
                <a href="{{ route('attendance.history') }}" class="text-xs text-gold-500 hover:text-gold-600 font-medium flex items-center gap-1 transition-colors group icon-click">
                    Lihat Semua
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guru</th>
                        <th class="px-6 py-4 text-center text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">NIP</th>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal & Hari</th>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Keluar</th>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($recentAttendances as $att)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <img src="{{ $att->user->photo_url }}" 
                                             class="w-10 h-10 rounded-full object-cover border-2 border-slate-200 dark:border-slate-700 group-hover:scale-105 transition-transform">
                                        <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 {{ $att->user->is_active ? 'bg-green-500' : 'bg-slate-400' }} border-2 border-white dark:border-slate-800 rounded-full"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-navy-800 dark:text-white">{{ $att->user->name }}</p>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ $att->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($att->user->nip)
                                <span class="inline-flex items-center px-2.5 py-1 bg-navy-100 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 rounded-lg text-sm font-semibold tracking-wide">
                                    {{ $att->user->nip }}
                                </span>
                                @else
                                <span class="text-xs text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-medium text-navy-800 dark:text-white">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($att->date)->locale('id')->isoFormat('dddd') }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($att->check_in)
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="clock" class="w-3.5 h-3.5 text-green-500"></i>
                                        <span class="text-sm font-mono text-slate-700 dark:text-slate-300">{{ $att->check_in }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($att->check_out)
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="clock" class="w-3.5 h-3.5 text-blue-500"></i>
                                        <span class="text-sm font-mono text-slate-700 dark:text-slate-300">{{ $att->check_out }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-slate-400">Belum keluar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'Hadir' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                        'Terlambat' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        'Izin' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                        'Alpha' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    ];
                                    $statusIcons = [
                                        'Hadir' => 'check-circle',
                                        'Terlambat' => 'clock',
                                        'Izin' => 'file-text',
                                        'Alpha' => 'x-circle',
                                    ];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium {{ $statusColors[$att->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    <i data-lucide="{{ $statusIcons[$att->status] ?? 'circle' }}" class="w-3 h-3"></i>
                                    {{ $att->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                                        <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada data presensi</p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Presensi akan muncul setelah guru melakukan check-in</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* ==========================================
       CHART LOADING ANIMATIONS
       ========================================== */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    @keyframes spinReverse {
        from { transform: rotate(360deg); }
        to { transform: rotate(0deg); }
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    .dark .dark-dot { background: #facc15 !important; }

    /* ==========================================
       PULSE DOT ANIMATION - PERMANENT & STABLE
       ========================================== */
    .pulse-dot {
        display: inline-block !important;
        animation: pulse-green 2s cubic-bezier(0.4, 0, 0.6, 1) infinite !important;
        transform-origin: center !important;
        will-change: transform, opacity !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    @keyframes pulse-green {
        0% {
            transform: scale(1);
            opacity: 1;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        }
        50% {
            transform: scale(1.15);
            opacity: 0.85;
            box-shadow: 0 0 0 6px rgba(34, 197, 94, 0);
        }
        100% {
            transform: scale(1);
            opacity: 1;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
        }
    }

    /* ==========================================
       CARD HOVER ANIMATION - NO ICON ISSUES
       ========================================== */
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.1);
    }
    
    .dark .card-hover:hover {
        box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.3);
    }

    /* Icons should NOT be affected by card hover */
    .card-hover .lucide-icon,
    .card-hover svg,
    .card-hover [data-lucide] {
        transform: none !important;
        transition: none !important;
    }

    /* ==========================================
       ICON CLICK ANIMATION
       ========================================== */
    .icon-click {
        transition: transform 0.15s ease !important;
    }
    
    .icon-click:active {
        transform: scale(0.92) !important;
    }

    /* ==========================================
       PAGE LOAD ANIMATIONS - STAGGERED
       ========================================== */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .animate-fade-in-left {
        animation: fadeInLeft 0.6s ease-out forwards;
    }

    .animate-fade-in-right {
        animation: fadeInRight 0.6s ease-out forwards;
    }

    /* Staggered animations for stats cards */
    .animate-stagger-1 { animation: fadeInUp 0.5s ease-out 0.1s forwards; opacity: 0; }
    .animate-stagger-2 { animation: fadeInUp 0.5s ease-out 0.2s forwards; opacity: 0; }
    .animate-stagger-3 { animation: fadeInUp 0.5s ease-out 0.3s forwards; opacity: 0; }
    .animate-stagger-4 { animation: fadeInUp 0.5s ease-out 0.4s forwards; opacity: 0; }
    .animate-stagger-5 { animation: fadeInUp 0.5s ease-out 0.5s forwards; opacity: 0; }

    /* Confetti canvas overlay */
    #attendanceChart {
        position: relative;
    }
    
    /* Bounce animation for holiday bars */
    @keyframes barBounce {
        0%, 100% { transform: scaleY(1); }
        50% { transform: scaleY(1.05); }
    }
    
    .chartjs-render-monitor .bar-holiday {
        animation: barBounce 1s ease-in-out infinite;
        transform-origin: bottom;
    }
    
    /* ==========================================
       CHART — Premium shell & loading dot
       ========================================== */
    .chart-premium-card {
        transition: box-shadow 0.35s ease, border-color 0.35s ease;
    }

    #attendanceChart {
        width: 100% !important;
        height: 100% !important;
        display: block;
    }

    .chartjs-render-monitor {
        animation: chartFadeIn 0.85s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    @keyframes chartFadeIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ==========================================
       DROPDOWN ANIMATION (Three Dots Menu)
       ========================================== */
    [x-cloak] {
        display: none !important;
    }

    /* ==========================================
       SMOOTH SCROLLBAR
       ========================================== */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .dark ::-webkit-scrollbar-thumb {
        background: #334155;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* ==========================================
       UTILITY CLASSES
       ========================================== */
    .tabular-nums {
        font-variant-numeric: tabular-nums;
    }

    /* Confetti canvas overlay */
    #attendanceChart {
        position: relative;
    }

    /* Bounce animation for holiday bars */
    @keyframes barBounce {
        0%, 100% { transform: scaleY(1); }
        50% { transform: scaleY(1.05); }
    }

    .chartjs-render-monitor .bar-holiday {
        animation: barBounce 1s ease-in-out infinite;
        transform-origin: bottom;
    }
</style>

<script>
    // ==========================================
    // GLOBAL VARIABLES
    // ==========================================
    let currentChart = null;
    let watchId = null;
    let clockInterval = null;
    let chartLoadingTimer = null;
    let chartAnimationId = null;

    // ==========================================
    // ICON CLICK ANIMATION FUNCTION
    // ==========================================
    function animateClick(element) {
        if (!element) return;
        element.style.transform = 'scale(0.92)';
        setTimeout(() => {
            element.style.transform = '';
        }, 150);
    }

    // ==========================================
    // 1. REALTIME CLOCK (FIXED)
    // ==========================================
    function updateRealtimeClock() {
        const clockEls = document.querySelectorAll('.realtime-clock');
        const dateEls = document.querySelectorAll('.realtime-date');
        
        if (clockEls.length === 0) return;

        const now = new Date();
        
        // Update Time (HH:mm:ss)
        const timeString = now.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit',
            hour12: false 
        });
        clockEls.forEach(el => {
            if (el) el.textContent = timeString;
        });

        // Update Date if element exists
        if (dateEls.length > 0) {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const dateString = now.toLocaleDateString('id-ID', options);
            dateEls.forEach(el => {
                if (el) el.textContent = dateString;
            });
        }

        // Check Entry Status
        checkAttendanceStatus(now);
    }

    function checkAttendanceStatus(now) {
        // Function maintained for potential future use, 
        // but UI elements removed per user request.
    }

    // ==========================================
    // 2. LOCATION SERVICE (FIXED)
    // ==========================================
    async function reverseGeocode(lat, lng) {
        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10&accept-language=id`
            );
            
            if (!response.ok) throw new Error('Gagal mengambil data lokasi');
            
            const data = await response.json();
            const address = data.address || {};
            let locationName = 'Lokasi Tidak Dikenal';
            
            if (address.city) locationName = address.city;
            else if (address.town) locationName = address.town;
            else if (address.village) locationName = address.village;
            else if (address.suburb) locationName = address.suburb;
            else if (address.county) locationName = address.county;
            
            const parts = [];
            if (address.suburb) parts.push(address.suburb);
            if (address.city || address.town || address.village) parts.push(locationName);
            if (address.state) parts.push(address.state);
            if (address.country) parts.push(address.country);
            
            return {
                name: locationName,
                address: parts.join(', ') || data.display_name || 'Indonesia',
            };
            
        } catch (error) {
            console.error('Geocoding error:', error);
            return {
                name: 'Lokasi Tidak Dikenal',
                address: 'Gagal mengambil alamat lengkap',
            };
        }
    }

    function updateLocationDisplay(lat, lng, accuracy, locationData) {
        const nameEl = document.getElementById('locationNameDisplay');
        const addressEl = document.getElementById('locationAddressDisplay');
        const statusEl = document.getElementById('locationStatus');
        const statusDotEl = document.getElementById('locationStatusDot');
        const updateEl = document.getElementById('lastUpdate');
        const accuracyEl = document.getElementById('accuracyInfo');
        
        // Update location text using textContent (SAFE - no HTML parsing)
        if (nameEl) {
            nameEl.textContent = locationData.name;
            nameEl.className = 'text-sm font-bold text-navy-800 dark:text-white truncate';
        }
        if (addressEl) {
            addressEl.textContent = locationData.address;
            addressEl.className = 'text-[10px] text-slate-500 dark:text-slate-400 truncate mt-0.5';
        }
        
        // Update status indicator: modify className and dot SEPARATELY (NOT innerHTML)
        if (statusEl) {
            statusEl.textContent = 'Aktif';
            statusEl.className = 'px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[9px] font-medium rounded-full';
        }
        
        // Update status dot: change color and animation
        if (statusDotEl) {
            statusDotEl.className = 'w-1.5 h-1.5 bg-green-500 rounded-full pulse-dot flex-shrink-0';
            statusDotEl.style.display = 'inline-block !important';
            statusDotEl.style.animation = 'pulse-green 2s infinite';
        }
        
        // Update accuracy info
        if (accuracyEl) accuracyEl.textContent = `±${Math.round(accuracy)}m`;
        
        // Update last checked time
        if (updateEl) {
            const now = new Date();
            updateEl.textContent = now.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
        }
        
        // Re-render Lucide icons if they exist
        if (window.lucide && typeof lucide.createIcons === 'function') {
            try {
                lucide.createIcons();
            } catch(e) {
                console.debug('Icon refresh skipped:', e.message);
            }
        }
    }

    function startLocationTracking() {
        if (!navigator.geolocation) {
            updateLocationDisplay(-6.2, 106.8, 100, { name: 'Indonesia', address: 'Browser tidak mendukung geolocation' });
            return;
        }

        const options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 15000
        };

        // Initial Get
        navigator.geolocation.getCurrentPosition(
            async (position) => {
                const { latitude, longitude, accuracy } = position.coords;
                const locationData = await reverseGeocode(latitude, longitude);
                updateLocationDisplay(latitude, longitude, accuracy, locationData);
            },
            (error) => {
                let msg = 'Izin lokasi ditolak';
                if (error.code === 2) msg = 'Lokasi tidak tersedia';
                if (error.code === 3) msg = 'Waktu habis';
                updateLocationDisplay(-6.2, 106.8, 0, { name: 'Gagal', address: msg });
            },
            options
        );

        // Watch Position (Real-time)
        watchId = navigator.geolocation.watchPosition(
            async (position) => {
                const { latitude, longitude, accuracy } = position.coords;
                const locationData = await reverseGeocode(latitude, longitude);
                updateLocationDisplay(latitude, longitude, accuracy, locationData);
            },
            () => {
                // Silent fail on watch, keep last known
            },
            options
        );
    }

    // ==========================================
    // 3. ICON INITIALIZATION & RE-RENDERING
    // ==========================================
    function initIcons() {
        if (window.lucide && typeof lucide.createIcons === 'function') {
            try {
                lucide.createIcons({
                    attrs: {
                        class: "lucide-icon"
                    }
                });
            } catch(e) {
                console.warn('Icon initialization warning:', e.message);
            }
        }
    }

    // Re-render icons after DOM updates
    function refreshIcons() {
        if (window.lucide && typeof lucide.createIcons === 'function') {
            try {
                // Use a small delay to ensure DOM is fully updated
                setTimeout(() => {
                    lucide.createIcons();
                }, 0);
            } catch(e) {
                console.debug('Icon refresh skipped:', e.message);
            }
        }
    }

    // ==========================================
    // CONFETTI EFFECT PLUGIN
    // ==========================================
    const confettiPlugin = {
        id: 'confettiEffect',
        afterEvent: (chart, args) => {
            const { event } = args;
            if (event.type !== 'mousemove') return;
            
            const elements = chart.getElementsAtEventForMode(event, 'index', { intersect: false }, true);
            if (!elements.length) return;
            
            const element = elements[0];
            const index = element.index;
            
            // Cek apakah ini bar libur
            const isHoliday = window.holidayIndices && window.holidayIndices.includes(index);
            
            if (isHoliday) {
                createConfetti(chart, element.x, element.y);
            }
        }
    };

    // Confetti particles
    let confettiParticles = [];

    function createConfetti(chart, x, y) {
        const colors = ['#facc15', '#ef4444', '#3b82f6', '#22c55e', '#a855f7', '#ec4899'];
        
        for (let i = 0; i < 8; i++) {
            confettiParticles.push({
                x: x,
                y: y,
                vx: (Math.random() - 0.5) * 8,
                vy: (Math.random() - 0.5) * 8 - 4,
                color: colors[Math.floor(Math.random() * colors.length)],
                size: Math.random() * 6 + 3,
                rotation: Math.random() * 360,
                rotationSpeed: (Math.random() - 0.5) * 10,
                life: 1,
            });
        }
    }

    function updateConfetti(chart) {
        const ctx = chart.ctx;
        
        confettiParticles = confettiParticles.filter(p => p.life > 0);
        
        confettiParticles.forEach(p => {
            ctx.save();
            ctx.translate(p.x, p.y);
            ctx.rotate((p.rotation * Math.PI) / 180);
            ctx.fillStyle = p.color;
            ctx.globalAlpha = p.life;
            ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.6);
            ctx.restore();
            
            p.x += p.vx;
            p.y += p.vy;
            p.vy += 0.3; // Gravity
            p.rotation += p.rotationSpeed;
            p.life -= 0.02;
        });
    }

    // ==========================================
    // HOLIDAY BACKGROUND PLUGIN (Pattern Stripes)
    // ==========================================
    const holidayBackgroundPlugin = {
        id: 'holidayBackground',
        beforeDraw: (chart) => {
            const ctx = chart.ctx;
            const chartArea = chart.chartArea;
            if (!chartArea || !window.holidayIndices || !window.holidayIndices.length) return;

            window.holidayIndices.forEach(index => {
                const meta = chart.getDatasetMeta(0);
                if (!meta.data[index]) return;
                const bar = meta.data[index];
                const barWidth = bar.width || 28;
                const x = bar.x - barWidth / 2 - 4;
                const width = barWidth + 8;

                ctx.save();
                ctx.beginPath();
                ctx.rect(x, chartArea.top, width, chartArea.bottom - chartArea.top);
                ctx.clip();

                ctx.strokeStyle = 'rgba(250, 204, 21, 0.15)';
                ctx.lineWidth = 2;
                for (let i = -50; i < 200; i += 8) {
                    ctx.beginPath();
                    ctx.moveTo(x + i, chartArea.top);
                    ctx.lineTo(x + i - 30, chartArea.bottom);
                    ctx.stroke();
                }

                ctx.strokeStyle = 'rgba(250, 204, 21, 0.4)';
                ctx.lineWidth = 2;
                ctx.strokeRect(x, chartArea.top, width, chartArea.bottom - chartArea.top);
                ctx.restore();
            });
        }
    };

    // ==========================================
    // HOLIDAY BADGE PLUGIN (DISABLED)
    // ==========================================
    /*
    const holidayBadgePlugin = {
        id: 'holidayBadge',
        afterDraw: (chart) => {
            const ctx = chart.ctx;
            
            if (!window.holidayIndices || !window.holidayIndices.length) return;
            
            window.holidayIndices.forEach(index => {
                const meta = chart.getDatasetMeta(0);
                if (!meta.data[index]) return;
                
                const bar = meta.data[index];
                const barWidth = bar.width || 28;
                
                // Draw badge "LIBUR"
                const badgeText = 'LIBUR';
                ctx.font = 'bold 10px system-ui, sans-serif';
                const textWidth = ctx.measureText(badgeText).width;
                const badgeWidth = textWidth + 12;
                const badgeHeight = 18;
                const badgeX = bar.x - badgeWidth / 2;
                const badgeY = bar.y - 30;
                
                // Badge background
                ctx.fillStyle = '#facc15';
                ctx.beginPath();
                ctx.roundRect(badgeX, badgeY, badgeWidth, badgeHeight, 4);
                ctx.fill();
                
                // Badge shadow
                ctx.shadowColor = 'rgba(250, 204, 21, 0.5)';
                ctx.shadowBlur = 4;
                ctx.shadowOffsetY = 2;
                
                // Badge text
                ctx.fillStyle = '#0f172a';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(badgeText, bar.x, badgeY + badgeHeight / 2);
                
                ctx.shadowColor = 'transparent';
            });
        }
    };
    */

    // ==========================================
    // BOUNCE ANIMATION FOR HOLIDAY BARS
    // ==========================================
    const bouncePlugin = {
        id: 'bounceAnimation',
        afterDraw: (chart) => {
            const ctx = chart.ctx;
            const time = Date.now() / 1000;
            if (!window.holidayIndices || !window.holidayIndices.length) return;

            window.holidayIndices.forEach(index => {
                const meta = chart.getDatasetMeta(0);
                if (!meta.data[index]) return;
                const bar = meta.data[index];
                const bounce = Math.sin(time * 3) * 2;

                ctx.save();
                ctx.fillStyle = 'rgba(250, 204, 21, 0.3)';
                ctx.beginPath();
                if (ctx.roundRect) {
                    ctx.roundRect(
                        bar.x - bar.width / 2,
                        bar.y - bounce,
                        bar.width,
                        bar.base - bar.y + bounce,
                        6
                    );
                } else {
                    ctx.rect(
                        bar.x - bar.width / 2,
                        bar.y - bounce,
                        bar.width,
                        bar.base - bar.y + bounce
                    );
                }
                ctx.fill();
                ctx.restore();
            });
        }
    };

    // ==========================================
    // 4. CHART.JS — premium line + loading dot (1s)
    // ==========================================
    function showChartLoading() {
        const overlay = document.getElementById('chart-loading-overlay');
        if (overlay) overlay.style.display = 'block';
        if (chartLoadingTimer) {
            clearTimeout(chartLoadingTimer);
            chartLoadingTimer = null;
        }
    }

    function scheduleHideChartLoading() {
        const overlay = document.getElementById('chart-loading-overlay');
        if (!overlay) return;
        chartLoadingTimer = setTimeout(() => {
            overlay.style.display = 'none';
            chartLoadingTimer = null;
        }, 1000);
    }

    function buildAttendanceChartLabels(days) {
        const labels = [];
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // ✅ HARI DALAM BAHASA INDONESIA
        const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        
        for (let i = days - 1; i >= 0; i--) {
            const d = new Date(today);
            d.setDate(d.getDate() - i);
            labels.push(dayNames[d.getDay()] + ' ' + String(d.getDate()).padStart(2, '0'));
        }
        return labels;
    }


    function initChart(days = 7) {
        const canvas = document.getElementById('attendanceChart');
        if (!canvas || typeof Chart === 'undefined') return;

        showChartLoading();

        // Ambil data dari controller
        let chartHadirData = <?php echo json_encode($chartHadirData ?? []); ?>;
        let chartTerlambatData = <?php echo json_encode($chartTerlambatData ?? []); ?>;
        let chartTidakHadirData = <?php echo json_encode($chartTidakHadirData ?? []); ?>;
        let chartIzinData = <?php echo json_encode($chartIzinData ?? []); ?>;

        // Validasi data
        const defaultData = new Array(Math.max(days, 7)).fill(0);
        if (!chartHadirData || !Array.isArray(chartHadirData) || chartHadirData.length === 0) chartHadirData = defaultData;
        if (!chartTerlambatData || !Array.isArray(chartTerlambatData) || chartTerlambatData.length === 0) chartTerlambatData = defaultData;
        if (!chartTidakHadirData || !Array.isArray(chartTidakHadirData) || chartTidakHadirData.length === 0) chartTidakHadirData = defaultData;
        if (!chartIzinData || !Array.isArray(chartIzinData) || chartIzinData.length === 0) chartIzinData = defaultData;

        // Slice sesuai periode
        let slicedHadir = chartHadirData.slice(-days);
        let slicedTerlambat = chartTerlambatData.slice(-days);
        let slicedTidakHadir = chartTidakHadirData.slice(-days);
        let slicedIzin = chartIzinData.slice(-days);

        // Padding jika kurang
        const padIfNeeded = (arr, days) => {
            if (arr.length < days) {
                const pad = days - arr.length;
                return new Array(pad).fill(0).concat(arr);
            }
            return arr;
        };
        slicedHadir = padIfNeeded(slicedHadir, days);
        slicedTerlambat = padIfNeeded(slicedTerlambat, days);
        slicedTidakHadir = padIfNeeded(slicedTidakHadir, days);
        slicedIzin = padIfNeeded(slicedIzin, days);

        const labels = buildAttendanceChartLabels(days);

        const isDark = document.body && document.body.classList.contains('dark');
        const tickColor = isDark ? '#94a3b8' : '#64748b';
        const gridColor = isDark ? 'rgba(148, 163, 184, 0.12)' : 'rgba(148, 163, 184, 0.18)';
        const maxVal = Math.max(0, ...slicedHadir, ...slicedTerlambat, ...slicedTidakHadir, ...slicedIzin);
        const yMax = maxVal > 0 ? maxVal + Math.max(2, Math.ceil(maxVal * 0.15)) : 6;

        if (currentChart) {
            currentChart.destroy();
            currentChart = null;
        }
        if (chartAnimationId) {
            cancelAnimationFrame(chartAnimationId);
            chartAnimationId = null;
        }

        const ctx = canvas.getContext('2d');

        // Setup holiday indices & names (dynamic based on current period)
        let holidayData = <?php echo json_encode($holidayDates ?? []); ?>;
        const todayMidnight = new Date();
        todayMidnight.setHours(0, 0, 0, 0);

        window.holidayIndices = [];
        window.holidayNames = {};
        holidayData.forEach(h => {
            const hDate = new Date(h.date + 'T00:00:00');
            const diffDays = Math.round((todayMidnight - hDate) / 86400000);
            const chartIndex = (days - 1) - diffDays;
            if (chartIndex >= 0 && chartIndex < days) {
                window.holidayIndices.push(chartIndex);
                window.holidayNames[chartIndex] = h.name;
            }
        });

        // Helper untuk buat gradient
        const makeGradient = (colorStart, colorEnd) => {
            return (context) => {
                const chart = context.chart;
                const {ctx, chartArea} = chart;
                if (!chartArea) return colorStart;
                const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                gradient.addColorStop(0, colorStart);
                gradient.addColorStop(1, colorEnd);
                return gradient;
            };
        };

        try {
            currentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Hadir',
                            data: slicedHadir,
                            backgroundColor: makeGradient('#10b981', '#22c55e'),
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 28,
                            hoverBackgroundColor: '#34d399',
                        },
                        {
                            label: 'Terlambat',
                            data: slicedTerlambat,
                            backgroundColor: makeGradient('#eab308', '#facc15'),
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 28,
                            hoverBackgroundColor: '#fde047',
                        },
                        {
                            label: 'Tidak Hadir',
                            data: slicedTidakHadir,
                            backgroundColor: makeGradient('#ef4444', '#f87171'),
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 28,
                            hoverBackgroundColor: '#fca5a5',
                        },
                        {
                            label: 'Izin/Cuti',
                            data: slicedIzin,
                            backgroundColor: makeGradient('#3b82f6', '#60a5fa'),
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 28,
                            hoverBackgroundColor: '#93c5fd',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: true,
                            backgroundColor: isDark ? 'rgba(15, 23, 42, 0.96)' : 'rgba(255, 255, 255, 0.98)',
                            titleColor: isDark ? '#f8fafc' : '#0f172a',
                            bodyColor: isDark ? '#e2e8f0' : '#334155',
                            titleFont: { size: 12, weight: '600', family: "system-ui, sans-serif" },
                            bodyFont: { size: 12, family: "system-ui, sans-serif" },
                            padding: 14,
                            cornerRadius: 12,
                            displayColors: true,
                            borderColor: isDark ? 'rgba(148, 163, 184, 0.25)' : 'rgba(15, 23, 42, 0.08)',
                            borderWidth: 1,
                            callbacks: {
                                title: (items) => {
                                    const index = items[0].dataIndex;
                                    const label = items[0].label;
                                    
                                    // Bahasa hari Indonesia
                                    const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                    const date = new Date();
                                    date.setDate(date.getDate() - ((days - 1) - index));
                                    const dayName = dayNames[date.getDay()];
                                    
                                    if (window.holidayIndices.includes(index)) {
                                        return `\uD83C\uDF89 ${dayName} - ${window.holidayNames[index] || 'Hari Libur'}`;
                                    }
                                    return dayName;
                                },
                                label: (context) => {
                                    const v = context.parsed.y;
                                    const n = typeof v === 'number' && v % 1 !== 0 ? v.toFixed(1) : v;
                                    return ' ' + context.dataset.label + ': ' + n;
                                },
                                afterBody: (items) => {
                                    const index = items[0].dataIndex;
                                    if (window.holidayIndices.includes(index)) {
                                        return ['', '\uD83C\uDFD6\uFE0F Hari Libur - Tidak ada absensi'];
                                    }
                                    return [];
                                },
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: yMax,
                            grid: {
                                color: gridColor,
                                drawBorder: false,
                                lineWidth: 1,
                                drawTicks: false,
                            },
                            ticks: {
                                color: tickColor,
                                padding: 10,
                                font: { size: 11, weight: '500', family: "system-ui, sans-serif" },
                            },
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: {
                                color: tickColor,
                                padding: 10,
                                maxRotation: 45,
                                minRotation: 0,
                                font: { size: 10, weight: '500', family: "system-ui, sans-serif" },
                                callback: function(value, index) {
                                    if (window.holidayIndices.includes(index)) {
                                        return this.getLabelForValue(value);
                                    }
                                    return this.getLabelForValue(value);
                                }
                            },
                        },
                    },
                    animation: {
                        duration: 900,
                        easing: 'easeOutQuart',
                    },
                },
                plugins: [
                    holidayBackgroundPlugin,
                    // holidayBadgePlugin,
                    bouncePlugin,
                    confettiPlugin,
                ],
            });

            // Animation loop for confetti and bounce
            function animate() {
                if (currentChart) {
                    updateConfetti(currentChart);
                    currentChart.draw();
                }
                requestAnimationFrame(animate);
            }
            animate();

        } catch (e) {
            console.error('Chart error', e);
        } finally {
            scheduleHideChartLoading();
        }

        // Update badge total
        const totalHadir = slicedHadir.reduce((a, b) => a + b, 0);
        const totalTerlambat = slicedTerlambat.reduce((a, b) => a + b, 0);
        const totalTidakHadir = slicedTidakHadir.reduce((a, b) => a + b, 0);
        const totalIzin = slicedIzin.reduce((a, b) => a + b, 0);
        const totalAll = totalHadir + totalTerlambat + totalTidakHadir + totalIzin;

        const badgeEl = document.getElementById('chart-total-badge');
        if (badgeEl) {
            badgeEl.textContent = totalAll > 0
                ? '· Total ' + totalAll + ' presensi (periode ini)'
                : '· Belum ada data di periode ini';
        }

        const periodEl = document.getElementById('chart-period');
        if (periodEl) periodEl.textContent = days + ' hari terakhir';

        try {
            refreshIcons();
        } catch (e) {
            /* ignore */
        }
    }

    function updateChartPeriod(days) {
        initChart(days);
    }
    window.updateChartPeriod = updateChartPeriod;

    // ==========================================
    // INITIALIZATION (DOM READY)
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Start Clock Immediately
        try {
            updateRealtimeClock();
            clockInterval = setInterval(updateRealtimeClock, 1000);
        } catch(e) {
            console.error('Clock error', e);
        }

        // 2. Start Location
        try {
            startLocationTracking();
            // Refresh icons after location updates start
            setTimeout(() => refreshIcons(), 500);
        } catch(e) {
            console.error('Location error', e);
        }

        // 3. Initialize Chart  
        try {
            initChart(7);
        } catch(e) {
            console.error('Chart error', e);
        }

        // 4. Initialize Icons
        try {
            initIcons();
        } catch(e) {
            console.error('Icon error', e);
        }

        // 5. Auto-hide Notifications
        try {
            const notifications = document.querySelectorAll('.bg-green-50, .bg-red-50');
            notifications.forEach(notification => {
                setTimeout(() => {
                    notification.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateY(-10px)';
                    setTimeout(() => notification.remove(), 500);
                }, 2000);
            });
        } catch(e) {
            console.error('Notification error', e);
        }

        // 6. Add click animation to all icon buttons
        try {
            const iconButtons = document.querySelectorAll('.icon-click, button[pixel], [x-data] button');
            iconButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    animateClick(this);
                });
            });
        } catch(e) {
            console.error('Icon animation error', e);
        }
    });

    // Cleanup on unload
    window.addEventListener('beforeunload', () => {
        if (clockInterval) clearInterval(clockInterval);
        if (watchId) navigator.geolocation.clearWatch(watchId);
        if (chartLoadingTimer) clearTimeout(chartLoadingTimer);
        if (currentChart) currentChart.destroy();
        if (chartAnimationId) cancelAnimationFrame(chartAnimationId);
    });
</script>
@endsection