@extends('layouts.teacher')

@section('page-title', 'Presensi Kelas')

@section('content')
<div class="fade-in space-y-6" x-data="classAttendance()">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg">
            <i data-lucide="scan" class="w-6 h-6 text-white dark:text-navy-900"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Presensi Kelas</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
    </div>

    <!-- Progress Stats -->
    <div class="grid grid-cols-3 gap-3">
        <div class="card p-4">
            <p class="text-xs text-slate-500 dark:text-slate-400">Total Kelas</p>
            <p class="text-2xl font-bold text-navy-800 dark:text-white">{{ $totalClasses }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-500 dark:text-slate-400">Berlangsung</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $inProgressClasses }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-500 dark:text-slate-400">Selesai</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $completedClasses }}</p>
        </div>
    </div>

    <!-- Mode Toggle -->
    <div class="card p-6">
        <h3 class="text-base font-bold text-navy-800 dark:text-white mb-4">Mode Scan</h3>
        
        <div class="flex gap-2 p-1 bg-slate-100 dark:bg-slate-700 rounded-xl">
            <button @click="mode = 'in'" 
                    :class="mode === 'in' ? 'bg-green-500 text-white shadow-lg' : 'text-slate-600 dark:text-slate-400'"
                    class="flex-1 px-4 py-3 rounded-lg font-bold transition-all flex items-center justify-center gap-2">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                Masuk
            </button>
            <button @click="mode = 'out'"
                    :class="mode === 'out' ? 'bg-red-500 text-white shadow-lg' : 'text-slate-600 dark:text-slate-400'"
                    class="flex-1 px-4 py-3 rounded-lg font-bold transition-all flex items-center justify-center gap-2">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                Keluar
            </button>
        </div>

        <!-- Info Box -->
        <div class="mt-4 p-4 rounded-xl"
             :class="mode === 'in' ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800'">
            <div class="flex items-start gap-2">
                <i :data-lucide="mode === 'in' ? 'log-in' : 'log-out'" 
                   class="w-4 h-4 mt-0.5"
                   :class="mode === 'in' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"></i>
                <div class="text-xs">
                    <p class="font-bold mb-1"
                       :class="mode === 'in' ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300'">
                        Mode <span x-text="mode === 'in' ? 'Masuk' : 'Keluar'"></span>
                    </p>
                    <p :class="mode === 'in' ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400'">
                        <span x-text="mode === 'in' ? 'Scan QR saat masuk kelas untuk memulai presensi' : 'Scan QR saat keluar kelas untuk menyelesaikan presensi'"></span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Scanner -->
    <div class="card p-6">
        <h3 class="text-base font-bold text-navy-800 dark:text-white mb-4">Scan QR Code</h3>

        <!-- Camera Viewport -->
        <div class="flex justify-center">
        <div class="relative rounded-2xl overflow-hidden bg-slate-900" style="width:100%; max-width:360px; aspect-ratio:1/1;">
            <!-- Video feed -->
            <video id="qr-video" class="absolute inset-0 w-full h-full object-cover" autoplay playsinline muted></video>

            <!-- Idle overlay (shown before scan starts) -->
            <div id="qr-idle-overlay" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/90 text-white gap-3">
                <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center">
                    <i data-lucide="scan-line" class="w-8 h-8 text-white"></i>
                </div>
                <p class="text-sm font-medium text-slate-300">Tekan tombol untuk mulai scan</p>
            </div>

            <!-- Scan box overlay (hidden until scanning) -->
            <div id="qr-scan-overlay" class="absolute inset-0 hidden">
                <!-- Dark edges -->
                <div class="absolute inset-0 bg-black/50"></div>
                <!-- Clear scan window -->
                <div id="qr-scan-box" class="absolute" style="top:50%;left:50%;transform:translate(-50%,-50%);width:220px;height:220px;">
                    <!-- Transparent cutout via box-shadow trick -->
                    <div class="absolute inset-0 rounded-lg" style="box-shadow: 0 0 0 9999px rgba(0,0,0,0.5);"></div>
                    <!-- Corner brackets -->
                    <span class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-gold-400 rounded-tl-lg"></span>
                    <span class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-gold-400 rounded-tr-lg"></span>
                    <span class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-gold-400 rounded-bl-lg"></span>
                    <span class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-gold-400 rounded-br-lg"></span>
                    <!-- Laser sweep -->
                    <div class="qr-laser absolute left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-gold-400 to-transparent" style="top:0;"></div>
                </div>
                <p class="absolute bottom-6 left-0 right-0 text-center text-xs text-white/70">Arahkan QR Code ke dalam kotak</p>
            </div>
        </div>
        </div><!-- /justify-center -->

        <div class="flex gap-2 mt-4 max-w-sm mx-auto">
            <button @click="startScanner()" x-show="!scanning"
                    class="flex-1 px-4 py-3 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl font-bold flex items-center justify-center gap-2 transition-all hover:opacity-90">
                <i data-lucide="camera" class="w-4 h-4"></i>
                Mulai Scan
            </button>
            <button @click="stopScanner()" x-show="scanning"
                    class="flex-1 px-4 py-3 bg-red-500 text-white rounded-xl font-bold flex items-center justify-center gap-2 transition-all hover:bg-red-600">
                <i data-lucide="square" class="w-4 h-4"></i>
                Stop Scan
            </button>
        </div>
    </div>

    <!-- Result Toast -->
    <div x-show="showResult" x-transition class="card p-6 border-2" 
         :class="resultSuccess ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-red-500 bg-red-50 dark:bg-red-900/20'">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                 :class="resultSuccess ? 'bg-green-500' : 'bg-red-500'">
                <i :data-lucide="resultSuccess ? 'check' : 'x'" class="w-5 h-5 text-white"></i>
            </div>
            <div class="flex-1">
                <p class="font-bold" :class="resultSuccess ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300'"
                   x-text="resultMessage"></p>
                <div x-show="resultData" class="mt-2 space-y-1 text-xs"
                     :class="resultSuccess ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400'">
                    <template x-if="resultData?.classroom">
                        <p><strong>Kelas:</strong> <span x-text="resultData.classroom"></span></p>
                    </template>
                    <template x-if="resultData?.subject">
                        <p><strong>Mapel:</strong> <span x-text="resultData.subject"></span></p>
                    </template>
                    <template x-if="resultData?.duration">
                        <p><strong>Durasi:</strong> <span x-text="resultData.duration"></span></p>
                    </template>
                    <template x-if="resultData?.status">
                        <p><strong>Status:</strong> <span x-text="resultData.status"></span></p>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Hari Ini -->
    <div class="card p-6">
        <h3 class="text-base font-bold text-navy-800 dark:text-white mb-4">Jadwal Hari Ini</h3>
        <div class="space-y-3">
            @foreach($schedules as $schedule)
            @php
                $att = $schedule->classAttendances->first();
                $isComplete = $att && $att->isComplete();
                $isInProgress = $att && $att->check_in_time && !$att->check_out_time;
                $isPending = !$att;
            @endphp
            <div class="p-4 rounded-xl border-2 transition-all"
                 :class="{
                     'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800': {{ $isComplete ? 'true' : 'false' }},
                     'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800': {{ $isInProgress ? 'true' : 'false' }},
                     'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700': {{ $isPending ? 'true' : 'false' }}
                 }">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                             :class="{
                                 'bg-green-100 dark:bg-green-900/30': {{ $isComplete ? 'true' : 'false' }},
                                 'bg-blue-100 dark:bg-blue-900/30': {{ $isInProgress ? 'true' : 'false' }},
                                 'bg-slate-200 dark:bg-slate-600': {{ $isPending ? 'true' : 'false' }}
                             }">
                            @if($isComplete)
                                <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                            @elseif($isInProgress)
                                <i data-lucide="clock" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                            @else
                                <i data-lucide="circle" class="w-5 h-5 text-slate-500 dark:text-slate-400"></i>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-bold text-navy-800 dark:text-white">
                                {{ $schedule->classroom->name ?? '-' }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $schedule->subject->name ?? '-' }} • Jam ke-{{ $schedule->period }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                            </p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold"
                          :class="{
                              'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400': {{ $isComplete ? 'true' : 'false' }},
                              'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400': {{ $isInProgress ? 'true' : 'false' }},
                              'bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-400': {{ $isPending ? 'true' : 'false' }}
                          }">
                        @if($isComplete)
                            Selesai
                        @elseif($isInProgress)
                            Berlangsung
                        @else
                            Belum
                        @endif
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════ -->
    <!--  Shared Space Modal                                              -->
    <!-- ══════════════════════════════════════════════════════════════════ -->
    <div x-show="showSharedSpaceModal"
         x-cloak
         class="fixed inset-0 z-[999]"
         style="display:none;"
         @keydown.escape.window="showSharedSpaceModal=false">

        <!-- Blurred backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
             @click="showSharedSpaceModal = false"></div>

        <!-- Sheet wrapper -->
        <div class="absolute bottom-0 left-0 right-0 sm:inset-0 sm:flex sm:items-center sm:justify-center sm:p-6 pointer-events-none">
            <div class="pointer-events-auto w-full sm:max-w-md bg-white dark:bg-slate-900 rounded-t-[2rem] sm:rounded-[2rem] shadow-2xl flex flex-col"
                 style="max-height:93dvh; max-height:93vh;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-y-full opacity-0 sm:translate-y-4 sm:scale-95"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
                 x-transition:leave-end="translate-y-full opacity-0 sm:translate-y-4 sm:scale-95"
                 @click.stop>

                <!-- ── Drag pill ── -->
                <div class="flex-shrink-0 flex justify-center pt-3 pb-0 sm:hidden rounded-t-[2rem] bg-white dark:bg-slate-900">
                    <div class="w-9 h-1 bg-slate-300 dark:bg-slate-600 rounded-full"></div>
                </div>

                <!-- ── Header ── -->
                <div class="flex-shrink-0 px-5 pt-4 pb-4 bg-white dark:bg-slate-900 rounded-t-[2rem] sm:rounded-t-[2rem]">
                    <!-- Location badge -->
                    <div class="flex items-center gap-2 mb-3">
                        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold"
                             :class="mode === 'in'
                                 ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                                 : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'">
                            <span class="w-1.5 h-1.5 rounded-full animate-pulse"
                                  :class="mode === 'in' ? 'bg-emerald-500' : 'bg-red-500'"></span>
                            <span x-text="mode === 'in' ? 'SCAN MASUK' : 'SCAN KELUAR'"></span>
                        </div>
                    </div>

                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                                 :class="mode==='in' ? 'bg-gradient-to-br from-emerald-400 to-teal-500 shadow-lg shadow-emerald-500/30' : 'bg-gradient-to-br from-red-400 to-rose-500 shadow-lg shadow-red-500/30'">
                                <!-- building-2 icon -->
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white leading-tight truncate"
                                    x-text="mode==='in' ? 'Presensi Masuk Kelas' : 'Presensi Keluar Kelas'"></h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1 truncate">
                                    <!-- map-pin icon -->
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                                    </svg>
                                    <span x-text="sharedSpaceLocation || 'Ruangan Bersama'"></span>
                                </p>
                            </div>
                        </div>
                        <button @click="showSharedSpaceModal=false"
                                class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- ── Divider ── -->
                <div class="flex-shrink-0 h-px bg-slate-100 dark:bg-slate-800 mx-5"></div>

                <!-- ── Scrollable Body ── -->
                <div class="overflow-y-auto flex-1 overscroll-contain bg-white dark:bg-slate-900"
                     style="padding-bottom: 0.5rem;"
                     @click.stop>

                    <!-- MODE IN -->
                    <div x-show="mode === 'in'" class="px-5 pt-5 pb-3 space-y-5">

                        <!-- Step bar -->
                        <div class="flex items-center gap-1.5 mb-1">
                            <template x-for="(s, i) in [{l:'Kelas',d:!!sharedSpaceSelectedClass},{l:'Mapel',d:!!sharedSpaceSelectedSubject},{l:'Jam',d:!!sharedSpacePeriod}]" :key="i">
                                <div class="flex items-center gap-1.5" :class="i<2?'flex-1':''">
                                    <div class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-black flex-shrink-0 transition-all"
                                         :class="s.d?'bg-emerald-500 text-white':'bg-slate-200 dark:bg-slate-700 text-slate-500'">
                                        <template x-if="s.d">
                                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                        </template>
                                        <span x-show="!s.d" x-text="i+1"></span>
                                    </div>
                                    <span class="text-[10px] font-semibold hidden sm:block" :class="s.d?'text-emerald-600 dark:text-emerald-400':'text-slate-400'" x-text="s.l"></span>
                                    <div x-show="i<2" class="flex-1 h-px transition-colors" :class="s.d?'bg-emerald-400':'bg-slate-200 dark:bg-slate-700'"></div>
                                </div>
                            </template>
                        </div>

                        <!-- KELAS -->
                        <div x-data="{ openKelas: false, searchKelas: '' }">
                            <label class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                                <span class="w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-black"
                                      :class="sharedSpaceSelectedClass?'bg-emerald-500 text-white':'bg-slate-200 dark:bg-slate-700 text-slate-500'">
                                    <template x-if="sharedSpaceSelectedClass"><i data-lucide="check" class="w-2.5 h-2.5"></i></template>
                                    <span x-show="!sharedSpaceSelectedClass">1</span>
                                </span>
                                Kelas <span class="text-red-400 normal-case font-normal">*</span>
                            </label>

                            <!-- Trigger -->
                            <button type="button" @click="openKelas = !openKelas"
                                    class="w-full flex items-center gap-3 px-4 py-3.5 rounded-2xl border-2 transition-all duration-200 text-left"
                                    :class="openKelas ? 'border-emerald-500 bg-white dark:bg-slate-800 shadow-md'
                                        : (sharedSpaceSelectedClass ? 'border-emerald-300 dark:border-emerald-700/60 bg-emerald-50 dark:bg-emerald-900/10'
                                        : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60')">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                                     :class="sharedSpaceSelectedClass ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-700'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                         :class="sharedSpaceSelectedClass ? 'text-white' : 'text-slate-500'">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p x-show="!sharedSpaceSelectedClass" class="text-sm text-slate-400 dark:text-slate-500">Pilih kelas...</p>
                                    <p x-show="sharedSpaceSelectedClass" class="text-sm font-bold text-slate-900 dark:text-white truncate"
                                       x-text="sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.code
                                           ? sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.name + ' (' + sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.code + ')'
                                           : sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.name"></p>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <button x-show="sharedSpaceSelectedClass" type="button" @click.stop="sharedSpaceSelectedClass=''"
                                            class="w-5 h-5 rounded-full bg-slate-300 dark:bg-slate-600 flex items-center justify-center hover:bg-red-400 transition-colors">
                                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="openKelas?'rotate-180 text-emerald-500':''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                                </div>
                            </button>

                            <!-- Inline expand (tidak absolute, tidak terpotong overflow) -->
                            <div x-show="openKelas"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0 -translate-y-2"
                                 @click.away="openKelas=false; searchKelas=''"
                                 class="mt-1.5 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                <div class="p-2.5 border-b border-slate-100 dark:border-slate-700">
                                    <div class="relative">
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                                        <input type="text" x-model="searchKelas" placeholder="Cari kelas..."
                                               class="w-full pl-9 pr-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-700 rounded-xl border-0 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 text-slate-800 dark:text-white placeholder:text-slate-400"
                                               @click.stop @keydown.escape="openKelas=false">
                                    </div>
                                </div>
                                <div class="max-h-48 overflow-y-auto overscroll-contain py-1">
                                    <template x-for="cls in sharedSpaceClasses.filter(c => !searchKelas || c.name.toLowerCase().includes(searchKelas.toLowerCase()) || (c.code && c.code.toLowerCase().includes(searchKelas.toLowerCase())))" :key="cls.id">
                                        <button type="button"
                                                @click="sharedSpaceSelectedClass=cls.id; openKelas=false; searchKelas=''; $nextTick(()=>{ if(window.lucide) lucide.createIcons(); })"
                                                class="w-full flex items-center gap-3 px-3.5 py-2.5 text-left transition-colors"
                                                :class="sharedSpaceSelectedClass==cls.id ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'hover:bg-slate-50 dark:hover:bg-slate-700/60'">
                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 text-[10px] font-black"
                                                 :class="sharedSpaceSelectedClass==cls.id ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-500'"
                                                 x-text="cls.code ? cls.code.replace(/[^A-Z0-9]/gi,'').slice(-3) : cls.name.slice(0,2).toUpperCase()"></div>
                                            <span class="flex-1 text-sm truncate"
                                                  :class="sharedSpaceSelectedClass==cls.id ? 'font-bold text-emerald-700 dark:text-emerald-400' : 'font-medium text-slate-700 dark:text-slate-200'"
                                                  x-text="cls.code ? cls.name+' ('+cls.code+')' : cls.name"></span>
                                            <svg x-show="sharedSpaceSelectedClass==cls.id" class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                        </button>
                                    </template>
                                    <p x-show="sharedSpaceClasses.filter(c => !searchKelas || c.name.toLowerCase().includes(searchKelas.toLowerCase()) || (c.code && c.code.toLowerCase().includes(searchKelas.toLowerCase()))).length===0"
                                       class="text-center text-xs text-slate-400 py-6">Tidak ditemukan</p>
                                </div>
                            </div>
                        </div>

                        <!-- MAPEL -->
                        <div x-data="{ openMapel: false, searchMapel: '' }">
                            <label class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                                <span class="w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-black"
                                      :class="sharedSpaceSelectedSubject?'bg-emerald-500 text-white':'bg-slate-200 dark:bg-slate-700 text-slate-500'">
                                    <template x-if="sharedSpaceSelectedSubject"><svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg></template>
                                    <span x-show="!sharedSpaceSelectedSubject">2</span>
                                </span>
                                Mata Pelajaran <span class="text-red-400 normal-case font-normal">*</span>
                            </label>

                            <button type="button" @click="openMapel = !openMapel"
                                    class="w-full flex items-center gap-3 px-4 py-3.5 rounded-2xl border-2 transition-all duration-200 text-left"
                                    :class="openMapel ? 'border-emerald-500 bg-white dark:bg-slate-800 shadow-md'
                                        : (sharedSpaceSelectedSubject ? 'border-emerald-300 dark:border-emerald-700/60 bg-emerald-50 dark:bg-emerald-900/10'
                                        : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60')">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                                     :class="sharedSpaceSelectedSubject ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-700'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                         :class="sharedSpaceSelectedSubject ? 'text-white' : 'text-slate-500'">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p x-show="!sharedSpaceSelectedSubject" class="text-sm text-slate-400 dark:text-slate-500">Pilih mata pelajaran...</p>
                                    <p x-show="sharedSpaceSelectedSubject" class="text-sm font-bold text-slate-900 dark:text-white truncate"
                                       x-text="sharedSpaceSubjects.find(s=>s.id==sharedSpaceSelectedSubject)?.name"></p>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <button x-show="sharedSpaceSelectedSubject" type="button" @click.stop="sharedSpaceSelectedSubject=''"
                                            class="w-5 h-5 rounded-full bg-slate-300 dark:bg-slate-600 flex items-center justify-center hover:bg-red-400 transition-colors">
                                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="openMapel?'rotate-180 text-emerald-500':''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                                </div>
                            </button>

                            <div x-show="openMapel"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0 -translate-y-2"
                                 @click.away="openMapel=false; searchMapel=''"
                                 class="mt-1.5 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                <div class="p-2.5 border-b border-slate-100 dark:border-slate-700">
                                    <div class="relative">
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                                        <input type="text" x-model="searchMapel" placeholder="Cari mata pelajaran..."
                                               class="w-full pl-9 pr-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-700 rounded-xl border-0 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 text-slate-800 dark:text-white placeholder:text-slate-400"
                                               @click.stop @keydown.escape="openMapel=false">
                                    </div>
                                </div>
                                <div class="max-h-48 overflow-y-auto overscroll-contain py-1">
                                    <template x-for="subject in sharedSpaceSubjects.filter(s => !searchMapel || s.name.toLowerCase().includes(searchMapel.toLowerCase()))" :key="subject.id">
                                        <button type="button"
                                                @click="sharedSpaceSelectedSubject=subject.id; openMapel=false; searchMapel=''"
                                                class="w-full flex items-center gap-3 px-3.5 py-2.5 text-left transition-colors"
                                                :class="sharedSpaceSelectedSubject==subject.id ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'hover:bg-slate-50 dark:hover:bg-slate-700/60'">
                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 text-[10px] font-black"
                                                 :class="sharedSpaceSelectedSubject==subject.id ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-500'"
                                                 x-text="subject.name.slice(0,2).toUpperCase()"></div>
                                            <span class="flex-1 text-sm truncate"
                                                  :class="sharedSpaceSelectedSubject==subject.id ? 'font-bold text-emerald-700 dark:text-emerald-400' : 'font-medium text-slate-700 dark:text-slate-200'"
                                                  x-text="subject.name"></span>
                                            <svg x-show="sharedSpaceSelectedSubject==subject.id" class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                        </button>
                                    </template>
                                    <p x-show="sharedSpaceSubjects.filter(s => !searchMapel || s.name.toLowerCase().includes(searchMapel.toLowerCase())).length===0"
                                       class="text-center text-xs text-slate-400 py-6">Tidak ditemukan</p>
                                </div>
                            </div>
                        </div>

                        <!-- JAM PELAJARAN -->
                        <div x-data="{ viewMode: 'grid' }">
                            <div class="flex items-center justify-between mb-3">
                                <label class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <span class="w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-black transition-all duration-300"
                                          :class="sharedSpacePeriod?'bg-emerald-500 text-white scale-110':'bg-slate-200 dark:bg-slate-700 text-slate-500'">
                                        <template x-if="sharedSpacePeriod">
                                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                        </template>
                                        <span x-show="!sharedSpacePeriod">3</span>
                                    </span>
                                    Jam Ke- <span class="text-red-400 normal-case font-normal">*</span>
                                    <span x-show="sharedSpacePeriod"
                                          x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0"
                                          class="px-1.5 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-full text-[10px] font-black normal-case"
                                          x-text="'JP ' + sharedSpacePeriod"></span>
                                </label>
                                <!-- Toggle grid / list -->
                                <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl">
                                    <button type="button" @click="viewMode='grid'"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-200"
                                            :class="viewMode==='grid'?'bg-white dark:bg-slate-700 shadow-sm text-emerald-600':'text-slate-400'">
                                        <!-- grid icon -->
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>
                                        </svg>
                                    </button>
                                    <button type="button" @click="viewMode='list'"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-200"
                                            :class="viewMode==='list'?'bg-white dark:bg-slate-700 shadow-sm text-emerald-600':'text-slate-400'">
                                        <!-- list icon -->
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- GRID VIEW -->
                            <div x-show="viewMode==='grid'"
                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="grid grid-cols-6 gap-2">
                                <template x-for="jam in [1,2,3,4,5,6,7,8,9,10,11,12]" :key="'g'+jam">
                                    <button type="button" @click="sharedSpacePeriod = jam"
                                            class="relative flex flex-col items-center justify-center rounded-xl font-bold transition-all duration-150 active:scale-90 select-none py-2.5"
                                            :class="sharedSpacePeriod==jam
                                                ? 'bg-emerald-500 text-white shadow-md scale-105 ring-2 ring-white dark:ring-slate-900'
                                                : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600'">
                                        <span class="text-sm leading-none font-extrabold" x-text="jam"></span>
                                        <span class="text-[8px] leading-none mt-0.5 font-semibold" :class="sharedSpacePeriod==jam?'opacity-80':'opacity-40'">JP</span>
                                        <template x-if="sharedSpacePeriod==jam">
                                            <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-white rounded-full border-2 border-emerald-500 shadow-sm"></span>
                                        </template>
                                    </button>
                                </template>
                            </div>

                            <!-- LIST VIEW -->
                            <div x-show="viewMode==='list'"
                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-1"
                                 class="grid grid-cols-2 gap-1.5">
                                <template x-for="jam in [1,2,3,4,5,6,7,8,9,10,11,12]" :key="'l'+jam">
                                    <button type="button" @click="sharedSpacePeriod = jam"
                                            class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all duration-150 active:scale-95 text-left border"
                                            :class="sharedSpacePeriod==jam
                                                ? 'bg-emerald-500 border-emerald-500 text-white shadow-md'
                                                : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-emerald-300 dark:hover:border-emerald-700'">
                                        <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-black"
                                             :class="sharedSpacePeriod==jam?'bg-white/20':'bg-slate-200 dark:bg-slate-700'">
                                            <span x-text="jam"></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold leading-none" x-text="'Jam ke-'+jam"></p>
                                            <p class="text-[9px] mt-0.5 leading-none" :class="sharedSpacePeriod==jam?'text-white/70':'text-slate-400'">Jam Pelajaran</p>
                                        </div>
                                        <template x-if="sharedSpacePeriod==jam">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                        </template>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Summary chip -->
                        <div x-show="sharedSpaceSelectedClass && sharedSpaceSelectedSubject && sharedSpacePeriod"
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="flex items-start gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl border border-emerald-200/70 dark:border-emerald-800/50">
                            <div class="w-8 h-8 bg-emerald-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-white"></i>
                            </div>
                            <div class="flex-1 min-w-0 text-xs space-y-0.5">
                                <p class="font-bold text-emerald-700 dark:text-emerald-400 mb-1">Siap disimpan ✓</p>
                                <p class="text-slate-600 dark:text-slate-400 truncate">📚 <span class="font-semibold text-slate-800 dark:text-white" x-text="sharedSpaceSubjects.find(s=>s.id==sharedSpaceSelectedSubject)?.name"></span></p>
                                <p class="text-slate-600 dark:text-slate-400 truncate">🏫 <span class="font-semibold text-slate-800 dark:text-white" x-text="sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.code?sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.name+' ('+sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.code+')':sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.name"></span></p>
                                <p class="text-slate-600 dark:text-slate-400">⏰ Jam ke-<span class="font-semibold text-slate-800 dark:text-white" x-text="sharedSpacePeriod"></span> · <span x-text="sharedSpaceLocation"></span></p>
                            </div>
                        </div>
                        <div class="h-2"></div>
                    </div>

                    <!-- MODE OUT -->
                    <div x-show="mode === 'out'" class="px-5 pt-5 pb-3 space-y-3">
                        <template x-if="sharedSpaceActiveSessions.length > 0">
                            <div>
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Sesi berlangsung — pilih untuk selesaikan</p>
                                <div class="space-y-2.5">
                                    <template x-for="session in sharedSpaceActiveSessions" :key="session.id">
                                        <div class="relative rounded-2xl border-2 cursor-pointer overflow-hidden transition-all duration-200 active:scale-[.98]"
                                             :class="sharedSpaceSelectedSession==session.id?'border-emerald-500 bg-emerald-50/70 dark:bg-emerald-900/20 shadow-md shadow-emerald-500/10':'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60 hover:border-slate-300 dark:hover:border-slate-600'"
                                             @click="sharedSpaceSelectedSession = session.id">
                                            <div x-show="sharedSpaceSelectedSession==session.id" class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500 rounded-l-2xl"></div>
                                            <div class="flex items-center gap-3 p-4 pl-5">
                                                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 text-xs font-black" :class="sharedSpaceSelectedSession==session.id?'bg-emerald-500 text-white shadow-md shadow-emerald-500/25':'bg-slate-100 dark:bg-slate-700 text-slate-500'" x-text="session.classroom_name.slice(0,3).toUpperCase()"></div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold truncate" :class="sharedSpaceSelectedSession==session.id?'text-emerald-700 dark:text-emerald-400':'text-slate-900 dark:text-white'" x-text="session.classroom_name"></p>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate" x-text="session.subject_name+' · Jam ke-'+session.period"></p>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <span class="text-[10px] text-slate-400 flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i><span x-text="'Masuk '+session.check_in_time"></span></span>
                                                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full" :class="session.duration_minutes>=30?'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400':'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'" x-text="session.duration_minutes+' mnt'"></span>
                                                    </div>
                                                </div>
                                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all flex-shrink-0" :class="sharedSpaceSelectedSession==session.id?'border-emerald-500 bg-emerald-500':'border-slate-300 dark:border-slate-600'">
                                                    <i data-lucide="check" class="w-3.5 h-3.5 text-white" x-show="sharedSpaceSelectedSession==session.id"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-if="sharedSpaceActiveSessions.length === 0">
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="inbox" class="w-8 h-8 text-slate-400 dark:text-slate-500"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Tidak Ada Sesi Aktif</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Lakukan scan masuk terlebih dahulu</p>
                            </div>
                        </template>
                        <div class="h-2"></div>
                    </div>

                </div><!-- /scrollable body -->

                <!-- ── Footer ── -->
                <div class="flex-shrink-0 px-5 py-4 border-t border-slate-100 dark:border-slate-800"
                     style="padding-bottom:calc(1rem + env(safe-area-inset-bottom))">
                    <template x-if="mode === 'in'">
                        <button @click="submitSharedSpaceAttendance()"
                                :disabled="!sharedSpaceSelectedClass || !sharedSpaceSelectedSubject || !sharedSpacePeriod"
                                class="w-full py-4 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 transition-all duration-200
                                       bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white shadow-lg shadow-emerald-500/25
                                       disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none disabled:hover:bg-emerald-500">
                            <i data-lucide="log-in" class="w-4 h-4"></i>
                            Simpan Presensi Masuk
                        </button>
                    </template>
                    <template x-if="mode === 'out'">
                        <div>
                            <button x-show="sharedSpaceActiveSessions.length > 0"
                                    @click="submitSharedSpaceCheckOut()"
                                    :disabled="!sharedSpaceSelectedSession"
                                    class="w-full py-4 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 transition-all duration-200
                                           bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white shadow-lg shadow-emerald-500/25
                                           disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none">
                                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                Selesaikan Sesi Ini
                            </button>
                        </div>
                    </template>
                </div>

            </div>
        </div>
    </div>

    <!-- Dynamic Class Selection Modal -->
    <div x-show="showClassSelection" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6" @click.away="showClassSelection = false">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-navy-800 dark:text-white">Pilih Kelas</h3>
                <button @click="showClassSelection = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                Anda sedang di lokasi <span class="font-semibold text-navy-800 dark:text-white" x-text="selectedLocation"></span>. 
                Pilih kelas yang sedang Anda ajarkan:
            </p>
            
            <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                <template x-for="schedule in classSchedules" :key="schedule.id">
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer transition-all"
                         @click="selectClass(schedule)">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-bold text-navy-800 dark:text-white" x-text="schedule.classroom_name"></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400" x-text="schedule.subject"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-navy-800 dark:text-white" x-text="'Jam ke-' + schedule.period"></p>
                                <p class="text-[10px] text-slate-400" x-text="schedule.start_time + ' - ' + schedule.end_time"></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<!-- jsQR Library -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<script>
    // Scanner globals
    let _qrStream = null;
    let _qrScanning = false;
    let _qrAlpine = null;
    let _lastTickTime = 0;
    const _QR_INTERVAL_MS = 80; // ~12 fps decode — cukup cepat, tidak block UI
    const _qrCanvas = document.createElement('canvas');
    const _qrCtx = _qrCanvas.getContext('2d', { willReadFrequently: true });
    const _qrCanvas2 = document.createElement('canvas');
    const _qrCtx2 = _qrCanvas2.getContext('2d', { willReadFrequently: true });

    function _tryDecode(canvas, ctx, video, sx, sy, sw, sh, dw, dh) {
        canvas.width = dw; canvas.height = dh;
        ctx.drawImage(video, sx, sy, sw, sh, 0, 0, dw, dh);
        const img = ctx.getImageData(0, 0, dw, dh);
        return jsQR(img.data, dw, dh, { inversionAttempts: 'attemptBoth' });
    }

    function startQrVideo(alpineCtx) {
        _qrAlpine = alpineCtx;
        const video = document.getElementById('qr-video');
        const idle  = document.getElementById('qr-idle-overlay');
        const scanOverlay = document.getElementById('qr-scan-overlay');

        // Resolusi lebih rendah = startup lebih cepat di HP
        // ideal 1280 sudah cukup untuk QR scan, 1920 justru lambat di mobile
        navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: { ideal: 'environment' },
                width:  { ideal: 1280, min: 480 },
                height: { ideal: 720,  min: 360 },
            }
        })
        .then(stream => {
            _qrStream = stream;
            video.srcObject = stream;
            video.play();
            idle.classList.add('hidden');
            scanOverlay.classList.remove('hidden');
            _qrScanning = true;
            alpineCtx.scanning = true;
            _lastTickTime = 0;
            requestAnimationFrame(tickQr);
        })
        .catch(err => {
            console.error('Camera error:', err);
            alert('Gagal mengakses kamera. Pastikan izin kamera sudah diberikan.');
        });
    }

    function stopQrVideo(alpineCtx) {
        _qrScanning = false;
        if (_qrStream) {
            _qrStream.getTracks().forEach(t => t.stop());
            _qrStream = null;
        }
        const video = document.getElementById('qr-video');
        if (video) video.srcObject = null;
        document.getElementById('qr-idle-overlay')?.classList.remove('hidden');
        document.getElementById('qr-scan-overlay')?.classList.add('hidden');
        if (alpineCtx) alpineCtx.scanning = false;
    }

    function tickQr(ts) {
        if (!_qrScanning) return;

        // Throttle: decode max ~12x/detik agar tidak block JS thread
        if (ts - _lastTickTime < _QR_INTERVAL_MS) {
            requestAnimationFrame(tickQr);
            return;
        }
        _lastTickTime = ts;

        const video = document.getElementById('qr-video');
        if (!video || video.readyState < 2) { requestAnimationFrame(tickQr); return; }

        const vw = video.videoWidth, vh = video.videoHeight;
        if (!vw || !vh) { requestAnimationFrame(tickQr); return; }

        // Pass 1: full frame downsampled to 400px — cepat, cocok untuk QR jauh
        const scale = Math.min(1, 400 / vw);
        let code = _tryDecode(_qrCanvas, _qrCtx, video, 0, 0, vw, vh, Math.round(vw * scale), Math.round(vh * scale));

        // Pass 2: center crop 60% — QR dekat / miring
        if (!code) {
            const cx = Math.round(vw * 0.2), cy = Math.round(vh * 0.2);
            const cw = Math.round(vw * 0.6), ch = Math.round(vh * 0.6);
            const dw = Math.min(cw, 360);
            code = _tryDecode(_qrCanvas2, _qrCtx2, video, cx, cy, cw, ch, dw, Math.round(ch * dw / cw));
        }

        if (code && code.data) {
            stopQrVideo(_qrAlpine);
            if (_qrAlpine) _qrAlpine.processScan(code.data);
            return;
        }
        requestAnimationFrame(tickQr);
    }

    function classAttendance() {
        return {
            mode: 'in',
            scanning: false,
            showResult: false,
            resultSuccess: false,
            resultMessage: '',
            resultData: null,

            showClassSelection: false,
            selectedLocation: '',
            classSchedules: [],
            selectedScheduleId: null,
            scannedQrData: '',

            // Shared space state
            showSharedSpaceModal: false,
            sharedSpaceLocation: '',
            sharedSpaceLocationId: '',
            sharedSpaceClasses: [],
            sharedSpaceSubjects: [],
            sharedSpaceActiveSessions: [],   // sesi aktif untuk mode OUT
            sharedSpaceSelectedClass: '',
            sharedSpaceSelectedSubject: '',
            sharedSpacePeriod: '',
            sharedSpaceSelectedSession: '',  // attendance_id untuk mode OUT

            startScanner() {
                startQrVideo(this);
            },

            stopScanner() {
                stopQrVideo(this);
            },

            processScan(qrData) {
                this.scannedQrData = qrData;

                this._post('{{ route("teacher.class-attendance.scan") }}', { qr_data: qrData, mode: this.mode })
                .then(({ status, data }) => {
                    if (data.is_shared_space) {
                        this.showSharedSpaceModal       = true;
                        this.sharedSpaceLocation        = data.classroom?.name || '';
                        this.sharedSpaceLocationId      = data.classroom?.id || this.extractClassroomId(qrData);
                        this.sharedSpaceClasses         = data.all_classes || [];
                        this.sharedSpaceSubjects        = data.subjects || [];
                        this.sharedSpaceActiveSessions  = data.active_sessions || [];
                        this.sharedSpaceSelectedClass   = '';
                        this.sharedSpaceSelectedSubject = '';
                        this.sharedSpacePeriod          = '';
                        this.sharedSpaceSelectedSession = '';
                        setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 50);
                    } else if (data.schedules) {
                        this.showClassSelection = true;
                        this.selectedLocation   = data.message;
                        this.classSchedules     = data.schedules;
                        setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 50);
                    } else {
                        this.handleScanResponse(status, data);
                    }
                });
            },

            selectClass(schedule) {
                this.selectedScheduleId = schedule.id;
                this.showClassSelection = false;
                this.processScanWithSchedule();
            },

            processScanWithSchedule() {
                this._post('{{ route("teacher.class-attendance.scan") }}', {
                    qr_data:     this.scannedQrData,
                    mode:        this.mode,
                    schedule_id: this.selectedScheduleId
                })
                .then(({ status, data }) => { this.handleScanResponse(status, data); });
            },

            // Submit presensi MASUK shared space (ON-DEMAND)
            submitSharedSpaceAttendance() {
                if (!this.sharedSpaceSelectedClass || !this.sharedSpaceSelectedSubject || !this.sharedSpacePeriod) {
                    alert('Lengkapi kelas, mata pelajaran, dan jam ke- terlebih dahulu.');
                    return;
                }
                this._post('{{ route("teacher.class-attendance.save-shared") }}', {
                    classroom_id:          this.sharedSpaceLocationId,
                    selected_classroom_id: this.sharedSpaceSelectedClass,
                    subject_id:            this.sharedSpaceSelectedSubject,
                    period:                this.sharedSpacePeriod,
                    mode:                  'in',
                })
                .then(({ status, data }) => {
                    this.showSharedSpaceModal = false;
                    this.handleScanResponse(status, data);
                });
            },

            // Submit presensi KELUAR shared space
            submitSharedSpaceCheckOut() {
                if (!this.sharedSpaceSelectedSession) {
                    alert('Pilih sesi yang ingin diselesaikan.');
                    return;
                }
                this._post('{{ route("teacher.class-attendance.save-shared") }}', {
                    classroom_id:  this.sharedSpaceLocationId,
                    attendance_id: this.sharedSpaceSelectedSession,
                    mode:          'out',
                })
                .then(({ status, data }) => {
                    this.showSharedSpaceModal = false;
                    this.handleScanResponse(status, data);
                });
            },

            // ─── Safe fetch helper: selalu resolve, tidak pernah crash ───────
            _post(url, payload) {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                return fetch(url, {
                    method:  'POST',
                    headers: {
                        'Content-Type':     'application/json',
                        'X-CSRF-TOKEN':     csrf,
                        'Accept':           'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                })
                .then(res => {
                    const contentType = res.headers.get('content-type') || '';
                    if (contentType.includes('application/json')) {
                        return res.json().then(data => ({ status: res.status, data }));
                    }
                    // Server return HTML (mis. 419 CSRF expired, 500 debug page)
                    return res.text().then(text => ({
                        status: res.status,
                        data: {
                            success: false,
                            message: res.status === 419
                                ? 'Sesi habis, silakan refresh halaman.'
                                : `Server error (${res.status}). Coba refresh halaman.`,
                        }
                    }));
                })
                .catch(err => ({
                    status: 0,
                    data: { success: false, message: 'Tidak ada koneksi internet atau server tidak dapat dijangkau.' }
                }));
            },

            extractClassroomId(qrData) {
                try {
                    const parsed = JSON.parse(qrData);
                    if (parsed.classroom_id) return parsed.classroom_id;
                } catch (e) {}
                const parts = String(qrData).split('|');
                return parts[0] || null;
            },

            handleScanResponse(status, data) {
                this.showResult    = true;
                this.resultSuccess = (status >= 200 && status < 300) && data?.success;
                this.resultMessage = data?.message || 'Terjadi kesalahan sistem';
                this.resultData    = data?.data || null;

                if (this.resultSuccess) {
                    setTimeout(() => window.location.reload(), 2500);
                }
                if (window.lucide) lucide.createIcons();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });
</script>

<style>
    .qr-laser {
        animation: qrLaser 1.8s ease-in-out infinite;
    }
    @keyframes qrLaser {
        0%   { top: 0; opacity: 1; }
        50%  { top: calc(100% - 2px); opacity: 1; }
        100% { top: 0; opacity: 1; }
    }
    .fade-in { animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    [x-cloak] { display: none !important; }
</style>
@endsection
