@extends('layouts.app')

@section('page-title', 'Presensi Kelas')

@section('content')
    <div class="fade-in space-y-6">

        <!-- Header -->
        <div class="flex items-center gap-4">
            <div
                class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i data-lucide="scan-line" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Presensi Per Kelas</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Scan QR saat masuk dan keluar kelas</p>
            </div>
        </div>

        <!-- Alert -->
        @if(session('success'))
            <div
                class="card p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800">
                <div class="flex items-start gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5"></i>
                    <p class="text-sm font-medium text-green-800 dark:text-green-300 whitespace-pre-line">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div
                class="card p-4 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800">
                <div class="flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5"></i>
                    <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Jadwal Mengajar Hari Ini -->
        <div class="card p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-navy-800 dark:text-white">Jadwal Mengajar Hari Ini</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                    </p>
                </div>
            </div>

            @if($todaySchedules->isEmpty())
                <div class="text-center py-8">
                    <i data-lucide="calendar-off" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada jadwal mengajar hari ini</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($todaySchedules as $schedule)
                        @php
                            $key = $schedule->classroom_id . '_' . $schedule->period;
                            $attendance = $todayAttendances[$key] ?? null;
                            $isDone = $attendance && $attendance->check_out_time;
                            $isInProgress = $attendance && $attendance->check_in_time && !$attendance->check_out_time;
                        @endphp
                        <div
                            class="p-4 rounded-xl border-2 transition-all {{ $isDone ? 'bg-green-50 dark:bg-green-900/10 border-green-200 dark:border-green-800' : ($isInProgress ? 'bg-yellow-50 dark:bg-yellow-900/10 border-yellow-200 dark:border-yellow-800' : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700') }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center shadow-lg">
                                        <span class="text-white dark:text-navy-900 font-bold text-sm">{{ $schedule->period }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-navy-800 dark:text-white">{{ $schedule->classroom->code }}
                                        </h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $schedule->subject?->name ?? 'Mata Pelajaran' }} •
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($isDone)
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-bold">
                                            <i data-lucide="check-circle" class="w-3 h-3"></i>
                                            Selesai
                                        </span>
                                    @elseif($isInProgress)
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full text-xs font-bold">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            Berlangsung
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-full text-xs font-bold">
                                            <i data-lucide="circle" class="w-3 h-3"></i>
                                            Belum
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Scanner -->
        <div class="card p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-gold-100 dark:bg-gold-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="scan-line" class="w-5 h-5 text-gold-600 dark:text-gold-400"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-navy-800 dark:text-white">Scan QR Kelas</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Arahkan kamera ke QR Code di pintu kelas</p>
                </div>
            </div>

            <form action="{{ route('class-attendance.store') }}" method="POST" id="scan-form">
                @csrf
                <input type="hidden" name="qr_data" id="qr-data-input">
            </form>

            <!-- Camera viewport -->
            <div class="flex justify-center">
            <div class="relative rounded-2xl overflow-hidden bg-slate-900" style="width:100%; max-width:360px; aspect-ratio:1/1;">
                <video id="camera-video" class="absolute inset-0 w-full h-full object-cover" autoplay playsinline muted></video>

                <!-- Idle overlay -->
                <div id="cam-idle" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/90 text-white gap-3">
                    <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center">
                        <i data-lucide="scan-line" class="w-8 h-8 text-white"></i>
                    </div>
                    <p class="text-sm font-medium text-slate-300">Tekan tombol untuk mulai scan</p>
                </div>

                <!-- Scan box overlay -->
                <div id="cam-scan-overlay" class="absolute inset-0 hidden">
                    <div class="absolute inset-0 bg-black/50"></div>
                    <div class="absolute" style="top:50%;left:50%;transform:translate(-50%,-50%);width:220px;height:220px;">
                        <div class="absolute inset-0 rounded-lg" style="box-shadow:0 0 0 9999px rgba(0,0,0,0.5);"></div>
                        <span class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-gold-400 rounded-tl-lg"></span>
                        <span class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-gold-400 rounded-tr-lg"></span>
                        <span class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-gold-400 rounded-bl-lg"></span>
                        <span class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-gold-400 rounded-br-lg"></span>
                        <div class="qr-laser absolute left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-gold-400 to-transparent" style="top:0;"></div>
                    </div>
                    <p class="absolute bottom-6 left-0 right-0 text-center text-xs text-white/70">Arahkan QR Code ke dalam kotak</p>
                </div>
            </div>
            </div><!-- /justify-center -->

            <div class="flex gap-2 mt-4 max-w-sm mx-auto">
                <button id="btn-start" onclick="startCamera()"
                        class="flex-1 px-4 py-3 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl font-bold flex items-center justify-center gap-2 transition-all hover:opacity-90">
                    <i data-lucide="camera" class="w-4 h-4"></i>
                    Mulai Scan
                </button>
                <button id="btn-stop" onclick="stopCamera()" class="hidden flex-1 px-4 py-3 bg-red-500 text-white rounded-xl font-bold flex items-center justify-center gap-2 transition-all hover:bg-red-600">
                    <i data-lucide="square" class="w-4 h-4"></i>
                    Stop Scan
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script>
        let camStream = null;
        let camScanning = false;
        const video = document.getElementById('camera-video');

        // Two reusable canvases: one for downsampled full frame, one for center crop
        const c1 = document.createElement('canvas');
        const ctx1 = c1.getContext('2d', { willReadFrequently: true });
        const c2 = document.createElement('canvas');
        const ctx2 = c2.getContext('2d', { willReadFrequently: true });

        function startCamera() {
            // Request high resolution — browser will use best available
            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'environment',
                    width:  { ideal: 1920, min: 640 },
                    height: { ideal: 1080, min: 480 }
                }
            })
            .then(stream => {
                camStream = stream;
                video.srcObject = stream;
                video.play();
                document.getElementById('cam-idle').classList.add('hidden');
                document.getElementById('cam-scan-overlay').classList.remove('hidden');
                document.getElementById('btn-start').classList.add('hidden');
                document.getElementById('btn-stop').classList.remove('hidden');
                camScanning = true;
                requestAnimationFrame(scanTick);
            })
            .catch(() => alert('Kamera tidak dapat diakses. Pastikan izin kamera sudah diberikan.'));
        }

        function stopCamera() {
            camScanning = false;
            if (camStream) { camStream.getTracks().forEach(t => t.stop()); camStream = null; }
            video.srcObject = null;
            document.getElementById('cam-idle').classList.remove('hidden');
            document.getElementById('cam-scan-overlay').classList.add('hidden');
            document.getElementById('btn-start').classList.remove('hidden');
            document.getElementById('btn-stop').classList.add('hidden');
        }

        function tryDecode(canvas, ctx, sx, sy, sw, sh, dw, dh) {
            canvas.width = dw;
            canvas.height = dh;
            ctx.drawImage(video, sx, sy, sw, sh, 0, 0, dw, dh);
            const img = ctx.getImageData(0, 0, dw, dh);
            return jsQR(img.data, dw, dh, { inversionAttempts: 'attemptBoth' });
        }

        function scanTick() {
            if (!camScanning) return;
            if (video.readyState < 2) { requestAnimationFrame(scanTick); return; }

            const vw = video.videoWidth, vh = video.videoHeight;
            if (!vw || !vh) { requestAnimationFrame(scanTick); return; }

            // --- Pass 1: Full frame downsampled to 640px wide (fast) ---
            const scale = Math.min(1, 640 / vw);
            const dw1 = Math.round(vw * scale), dh1 = Math.round(vh * scale);
            let code = tryDecode(c1, ctx1, 0, 0, vw, vh, dw1, dh1);

            // --- Pass 2: Center crop 60% (catches close-up / off-center QR) ---
            if (!code) {
                const cx = Math.round(vw * 0.2), cy = Math.round(vh * 0.2);
                const cw = Math.round(vw * 0.6), ch = Math.round(vh * 0.6);
                const dw2 = Math.min(cw, 480), dh2 = Math.round(ch * dw2 / cw);
                code = tryDecode(c2, ctx2, cx, cy, cw, ch, dw2, dh2);
            }

            if (code && code.data) {
                stopCamera();
                document.getElementById('qr-data-input').value = code.data;
                document.getElementById('scan-form').submit();
                return;
            }
            requestAnimationFrame(scanTick);
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>

    <style>
        .qr-laser {
            animation: qrLaser 1.8s ease-in-out infinite;
        }
        @keyframes qrLaser {
            0%   { top: 0; }
            50%  { top: calc(100% - 2px); }
            100% { top: 0; }
        }
    </style>
@endsection