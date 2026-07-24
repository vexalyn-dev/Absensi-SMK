@extends('layouts.teacher')

@section('page-title', 'Presensi Kelas')

@section('content')
<div class="fade-in space-y-6" x-data="classAttendance()">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
            <svg class="w-6 h-6 text-white dark:text-navy-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5zM13.5 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5z" />
            </svg>
        </div>
        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white truncate">Presensi Kelas</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 truncate">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
    </div>

    <!-- Progress Stats -->
    <div class="grid grid-cols-3 gap-2 sm:gap-4">
        <div class="card p-3 sm:p-4">
            <p class="text-[10px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 truncate">Total Kelas</p>
            <p class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white mt-0.5">{{ $totalClasses }}</p>
        </div>
        <div class="card p-3 sm:p-4">
            <p class="text-[10px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 truncate">Berlangsung</p>
            <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-0.5">{{ $inProgressClasses }}</p>
        </div>
        <div class="card p-3 sm:p-4">
            <p class="text-[10px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 truncate">Selesai</p>
            <p class="text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400 mt-0.5">{{ $completedClasses }}</p>
        </div>
    </div>

    <!-- Mode Toggle -->
    <div class="card p-4 sm:p-6">
        <h3 class="text-base font-bold text-navy-800 dark:text-white mb-3 sm:mb-4">Mode Scan</h3>
        
        <div class="flex gap-2 p-1 bg-slate-100 dark:bg-slate-700/80 rounded-xl">
            <button @click="mode = 'in'" 
                    :class="mode === 'in' ? 'bg-green-500 text-white shadow-lg' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="flex-1 px-3 py-2.5 sm:px-4 sm:py-3 rounded-lg font-bold text-sm transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                </svg>
                <span>Masuk</span>
            </button>
            <button @click="mode = 'out'"
                    :class="mode === 'out' ? 'bg-red-500 text-white shadow-lg' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="flex-1 px-3 py-2.5 sm:px-4 sm:py-3 rounded-lg font-bold text-sm transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
                <span>Keluar</span>
            </button>
        </div>

        <!-- Info Box -->
        <div class="mt-4 p-3.5 sm:p-4 rounded-xl transition-all"
             :class="mode === 'in' ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800'">
            <div class="flex items-start gap-2.5">
                <div x-show="mode === 'in'" class="w-4 h-4 mt-0.5 text-green-600 dark:text-green-400 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                    </svg>
                </div>
                <div x-show="mode === 'out'" class="w-4 h-4 mt-0.5 text-red-600 dark:text-red-400 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                </div>
                <div class="text-xs min-w-0">
                    <p class="font-bold mb-0.5"
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
    <div class="card p-4 sm:p-6">
        <h3 class="text-base font-bold text-navy-800 dark:text-white mb-4">Scan QR Code</h3>

        <!-- Camera Viewport -->
        <div class="flex justify-center">
            <div class="relative rounded-2xl overflow-hidden bg-slate-900 w-full max-w-[320px] sm:max-w-[360px] aspect-square shadow-inner">
                <!-- Video feed -->
                <video id="qr-video" class="absolute inset-0 w-full h-full object-cover" autoplay playsinline muted></video>

                <!-- Idle overlay -->
                <div id="qr-idle-overlay" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/90 text-white gap-3 p-4 text-center">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white/10 flex items-center justify-center">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75v4.5m0-4.5h-4.5m4.5 0L15 9m5.25 11.25v-4.5m0 4.5h-4.5m4.5 0L15 15" />
                        </svg>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-slate-300">Tekan tombol untuk mulai scan</p>
                </div>

                <!-- Scan box overlay -->
                <div id="qr-scan-overlay" class="absolute inset-0 hidden">
                    <div class="absolute inset-0 bg-black/50"></div>
                    <div id="qr-scan-box" class="absolute" style="top:50%;left:50%;transform:translate(-50%,-50%);width:200px;height:200px;">
                        <div class="absolute inset-0 rounded-lg" style="box-shadow: 0 0 0 9999px rgba(0,0,0,0.5);"></div>
                        <span class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-gold-400 rounded-tl-lg"></span>
                        <span class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-gold-400 rounded-tr-lg"></span>
                        <span class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-gold-400 rounded-bl-lg"></span>
                        <span class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-gold-400 rounded-br-lg"></span>
                        <div class="qr-laser absolute left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-gold-400 to-transparent" style="top:0;"></div>
                    </div>
                    <p class="absolute bottom-4 sm:bottom-6 left-0 right-0 text-center text-[11px] sm:text-xs text-white/80 font-medium px-2">Arahkan QR Code ke dalam kotak</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mt-4 max-w-xs sm:max-w-md mx-auto">
            <button @click="startScanner()" x-show="!scanning"
                    class="flex-1 px-4 py-3 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all hover:opacity-90 shadow-md active:scale-95">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0c-.693.04-1.344.436-1.736 1.039l-.821 1.316Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008v-.008Z" />
                </svg>
                <span>Mulai Scan</span>
            </button>
            <button @click="stopScanner()" x-show="scanning"
                    class="flex-1 px-4 py-3 bg-red-500 text-white rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all hover:bg-red-600 shadow-md active:scale-95">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="5" y="5" width="14" height="14" rx="2" />
                </svg>
                <span>Stop Scan</span>
            </button>
            <button type="button" @click="document.getElementById('qr-file-input-class').click()"
                    class="px-4 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-md active:scale-95">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                <span>Unggah QR</span>
            </button>
            <input type="file" id="qr-file-input-class" accept="image/*" class="hidden" @change="uploadImage($event)">
        </div>
    </div>

    <!-- Result Toast -->
    <div x-show="showResult" x-transition class="card p-4 sm:p-6 border-2" 
         :class="resultSuccess ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-red-500 bg-red-50 dark:bg-red-900/20'">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                 :class="resultSuccess ? 'bg-green-500' : 'bg-red-500'">
                <svg x-show="resultSuccess" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <svg x-show="!resultSuccess" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-sm sm:text-base leading-snug" :class="resultSuccess ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300'"
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
    <div class="card p-4 sm:p-6">
        <h3 class="text-base font-bold text-navy-800 dark:text-white mb-4">Jadwal Hari Ini</h3>
        <div class="space-y-3">
            @foreach($schedules as $schedule)
            @php
                $att = $schedule->classAttendances->first();
                $isComplete = $att && $att->isComplete();
                $isInProgress = $att && $att->check_in_time && !$att->check_out_time;
                $isPending = !$att;
            @endphp
            <div class="p-3.5 sm:p-4 rounded-xl border-2 transition-all"
                 :class="{
                     'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800': {{ $isComplete ? 'true' : 'false' }},
                     'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800': {{ $isInProgress ? 'true' : 'false' }},
                     'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700': {{ $isPending ? 'true' : 'false' }}
                 }">
                <div class="flex items-center justify-between gap-2.5">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                             :class="{
                                 'bg-green-100 dark:bg-green-900/30': {{ $isComplete ? 'true' : 'false' }},
                                 'bg-blue-100 dark:bg-blue-900/30': {{ $isInProgress ? 'true' : 'false' }},
                                 'bg-slate-200 dark:bg-slate-600': {{ $isPending ? 'true' : 'false' }}
                             }">
                            @if($isComplete)
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            @elseif($isInProgress)
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="9" />
                                </svg>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-navy-800 dark:text-white truncate">
                                {{ $schedule->classroom->name ?? '-' }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                {{ $schedule->subject->name ?? '-' }} • Jam ke-{{ $schedule->period }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                            </p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold flex-shrink-0 text-center"
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
                 style="max-height:92dvh; max-height:92vh;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-y-full opacity-0 sm:translate-y-4 sm:scale-95"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
                 x-transition:leave-end="translate-y-full opacity-0 sm:translate-y-4 sm:scale-95"
                 @click.stop>

                <!-- Drag pill for mobile -->
                <div class="flex-shrink-0 flex justify-center pt-3 pb-0 sm:hidden rounded-t-[2rem] bg-white dark:bg-slate-900">
                    <div class="w-10 h-1 bg-slate-300 dark:bg-slate-600 rounded-full"></div>
                </div>

                <!-- Header -->
                <div class="flex-shrink-0 px-5 pt-3 sm:pt-5 pb-4 bg-white dark:bg-slate-900 rounded-t-[2rem] sm:rounded-t-[2rem]">
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
                            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                                 :class="mode==='in' ? 'bg-gradient-to-br from-emerald-400 to-teal-500 shadow-lg shadow-emerald-500/30' : 'bg-gradient-to-br from-red-400 to-rose-500 shadow-lg shadow-red-500/30'">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white leading-tight truncate"
                                    x-text="mode==='in' ? 'Presensi Masuk Kelas' : 'Presensi Keluar Kelas'"></h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1 truncate">
                                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                                    </svg>
                                    <span class="truncate" x-text="sharedSpaceLocation || 'Ruangan Bersama'"></span>
                                </p>
                            </div>
                        </div>
                        <button @click="showSharedSpaceModal=false"
                                class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Divider -->
                <div class="flex-shrink-0 h-px bg-slate-100 dark:bg-slate-800 mx-5"></div>

                <!-- Scrollable Body -->
                <div class="overflow-y-auto flex-1 overscroll-contain bg-white dark:bg-slate-900"
                     style="padding-bottom: 0.5rem;"
                     @click.stop>

                    <!-- MODE IN -->
                    <div x-show="mode === 'in'" class="px-5 pt-4 pb-3 space-y-4">

                        <!-- Step bar -->
                        <div class="flex items-center gap-1.5 mb-1">
                            <template x-for="(s, i) in [{l:'Kelas',d:!!sharedSpaceSelectedClass},{l:'Mapel',d:!!sharedSpaceSelectedSubject},{l:'Jam',d:!!sharedSpacePeriod}]" :key="i">
                                <div class="flex items-center gap-1.5" :class="i<2?'flex-1':''">
                                    <div class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-black flex-shrink-0 transition-all"
                                         :class="s.d?'bg-emerald-500 text-white':'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400'">
                                        <template x-if="s.d">
                                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                        </template>
                                        <span x-show="!s.d" x-text="i+1"></span>
                                    </div>
                                    <span class="text-[10px] font-semibold hidden sm:block" :class="s.d?'text-emerald-600 dark:text-emerald-400':'text-slate-400 dark:text-slate-500'" x-text="s.l"></span>
                                    <div x-show="i<2" class="flex-1 h-px transition-colors" :class="s.d?'bg-emerald-400':'bg-slate-200 dark:bg-slate-700'"></div>
                                </div>
                            </template>
                        </div>

                        <!-- KELAS DROPDOWN -->
                        <div>
                            <label class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                                <span class="w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-black"
                                      :class="sharedSpaceSelectedClass?'bg-emerald-500 text-white':'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400'">
                                    <template x-if="sharedSpaceSelectedClass">
                                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                    </template>
                                    <span x-show="!sharedSpaceSelectedClass">1</span>
                                </span>
                                <span>Kelas</span> <span class="text-red-400 normal-case font-normal">*</span>
                            </label>

                            <!-- Trigger button -->
                            <button type="button" @click.stop="openKelas = !openKelas; openMapel = false"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl border-2 transition-all duration-200 text-left"
                                    :class="openKelas ? 'border-emerald-500 bg-white dark:bg-slate-800 shadow-md'
                                        : (sharedSpaceSelectedClass ? 'border-emerald-300 dark:border-emerald-700/60 bg-emerald-50 dark:bg-emerald-900/10'
                                        : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60')">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                                     :class="sharedSpaceSelectedClass ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-700'">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                         :class="sharedSpaceSelectedClass ? 'text-white' : 'text-slate-500 dark:text-slate-400'">
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
                                            class="w-5 h-5 rounded-full bg-slate-300 dark:bg-slate-600 flex items-center justify-center hover:bg-red-500 transition-colors">
                                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="openKelas?'rotate-180 text-emerald-500':''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                                </div>
                            </button>

                            <!-- Inline expand list -->
                            <div x-show="openKelas"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0 -translate-y-2"
                                 @click.away="openKelas = false"
                                 class="mt-1.5 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                <div class="p-2.5 border-b border-slate-100 dark:border-slate-700">
                                    <div class="relative">
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                                        <input type="text" x-model="searchKelas" placeholder="Cari kelas..."
                                               class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 rounded-xl border-0 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 text-slate-800 dark:text-white placeholder:text-slate-400"
                                               @click.stop @keydown.escape="openKelas=false">
                                    </div>
                                </div>
                                <div class="max-h-48 overflow-y-auto overscroll-contain py-1">
                                    <template x-for="cls in sharedSpaceClasses.filter(c => !searchKelas || c.name.toLowerCase().includes(searchKelas.toLowerCase()) || (c.code && c.code.toLowerCase().includes(searchKelas.toLowerCase())))" :key="cls.id">
                                        <button type="button"
                                                @click.stop="sharedSpaceSelectedClass=cls.id; openKelas=false; searchKelas=''"
                                                class="w-full flex items-center gap-3 px-3.5 py-2.5 text-left transition-colors"
                                                :class="sharedSpaceSelectedClass==cls.id ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'hover:bg-slate-50 dark:hover:bg-slate-700/60'">
                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 text-[10px] font-black"
                                                 :class="sharedSpaceSelectedClass==cls.id ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'"
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

                        <!-- MAPEL DROPDOWN -->
                        <div>
                            <label class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                                <span class="w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-black"
                                      :class="sharedSpaceSelectedSubject?'bg-emerald-500 text-white':'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400'">
                                    <template x-if="sharedSpaceSelectedSubject">
                                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                    </template>
                                    <span x-show="!sharedSpaceSelectedSubject">2</span>
                                </span>
                                <span>Mata Pelajaran</span> <span class="text-red-400 normal-case font-normal">*</span>
                            </label>

                            <!-- Trigger button -->
                            <button type="button" @click.stop="openMapel = !openMapel; openKelas = false"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl border-2 transition-all duration-200 text-left"
                                    :class="openMapel ? 'border-emerald-500 bg-white dark:bg-slate-800 shadow-md'
                                        : (sharedSpaceSelectedSubject ? 'border-emerald-300 dark:border-emerald-700/60 bg-emerald-50 dark:bg-emerald-900/10'
                                        : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60')">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                                     :class="sharedSpaceSelectedSubject ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-700'">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                         :class="sharedSpaceSelectedSubject ? 'text-white' : 'text-slate-500 dark:text-slate-400'">
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
                                            class="w-5 h-5 rounded-full bg-slate-300 dark:bg-slate-600 flex items-center justify-center hover:bg-red-500 transition-colors">
                                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="openMapel?'rotate-180 text-emerald-500':''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                                </div>
                            </button>

                            <!-- Inline expand list -->
                            <div x-show="openMapel"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0 -translate-y-2"
                                 @click.away="openMapel = false"
                                 class="mt-1.5 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                <div class="p-2.5 border-b border-slate-100 dark:border-slate-700">
                                    <div class="relative">
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                                        <input type="text" x-model="searchMapel" placeholder="Cari mata pelajaran..."
                                               class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 rounded-xl border-0 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 text-slate-800 dark:text-white placeholder:text-slate-400"
                                               @click.stop @keydown.escape="openMapel=false">
                                    </div>
                                </div>
                                <div class="max-h-48 overflow-y-auto overscroll-contain py-1">
                                    <template x-for="subject in sharedSpaceSubjects.filter(s => !searchMapel || s.name.toLowerCase().includes(searchMapel.toLowerCase()))" :key="subject.id">
                                        <button type="button"
                                                @click.stop="sharedSpaceSelectedSubject=subject.id; openMapel=false; searchMapel=''"
                                                class="w-full flex items-center gap-3 px-3.5 py-2.5 text-left transition-colors"
                                                :class="sharedSpaceSelectedSubject==subject.id ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'hover:bg-slate-50 dark:hover:bg-slate-700/60'">
                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 text-[10px] font-black"
                                                 :class="sharedSpaceSelectedSubject==subject.id ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'"
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
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <span class="w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-black transition-all duration-300"
                                          :class="sharedSpacePeriod?'bg-emerald-500 text-white scale-110':'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400'">
                                        <template x-if="sharedSpacePeriod">
                                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                        </template>
                                        <span x-show="!sharedSpacePeriod">3</span>
                                    </span>
                                    <span>Jam Ke-</span> <span class="text-red-400 normal-case font-normal">*</span>
                                    <span x-show="sharedSpacePeriod"
                                          x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0"
                                          class="px-1.5 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-full text-[10px] font-black normal-case"
                                          x-text="'JP ' + sharedSpacePeriod"></span>
                                </label>
                                <!-- Toggle grid / list -->
                                <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl">
                                    <button type="button" @click="jamViewMode='grid'"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-200"
                                            :class="jamViewMode==='grid'?'bg-white dark:bg-slate-700 shadow-sm text-emerald-600 dark:text-emerald-400':'text-slate-400 dark:text-slate-500'">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>
                                        </svg>
                                    </button>
                                    <button type="button" @click="jamViewMode='list'"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-200"
                                            :class="jamViewMode==='list'?'bg-white dark:bg-slate-700 shadow-sm text-emerald-600 dark:text-emerald-400':'text-slate-400 dark:text-slate-500'">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- GRID VIEW -->
                            <div x-show="jamViewMode==='grid'"
                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="grid grid-cols-6 gap-1.5 sm:gap-2">
                                <template x-for="jam in [1,2,3,4,5,6,7,8,9,10,11,12]" :key="'g'+jam">
                                    <button type="button" @click="sharedSpacePeriod = jam"
                                            class="relative flex flex-col items-center justify-center rounded-xl font-bold transition-all duration-150 active:scale-90 select-none py-2.5"
                                            :class="sharedSpacePeriod==jam
                                                ? 'bg-emerald-500 text-white shadow-md scale-105 ring-2 ring-white dark:ring-slate-900'
                                                : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600'">
                                        <span class="text-sm leading-none font-extrabold" x-text="jam"></span>
                                        <span class="text-[8px] leading-none mt-0.5 font-semibold" :class="sharedSpacePeriod==jam?'opacity-80':'opacity-50'">JP</span>
                                        <template x-if="sharedSpacePeriod==jam">
                                            <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-white rounded-full border-2 border-emerald-500 shadow-sm"></span>
                                        </template>
                                    </button>
                                </template>
                            </div>

                            <!-- LIST VIEW -->
                            <div x-show="jamViewMode==='list'"
                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-1"
                                 class="grid grid-cols-2 gap-1.5 sm:gap-2">
                                <template x-for="jam in [1,2,3,4,5,6,7,8,9,10,11,12]" :key="'l'+jam">
                                    <button type="button" @click="sharedSpacePeriod = jam"
                                            class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all duration-150 active:scale-95 text-left border"
                                            :class="sharedSpacePeriod==jam
                                                ? 'bg-emerald-500 border-emerald-500 text-white shadow-md'
                                                : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:border-emerald-300 dark:hover:border-emerald-700'">
                                        <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-black"
                                             :class="sharedSpacePeriod==jam?'bg-white/20':'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300'">
                                            <span x-text="jam"></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold leading-none" x-text="'Jam ke-'+jam"></p>
                                            <p class="text-[9px] mt-0.5 leading-none" :class="sharedSpacePeriod==jam?'text-white/70':'text-slate-400 dark:text-slate-500'">Jam Pelajaran</p>
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
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="flex items-start gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl border border-emerald-200/70 dark:border-emerald-800/50">
                            <div class="w-8 h-8 bg-emerald-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                            </div>
                            <div class="flex-1 min-w-0 text-xs space-y-0.5">
                                <p class="font-bold text-emerald-700 dark:text-emerald-400 mb-1">Siap disimpan ✓</p>
                                <p class="text-slate-600 dark:text-slate-400 truncate">📚 <span class="font-semibold text-slate-800 dark:text-white" x-text="sharedSpaceSubjects.find(s=>s.id==sharedSpaceSelectedSubject)?.name"></span></p>
                                <p class="text-slate-600 dark:text-slate-400 truncate">🏫 <span class="font-semibold text-slate-800 dark:text-white"
                                   x-text="sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.code
                                       ? sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.name+' ('+sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.code+')'
                                       : sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.name"></span></p>
                                <p class="text-slate-600 dark:text-slate-400">⏰ Jam ke-<span class="font-semibold text-slate-800 dark:text-white" x-text="sharedSpacePeriod"></span> · <span x-text="sharedSpaceLocation"></span></p>
                            </div>
                        </div>
                        <div class="h-2"></div>
                    </div>

                    <!-- MODE OUT -->
                    <div x-show="mode === 'out'" class="px-5 pt-4 pb-3 space-y-3">
                        <template x-if="sharedSpaceActiveSessions.length > 0">
                            <div>
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Sesi berlangsung — pilih untuk selesaikan</p>
                                <div class="space-y-2.5">
                                    <template x-for="session in sharedSpaceActiveSessions" :key="session.id">
                                        <div class="relative rounded-2xl border-2 cursor-pointer overflow-hidden transition-all duration-200 active:scale-[.98]"
                                             :class="sharedSpaceSelectedSession==session.id
                                                 ? 'border-emerald-500 bg-emerald-50/70 dark:bg-emerald-900/20 shadow-md'
                                                 : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60 hover:border-slate-300 dark:hover:border-slate-600'"
                                             @click="sharedSpaceSelectedSession = session.id">
                                            <template x-if="sharedSpaceSelectedSession==session.id">
                                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500 rounded-l-2xl"></div>
                                            </template>
                                            <div class="flex items-center gap-3 p-3.5 sm:p-4 pl-4 sm:pl-5">
                                                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center flex-shrink-0 text-xs font-black"
                                                     :class="sharedSpaceSelectedSession==session.id ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'"
                                                     x-text="session.classroom_name.slice(0,3).toUpperCase()"></div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold truncate"
                                                       :class="sharedSpaceSelectedSession==session.id ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-900 dark:text-white'"
                                                       x-text="session.classroom_name"></p>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate"
                                                       x-text="session.subject_name+' · Jam ke-'+session.period"></p>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                                            <span x-text="'Masuk '+session.check_in_time"></span>
                                                        </span>
                                                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full"
                                                              :class="session.duration_minutes>=30
                                                                  ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                                                                  : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'"
                                                              x-text="session.duration_minutes+' mnt'"></span>
                                                    </div>
                                                </div>
                                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all flex-shrink-0"
                                                     :class="sharedSpaceSelectedSession==session.id ? 'border-emerald-500 bg-emerald-500' : 'border-slate-300 dark:border-slate-600'">
                                                    <template x-if="sharedSpaceSelectedSession==session.id">
                                                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-if="sharedSpaceActiveSessions.length === 0">
                            <div class="text-center py-10 sm:py-12">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Tidak Ada Sesi Aktif</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Lakukan scan masuk terlebih dahulu</p>
                            </div>
                        </template>
                        <div class="h-2"></div>
                    </div>

                </div><!-- /scrollable body -->

                <!-- Footer -->
                <div class="flex-shrink-0 px-5 py-4 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 sm:rounded-b-[2rem]"
                     style="padding-bottom:calc(1rem + env(safe-area-inset-bottom))">
                    <template x-if="mode === 'in'">
                        <button @click="submitSharedSpaceAttendance()"
                                :disabled="!sharedSpaceSelectedClass || !sharedSpaceSelectedSubject || !sharedSpacePeriod"
                                class="w-full py-3.5 sm:py-4 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 transition-all duration-200
                                       bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white shadow-lg shadow-emerald-500/25
                                       disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/>
                            </svg>
                            <span>Simpan Presensi Masuk</span>
                        </button>
                    </template>
                    <template x-if="mode === 'out'">
                        <div>
                            <button x-show="sharedSpaceActiveSessions.length > 0"
                                    @click="submitSharedSpaceCheckOut()"
                                    :disabled="!sharedSpaceSelectedSession"
                                    class="w-full py-3.5 sm:py-4 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 transition-all duration-200
                                           bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white shadow-lg shadow-emerald-500/25
                                           disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                </svg>
                                <span>Selesaikan Sesi Ini</span>
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
                <button @click="showClassSelection = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
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
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-navy-800 dark:text-white truncate" x-text="schedule.classroom_name"></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate" x-text="schedule.subject"></p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-sm font-semibold text-navy-800 dark:text-white" x-text="'Jam ke-' + schedule.period"></p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500" x-text="schedule.start_time + ' - ' + schedule.end_time"></p>
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
    const _QR_INTERVAL_MS = 80;
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

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Akses kamera tidak didukung di peramban ini atau membutuhkan jaringan aman (HTTPS). Silakan gunakan tombol "Unggah QR".');
            return;
        }

        const constraintsList = [
            { video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } } },
            { video: { facingMode: 'environment' } },
            { video: true }
        ];

        function tryNextConstraint(index) {
            if (index >= constraintsList.length) {
                alert('Gagal mengakses kamera. Pastikan izin kamera sudah diberikan di peramban Anda atau coba tombol "Unggah QR".');
                return;
            }
            navigator.mediaDevices.getUserMedia(constraintsList[index])
            .then(stream => {
                _qrStream = stream;
                video.srcObject = stream;
                video.play();
                if (idle) idle.classList.add('hidden');
                if (scanOverlay) scanOverlay.classList.remove('hidden');
                _qrScanning = true;
                if (alpineCtx) alpineCtx.scanning = true;
                _lastTickTime = 0;
                requestAnimationFrame(tickQr);
            })
            .catch(err => {
                console.warn('Camera constraint index ' + index + ' failed:', err);
                tryNextConstraint(index + 1);
            });
        }

        tryNextConstraint(0);
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

        if (ts - _lastTickTime < _QR_INTERVAL_MS) {
            requestAnimationFrame(tickQr);
            return;
        }
        _lastTickTime = ts;

        const video = document.getElementById('qr-video');
        if (!video || video.readyState < 2) { requestAnimationFrame(tickQr); return; }

        const vw = video.videoWidth, vh = video.videoHeight;
        if (!vw || !vh) { requestAnimationFrame(tickQr); return; }

        const scale = Math.min(1, 400 / vw);
        let code = _tryDecode(_qrCanvas, _qrCtx, video, 0, 0, vw, vh, Math.round(vw * scale), Math.round(vh * scale));

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
            sharedSpaceActiveSessions: [],
            sharedSpaceSelectedClass: '',
            sharedSpaceSelectedSubject: '',
            sharedSpacePeriod: '',
            sharedSpaceSelectedSession: '',
            
            // Dropdown open & search state
            openKelas:    false,
            searchKelas:  '',
            openMapel:    false,
            searchMapel:  '',
            jamViewMode:  'grid',

            init() {
                this.$watch('showSharedSpaceModal', (val) => {
                    if (!val) {
                        this.openKelas = false;
                        this.openMapel = false;
                        this.searchKelas = '';
                        this.searchMapel = '';
                    }
                });
            },

            startScanner() {
                startQrVideo(this);
            },

            stopScanner() {
                stopQrVideo(this);
            },

            uploadImage(event) {
                const file = event.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        canvas.width = img.width;
                        canvas.height = img.height;
                        ctx.drawImage(img, 0, 0);
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'attemptBoth' });
                        if (code && code.data) {
                            this.processScan(code.data);
                        } else {
                            alert('QR Code tidak terdeteksi dalam gambar yang diunggah.');
                        }
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
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
                        this.openKelas   = false;
                        this.searchKelas = '';
                        this.openMapel   = false;
                        this.searchMapel = '';
                        this.jamViewMode = 'grid';
                    } else if (data.schedules) {
                        this.showClassSelection = true;
                        this.selectedLocation   = data.message;
                        this.classSchedules     = data.schedules;
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

            // Safe fetch helper
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
            }
        }
    }
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
