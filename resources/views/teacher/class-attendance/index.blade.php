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

    <!-- Shared Space Modal — slide-up dari bawah di mobile, center di desktop -->
    <div x-show="showSharedSpaceModal"
         x-cloak
         class="fixed inset-0 z-50 bg-black/50"
         style="display:none;">

        <!-- Backdrop klik tutup -->
        <div class="absolute inset-0" @click="showSharedSpaceModal = false"></div>

        <!-- Sheet: full-width di bawah (mobile), max-w-lg centered (desktop) -->
        <div class="absolute bottom-0 left-0 right-0 sm:static sm:flex sm:items-center sm:justify-center sm:h-full sm:p-4 pointer-events-none">
            <div class="pointer-events-auto bg-white dark:bg-slate-800 w-full sm:max-w-lg sm:rounded-2xl rounded-t-2xl shadow-2xl flex flex-col"
                 style="max-height: 92dvh; max-height: 92vh;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-y-full sm:translate-y-0 sm:opacity-0 sm:scale-95"
                 x-transition:enter-end="translate-y-0 sm:opacity-100 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-y-0 sm:opacity-100 sm:scale-100"
                 x-transition:leave-end="translate-y-full sm:translate-y-0 sm:opacity-0 sm:scale-95"
                 @click.stop>

                <!-- Drag handle (mobile only) -->
                <div class="flex justify-center pt-3 pb-1 sm:hidden flex-shrink-0">
                    <div class="w-10 h-1 bg-slate-300 dark:bg-slate-600 rounded-full"></div>
                </div>

                <!-- Header -->
                <div class="px-5 pt-3 pb-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="building-2" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-navy-800 dark:text-white leading-tight"
                                x-text="mode === 'in' ? 'Presensi Masuk' : 'Presensi Keluar'"></h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400" x-text="sharedSpaceLocation"></p>
                        </div>
                    </div>
                    <button @click="showSharedSpaceModal = false"
                            class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg flex-shrink-0">
                        <i data-lucide="x" class="w-4 h-4 text-slate-400"></i>
                    </button>
                </div>

                <!-- Scrollable Body -->
                <div class="overflow-y-auto flex-1 overscroll-contain">

                    <!-- MODE IN body -->
                    <div x-show="mode === 'in'" class="p-4 space-y-4">
                        <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800">
                            <p class="text-xs text-green-700 dark:text-green-400 font-medium">
                                <i data-lucide="info" class="w-3.5 h-3.5 inline mr-1"></i>
                                Pilih kelas, mapel, dan jam yang akan diajar
                            </p>
                        </div>

                        <!-- Kelas -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-1.5">
                                Kelas <span class="text-red-500">*</span>
                            </label>
                            <select x-model="sharedSpaceSelectedClass"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                                <option value="">Pilih kelas...</option>
                                <template x-for="cls in sharedSpaceClasses" :key="cls.id">
                                    <option :value="cls.id" x-text="cls.code ? `${cls.name} (${cls.code})` : cls.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Mapel -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-1.5">
                                Mata Pelajaran <span class="text-red-500">*</span>
                            </label>
                            <select x-model="sharedSpaceSelectedSubject"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                                <option value="">Pilih mata pelajaran...</option>
                                <template x-for="subject in sharedSpaceSubjects" :key="subject.id">
                                    <option :value="subject.id" x-text="subject.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Jam ke- (grid button) -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-1.5">
                                Jam Ke- <span class="text-red-500">*</span>
                                <span class="text-xs font-normal text-slate-400 ml-1">(1–12)</span>
                            </label>
                            <div class="grid grid-cols-6 gap-2">
                                <template x-for="jam in [1,2,3,4,5,6,7,8,9,10,11,12]" :key="jam">
                                    <button type="button"
                                            @click="sharedSpacePeriod = jam"
                                            :class="sharedSpacePeriod == jam
                                                ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 ring-2 ring-navy-800 dark:ring-gold-400 font-bold'
                                                : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600'"
                                            class="h-10 rounded-lg text-sm transition-all"
                                            x-text="jam">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- MODE OUT body -->
                    <div x-show="mode === 'out'" class="p-4 space-y-3">
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800">
                            <p class="text-xs text-red-700 dark:text-red-400 font-medium">
                                <i data-lucide="log-out" class="w-3.5 h-3.5 inline mr-1"></i>
                                Pilih sesi yang sedang berlangsung
                            </p>
                        </div>

                        <template x-if="sharedSpaceActiveSessions.length > 0">
                            <div class="space-y-2">
                                <template x-for="session in sharedSpaceActiveSessions" :key="session.id">
                                    <div class="p-3.5 rounded-xl border-2 cursor-pointer transition-all active:scale-[.98]"
                                         :class="sharedSpaceSelectedSession == session.id
                                             ? 'border-red-400 bg-red-50 dark:bg-red-900/20'
                                             : 'border-slate-200 dark:border-slate-700'"
                                         @click="sharedSpaceSelectedSession = session.id">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="min-w-0">
                                                <p class="font-bold text-sm text-navy-800 dark:text-white truncate" x-text="session.classroom_name"></p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate"
                                                   x-text="session.subject_name + ' • Jam ke-' + session.period"></p>
                                            </div>
                                            <div class="text-right flex-shrink-0">
                                                <p class="text-xs font-semibold text-slate-600 dark:text-slate-300"
                                                   x-text="'Masuk ' + session.check_in_time"></p>
                                                <p class="text-[10px] text-slate-400"
                                                   x-text="session.duration_minutes + ' mnt'"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="sharedSpaceActiveSessions.length === 0">
                            <div class="text-center py-8">
                                <i data-lucide="inbox" class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-2"></i>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada sesi aktif.</p>
                                <p class="text-xs text-slate-400 mt-1">Scan masuk terlebih dahulu.</p>
                            </div>
                        </template>
                    </div>

                </div><!-- /scrollable body -->

                <!-- Footer (sticky) -->
                <div class="px-4 py-4 border-t border-slate-200 dark:border-slate-700 flex-shrink-0"
                     style="padding-bottom: calc(1rem + env(safe-area-inset-bottom))">
                    <template x-if="mode === 'in'">
                        <button @click="submitSharedSpaceAttendance()"
                                :disabled="!sharedSpaceSelectedClass || !sharedSpaceSelectedSubject || !sharedSpacePeriod"
                                class="w-full py-3.5 bg-green-500 hover:bg-green-600 active:bg-green-700
                                       disabled:opacity-40 disabled:cursor-not-allowed
                                       text-white rounded-xl font-bold text-sm
                                       flex items-center justify-center gap-2 transition-all">
                            <i data-lucide="log-in" class="w-4 h-4"></i>
                            Simpan Presensi Masuk
                        </button>
                    </template>
                    <template x-if="mode === 'out'">
                        <div>
                            <button x-show="sharedSpaceActiveSessions.length > 0"
                                    @click="submitSharedSpaceCheckOut()"
                                    :disabled="!sharedSpaceSelectedSession"
                                    class="w-full py-3.5 bg-red-500 hover:bg-red-600 active:bg-red-700
                                           disabled:opacity-40 disabled:cursor-not-allowed
                                           text-white rounded-xl font-bold text-sm
                                           flex items-center justify-center gap-2 transition-all">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
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
