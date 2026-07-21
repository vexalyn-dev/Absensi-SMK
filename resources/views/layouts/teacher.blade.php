<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Dashboard') - {{ config('app.name', 'ICB CT') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Reset body margin/padding */
        body { margin: 0; padding: 0; }

        /* Hide scrollbar visually but keep scroll functionality */
        .scrollbar-hide {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;     /* Firefox */
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;             /* Chrome, Safari, Opera */
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 dark:bg-slate-900 m-0 p-0">
    @php
        // Get teacher data for current user with error handling
        $teacherData = null;
        $teacherSubject = null;

        try {
            $teacherData = \App\Models\Teacher::where('user_id', auth()->id())->first();
            $teacherSubject = $teacherData ? $teacherData->major_specialty : null;
        } catch (\Exception $e) {
            // Model belum ada atau error lain
            $teacherSubject = null;
        }
    @endphp
    <div class="flex min-h-screen m-0 p-0" x-data="{ sidebarOpen: false }">
        
        <!-- Sidebar Overlay (Mobile) -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"
             style="display: none;"></div>

        <!-- Sidebar -->
        <aside id="sidebar" 
               class="fixed top-0 left-0 h-screen w-64 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 flex flex-col z-50 transition-transform duration-300 lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            
            <!-- Logo -->
            <div class="p-5 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    @php $appSettings = \App\Models\AppSetting::getInstance(); @endphp
                    @if($appSettings->app_logo)
                        <img src="{{ asset('storage/' . $appSettings->app_logo) }}" alt="Logo" class="w-10 h-10 object-contain">
                    @else
                        <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 rounded-lg flex items-center justify-center">
                            <i data-lucide="school" class="w-5 h-5 text-white"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-sm font-bold text-navy-800 dark:text-white">ICB CINTA TEKNIKA</h1>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Portal Guru</p>
                    </div>
                </div>
            </div>

            <!-- Menu -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <a href="{{ route('teacher.dashboard') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.dashboard') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('teacher.schedule') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.schedule') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="calendar-range" class="w-4 h-4"></i>
                    <span>Jadwal Mengajar</span>
                </a>

                <a href="{{ route('teacher.work-schedule') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.work-schedule') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="briefcase" class="w-4 h-4"></i>
                    <span>Jadwal Kerja</span>
                </a>

                <a href="{{ route('teacher.attendance') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.attendance') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="scan-line" class="w-4 h-4"></i>
                    <span>Presensi Harian</span>
                </a>

                <a href="{{ route('teacher.class-attendance') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.class-attendance') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="scan" class="w-4 h-4"></i>
                    <span>Presensi Kelas</span>
                </a>

                <a href="{{ route('teacher.history', ['type' => 'daily']) }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.history') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="history" class="w-4 h-4"></i>
                    <span>Riwayat</span>
                </a>

                <a href="{{ route('teacher.leave') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teacher.leave*') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700' }}">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Izin/Sakit</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 min-h-screen">
            <!-- Top Bar -->
            <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 sticky top-0 z-20">
                <div class="px-4 sm:px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <!-- Hamburger Menu (Mobile Only) -->
                        <button @click="sidebarOpen = true" 
                                class="lg:hidden p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                            <i data-lucide="menu" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
                        </button>
                        
                        <div>
                            <h2 class="text-lg font-bold text-navy-800 dark:text-white">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} pukul {{ now()->format('H.i') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <!-- Dark/Light Mode Toggle -->
                        <button onclick="toggleDarkMode()" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Toggle Dark Mode">
                            <i data-lucide="sun" class="w-5 h-5 text-slate-600 dark:text-slate-400 hidden dark:block"></i>
                            <i data-lucide="moon" class="w-5 h-5 text-slate-600 dark:text-slate-400 block dark:hidden"></i>
                        </button>

                        <!-- Notifikasi Dropdown -->
                        <div class="relative" x-data="notificationDropdown()" 
                        @click.outside="open = false"
                        x-init="init()">
                            
                            <button @click="open = !open" class="relative p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                <i data-lucide="bell" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
                                @if(auth()->user()->unreadCount() > 0)
                                <span class="notification-badge absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                @endif
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                 class="absolute right-0 mt-2 w-72 sm:w-80 md:w-96 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl overflow-hidden z-50 max-h-[400px] sm:max-h-[480px] overflow-y-auto scrollbar-hide"
                                 x-cloak>
                                
                                <!-- Header -->
                                <div class="p-3 sm:p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 sticky top-0 z-10">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-bold text-navy-800 dark:text-white">Notifikasi</h3>
                                        @php $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count(); @endphp
                                        @if($unreadCount > 0)
                                        <button onclick="markAllNotifRead('teacher')"
                                                class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 text-[11px] font-semibold hover:opacity-90 transition-opacity">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/></svg>
                                            Tandai Dibaca
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Notifications List -->
                                <div class="divide-y divide-slate-200 dark:divide-slate-700">
                                    @forelse(auth()->user()->notifications()->take(5)->get() as $notif)
                                    <div class="flex items-start hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors notif-item">
                                        <a href="{{ $notif->action_url ?? '#' }}" class="flex-1 p-3 min-w-0">
                                            <div class="flex items-start gap-2.5">
                                                <div class="w-8 h-8 rounded-lg {{ $notif->color }} flex items-center justify-center flex-shrink-0">
                                                    <i data-lucide="{{ $notif->icon }}" class="w-4 h-4"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs text-navy-800 dark:text-white line-clamp-1 notif-text {{ $notif->is_read ? 'font-medium opacity-60' : 'font-semibold' }}">{{ $notif->title }}</p>
                                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2 leading-relaxed">{{ $notif->message }}</p>
                                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-1">{{ $notif->time_ago }}</p>
                                                </div>
                                            </div>
                                        </a>
                                        {{-- Check indicator --}}
                                        <div class="shrink-0 self-center mr-3 notif-check-wrap">
                                            @if(!$notif->is_read)
                                            {{-- Belum dibaca: single check abu --}}
                                            <svg class="notif-check-single" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                            <svg class="notif-check-double hidden" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/></svg>
                                            @else
                                            {{-- Sudah dibaca: double check hijau --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/></svg>
                                            @endif
                                        </div>
                                    </div>
                                    @empty
                                    <div class="p-8 text-center">
                                        <i data-lucide="bell-off" class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-2"></i>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Tidak ada notifikasi</p>
                                    </div>
                                    @endforelse
                                </div>
                                
                                <!-- Footer -->
                                <div class="p-2.5 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 text-center sticky bottom-0">
                                    <a href="{{ route('teacher.notifications') }}" class="text-xs font-semibold text-navy-800 dark:text-gold-400 hover:underline">
                                        Lihat Semua Notifikasi
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="flex items-center gap-3 p-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                <img src="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0F172A&color=fff' }}" 
                                     class="w-9 h-9 rounded-full object-cover border-2 border-slate-200 dark:border-slate-600">
                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-semibold text-navy-800 dark:text-white truncate max-w-[150px]">{{ auth()->user()->name }}</p>
                                    <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400">Guru</span>
                                        @if($teacherSubject)
                                        <span class="px-1.5 py-0.5 bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400 rounded text-[9px] font-bold">
                                            {{ $teacherSubject }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 hidden sm:block"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden z-50"
                                 x-cloak>
                                
                                <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50">
                                    <p class="text-sm font-bold text-navy-800 dark:text-white truncate">{{ auth()->user()->name }}</p>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <span class="text-xs text-slate-500 dark:text-slate-400">Guru</span>
                                        @if($teacherSubject)
                                        <span class="px-2 py-0.5 bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400 rounded text-[10px] font-bold">
                                            {{ $teacherSubject }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="py-1">
                                    <a href="{{ route('teacher.profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                        <i data-lucide="user" class="w-4 h-4"></i>
                                        <span>Profil Saya</span>
                                    </a>
                                </div>
                                
                                <div class="border-t border-slate-200 dark:border-slate-700 py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                            <i data-lucide="log-out" class="w-4 h-4"></i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-4 sm:p-6">
                @yield('content')
                
                <!-- Spacer for Bottom Nav (Mobile Only) -->
                <div class="lg:hidden h-32"></div>
            </div>
        </main>
        
        <!-- Mobile Overlay (tidak digunakan lagi karena sidebar hidden di mobile) -->
    </div>

    <script>
        // Check saved theme on load — HARUS di atas segalanya
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }

        // Initialize Lucide icons
        function initIcons() {
            if (window.lucide) lucide.createIcons();
        }

        // Toggle Dark Mode
        function toggleDarkMode() {
            const html = document.documentElement;
            html.classList.toggle('dark');
            localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
            initIcons();
        }

        // Mark notification as read
        function markAsRead(notificationId) {
            fetch(`/teacher/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                if (response.ok) {
                    setTimeout(() => window.location.reload(), 400);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => initIcons());
        document.addEventListener('alpine:initialized', () => initIcons());

        // Mark single notification as read
        function markNotifRead(btn, id, type) {
            const url = type === 'admin'
                ? `/notifications/${id}/read`
                : `/teacher/notifications/${id}/read`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(res => {
                if (res.ok) {
                    const wrapper = btn.closest('.group');
                    if (wrapper) {
                        // Hilangkan bold dari teks
                        const boldText = wrapper.querySelector('.font-semibold');
                        if (boldText) boldText.classList.add('opacity-60');

                        // Ganti tombol jadi icon check-check hijau
                        btn.outerHTML = `<div class="shrink-0 self-center mr-3 p-1.5"><i data-lucide="check-check" class="w-3.5 h-3.5 text-green-400"></i></div>`;
                        initIcons();

                        // Update badge count di bell icon
                        const badge = document.querySelector('.notification-badge, [data-notif-count]');
                        if (badge) {
                            const count = parseInt(badge.textContent) - 1;
                            if (count <= 0) badge.remove();
                            else badge.textContent = count;
                        }
                    }
                }
            });
        }

        // Mark ALL notifications as read
        function markAllNotifRead(type) {
            const url = type === 'teacher'
                ? '/teacher/notifications/read-all'
                : '/notifications/mark-all-read';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(res => {
                if (res.ok) {
                    // Semua single check → double check hijau
                    document.querySelectorAll('.notif-check-wrap').forEach(wrap => {
                        const single = wrap.querySelector('.notif-check-single');
                        const double = wrap.querySelector('.notif-check-double');
                        if (single) single.classList.add('hidden');
                        if (double) double.classList.remove('hidden');
                    });

                    // Semua teks bold → normal
                    document.querySelectorAll('.notif-text').forEach(el => {
                        el.classList.remove('font-bold', 'font-semibold');
                        el.classList.add('font-medium', 'opacity-60');
                    });

                    // Hapus tombol "Tandai Dibaca"
                    const btn = document.querySelector('[onclick*="markAllNotifRead"]');
                    if (btn) btn.remove();

                    // Hapus badge notifikasi
                    document.querySelectorAll('.notification-badge, [data-notif-count]').forEach(b => b.remove());
                }
            });
        }
    </script>
    
    <!-- Alpine.js for dropdown -->
    <script>
        function notificationDropdown() {
            return {
                open: false,
                markRead() {
                    // Badge hanya dihapus manual via tombol "Tandai Dibaca"
                    // Tidak auto-remove saat buka dropdown
                },
                init() {
                    this.$watch('open', value => this.markRead());
                }
            };
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Real-time Notification System -->
    <script>
        let lastNotificationIds = new Set();

        // Initialize notification sound (iPhone-like)
        function initNotificationSound() {
            // Create iPhone-like notification sound using Web Audio API
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            
            // Create a simple notification tone
            const duration = 0.5;
            const now = audioContext.currentTime;
            
            const osc = audioContext.createOscillator();
            const gain = audioContext.createGain();
            
            osc.connect(gain);
            gain.connect(audioContext.destination);
            
            // iPhone notification tone frequencies
            osc.frequency.setValueAtTime(800, now);
            osc.frequency.setValueAtTime(1000, now + 0.1);
            
            gain.gain.setValueAtTime(0.3, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + duration);
            
            osc.start(now);
            osc.stop(now + duration);
        }

        // Play notification sound
        function playNotificationSound() {
            try {
                initNotificationSound();
            } catch (e) {
                console.log('Audio API not available:', e.message);
            }
        }

        // Show toast notification
        function showNotificationToast(notification) {
            const toast = document.createElement('div');
            toast.className = `
                fixed bottom-6 right-6 p-4 rounded-lg shadow-lg animate-bounce-in
                flex items-start gap-3 max-w-sm z-50
                ${notification.bg_color} transition-all duration-300
            `;
            
            toast.innerHTML = `
                <div class="flex-1">
                    <h3 class="font-semibold text-sm">${notification.title}</h3>
                    <p class="text-xs opacity-90 mt-1">${notification.message}</p>
                    <p class="text-xs opacity-75 mt-2">${notification.created_at}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="flex-shrink-0 opacity-50 hover:opacity-100">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            `;
            
            document.body.appendChild(toast);
            
            // Render lucide icons
            lucide.createIcons();
            
            // Auto remove after 6 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(20px)';
                setTimeout(() => toast.remove(), 300);
            }, 6000);
        }

        // Poll for new notifications
        async function checkNotifications() {
            try {
                const response = await fetch('{{ route("teacher.notifications.api.unread") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.notifications.length > 0) {
                    // Update notification badge if it exists
                    const notifBadge = document.querySelector('.notification-badge');
                    if (notifBadge) {
                        notifBadge.textContent = data.unreadCount;
                        if (data.unreadCount > 0) {
                            notifBadge.style.display = 'block';
                        }
                    }
                    
                    // Show new notifications only
                    data.notifications.forEach(notification => {
                        if (!lastNotificationIds.has(notification.id)) {
                            lastNotificationIds.add(notification.id);
                            playNotificationSound();
                            showNotificationToast(notification);
                        }
                    });
                }
            } catch (error) {
                console.error('Error checking notifications:', error);
            }
        }

        // Start polling on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check immediately
            checkNotifications();
            
            // Then check every 5 seconds
            setInterval(checkNotifications, 5000);
        });
    </script>
    
    @stack('scripts')
</body>
</html>