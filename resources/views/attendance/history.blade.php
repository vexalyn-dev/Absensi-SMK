@extends('layouts.app')

@section('page-title', 'Riwayat Presensi')

@section('content')
<div class="space-y-6 animate-fade-in" x-data="attendanceApp()">

    <!-- Premium Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="calendar-check" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-navy-800 dark:text-white tracking-tight">Riwayat Presensi</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pantau dan filter data kehadiran guru</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a :href="getExportUrl()" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm shadow-md shadow-emerald-600/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Presensi -->
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar-range" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Presensi</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white" x-text="stats.total">{{ $stats['total'] }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1">Periode terpilih</p>
                </div>
            </div>
        </div>
        
        <!-- Hadir -->
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Hadir</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white" x-text="stats.hadir">{{ $stats['hadir'] }}</h3>
                    <p class="text-[10px] text-green-500 mt-1" x-text="stats.total > 0 ? Math.round((stats.hadir / stats.total) * 100) + '% dari total' : '0% dari total'">
                        {{ $stats['total'] > 0 ? round(($stats['hadir'] / $stats['total']) * 100) : 0 }}% dari total
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Terlambat -->
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Terlambat</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white" x-text="stats.terlambat">{{ $stats['terlambat'] }}</h3>
                    <p class="text-[10px] text-yellow-500 mt-1">Butuh perhatian</p>
                </div>
            </div>
        </div>
        
        <!-- Alpha -->
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Alpha</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white" x-text="stats.alpha">{{ $stats['alpha'] }}</h3>
                    <p class="text-[10px] text-red-500 mt-1">Tanpa keterangan</p>
                </div>
            </div>
        </div>

        <!-- Izin/Cuti -->
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="file-text" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Izin/Cuti</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white" x-text="stats.izin">{{ $stats['izin'] }}</h3>
                    <p class="text-[10px] text-blue-500 mt-1">Izin/Sakit/Cuti</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Filters with AJAX -->
    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-navy-800 dark:text-white flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4 text-navy-600 dark:text-gold-400"></i>
                Filter Data Presensi
            </h3>
            <button type="button" 
                    @click="resetFilters()" 
                    x-show="filters.start_date || filters.end_date || filters.teacher_id || filters.status" 
                    class="text-xs font-semibold text-red-600 dark:text-red-400 hover:underline flex items-center gap-1 transition-all">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                Reset Filter
            </button>
        </div>
        <form @submit.prevent="filterData" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Start Date -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Dari Tanggal</label>
                <div class="relative">
                    <i data-lucide="calendar" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="date" 
                           x-model="filters.start_date" 
                           @change="filterData"
                           class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                </div>
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Sampai Tanggal</label>
                <div class="relative">
                    <i data-lucide="calendar" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="date" 
                           x-model="filters.end_date" 
                           @change="filterData"
                           class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                </div>
            </div>

            <!-- Teacher Dropdown (Modern) -->
            <div x-data="{ open: false }" @click.outside="open = false">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Guru</label>
                <div class="relative">
                    <button type="button" 
                            @click="open = !open"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 flex items-center justify-between hover:border-navy-300 dark:hover:border-navy-600 transition-colors">
                        <span class="truncate" x-text="getTeacherName()">Semua Guru</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0 ml-2" :class="{'rotate-180': open}"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                         class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden max-h-60"
                         x-cloak>
                        
                        <div class="p-2 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 sticky top-0">
                            <div class="relative">
                                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                                <input type="text" 
                                       x-model="teacherSearch" 
                                       placeholder="Cari guru..." 
                                       class="w-full pl-9 pr-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                            </div>
                        </div>
                        
                        <div class="overflow-y-auto max-h-40">
                            <button type="button" 
                                    @click="selectTeacher(null); open = false; filterData()"
                                    class="w-full px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2"
                                    :class="!filters.teacher_id ? 'bg-navy-50 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 font-semibold' : 'text-slate-700 dark:text-slate-300'">
                                <i data-lucide="list" class="w-4 h-4" x-show="!filters.teacher_id"></i>
                                <span class="w-2 h-2 bg-slate-400 rounded-full" x-show="filters.teacher_id"></span>
                                Semua Guru
                            </button>
                            
                            <template x-for="teacher in filteredTeachers" :key="teacher.id">
                                <button type="button" 
                                        @click="selectTeacher(teacher.id); open = false; filterData()"
                                        class="w-full px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2"
                                        :class="filters.teacher_id == teacher.id ? 'bg-navy-50 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 font-semibold' : 'text-slate-700 dark:text-slate-300'">
                                    <i data-lucide="check" class="w-4 h-4 text-navy-600 dark:text-gold-400" x-show="filters.teacher_id == teacher.id"></i>
                                    <span class="w-2 h-2 bg-slate-400 rounded-full" x-show="filters.teacher_id != teacher.id"></span>
                                    <span x-text="teacher.name" class="truncate"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Dropdown (Modern) -->
            <div x-data="{ open: false }" @click.outside="open = false">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Status</label>
                <div class="relative">
                    <button type="button" 
                            @click="open = !open"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 flex items-center justify-between hover:border-navy-300 dark:hover:border-navy-600 transition-colors">
                        <span x-text="getStatusText()">Semua Status</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0 ml-2" :class="{'rotate-180': open}"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                         class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden"
                         x-cloak>
                        
                        <button type="button" 
                                @click="selectStatus(null); open = false; filterData()"
                                class="w-full px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2"
                                :class="!filters.status ? 'bg-navy-50 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 font-semibold' : 'text-slate-700 dark:text-slate-300'">
                            <i data-lucide="list" class="w-4 h-4" x-show="!filters.status"></i>
                            <span class="w-2 h-2 bg-slate-400 rounded-full" x-show="filters.status"></span>
                            Semua Status
                        </button>
                        
                        <template x-for="status in ['Hadir', 'Tepat Waktu', 'Terlambat', 'Izin', 'Alpha']" :key="status">
                            <button type="button" 
                                     @click="selectStatus(status); open = false; filterData()"
                                     class="w-full px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2"
                                     :class="filters.status === status ? 'bg-navy-50 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 font-semibold' : 'text-slate-700 dark:text-slate-300'">
                                <i data-lucide="check" class="w-4 h-4 text-navy-600 dark:text-gold-400" x-show="filters.status === status"></i>
                                <span class="w-2 h-2 rounded-full" 
                                      :class="{
                                          'bg-green-500': status === 'Hadir' || status === 'Tepat Waktu',
                                          'bg-yellow-500': status === 'Terlambat',
                                          'bg-blue-500': status === 'Izin',
                                          'bg-red-500': status === 'Alpha'
                                      }" 
                                      x-show="filters.status !== status"></span>
                                <span x-text="status"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <!-- Loading Indicator -->
    <div x-show="loading" class="flex justify-center py-12" x-cloak>
        <div class="flex items-center gap-3">
            <div class="w-6 h-6 border-2 border-navy-800 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm text-slate-600 dark:text-slate-400">Memuat data...</span>
        </div>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden" x-show="!loading">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Guru</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Masuk</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Keluar</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    <template x-for="att in attendances" :key="att.id">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 text-sm">
                                <p class="font-medium text-navy-800 dark:text-white" x-text="formatDate(att.date)"></p>
                                <p class="text-xs text-slate-500" x-text="formatDay(att.date)"></p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img x-show="att.user?.photo_url"
                                         :src="att.user?.photo_url || ''"
                                         :alt="att.user?.name || '?'"
                                         class="w-10 h-10 rounded-full object-cover border-2 border-white dark:border-slate-700 shadow-sm flex-shrink-0"
                                         x-on:error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='flex'">
                                    <div x-show="!att.user?.photo_url"
                                         class="w-10 h-10 rounded-full bg-gradient-to-br from-gold-400 to-gold-500 flex items-center justify-center text-navy-900 font-bold text-xs flex-shrink-0">
                                        <span x-text="att.user?.name?.charAt(0)?.toUpperCase() || '?'"></span>
                                    </div>
                                    <span class="text-sm font-medium text-navy-800 dark:text-white truncate" x-text="att.user?.name || '-'"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 font-mono">
                                <span x-text="formatTime(att.check_in)"></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 font-mono">
                                <span x-text="formatTime(att.check_out)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                      :class="getStatusClass(att.status)"
                                      x-text="att.status"></span>
                            </td>
                        </tr>
                    </template>
                    
                    <!-- Empty State -->
                    <template x-if="attendances.length === 0 && !loading">
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <i data-lucide="inbox" class="w-12 h-12 text-slate-300 dark:text-slate-600"></i>
                                    <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada data presensi</p>
                                    <p class="text-xs text-slate-400">Coba ubah filter pencarian</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- AJAX Pagination -->
        <div x-show="pagination && pagination.links && pagination.links.length > 3" class="p-4 border-t border-slate-200 dark:border-slate-700" x-cloak>
            <nav class="flex items-center justify-center gap-1">
                <!-- Previous -->
                <template x-if="pagination.prev_page_url">
                    <button @click="loadPage(pagination.prev_page_url)" 
                            class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-1">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                </template>
                
                <!-- Page Numbers -->
                <template x-for="link in pagination.links" :key="link.label">
                    <template x-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')">
                        <button @click="loadPage(link.url)"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors min-w-[40px]"
                                :class="link.active 
                                    ? 'bg-navy-800 text-white dark:bg-gold-500 dark:text-navy-900' 
                                    : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                x-text="link.label.replace('&laquo;', '').replace('&raquo;', '').trim()"></button>
                    </template>
                </template>
                
                <!-- Next -->
                <template x-if="pagination.next_page_url">
                    <button @click="loadPage(pagination.next_page_url)" 
                            class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-1">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </template>
            </nav>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div x-show="toast.show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-2"
     class="fixed top-6 right-6 z-[110] flex items-center gap-3 px-5 py-4 rounded-xl shadow-2xl"
     :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
     x-cloak>
    <i :data-lucide="toast.type === 'success' ? 'check-circle' : 'alert-circle'" class="w-5 h-5"></i>
    <p class="text-sm font-medium" x-text="toast.message"></p>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('attendanceApp', () => ({
            // Initial data from server
            attendances: {!! json_encode($transformedItems ?? ($attendances->items() ?? [])) !!},
            stats: {!! json_encode($stats ?? ['total' => 0, 'hadir' => 0, 'terlambat' => 0, 'alpha' => 0, 'izin' => 0]) !!},
            pagination: {!! json_encode($attendances ?? null) !!},
            teachers: {!! json_encode($teachers ?? []) !!},
            statuses: {!! json_encode($statuses ?? []) !!},
            
            // Filters
            filters: {
                start_date: '{{ request("start_date") }}',
                end_date: '{{ request("end_date") }}',
                teacher_id: '{{ request("teacher_id") }}',
                status: '{{ request("status") }}'
            },
            
            // UI State
            loading: false,
            teacherSearch: '',
            toast: { show: false, message: '', type: 'success' },
            
            init() {
                if (window.lucide) lucide.createIcons();
            },
            
            // Get teacher name by ID
            getTeacherName() {
                if (!this.filters.teacher_id) return 'Semua Guru';
                const teacher = this.teachers.find(t => t.id == this.filters.teacher_id);
                return teacher ? teacher.name : 'Semua Guru';
            },
            
            // Get status display text
            getStatusText() {
                return this.filters.status || 'Semua Status';
            },
            
            // Select teacher
            selectTeacher(id) {
                this.filters.teacher_id = id;
            },
            
            // Select status
            selectStatus(status) {
                this.filters.status = status;
            },
            
            // Get filtered teachers list
            get filteredTeachers() {
                if (!this.teacherSearch) return this.teachers;
                return this.teachers.filter(t => 
                    t.name.toLowerCase().includes(this.teacherSearch.toLowerCase())
                );
            },
            
            // Reset all filters
            resetFilters() {
                this.filters = {
                    start_date: '',
                    end_date: '',
                    teacher_id: null,
                    status: null
                };
                this.teacherSearch = '';
                this.filterData();
            },
            
            // Build query params for AJAX
            buildParams(page = null) {
                const params = new URLSearchParams();
                if (this.filters.start_date) params.append('start_date', this.filters.start_date);
                if (this.filters.end_date) params.append('end_date', this.filters.end_date);
                if (this.filters.teacher_id) params.append('teacher_id', this.filters.teacher_id);
                if (this.filters.status) params.append('status', this.filters.status);
                if (page && page > 1) params.append('page', page);
                params.append('ajax', '1');
                return params;
            },
            
            // Fetch data via AJAX
            async fetchData(page = null) {
                this.loading = true;
                const baseUrl = '{{ route("attendance.history") }}';
                try {
                    const response = await fetch(`${baseUrl}?${this.buildParams(page)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Network error');

                    const data = await response.json();
                    this.attendances = data.data || [];
                    this.stats = data.stats || this.stats;
                    this.pagination = data;

                } catch (error) {
                    console.error('Fetch error:', error);
                    this.showToast('Gagal memuat data', 'error');
                } finally {
                    this.loading = false;
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                }
            },

            // Filter data — reset page
            async filterData() {
                await this.fetchData(null);
            },

            // Load page
            async loadPage(url) {
                const pageMatch = url.match(/[?&]page=(\d+)/);
                const page = pageMatch ? parseInt(pageMatch[1]) : 1;
                await this.fetchData(page);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            
            // Get export URL with current filters
            getExportUrl() {
                const params = new URLSearchParams();
                if (this.filters.start_date) params.append('start_date', this.filters.start_date);
                if (this.filters.end_date) params.append('end_date', this.filters.end_date);
                if (this.filters.teacher_id) params.append('teacher_id', this.filters.teacher_id);
                if (this.filters.status) params.append('status', this.filters.status);
                
                const baseUrl = '{{ route("attendance.export") }}';
                return params.toString() ? `${baseUrl}?${params}` : baseUrl;
            },
            
            // Format date for display
            formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            },
            
            // Format day name
            formatDay(dateString) {
                if (!dateString) return '';
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const date = new Date(dateString);
                return days[date.getDay()];
            },

            // Format time display clean
            formatTime(timeStr) {
                if (!timeStr || timeStr === '-') return '-';
                if (typeof timeStr === 'string' && timeStr.includes('WIB')) return timeStr;
                if (typeof timeStr === 'string' && timeStr.includes('T')) {
                    const d = new Date(timeStr);
                    if (!isNaN(d.getTime())) {
                        const h = String(d.getHours()).padStart(2, '0');
                        const m = String(d.getMinutes()).padStart(2, '0');
                        return `${h}:${m} WIB`;
                    }
                }
                const match = String(timeStr).match(/(\d{2}:\d{2})/);
                return match ? `${match[1]} WIB` : `${timeStr} WIB`;
            },
            
            // Get status badge class
            getStatusClass(status) {
                const classes = {
                    'Hadir': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                    'Tepat Waktu': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                    'Terlambat': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                    'Izin': 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                    'Sakit': 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                    'Cuti': 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                    'Alpha': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                };
                return classes[status] || 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
            },
            
            // Show toast notification
            showToast(message, type = 'success') {
                this.toast = { show: true, message, type };
                setTimeout(() => {
                    this.toast.show = false;
                }, 4000);
            }
        }));
    });
    
    // Init icons
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });
</script>

<style>
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    [x-cloak] { display: none !important; }
</style>
@endsection