<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ICB CT') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.7/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@1.7.0/dist/umd/lucide.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .dark ::-webkit-scrollbar-thumb { background: #334155; }
        
        /* Animations */
        .fade-in { animation: fadeIn 0.4s ease-out; }
        .slide-up { animation: slideUp 0.4s ease-out; }
        .slide-in-left { animation: slideInLeft 0.3s ease-out; }
        .scale-in { animation: scaleIn 0.2s ease-out; }
        .bounce-in { animation: bounceIn 0.5s ease-out; }
        
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideInLeft { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        @keyframes bounceIn { 
            0% { opacity: 0; transform: scale(0.3); }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        /* Hover Glow Effect */
        .hover-glow { transition: all 0.3s ease; }
        .hover-glow:hover { 
            box-shadow: 0 0 20px rgba(250, 204, 21, 0.3);
            transform: translateY(-2px);
        }
        
        /* Nav Item Animation */
        .nav-item { position: relative; overflow: hidden; }
        .nav-item::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }
        .nav-item:hover::before { left: 100%; }
        
        /* ==========================================
           PULSE DOT ANIMATION - FIXED & PERMANENT
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
        
        /* Dropdown Animation */
        .dropdown-enter { animation: dropdownIn 0.25s ease-out; }
        @keyframes dropdownIn {
            from { opacity: 0; transform: translateY(-10px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        
        /* Card Hover Effect */
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .dark .card-hover:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        /* Button Ripple Effect */
        .btn-ripple {
            position: relative;
            overflow: hidden;
        }
        .btn-ripple::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            width: 0; height: 0;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        .btn-ripple:active::after {
            width: 300px; height: 300px;
        }

        /* ==========================================
           ICON CLICK ANIMATION - NEW
           ========================================== */
        .icon-click {
            transition: transform 0.15s ease !important;
        }
        
        .icon-click:active {
            transform: scale(0.92) !important;
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>

    <!-- Favicon Dynamic dari AppSetting -->
    @php $appSettings = \App\Models\AppSetting::getInstance(); @endphp
    @if($appSettings && $appSettings->app_favicon)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $appSettings->app_favicon) }}?v={{ time() }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('storage/' . $appSettings->app_favicon) }}?v={{ time() }}">
    @endif
</head>

<body class="h-full bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-slate-100 transition-colors duration-300"
      x-data="{ 
          sidebarOpen: false, 
          darkMode: (() => { 
              try { 
                  return localStorage.getItem('theme') === 'dark' || 
                         (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
              } catch(e) { return false; } 
          })(),
          profileOpen: false,
          spinAnim: false
      }"
      :class="{ 'dark': darkMode }"
      x-init="
          $watch('darkMode', val => {
              try { localStorage.setItem('theme', val ? 'dark' : 'light'); } catch(e) {}
              if (val) document.documentElement.classList.add('dark');
              else document.documentElement.classList.remove('dark');
          });
          if (darkMode) document.documentElement.classList.add('dark');
          Alpine.store('toast', {
              items: [],
              show(message, type = 'success') {
                  const id = Date.now();
                  this.items.push({ id, message, type });
                  setTimeout(() => this.items = this.items.filter(i => i.id !== id), 5000);
              }
          });
          void 0;
      ">



    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm">
    </div>

    <!-- ========================================== -->
    <!-- SIDEBAR (Optimized Size)                   -->
    <!-- ========================================== -->
    <aside id="sidebar" 
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed top-0 left-0 z-50 flex h-screen w-64 flex-col bg-white dark:bg-navy-900 border-r border-slate-200 dark:border-slate-800 
                  transition-transform duration-300 ease-out">
        
        @php
            $appSettings = \App\Models\AppSetting::getInstance();
        @endphp

        <!-- Logo -->
        <div class="flex items-center gap-3 h-16 px-5 border-b border-slate-200 dark:border-slate-800">
            @if($appSettings->app_logo)
                <img src="{{ asset('storage/' . $appSettings->app_logo) }}" class="w-9 h-9 object-contain">
            @else
                <div class="w-9 h-9 bg-gradient-to-br from-gold-400 to-gold-500 rounded-lg flex items-center justify-center shadow-lg shadow-gold-400/30 hover-glow">
                    <i data-lucide="layers" class="w-5 h-5 text-navy-900"></i>
                </div>
            @endif
            <div>
                <h1 class="font-bold text-base text-navy-800 dark:text-white leading-tight">
                    {{ $appSettings->app_name ?? 'ICB CT' }}
                </h1>
                <p class="text-[9px] text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ Auth::user()->isAdmin() ? 'Presensi Guru' : 'Portal Guru' }}</p>
            </div>
        </div>

        <!-- Navigation -->
        @if(Auth::user()->isAdmin())
        <nav class="flex-1 px-3 py-6 space-y-6 overflow-y-auto no-scrollbar">
            
            <!-- MENU UTAMA -->
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Menu Utama</p>
                
                <a href="{{ route('dashboard') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('dashboard') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <!-- DATA MASTER -->
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Data Master</p>
                
                <a href="{{ route('teachers.index') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teachers.*') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span>Data Guru</span>
                </a>

                <a href="{{ route('classrooms.index') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('classrooms.*') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                    <i data-lucide="school" class="w-4 h-4"></i>
                    <span>Data Kelas</span>
                </a>

                <a href="{{ route('subjects.index') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('subjects.*') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                    <span>Mata Pelajaran</span>
                </a>

                <a href="{{ route('schedules.index') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('schedules.*') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                    <i data-lucide="calendar-clock" class="w-4 h-4"></i>
                    <span>Jadwal Kerja</span>
                </a>

                <a href="{{ route('teaching-schedules.index') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('teaching-schedules.*') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                    <i data-lucide="calendar-range" class="w-4 h-4"></i>
                    <span>Jadwal Mengajar</span>
                </a>
            </div>

            <!-- PRESENSI -->
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Presensi</p>
                
                <a href="{{ route('attendance.scan') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('attendance.scan') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                    <i data-lucide="scan-line" class="w-4 h-4"></i>
                    <span>Presensi Harian</span>
                </a>

                <a href="{{ route('class-attendance.scan') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('class-attendance.*') && !request()->routeIs('admin.class-attendance.*')
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                    <i data-lucide="scan" class="w-4 h-4"></i>
                    <span>Presensi Kelas</span>
                </a>

                <a href="{{ route('admin.class-attendance.manual') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('admin.class-attendance.manual*') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                    <span>Manual Presensi</span>
                </a>

                <a href="{{ route('attendance.history') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('attendance.history') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                    <i data-lucide="calendar-check" class="w-4 h-4"></i>
                    <span>Riwayat Presensi</span>
                </a>
            </div>

            <!-- FITUR LAINNYA -->
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Fitur Lainnya</p>
                
                <a href="{{ route('leaves.index') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('leaves.*') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Izin & Sakit</span>
                </a>

                <a href="{{ route('reports.index') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('reports.*') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                    <span>Laporan</span>
                </a>
            </div>

            <!-- PENGATURAN -->
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Pengaturan</p>
                
                <a href="{{ route('holidays.index') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('holidays.*') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                    <i data-lucide="calendar-off" class="w-4 h-4"></i>
                    <span>Kalender Libur</span>
                </a>

                <a href="{{ route('settings.index') }}" 
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('settings.*') 
                              ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30' 
                              : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                    <i data-lucide="settings" class="w-4 h-4"></i>
                    <span>Pengaturan</span>
                </a>
            </div>
        </nav>
        @else
        {{-- Menu guru --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto no-scrollbar">
            @include('layouts.partials.sidebar-nav')
        </nav>
        @endif

    </aside>

    <!-- ========================================== -->
    <!-- MAIN CONTENT                               -->
    <!-- ========================================== -->
    <div class="lg:ml-64 min-h-screen flex flex-col transition-all duration-300">
        
        <!-- Top Header -->
        <header class="sticky top-0 z-30 bg-white/80 dark:bg-navy-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-5 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors icon-click">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                
                <div>
                    <h2 class="text-base font-bold text-navy-800 dark:text-white">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium" id="realtime-clock">Memuat...</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Dark Mode Toggle -->
                <button @click="darkMode = !darkMode; spinAnim = true; setTimeout(() => spinAnim = false, 500)" 
                        class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-all hover:scale-110 group cursor-pointer focus:outline-none">
                    <i data-lucide="sun" x-show="!darkMode" class="w-5 h-5 text-slate-600 transition-transform" :class="spinAnim ? 'rotate-center' : ''"></i>
                    <i data-lucide="moon" x-show="darkMode" x-cloak class="w-5 h-5 text-gold-400 transition-transform" :class="spinAnim ? 'rotate-center' : ''"></i>
                </button>

                <!-- Notifications Dropdown -->
                <div class="relative" x-data="notificationDropdownAdmin()"
                     @click.outside="open = false"
                     x-init="init()">
                    <button @click="open = !open" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-all hover:scale-110 relative icon-click">
                        <i data-lucide="bell" class="w-5 h-5 text-slate-600 dark:text-slate-300"></i>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                        <span class="notification-badge absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                        @endif
                    </button>

                    <!-- Dropdown Content -->
                    <div x-show="open" 
                         x-transition:enter="dropdown-enter"
                         x-cloak
                         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-navy-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden z-50">
                        
                        <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                            <h3 class="text-sm font-bold text-navy-800 dark:text-white">Notifikasi</h3>
                            @if(Auth::user()->notifications->where('read_at', null)->count() > 0)
                            <button onclick="markAllNotifRead('admin')"
                                    class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 text-[11px] font-semibold hover:opacity-90 transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/></svg>
                                Tandai Dibaca
                            </button>
                            @endif
                        </div>

                        <div class="max-h-[400px] overflow-y-auto">
                            @forelse(Auth::user()->notifications->take(5) as $notification)
                                <div class="flex items-start border-b border-slate-100 dark:border-slate-700/50 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors notif-item">
                                    <a href="{{ $notification->data['url'] ?? '#' }}" class="flex-1 block p-4">
                                        <div class="flex gap-3 items-start">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 
                                                {{ ($notification->data['type'] ?? '') === 'success' ? 'bg-green-100 text-green-600' : 
                                                   (($notification->data['type'] ?? '') === 'error' ? 'bg-red-100 text-red-600' : 
                                                   'bg-blue-100 text-blue-600') }}">
                                                <i data-lucide="{{ ($notification->data['type'] ?? '') === 'success' ? 'check-circle' : 
                                                                 (($notification->data['type'] ?? '') === 'error' ? 'alert-circle' : 'bell') }}" class="w-4 h-4"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs text-navy-800 dark:text-slate-200 {{ $notification->read_at ? 'font-medium opacity-60' : 'font-bold' }} notif-text">
                                                    {{ $notification->data['message'] }}
                                                </p>
                                                <p class="text-[10px] text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </a>
                                    {{-- Check indicator --}}
                                    <div class="shrink-0 self-center mr-3 notif-check-wrap">
                                        @if(!$notification->read_at)
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
                                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400">
                                        <i data-lucide="bell-off" class="w-6 h-6"></i>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Tidak ada notifikasi baru</p>
                                </div>
                            @endforelse
                        </div>

                        @if(Auth::user()->notifications->count() > 5)
                            <a href="{{ route('notifications.index') }}" class="block p-3 text-center text-xs font-semibold text-navy-800 dark:text-white bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                Lihat {{ Auth::user()->notifications->count() - 5 }} Notifikasi Lainnya
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Profile Dropdown (With Photo) -->
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="flex items-center gap-2.5 p-1.5 pr-3 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-all hover:scale-105 icon-click">
                        <div class="relative">
                            <img src="{{ Auth::user()->photo_url }}" 
                                 class="w-8 h-8 rounded-full object-cover border-2 border-slate-200 dark:border-slate-700 shadow-sm">
                            <!-- PERMANENT GREEN DOT - FIXED -->
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-navy-900 rounded-full pulse-dot" style="display:inline-block!important; animation:pulse-green 2s infinite!important;"></span>
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-[11px] font-semibold text-navy-800 dark:text-white leading-tight">{{ Auth::user()->name }}</p>
                            <div class="flex items-center">
                                <span class="text-[9px] text-green-600 dark:text-green-400 font-medium">
                                    {{ Auth::user()->isAdmin() ? 'Operator' : (Auth::user()->subject ?? 'Guru') }}
                                </span>
                            </div>
                        </div>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         x-transition:enter="dropdown-enter"
                         x-cloak
                         class="absolute right-0 mt-2 w-72 bg-white dark:bg-navy-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        
                        <!-- Profile Header -->
                        <div class="p-4 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-slate-700 dark:to-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <img src="{{ Auth::user()->photo_url }}" 
                                         class="w-12 h-12 rounded-full object-cover border-2 border-gold-400/50 shadow-lg">
                                    <!-- PERMANENT GREEN DOT - FIXED -->
                                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 border-2 border-navy-800 dark:border-slate-700 rounded-full pulse-dot" style="display:inline-block!important; animation:pulse-green 2s infinite!important;"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-slate-300 truncate">{{ Auth::user()->email }}</p>
                                    <div class="flex items-center gap-1.5 mt-1.5">
                                        <span class="w-2 h-2 bg-green-400 rounded-full pulse-dot" style="display:inline-block!important; animation:pulse-green 2s infinite!important;"></span>
                                        <span class="text-[10px] text-green-400 font-medium">Online Sekarang</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-2">
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all hover:pl-5">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                    <span>Profile Saya</span>
                                </a>

                            @else
                                <a href="{{ route('teacher.profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all hover:pl-5">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                    <span>Profil Saya</span>
                                </a>
                            @endif

                            <hr class="my-2 border-slate-200 dark:border-slate-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all hover:pl-5">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-5 lg:p-6 overflow-x-hidden">
            @if (session('success') || session('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2000)" x-show="show"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2">
                @if (session('success'))
                <div class="mb-5 flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl slide-up">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                    <p class="text-sm text-green-800 dark:text-green-200">{{ is_array(session('success')) ? implode(' ', session('success')) : session('success') }}</p>
                </div>
                @endif

                @if (session('error'))
                <div class="mb-5 flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl slide-up">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                    <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
                @endif
            </div>
            @endif

            @yield('content')
        </main>
    </div>


    <script>
        // Initialize icons (Lucide v1: pass full icon map from UMD bundle)
        function initIcons() {
            if (!window.lucide || typeof lucide.createIcons !== 'function') return;
            try {
                const opts = {};
                if (lucide.icons && typeof lucide.icons === 'object') {
                    opts.icons = lucide.icons;
                }
                lucide.createIcons(opts);
            } catch (e) {
                console.warn('Lucide icons:', e);
            }
        }

        // Clock function
        function updateClock() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            const clockEl = document.getElementById('realtime-clock');
            if (clockEl) {
                clockEl.textContent = now.toLocaleDateString('id-ID', options);
            }
        }
        
        // Start clock
        updateClock();
        setInterval(updateClock, 1000);

        // Notification Dropdown Functions
        function notificationDropdownAdmin() {
            return {
                open: false,
                markRead() {
                    if (this.open) {
                        // Badge hanya dihapus manual via tombol "Tandai Dibaca"
                        // Tidak auto-remove saat buka dropdown
                    }
                },
                init() {
                    this.$watch('open', value => this.markRead());
                }
            };
        }

        // Initialize on DOM ready (after deferred lucide.min.js)
        document.addEventListener('DOMContentLoaded', function() {
            initIcons();
            requestAnimationFrame(function() { initIcons(); });
            
            // Add click animation to all icon buttons
            const iconButtons = document.querySelectorAll('.icon-click');
            iconButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    this.style.transform = 'scale(0.92)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
        });

        // Reinitialize icons after Alpine
        document.addEventListener('alpine:initialized', () => {
            initIcons();
        });

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
                    // Ganti tombol check jadi check-check (sudah dibaca)
                    const wrapper = btn.closest('.group');
                    if (wrapper) {
                        // Hapus blue dot
                        const blueDot = wrapper.querySelector('.bg-blue-500.rounded-full');
                        if (blueDot) blueDot.remove();

                        // Hilangkan bold dari teks
                        const boldText = wrapper.querySelector('.font-bold');
                        if (boldText) boldText.classList.remove('font-bold');

                        // Ganti tombol jadi icon check-check hijau
                        btn.outerHTML = `<div class="shrink-0 self-center mr-3 p-1.5"><i data-lucide="check-check" class="w-3.5 h-3.5 text-green-400"></i></div>`;
                        initIcons();

                        // Update badge count di bell icon
                        const badge = document.querySelector('.notification-badge');
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
                const response = await fetch('{{ route("notifications.api.unread") }}', {
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
</body>
</html>