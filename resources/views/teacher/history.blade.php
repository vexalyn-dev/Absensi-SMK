@extends('layouts.teacher')

@section('page-title', 'Riwayat Presensi')

@section('content')
<div class="fade-in space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="history" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Riwayat Presensi</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Lihat riwayat kehadiran Anda</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button onclick="switchTab('daily')" id="tab-daily" 
               class="px-4 py-2 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-lg text-sm font-semibold transition-all">
                Presensi Harian
            </button>
            <button onclick="switchTab('class')" id="tab-class"
               class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-semibold transition-all">
                Presensi Kelas
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="card p-4 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="file-text" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Total</p>
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white" id="stat-total">0</h3>
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
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white" id="stat-hadir">0</h3>
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
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white" id="stat-terlambat">0</h3>
                </div>
            </div>
        </div>

        <div class="card p-4 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="file-check" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Izin</p>
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white" id="stat-izin">0</h3>
                </div>
            </div>
        </div>

        <div class="card p-4 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="x-circle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Alpha</p>
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white" id="stat-alpha">0</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="card overflow-hidden relative" style="min-height: 400px;">
        <!-- Modern Premium Loading Overlay -->
        <div id="loading-spinner" class="hidden absolute inset-0 bg-gradient-to-br from-white/95 via-white/90 to-slate-50/95 dark:from-slate-900/95 dark:via-slate-900/90 dark:to-slate-800/95 backdrop-blur-md z-50 flex items-center justify-center">
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
                        <i data-lucide="history" class="w-8 h-8 text-navy-600 dark:text-gold-400 animate-pulse"></i>
                    </div>
                </div>
                
                <!-- Text -->
                <div class="mt-8">
                    <p class="text-base font-bold mb-1" style="color: #000000 !important;">Memuat Riwayat</p>
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
        
        <div id="table-container">
            <!-- Content will be loaded via AJAX -->
        </div>
    </div>
</div>

<script>
    let currentTab = 'daily';
    let currentPage = 1;
    const historyDataUrl = "{{ route('teacher.history.data') }}";

    function switchTab(type) {
        currentTab = type;
        currentPage = 1;
        
        // Update button styles
        if (type === 'daily') {
            document.getElementById('tab-daily').classList.remove('bg-slate-100', 'dark:bg-slate-700', 'text-slate-700', 'dark:text-slate-300');
            document.getElementById('tab-daily').classList.add('bg-navy-800', 'dark:bg-gold-400', 'text-white', 'dark:text-navy-900');
            
            document.getElementById('tab-class').classList.remove('bg-navy-800', 'dark:bg-gold-400', 'text-white', 'dark:text-navy-900');
            document.getElementById('tab-class').classList.add('bg-slate-100', 'dark:bg-slate-700', 'text-slate-700', 'dark:text-slate-300');
        } else {
            document.getElementById('tab-class').classList.remove('bg-slate-100', 'dark:bg-slate-700', 'text-slate-700', 'dark:text-slate-300');
            document.getElementById('tab-class').classList.add('bg-navy-800', 'dark:bg-gold-400', 'text-white', 'dark:text-navy-900');
            
            document.getElementById('tab-daily').classList.remove('bg-navy-800', 'dark:bg-gold-400', 'text-white', 'dark:text-navy-900');
            document.getElementById('tab-daily').classList.add('bg-slate-100', 'dark:bg-slate-700', 'text-slate-700', 'dark:text-slate-300');
        }
        
        loadData();
    }

    function loadData(page = 1) {
        currentPage = page;
        document.getElementById('loading-spinner').classList.remove('hidden');
        document.getElementById('table-container').classList.add('hidden');
        
        fetch(`${historyDataUrl}?type=${currentTab}&page=${page}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            renderTable(data);
            updateStats(data.stats);
            document.getElementById('loading-spinner').classList.add('hidden');
            document.getElementById('table-container').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loading-spinner').classList.add('hidden');
        });
    }

    function renderTable(data) {
        const container = document.getElementById('table-container');
        const attendances = Array.isArray(data.attendances) ? data.attendances : (data.attendances?.data || []);
        const normalizedData = { ...data, attendances };
        
        if (currentTab === 'daily') {
            renderDailyTable(normalizedData);
        } else {
            renderClassTable(normalizedData);
        }
    }

    function renderDailyTable(data) {
        const container = document.getElementById('table-container');
        const attendances = Array.isArray(data.attendances) ? data.attendances : (data.attendances?.data || []);
        
        if (attendances.length === 0) {
            container.innerHTML = `
                <div class="p-12 text-center">
                    <i data-lucide="inbox" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada data presensi</p>
                </div>
            `;
            return;
        }
        
        // Desktop/Tablet: Table view
        let html = `
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Masuk</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Pulang</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
        `;
        
        attendances.forEach(att => {
            html += `
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-navy-800 dark:text-white">${att.date_formatted}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">${att.day_name}</p>
                    </td>
                    <td class="px-6 py-4">
                        ${att.check_in ? `
                            <div class="flex items-center gap-2">
                                <i data-lucide="clock" class="w-4 h-4 text-green-500"></i>
                                <span class="text-sm font-mono text-slate-700 dark:text-slate-300">${att.check_in}</span>
                            </div>
                        ` : '<span class="text-sm text-slate-400">-</span>'}
                    </td>
                    <td class="px-6 py-4">
                        ${att.check_out ? `
                            <div class="flex items-center gap-2">
                                <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i>
                                <span class="text-sm font-mono text-slate-700 dark:text-slate-300">${att.check_out}</span>
                            </div>
                        ` : '<span class="text-sm text-slate-400">-</span>'}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                            ${att.status === 'Hadir' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ''}
                            ${att.status === 'Terlambat' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : ''}
                            ${att.status === 'Izin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : ''}
                            ${att.status === 'Alpha' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : ''}">
                            ${att.status}
                        </span>
                    </td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        
        // Mobile: Card view
        html += '<div class="md:hidden space-y-3 p-4">';
        attendances.forEach(att => {
            html += `
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="text-sm font-bold text-navy-800 dark:text-white">${att.date_formatted}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">${att.day_name}</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                            ${att.status === 'Hadir' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ''}
                            ${att.status === 'Terlambat' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : ''}
                            ${att.status === 'Izin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : ''}
                            ${att.status === 'Alpha' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : ''}">
                            ${att.status}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex items-center gap-2">
                            <i data-lucide="clock" class="w-4 h-4 text-green-500 flex-shrink-0"></i>
                            <div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">Masuk</p>
                                <p class="text-sm font-mono font-semibold text-slate-700 dark:text-slate-300">${att.check_in || '-'}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="clock" class="w-4 h-4 text-blue-500 flex-shrink-0"></i>
                            <div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">Pulang</p>
                                <p class="text-sm font-mono font-semibold text-slate-700 dark:text-slate-300">${att.check_out || '-'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        // Pagination
        if (data.last_page > 1) {
            html += `<div class="p-4 border-t border-slate-200 dark:border-slate-700">${data.links}</div>`;
        }
        
        container.innerHTML = html;
        
        if (window.lucide) lucide.createIcons();
    }

    function renderClassTable(data) {
        const container = document.getElementById('table-container');
        const attendances = Array.isArray(data.attendances) ? data.attendances : (data.attendances?.data || []);
        
        if (attendances.length === 0) {
            container.innerHTML = `
                <div class="p-12 text-center">
                    <i data-lucide="inbox" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada data presensi kelas</p>
                </div>
            `;
            return;
        }
        
        // Desktop/Tablet: Table view
        let html = `
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Mapel</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Masuk</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Keluar</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
        `;
        
        attendances.forEach(att => {
            html += `
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-navy-800 dark:text-white">${att.date_formatted}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">${att.day_name}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-navy-800 dark:text-white">${att.classroom_name || '-'}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-slate-700 dark:text-slate-300">${att.subject_name || '-'}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-mono text-slate-700 dark:text-slate-300">Jam ${att.period}</span>
                    </td>
                    <td class="px-6 py-4">
                        ${att.check_in_time ? `
                            <div class="flex items-center gap-2">
                                <i data-lucide="clock" class="w-4 h-4 text-green-500"></i>
                                <span class="text-sm font-mono text-slate-700 dark:text-slate-300">${att.check_in_time} WIB</span>
                            </div>
                        ` : '<span class="text-sm text-slate-400">-</span>'}
                    </td>
                    <td class="px-6 py-4">
                        ${att.check_out_time ? `
                            <div class="flex items-center gap-2">
                                <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i>
                                <span class="text-sm font-mono text-slate-700 dark:text-slate-300">${att.check_out_time} WIB</span>
                            </div>
                        ` : '<span class="text-sm text-slate-400">-</span>'}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                            ${att.status === 'Hadir' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ''}
                            ${att.status === 'Terlambat' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : ''}">
                            ${att.status}
                        </span>
                    </td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        
        // Mobile: Card view
        html += '<div class="md:hidden space-y-3 p-4">';
        attendances.forEach(att => {
            html += `
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="text-sm font-bold text-navy-800 dark:text-white">${att.date_formatted}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">${att.day_name}</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                            ${att.status === 'Hadir' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ''}
                            ${att.status === 'Terlambat' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : ''}">
                            ${att.status}
                        </span>
                    </div>
                    <div class="space-y-2 mb-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Kelas</span>
                            <span class="text-sm font-bold text-navy-800 dark:text-white">${att.classroom_name || '-'}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Mapel</span>
                            <span class="text-sm text-slate-700 dark:text-slate-300">${att.subject_name || '-'}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Jam Pelajaran</span>
                            <span class="text-sm font-mono text-slate-700 dark:text-slate-300">Jam ${att.period}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <div class="flex items-center gap-2">
                            <i data-lucide="clock" class="w-4 h-4 text-green-500 flex-shrink-0"></i>
                            <div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">Masuk</p>
                                <p class="text-sm font-mono font-semibold text-slate-700 dark:text-slate-300">${att.check_in_time ? att.check_in_time + ' WIB' : '-'}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="clock" class="w-4 h-4 text-blue-500 flex-shrink-0"></i>
                            <div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">Keluar</p>
                                <p class="text-sm font-mono font-semibold text-slate-700 dark:text-slate-300">${att.check_out_time ? att.check_out_time + ' WIB' : '-'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        // Pagination
        if (data.last_page > 1) {
            html += `<div class="p-4 border-t border-slate-200 dark:border-slate-700">${data.links}</div>`;
        }
        
        container.innerHTML = html;
        
        if (window.lucide) lucide.createIcons();
    }

    function updateStats(stats = {}) {
        document.getElementById('stat-total').textContent = stats.total ?? 0;
        document.getElementById('stat-hadir').textContent = stats.hadir ?? 0;
        document.getElementById('stat-terlambat').textContent = stats.terlambat ?? 0;
        document.getElementById('stat-izin').textContent = stats.izin ?? 0;
        document.getElementById('stat-alpha').textContent = stats.alpha ?? 0;
    }

    // Handle pagination click
    document.addEventListener('click', function(e) {
        if (e.target.matches('.pagination a')) {
            e.preventDefault();
            const url = new URL(e.target.href);
            const page = url.searchParams.get('page') || 1;
            loadData(page);
        }
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
        loadData();
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
</style>
@endsection