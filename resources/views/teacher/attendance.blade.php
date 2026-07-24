@extends('layouts.teacher')

@section('page-title', 'Presensi Harian')

@section('content')
<div class="fade-in space-y-6" x-data="{ activeTab: 'scan', mode: 'masuk', scanning: false }">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="scan-line" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Presensi Harian</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Scan QR Code untuk presensi datang dan pulang</p>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="card p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 animate-fade-in">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                <i data-lucide="check" class="w-4 h-4 text-white"></i>
            </div>
            <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="card p-4 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800 animate-fade-in">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0">
                <i data-lucide="x" class="w-4 h-4 text-white"></i>
            </div>
            <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Presensi Area -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Today's Attendance Status Card -->
            <div class="card p-6 bg-gradient-to-br from-white to-slate-50 dark:from-slate-800 dark:to-slate-900 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                            <i data-lucide="calendar-check" class="w-6 h-6 text-white dark:text-navy-900"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-navy-800 dark:text-white">Status Hari Ini</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                        </div>
                    </div>
                    @if($todayAttendance)
                    <span class="px-4 py-2 rounded-full text-sm font-bold {{ $todayAttendance->status === 'Hadir' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                        {{ $todayAttendance->status }}
                    </span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl border-2 {{ $todayAttendance && $todayAttendance->check_in ? 'bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-green-200 dark:border-green-800' : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700' }}">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl {{ $todayAttendance && $todayAttendance->check_in ? 'bg-green-500' : 'bg-slate-300 dark:bg-slate-600' }} flex items-center justify-center transition-colors">
                                <i data-lucide="clock" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Jam Masuk</p>
                                @if($scheduleStart)
                                <p class="text-[10px] text-slate-400 dark:text-slate-500">Jadwal: {{ \Carbon\Carbon::parse($scheduleStart)->format('H:i') }}</p>
                                @else
                                <p class="text-[10px] text-slate-400 dark:text-slate-500">Belum diatur</p>
                                @endif
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold {{ $todayAttendance && $todayAttendance->check_in ? 'text-green-700 dark:text-green-400' : 'text-slate-400' }}">
                            @if($todayAttendance && $todayAttendance->check_in)
                                {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') }}
                            @elseif($scheduleStart)
                                {{ \Carbon\Carbon::parse($scheduleStart)->format('H:i') }}
                            @else
                                --:--
                            @endif
                        </h3>
                    </div>

                    <div class="p-4 rounded-2xl border-2 {{ $todayAttendance && $todayAttendance->check_out ? 'bg-gradient-to-br from-navy-50 to-slate-50 dark:from-navy-900/20 dark:to-slate-900/20 border-navy-200 dark:border-navy-800' : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700' }}">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl {{ $todayAttendance && $todayAttendance->check_out ? 'bg-navy-800 dark:bg-gold-400' : 'bg-slate-300 dark:bg-slate-600' }} flex items-center justify-center transition-colors">
                                <i data-lucide="clock" class="w-5 h-5 text-white dark:text-navy-900"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Jam Pulang</p>
                                @if($scheduleEnd)
                                <p class="text-[10px] text-slate-400 dark:text-slate-500">Jadwal: {{ \Carbon\Carbon::parse($scheduleEnd)->format('H:i') }}</p>
                                @else
                                <p class="text-[10px] text-slate-400 dark:text-slate-500">Belum diatur</p>
                                @endif
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold {{ $todayAttendance && $todayAttendance->check_out ? 'text-navy-800 dark:text-gold-400' : 'text-slate-400' }}">
                            @if($todayAttendance && $todayAttendance->check_out)
                                {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i') }}
                            @elseif($scheduleEnd)
                                {{ \Carbon\Carbon::parse($scheduleEnd)->format('H:i') }}
                            @else
                                --:--
                            @endif
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Tab Switcher Card -->
            <div class="card p-6 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-4 mb-6">
                    <h2 class="text-lg font-bold text-navy-800 dark:text-white">Metode Presensi</h2>
                    <div class="flex gap-2 bg-slate-100 dark:bg-slate-900/60 p-1 rounded-xl">
                        <button type="button" @click="activeTab = 'scan'; if(window.lucide) lucide.createIcons()"
                                :class="activeTab === 'scan' ? 'bg-white dark:bg-slate-800 text-navy-800 dark:text-gold-400 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                                class="px-4 py-2 rounded-lg text-xs transition-all flex items-center gap-2">
                            <i data-lucide="camera" class="w-4 h-4"></i>
                            <span>Scan Kamera</span>
                        </button>
                        <button type="button" @click="activeTab = 'my_qr'; stopCamera(); if(window.lucide) lucide.createIcons()"
                                :class="activeTab === 'my_qr' ? 'bg-white dark:bg-slate-800 text-navy-800 dark:text-gold-400 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                                class="px-4 py-2 rounded-lg text-xs transition-all flex items-center gap-2">
                            <i data-lucide="qr-code" class="w-4 h-4"></i>
                            <span>QR Code Saya</span>
                        </button>
                    </div>
                </div>

                <!-- Tab 1: Kamera Scanner -->
                <div x-show="activeTab === 'scan'" class="space-y-6">
                    <!-- Mode Selector (Masuk / Keluar) -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Pilih Mode Presensi</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" @click="mode = 'masuk'"
                                    :class="mode === 'masuk' ? 'bg-gradient-to-r from-emerald-500 to-green-600 text-white shadow-md ring-2 ring-green-400' : 'bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                                    class="py-3 px-4 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2">
                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                <span>Presensi Masuk</span>
                            </button>
                            <button type="button" @click="mode = 'keluar'"
                                    :class="mode === 'keluar' ? 'bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 shadow-md ring-2 ring-navy-700 dark:ring-gold-400' : 'bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                                    class="py-3 px-4 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span>Presensi Keluar</span>
                            </button>
                        </div>
                    </div>

                    <!-- Camera Viewport Box -->
                    <div id="camera-box" class="relative rounded-2xl overflow-hidden bg-slate-900 border-4 border-slate-200 dark:border-slate-700 aspect-[4/3] flex items-center justify-center shadow-inner">
                        <!-- Camera Feed -->
                        <video id="camera-video" class="absolute inset-0 w-full h-full object-cover" autoplay playsinline muted></video>

                        <!-- Idle State Overlay -->
                        <div id="camera-idle-overlay" class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-slate-900/85 text-white p-6 text-center backdrop-blur-sm">
                            <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mb-4 border border-white/20 shadow-lg">
                                <i data-lucide="scan" class="w-8 h-8 text-gold-400"></i>
                            </div>
                            <h3 class="text-lg font-bold mb-1" x-text="mode === 'masuk' ? 'Presensi Masuk' : 'Presensi Keluar'"></h3>
                            <p class="text-xs text-slate-300 mb-5 max-w-xs">Arahkan kamera ke QR Code milik Anda atau QR lokasi sekolah.</p>
                            
                            <button type="button" @click="startCamera(); scanning = true" 
                                    class="px-6 py-3 bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-500 hover:to-gold-600 text-navy-900 font-bold rounded-xl shadow-lg hover:scale-105 transition-all flex items-center gap-2 text-sm">
                                <i data-lucide="power" class="w-4 h-4"></i>
                                Mulai Scan Kamera
                            </button>
                        </div>

                        <!-- Scanner Animation Overlay -->
                        <div id="scanning-overlay" class="absolute inset-0 z-10 hidden pointer-events-none">
                            <div class="absolute inset-0 bg-black/40"></div>
                            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-48 sm:w-56 sm:h-56">
                                <div class="absolute inset-0 border-2 border-gold-400/50 rounded-xl"></div>
                                <span class="absolute top-0 left-0 w-6 h-6 border-t-4 border-l-4 border-gold-400 rounded-tl-lg"></span>
                                <span class="absolute top-0 right-0 w-6 h-6 border-t-4 border-r-4 border-gold-400 rounded-tr-lg"></span>
                                <span class="absolute bottom-0 left-0 w-6 h-6 border-b-4 border-l-4 border-gold-400 rounded-bl-lg"></span>
                                <span class="absolute bottom-0 right-0 w-6 h-6 border-b-4 border-r-4 border-gold-400 rounded-br-lg"></span>
                                <div class="w-full h-0.5 bg-gradient-to-r from-transparent via-gold-400 to-transparent shadow-[0_0_15px_rgba(250,204,21,0.8)] animate-pulse"></div>
                            </div>
                            <p class="absolute bottom-4 left-0 right-0 text-center text-xs text-white/90 font-medium">Posisikan QR Code di dalam kotak</p>
                        </div>
                    </div>

                    <!-- Hidden Canvas for jsQR -->
                    <canvas id="qr-canvas" class="hidden"></canvas>

                    <!-- Camera Control Actions -->
                    <div class="flex items-center justify-center gap-3">
                        <button type="button" x-show="scanning" @click="stopCamera(); scanning = false"
                                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                            <i data-lucide="stop-circle" class="w-5 h-5"></i>
                            Hentikan Kamera
                        </button>
                    </div>

                    <!-- Scan Confirmation Card -->
                    <div id="scan-result-card" class="hidden p-5 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-2xl border border-green-200 dark:border-green-800 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center text-white shrink-0">
                                <i data-lucide="check-circle" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-green-800 dark:text-green-300">QR Code Berhasil Terdeteksi!</h4>
                                <p class="text-xs text-green-600 dark:text-green-400">Klik tombol di bawah untuk menyelesaikan presensi.</p>
                            </div>
                        </div>

                        <!-- Form submission -->
                        <form id="teacher-attendance-form" action="{{ route('teacher.attendance.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="qr_data" id="form-qr-data">
                            <input type="hidden" name="mode" :value="mode">
                            <input type="hidden" name="latitude" id="form-latitude">
                            <input type="hidden" name="longitude" id="form-longitude">

                            <button type="submit" class="w-full py-3 bg-navy-800 hover:bg-navy-900 dark:bg-gold-400 dark:hover:bg-gold-500 text-white dark:text-navy-900 font-bold rounded-xl text-sm transition-all shadow-md flex items-center justify-center gap-2">
                                <span x-text="mode === 'masuk' ? 'Konfirmasi Presensi Masuk' : 'Konfirmasi Presensi Keluar'"></span>
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>

                </div>

                <!-- Tab 2: QR Code Saya -->
                <div x-show="activeTab === 'my_qr'" class="space-y-6">
                    <div class="flex flex-col items-center justify-center p-8 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border-2 border-slate-200 dark:border-slate-700">
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-lg border border-slate-100 dark:border-slate-700">
                            {!! QrCode::size(240)->generate(json_encode([
                                'teacher_id' => auth()->id(),
                                'token' => auth()->user()->qr_token
                            ])) !!}
                        </div>
                        <div class="mt-6 text-center space-y-1">
                            <p class="text-sm font-bold text-navy-800 dark:text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Tunjukkan QR Code ini ke scanner atau operator</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column - Recent History -->
        <div class="space-y-6">
            <div class="card p-5">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                            <i data-lucide="history" class="w-5 h-5 text-white dark:text-navy-900"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-navy-800 dark:text-white">Riwayat 7 Hari</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Presensi terakhir</p>
                        </div>
                    </div>
                    <a href="{{ route('teacher.history') }}" class="text-xs font-semibold text-navy-800 dark:text-gold-400 hover:underline">
                        Lihat Semua
                    </a>
                </div>

                @if($recentAttendance->isEmpty())
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="inbox" class="w-8 h-8 text-slate-400 dark:text-slate-500"></i>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada riwayat absensi</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($recentAttendance as $att)
                    <div class="p-3 bg-slate-50 dark:bg-slate-700/30 rounded-xl border border-slate-200 dark:border-slate-700 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="text-sm font-bold text-navy-800 dark:text-white">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($att->date)->locale('id')->isoFormat('dddd') }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold
                                {{ $att->status === 'Hadir' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                {{ $att->status === 'Terlambat' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                {{ $att->status === 'Izin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : '' }}
                                {{ $att->status === 'Alpha' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                {{ $att->status }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400">
                                <i data-lucide="clock" class="w-3 h-3 text-green-500"></i>
                                <span class="font-mono">{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '-' }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400">
                                <i data-lucide="clock" class="w-3 h-3 text-navy-600 dark:text-gold-400"></i>
                                <span class="font-mono">{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '-' }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>

<!-- Include jsQR for camera scanning -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<script>
    let videoStream = null;
    let isCameraActive = false;

    function startCamera() {
        const video = document.getElementById('camera-video');
        const idleOverlay = document.getElementById('camera-idle-overlay');
        const scanningOverlay = document.getElementById('scanning-overlay');

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(pos => {
                const latInput = document.getElementById('form-latitude');
                const lngInput = document.getElementById('form-longitude');
                if (latInput) latInput.value = pos.coords.latitude;
                if (lngInput) lngInput.value = pos.coords.longitude;
            }, err => console.log('Location access denied or unavailable'));
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Akses kamera tidak didukung di peramban ini atau membutuhkan koneksi HTTPS.');
            return;
        }

        const constraintsList = [
            { video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } } },
            { video: { facingMode: 'environment' } },
            { video: true }
        ];

        function tryNextConstraint(index) {
            if (index >= constraintsList.length) {
                alert('Gagal mengakses kamera. Pastikan izin kamera sudah diberikan.');
                return;
            }
            navigator.mediaDevices.getUserMedia(constraintsList[index])
            .then(stream => {
                videoStream = stream;
                video.srcObject = stream;
                isCameraActive = true;

                if (idleOverlay) idleOverlay.classList.add('hidden');
                if (scanningOverlay) scanningOverlay.classList.remove('hidden');

                requestAnimationFrame(scanFrame);
            })
            .catch(err => {
                console.warn('Camera constraint index ' + index + ' failed:', err);
                tryNextConstraint(index + 1);
            });
        }

        tryNextConstraint(0);
    }

    function stopCamera() {
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
        isCameraActive = false;
        
        const idleOverlay = document.getElementById('camera-idle-overlay');
        const scanningOverlay = document.getElementById('scanning-overlay');
        if (idleOverlay) idleOverlay.classList.remove('hidden');
        if (scanningOverlay) scanningOverlay.classList.add('hidden');
    }

    function scanFrame() {
        if (!isCameraActive) return;

        const video = document.getElementById('camera-video');
        const canvas = document.getElementById('qr-canvas');
        const ctx = canvas.getContext('2d');

        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.height = video.videoHeight;
            canvas.width = video.videoWidth;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: 'attemptBoth',
            });

            if (code && code.data) {
                handleScannedData(code.data);
                stopCamera();
                return;
            }
        }

        setTimeout(() => requestAnimationFrame(scanFrame), 150);
    }

    function handleScannedData(qrData) {
        document.getElementById('form-qr-data').value = qrData;
        const resultCard = document.getElementById('scan-result-card');
        if (resultCard) {
            resultCard.classList.remove('hidden');
            resultCard.scrollIntoView({ behavior: 'smooth' });
        }
        if (window.lucide) lucide.createIcons();
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });
</script>
@endsection