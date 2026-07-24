@extends('layouts.app')

@section('page-title', 'Presensi Kelas')

@section('content')
<div class="fade-in space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg">
            <i data-lucide="scan-line" class="w-6 h-6 text-white dark:text-navy-900"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Presensi Per Kelas</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Scan QR saat masuk dan keluar kelas</p>
        </div>
    </div>

    <!-- Flash alerts (dari redirect server) -->
    @if(session('success'))
        <div class="card p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800">
            <div class="flex items-start gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5"></i>
                <p class="text-sm font-medium text-green-800 dark:text-green-300 whitespace-pre-line">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="card p-4 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800">
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
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
            </div>
        </div>

        @if($todaySchedules->isEmpty())
            <div class="text-center py-8">
                <i data-lucide="calendar-off" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada jadwal mengajar hari ini</p>
            </div>
        @else
            <div class="space-y-3" id="schedule-list">
                @foreach($todaySchedules as $schedule)
                    @php
                        $key        = $schedule->classroom_id . '_' . $schedule->period;
                        $attendance = $todayAttendances[$key] ?? null;
                        $isDone     = $attendance && $attendance->check_out_time;
                        $isInProg   = $attendance && $attendance->check_in_time && !$attendance->check_out_time;
                    @endphp
                    <div class="p-4 rounded-xl border-2 transition-all
                        {{ $isDone   ? 'bg-green-50  dark:bg-green-900/10  border-green-200  dark:border-green-800'  : '' }}
                        {{ $isInProg ? 'bg-yellow-50 dark:bg-yellow-900/10 border-yellow-200 dark:border-yellow-800' : '' }}
                        {{ (!$isDone && !$isInProg) ? 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700' : '' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center shadow-lg">
                                    <span class="text-white dark:text-navy-900 font-bold text-sm">{{ $schedule->period }}</span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-navy-800 dark:text-white">{{ $schedule->classroom->code ?? $schedule->classroom->name }}</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $schedule->subject?->name ?? 'Mata Pelajaran' }} &bull;
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                @if($isDone)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-bold">
                                        <i data-lucide="check-circle" class="w-3 h-3"></i> Selesai
                                    </span>
                                @elseif($isInProg)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full text-xs font-bold">
                                        <i data-lucide="clock" class="w-3 h-3"></i> Berlangsung
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-full text-xs font-bold">
                                        <i data-lucide="circle" class="w-3 h-3"></i> Belum
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Scanner Card -->
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

        <!-- Hidden form (fallback non-JS submit) -->
        <form action="{{ route('class-attendance.store') }}" method="POST" id="scan-form" class="hidden">
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

                <!-- Scanning overlay with brackets -->
                <div id="cam-scan-overlay" class="absolute inset-0 hidden">
                    <div class="absolute inset-0 bg-black/40"></div>
                    <div class="absolute" style="top:50%;left:50%;transform:translate(-50%,-50%);width:220px;height:220px;">
                        <div class="absolute inset-0 rounded-lg" style="box-shadow:0 0 0 9999px rgba(0,0,0,0.45);"></div>
                        <span class="absolute top-0 left-0   w-8 h-8 border-t-4 border-l-4 border-gold-400 rounded-tl-lg"></span>
                        <span class="absolute top-0 right-0  w-8 h-8 border-t-4 border-r-4 border-gold-400 rounded-tr-lg"></span>
                        <span class="absolute bottom-0 left-0  w-8 h-8 border-b-4 border-l-4 border-gold-400 rounded-bl-lg"></span>
                        <span class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-gold-400 rounded-br-lg"></span>
                        <div class="qr-laser absolute left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-gold-400 to-transparent" style="top:0;"></div>
                    </div>
                    <p class="absolute bottom-6 left-0 right-0 text-center text-xs text-white/70">Arahkan QR Code ke dalam kotak</p>
                </div>

                <!-- Success flash overlay (shown briefly when QR detected) -->
                <div id="cam-success-flash" class="absolute inset-0 hidden flex-col items-center justify-center bg-green-600/80 text-white gap-2 z-10">
                    <i data-lucide="check-circle" class="w-12 h-12"></i>
                    <p class="text-sm font-bold">QR Terbaca!</p>
                </div>

                <!-- Processing overlay -->
                <div id="cam-processing" class="absolute inset-0 hidden flex-col items-center justify-center bg-slate-900/90 text-white gap-3 z-10">
                    <div class="w-10 h-10 border-4 border-white/30 border-t-white rounded-full animate-spin"></div>
                    <p class="text-sm font-medium text-slate-300">Memproses presensi...</p>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-2 mt-4 max-w-sm mx-auto">
            <button id="btn-start" onclick="startCamera()"
                    class="flex-1 px-4 py-3 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl font-bold flex items-center justify-center gap-2 transition-all hover:opacity-90 active:scale-95">
                <i data-lucide="camera" class="w-4 h-4"></i>
                Mulai Scan
            </button>
            <button id="btn-stop" onclick="stopCamera()"
                    class="hidden flex-1 px-4 py-3 bg-red-500 text-white rounded-xl font-bold flex items-center justify-center gap-2 transition-all hover:bg-red-600 active:scale-95">
                <i data-lucide="square" class="w-4 h-4"></i>
                Stop Scan
            </button>
        </div>

        <!-- Inline result (shows without full page reload) -->
        <div id="scan-result-box" class="hidden mt-4 max-w-sm mx-auto rounded-2xl p-4 border-2 transition-all">
            <div class="flex items-start gap-3">
                <div id="result-icon-wrap" class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0">
                    <i id="result-icon" data-lucide="check" class="w-4 h-4 text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p id="result-message" class="text-sm font-bold whitespace-pre-line"></p>
                </div>
                <button onclick="dismissResult()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex-shrink-0">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>

</div><!-- /fade-in -->

<!-- Toast container -->
<div id="toast-container" class="fixed bottom-4 right-4 z-[9999] space-y-3 pointer-events-none"></div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    // ─────────────────────────────────────────────────────────────────────────
    // State
    // ─────────────────────────────────────────────────────────────────────────
    let camStream   = null;
    let camScanning = false;
    let submitting  = false;   // anti-double-scan guard

    const video = document.getElementById('camera-video');

    // Two off-screen canvases with willReadFrequently for perf
    const c1   = document.createElement('canvas');
    const ctx1 = c1.getContext('2d', { willReadFrequently: true });
    const c2   = document.createElement('canvas');
    const ctx2 = c2.getContext('2d', { willReadFrequently: true });

    // ─────────────────────────────────────────────────────────────────────────
    // Camera control
    // ─────────────────────────────────────────────────────────────────────────
    function startCamera() {
        submitting = false;
        dismissResult();

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showToast('Akses kamera tidak didukung di peramban ini atau membutuhkan jaringan HTTPS.', 'error');
            return;
        }

        const constraintsList = [
            { video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } } },
            { video: { facingMode: 'environment' } },
            { video: true }
        ];

        function tryNextConstraint(index) {
            if (index >= constraintsList.length) {
                showToast('Kamera tidak dapat diakses. Pastikan izin kamera sudah diberikan.', 'error');
                return;
            }
            navigator.mediaDevices.getUserMedia(constraintsList[index])
            .then(stream => {
                camStream     = stream;
                video.srcObject = stream;
                video.play();

                document.getElementById('cam-idle').classList.add('hidden');
                document.getElementById('cam-scan-overlay').classList.remove('hidden');
                document.getElementById('btn-start').classList.add('hidden');
                document.getElementById('btn-stop').classList.remove('hidden');

                camScanning = true;
                requestAnimationFrame(scanTick);
            })
            .catch(err => {
                console.warn('Camera constraint index ' + index + ' failed:', err);
                tryNextConstraint(index + 1);
            });
        }

        tryNextConstraint(0);
    }

    function stopCamera() {
        camScanning = false;
        if (camStream) { camStream.getTracks().forEach(t => t.stop()); camStream = null; }
        video.srcObject = null;

        document.getElementById('cam-idle').classList.remove('hidden');
        document.getElementById('cam-scan-overlay').classList.add('hidden');
        document.getElementById('cam-success-flash').classList.add('hidden');
        document.getElementById('cam-success-flash').classList.remove('flex');
        document.getElementById('cam-processing').classList.add('hidden');
        document.getElementById('cam-processing').classList.remove('flex');
        document.getElementById('btn-start').classList.remove('hidden');
        document.getElementById('btn-stop').classList.add('hidden');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Dual-pass QR decode (fast full-frame + close-up center crop)
    // ─────────────────────────────────────────────────────────────────────────
    function tryDecode(canvas, ctx, sx, sy, sw, sh, dw, dh) {
        canvas.width  = dw;
        canvas.height = dh;
        ctx.drawImage(video, sx, sy, sw, sh, 0, 0, dw, dh);
        const img = ctx.getImageData(0, 0, dw, dh);
        return jsQR(img.data, dw, dh, { inversionAttempts: 'attemptBoth' });
    }

    function scanTick() {
        if (!camScanning || submitting) return;
        if (video.readyState < 2) { requestAnimationFrame(scanTick); return; }

        const vw = video.videoWidth, vh = video.videoHeight;
        if (!vw || !vh) { requestAnimationFrame(scanTick); return; }

        // Pass 1: full frame downsampled to 640px (fast path)
        const scale = Math.min(1, 640 / vw);
        let code = tryDecode(c1, ctx1, 0, 0, vw, vh, Math.round(vw * scale), Math.round(vh * scale));

        // Pass 2: center 60% crop (handles close-up / off-center QR)
        if (!code) {
            const cx = Math.round(vw * 0.2), cy = Math.round(vh * 0.2);
            const cw = Math.round(vw * 0.6), ch = Math.round(vh * 0.6);
            const dw = Math.min(cw, 480);
            code = tryDecode(c2, ctx2, cx, cy, cw, ch, dw, Math.round(ch * dw / cw));
        }

        if (code && code.data) {
            onQrDetected(code.data);
            return;
        }

        requestAnimationFrame(scanTick);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // On QR detected: flash, then submit via AJAX
    // ─────────────────────────────────────────────────────────────────────────
    function onQrDetected(data) {
        submitting  = true;
        camScanning = false;

        // Brief green flash before processing spinner
        const flash = document.getElementById('cam-success-flash');
        flash.classList.remove('hidden');
        flash.classList.add('flex');

        setTimeout(() => {
            flash.classList.remove('flex');
            flash.classList.add('hidden');

            // Show processing spinner
            const proc = document.getElementById('cam-processing');
            proc.classList.remove('hidden');
            proc.classList.add('flex');

            submitScan(data);
        }, 500);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX submit — no full-page reload
    // ─────────────────────────────────────────────────────────────────────────
    function submitScan(qrData) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        fetch('{{ route("class-attendance.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type':  'application/json',
                'X-CSRF-TOKEN':  csrfToken,
                'Accept':        'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ qr_data: qrData })
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            stopCamera();

            const success = (status >= 200 && status < 300) || data?.success;
            const message = data?.message || (success ? 'Presensi berhasil!' : 'Terjadi kesalahan.');

            showInlineResult(success, message);
            showToast(message, success ? 'success' : 'error');

            if (success) {
                // Reload schedule list after short delay
                setTimeout(() => window.location.reload(), 2500);
            }
        })
        .catch(err => {
            console.error('Submit error:', err);
            stopCamera();
            showInlineResult(false, 'Gagal mengirim data. Periksa koneksi internet Anda.');
            showToast('Gagal mengirim data. Periksa koneksi internet Anda.', 'error');
        })
        .finally(() => {
            submitting = false;
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Inline result box
    // ─────────────────────────────────────────────────────────────────────────
    function showInlineResult(success, message) {
        const box      = document.getElementById('scan-result-box');
        const iconWrap = document.getElementById('result-icon-wrap');
        const icon     = document.getElementById('result-icon');
        const msg      = document.getElementById('result-message');

        box.classList.remove('hidden', 'border-green-300', 'border-red-300',
            'bg-green-50', 'bg-red-50',
            'dark:bg-green-900/20', 'dark:bg-red-900/20',
            'dark:border-green-700', 'dark:border-red-700');

        if (success) {
            box.classList.add('border-green-300', 'bg-green-50', 'dark:bg-green-900/20', 'dark:border-green-700');
            iconWrap.className = 'w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 bg-green-500';
            icon.setAttribute('data-lucide', 'check');
            msg.className = 'text-sm font-bold whitespace-pre-line text-green-800 dark:text-green-300';
        } else {
            box.classList.add('border-red-300', 'bg-red-50', 'dark:bg-red-900/20', 'dark:border-red-700');
            iconWrap.className = 'w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 bg-red-500';
            icon.setAttribute('data-lucide', 'x');
            msg.className = 'text-sm font-bold whitespace-pre-line text-red-800 dark:text-red-300';
        }

        msg.textContent = message;
        if (window.lucide) lucide.createIcons();

        // Show scan-again button on error
        if (!success) {
            const existing = document.getElementById('btn-scan-again');
            if (!existing) {
                const btn = document.createElement('button');
                btn.id        = 'btn-scan-again';
                btn.className = 'mt-3 w-full px-4 py-2.5 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all hover:opacity-90';
                btn.innerHTML = '<i data-lucide="refresh-cw" class="w-4 h-4"></i> Scan Ulang';
                btn.onclick   = () => { dismissResult(); startCamera(); };
                box.appendChild(btn);
                if (window.lucide) lucide.createIcons();
            }
        }
    }

    function dismissResult() {
        const box = document.getElementById('scan-result-box');
        box.classList.add('hidden');
        const btn = document.getElementById('btn-scan-again');
        if (btn) btn.remove();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Toast notifications
    // ─────────────────────────────────────────────────────────────────────────
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const icons  = { success: 'check-circle', error: 'alert-circle', warning: 'alert-triangle', info: 'info' };
        const colors = {
            success: 'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-700 text-green-800 dark:text-green-300',
            error:   'bg-red-50   dark:bg-red-900/30   border-red-200   dark:border-red-700   text-red-800   dark:text-red-300',
            warning: 'bg-yellow-50 dark:bg-yellow-900/30 border-yellow-200 dark:border-yellow-700 text-yellow-800 dark:text-yellow-300',
            info:    'bg-blue-50  dark:bg-blue-900/30  border-blue-200  dark:border-blue-700  text-blue-800  dark:text-blue-300',
        };

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto flex items-start gap-3 px-4 py-3 ${colors[type]} border rounded-xl shadow-xl
            max-w-sm transform transition-all duration-300 translate-x-full opacity-0`;
        toast.innerHTML = `
            <i data-lucide="${icons[type]}" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
            <p class="flex-1 text-sm font-medium leading-snug">${message}</p>
            <button onclick="this.closest('div').remove()" class="flex-shrink-0 opacity-60 hover:opacity-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>`;

        container.appendChild(toast);
        if (window.lucide) lucide.createIcons();

        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        });

        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Init
    // ─────────────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });

    window.addEventListener('beforeunload', stopCamera);
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
    .fade-in { animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    #toast-container { pointer-events: none; }
    #toast-container > * { pointer-events: auto; }
</style>
@endsection
