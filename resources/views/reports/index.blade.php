@extends('layouts.app')

@section('page-title', 'Laporan Absensi')

@section('content')
<div class="fade-in space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="bar-chart-3" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Laporan Absensi</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.export', ['report_type' => 'daily', 'view_mode' => $viewMode, 'start_date' => $startDate, 'end_date' => $endDate, 'search' => $search]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Export Harian</span>
                <span class="sm:hidden">Harian</span>
            </a>
            <a href="{{ route('reports.export', ['report_type' => 'class', 'view_mode' => $viewMode, 'start_date' => $startDate, 'end_date' => $endDate, 'search' => $search]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Export Kelas</span>
                <span class="sm:hidden">Kelas</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card p-5" x-data="{ reportDropdownOpen: false }">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Cari Nama Guru</label>
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama guru..." id="search-input"
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                </div>
            </div>

            <!-- Modern Jenis Laporan Dropdown -->
            <div @click.outside="reportDropdownOpen = false">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Jenis Laporan</label>
                <input type="hidden" name="report_type" id="report-type" value="{{ $reportType }}">
                <div class="relative">
                    <button type="button" 
                            @click="reportDropdownOpen = !reportDropdownOpen"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 flex items-center justify-between hover:border-navy-300 dark:hover:border-navy-600 transition-colors">
                        <span class="font-medium flex items-center gap-2">
                            @if($reportType === 'daily')
                                <i data-lucide="calendar-check" class="w-4 h-4 text-navy-600 dark:text-gold-400"></i>
                                <span>Presensi Harian</span>
                            @else
                                <i data-lucide="school" class="w-4 h-4 text-navy-600 dark:text-gold-400"></i>
                                <span>Presensi Kelas</span>
                            @endif
                        </span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': reportDropdownOpen}"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="reportDropdownOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                         class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden"
                         x-cloak>
                        <button type="button" 
                                @click="document.getElementById('report-type').value = 'daily'; reportDropdownOpen = false; fetchData()"
                                class="w-full px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-3"
                                :class="'{{ $reportType }}' === 'daily' ? 'bg-navy-50 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 font-semibold' : 'text-slate-700 dark:text-slate-300'">
                            <i data-lucide="calendar-check" class="w-4 h-4 text-navy-600 dark:text-gold-400"></i>
                            <span>Presensi Harian</span>
                        </button>
                        <button type="button" 
                                @click="document.getElementById('report-type').value = 'class'; reportDropdownOpen = false; fetchData()"
                                class="w-full px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-3"
                                :class="'{{ $reportType }}' === 'class' ? 'bg-navy-50 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 font-semibold' : 'text-slate-700 dark:text-slate-300'">
                            <i data-lucide="school" class="w-4 h-4 text-navy-600 dark:text-gold-400"></i>
                            <span>Presensi Kelas</span>
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" id="start-date"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500"
                       onchange="fetchData()">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate }}" id="end-date"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500"
                       onchange="fetchData()">
            </div>

            <div class="md:col-span-4 flex items-end gap-2">
                <div class="flex gap-2 p-1 bg-slate-100 dark:bg-slate-700 rounded-lg">
                    <button type="button" onclick="changeViewMode('daily')"
                       class="px-4 py-2 rounded-md text-sm font-semibold transition-all {{ $viewMode === 'daily' ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                        Harian
                    </button>
                    <button type="button" onclick="changeViewMode('weekly')"
                       class="px-4 py-2 rounded-md text-sm font-semibold transition-all {{ $viewMode === 'weekly' ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                        Mingguan
                    </button>
                    <button type="button" onclick="changeViewMode('monthly')"
                       class="px-4 py-2 rounded-md text-sm font-semibold transition-all {{ $viewMode === 'monthly' ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                        Bulanan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="stats-container">
        <div class="card p-4 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="file-text" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Total Absensi</p>
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white" id="stat-total">{{ $totalAbsensi }}</h3>
                    <p class="text-[10px] text-blue-500">Laporan periode ini</p>
                </div>
            </div>
        </div>

        <div class="card p-4 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Hadir</p>
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white" id="stat-hadir">{{ $totalStats['hadir'] }}</h3>
                    <p class="text-[10px] text-green-500" id="stat-rate">{{ $kehadiranRate }}% tingkat kehadiran</p>
                </div>
            </div>
        </div>

        <div class="card p-4 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="clock" class="w-5 h-5 text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Terlambat</p>
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white" id="stat-terlambat">{{ $totalStats['terlambat'] }}</h3>
                    <p class="text-[10px] text-yellow-600">Perlu tinjauan ulang</p>
                </div>
            </div>
        </div>

        <div class="card p-4 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="x-circle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Alpha/Izin</p>
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white" id="stat-alpha">{{ $totalStats['alpha'] + $totalStats['izin'] + $totalStats['sakit'] }}</h3>
                    <p class="text-[10px] text-red-500">Ketidakhadiran guru</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="flex flex-wrap gap-3 p-4 card">
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 flex items-center justify-center text-xs font-bold">
                <i data-lucide="check" class="w-4 h-4"></i>
            </span>
            <span class="text-xs text-slate-600 dark:text-slate-400">Hadir</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 flex items-center justify-center text-xs font-bold">T</span>
            <span class="text-xs text-slate-600 dark:text-slate-400">Telat</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 flex items-center justify-center text-xs font-bold">
                <i data-lucide="x" class="w-4 h-4"></i>
            </span>
            <span class="text-xs text-slate-600 dark:text-slate-400">Alfa</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 flex items-center justify-center text-xs font-bold">I</span>
            <span class="text-xs text-slate-600 dark:text-slate-400">Izin</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-400 flex items-center justify-center text-xs font-bold">S</span>
            <span class="text-xs text-slate-600 dark:text-slate-400">Sakit</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center text-xs font-bold">-</span>
            <span class="text-xs text-slate-600 dark:text-slate-400">Libur</span>
        </div>
    </div>

    <!-- Table Container -->
    <div class="card overflow-hidden relative" id="table-container">
        <!-- Modern Premium Loading Overlay -->
        <div id="loading-overlay" class="absolute inset-0 bg-gradient-to-br from-white/95 via-white/90 to-slate-50/95 dark:from-slate-900/95 dark:via-slate-900/90 dark:to-slate-800/95 backdrop-blur-md z-50" style="display: none; align-items: center; justify-content: center; flex-direction: column;">
            <div class="text-center relative">
                <!-- Animated Background Circle -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-navy-100 to-gold-100 dark:from-navy-900/30 dark:to-gold-900/30 animate-pulse"></div>
                </div>
                
                <!-- Main Spinner -->
                <div class="relative">
                    <div class="w-20 h-20 border-4 border-slate-200 dark:border-slate-700 border-t-navy-800 dark:border-t-gold-400 rounded-full animate-spin mx-auto"></div>
                    
                    <!-- Inner rotating ring -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-14 h-14 border-4 border-transparent border-b-gold-400 dark:border-b-navy-400 rounded-full animate-spin" style="animation-direction: reverse; animation-duration: 1s;"></div>
                    </div>
                    
                    <!-- Center icon -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i data-lucide="bar-chart-3" class="w-8 h-8 text-navy-600 dark:text-gold-400 animate-pulse"></i>
                    </div>
                </div>
                
                <!-- Text -->
                <div class="mt-8">
                    <p class="text-base font-bold mb-1" style="color: #000000 !important;">Memuat Laporan</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Mohon tunggu sebentar...</p>
                </div>
                
                <!-- Progress dots -->
                <div class="flex items-center justify-center gap-1.5 mt-4">
                    <div class="w-2 h-2 bg-navy-600 dark:bg-gold-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></div>
                    <div class="w-2 h-2 bg-navy-600 dark:bg-gold-400 rounded-full animate-bounce" style="animation-delay: 150ms;"></div>
                    <div class="w-2 h-2 bg-navy-600 dark:bg-gold-400 rounded-full animate-bounce" style="animation-delay: 300ms;"></div>
                </div>
            </div>
        </div>
        
        @include('reports._table', ['reportData' => $reportData, 'dates' => $dates, 'reportType' => $reportType, 'viewMode' => $viewMode])
    </div>
</div>

<style>
    .fade-in { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Hide scrollbar for monthly view */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    [x-cloak] { display: none !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();

        // AJAX search
        let searchTimeout;
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    fetchData();
                }, 500);
            });
        }

        // Make fetchData global for Alpine.js and onchange events
        window.fetchData = function() {
            const loadingOverlay = document.getElementById('loading-overlay');
            const tableContainer = document.getElementById('table-container');
            
            // Show loading
            if (loadingOverlay) {
                loadingOverlay.classList.remove('hidden');
            }
            
            const reportType = document.getElementById('report-type')?.value || 'daily';
            const startDate = document.getElementById('start-date')?.value || '';
            const endDate = document.getElementById('end-date')?.value || '';
            const search = document.getElementById('search-input')?.value || '';
            
            const params = new URLSearchParams({
                report_type: reportType,
                start_date: startDate,
                end_date: endDate,
                search: search,
                view_mode: '{{ $viewMode }}',
                _token: document.querySelector('meta[name="csrf-token"]').content
            });

            fetch('{{ route("reports.index") }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                // Update table with loading overlay preserved
                const loadingElement = tableContainer.querySelector('#loading-overlay');
                tableContainer.innerHTML = data.html;
                if (loadingElement) {
                    tableContainer.insertBefore(loadingElement, tableContainer.firstChild);
                }
                
                // Update stats
                document.getElementById('stat-total').textContent = data.totalAbsensi;
                document.getElementById('stat-hadir').textContent = data.stats.hadir;
                document.getElementById('stat-terlambat').textContent = data.stats.terlambat;
                document.getElementById('stat-alpha').textContent = data.stats.alpha + data.stats.izin + data.stats.sakit;
                document.getElementById('stat-rate').textContent = data.kehadiranRate + '% tingkat kehadiran';

                if (window.lucide) lucide.createIcons();
                
                // Hide loading
                if (loadingOverlay) {
                    loadingOverlay.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Hide loading on error
                if (loadingOverlay) {
                    loadingOverlay.classList.add('hidden');
                }
            });
        };
        
        // Make fetchData available for Alpine.js
        window.reportFetchData = window.fetchData;
        
        // Change view mode function (Harian/Mingguan/Bulanan) dengan AJAX
        window.changeViewMode = function(mode) {
            const loadingOverlay = document.getElementById('loading-overlay');
            
            // Show loading
            if (loadingOverlay) {
                loadingOverlay.classList.remove('hidden');
            }
            
            const reportType = document.getElementById('report-type')?.value || 'daily';
            const search = document.getElementById('search-input')?.value || '';
            
            // Calculate dates based on view mode
            let startDate = '';
            if (mode === 'daily') {
                startDate = new Date().toISOString().split('T')[0];
            } else if (mode === 'weekly') {
                const now = new Date();
                const dayOfWeek = now.getDay();
                const diff = now.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
                startDate = new Date(now.setDate(diff)).toISOString().split('T')[0];
            } else {
                const now = new Date();
                startDate = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
            }
            
            const params = new URLSearchParams({
                report_type: reportType,
                view_mode: mode,
                start_date: startDate,
                search: search,
                _token: document.querySelector('meta[name="csrf-token"]').content
            });

            // Redirect with params
            window.location.href = '{{ route("reports.index") }}?' + params.toString();
        };
    });
</script>

@endsection