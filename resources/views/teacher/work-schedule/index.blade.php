@extends('layouts.teacher')

@section('page-title', 'Jadwal Kerja')

@section('content')
<div class="fade-in space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
            <i data-lucide="briefcase" class="w-6 h-6 text-white dark:text-navy-900"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Jadwal Kerja</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Jam kerja mingguan Anda</p>
        </div>
    </div>

    @if(count($scheduleData) > 0)
        <!-- Summary Card -->
        <div class="card p-6 bg-gradient-to-br from-navy-800 via-navy-900 to-slate-900 dark:from-gold-400 dark:via-gold-500 dark:to-yellow-500 text-white dark:text-navy-900 relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 dark:bg-navy-900/10 rounded-full -mr-16 -mt-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 dark:bg-navy-900/5 rounded-full -ml-12 -mb-12"></div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90 mb-2">Total Jam Kerja Mingguan</p>
                    <h2 class="text-4xl font-bold mb-2">{{ number_format($totalWeeklyHours, 1) }} Jam</h2>
                    <p class="text-sm opacity-75 flex items-center gap-2">
                        <i data-lucide="calendar-days" class="w-4 h-4"></i>
                        {{ $workDays }} hari kerja per minggu
                    </p>
                </div>
                <div class="w-20 h-20 bg-white/20 dark:bg-navy-900/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                    <i data-lucide="clock" class="w-10 h-10 text-white dark:text-navy-900"></i>
                </div>
            </div>
        </div>

        <!-- Schedule Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $dayShort = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            @endphp

            @foreach($scheduleData as $schedule)
            <div class="card p-5 relative overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1
                {{ $schedule['is_today'] 
                    ? 'border-2 border-navy-800 dark:border-gold-400 bg-gradient-to-br from-navy-50 to-slate-50 dark:from-navy-900/20 dark:to-slate-900/20' 
                    : 'border border-slate-200 dark:border-slate-700' }}"
                x-data="workScheduleCard('{{ $schedule['start_time'] }}', '{{ $schedule['end_time'] }}', {{ $schedule['is_today'] ? 'true' : 'false' }})">
                
                @if($schedule['is_today'])
                <div class="absolute top-0 right-0 px-3 py-1 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 text-xs font-bold rounded-bl-xl">
                    Hari Ini
                </div>
                @endif

                <!-- Header -->
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center
                            {{ $schedule['is_today'] 
                                ? 'bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 shadow-lg' 
                                : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                            <i data-lucide="calendar" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-navy-800 dark:text-white">
                                {{ $dayNames[$schedule['day_of_week']] }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $dayShort[$schedule['day_of_week']] }}
                            </p>
                        </div>
                    </div>
                    <div class="px-3 py-1.5 rounded-full text-xs font-bold
                        {{ $schedule['is_today'] 
                            ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900' 
                            : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                        <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                        {{ number_format($schedule['duration_minutes'] / 60, 1) }} Jam
                    </div>
                </div>

                <!-- Time Info -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="p-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-6 h-6 rounded-lg bg-green-500 flex items-center justify-center">
                                <i data-lucide="log-in" class="w-3.5 h-3.5 text-white"></i>
                            </div>
                            <p class="text-[10px] text-green-600 dark:text-green-400 font-semibold">Jam Masuk</p>
                        </div>
                        <p class="text-lg font-bold text-green-700 dark:text-green-300">
                            {{ $schedule['start_time'] }}
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-6 h-6 rounded-lg bg-red-500 flex items-center justify-center">
                                <i data-lucide="log-out" class="w-3.5 h-3.5 text-white"></i>
                            </div>
                            <p class="text-[10px] text-red-600 dark:text-red-400 font-semibold">Jam Pulang</p>
                        </div>
                        <p class="text-lg font-bold text-red-700 dark:text-red-300">
                            {{ $schedule['end_time'] }}
                        </p>
                    </div>
                </div>

                <!-- Real-time Progress Bar -->
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Progress Hari Ini</span>
                        <span class="text-xs font-bold" 
                              :class="progressPercent >= 100 ? 'text-green-600 dark:text-green-400' : (progressPercent > 0 ? 'text-navy-800 dark:text-gold-400' : 'text-slate-400')"
                              x-text="progressPercent.toFixed(1) + '%'"></span>
                    </div>
                    <div class="w-full h-3 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden relative">
                        <div class="h-full rounded-full transition-all duration-1000 ease-out"
                             :class="progressPercent >= 100 ? 'bg-gradient-to-r from-green-500 to-emerald-600' : (progressPercent > 0 ? 'bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500' : 'bg-slate-300 dark:bg-slate-600')"
                             :style="'width: ' + Math.min(progressPercent, 100) + '%'"></div>
                        
                        <!-- Current time indicator -->
                        <div x-show="progressPercent > 0 && progressPercent < 100"
                             class="absolute top-0 h-full w-1 bg-white dark:bg-navy-900 shadow-lg"
                             :style="'left: ' + Math.min(progressPercent, 100) + '%'"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2 text-[10px] text-slate-500 dark:text-slate-400">
                        <span x-text="currentTime"></span>
                        <span>Durasi: {{ $schedule['duration_text'] }}</span>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="flex items-center justify-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold"
                          :class="{
                              'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400': progressPercent >= 100,
                              'bg-navy-100 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300': progressPercent > 0 && progressPercent < 100,
                              'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400': progressPercent === 0
                          }">
                        <i :data-lucide="progressPercent >= 100 ? 'check-circle' : (progressPercent > 0 ? 'clock' : 'circle')" class="w-3 h-3 inline mr-1"></i>
                        <span x-text="progressPercent >= 100 ? 'Selesai' : (progressPercent > 0 ? 'Sedang Berjalan' : 'Belum Dimulai')"></span>
                    </span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Info Card -->
        <div class="card p-5 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-1">Informasi Jadwal Kerja</h4>
                    <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                        Jadwal kerja ini ditentukan oleh admin dan digunakan sebagai acuan jam masuk dan pulang Anda. 
                        Pastikan untuk melakukan presensi harian sesuai dengan jadwal yang telah ditetapkan.
                    </p>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="card p-12 text-center">
            <div class="w-24 h-24 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-lucide="briefcase" class="w-12 h-12 text-slate-400 dark:text-slate-500"></i>
            </div>
            <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-2">Belum Ada Jadwal Kerja</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                Hubungi admin untuk mengatur jadwal kerja Anda
            </p>
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 rounded-lg text-xs text-slate-600 dark:text-slate-400">
                <i data-lucide="mail" class="w-4 h-4"></i>
                admin@icbct.sch.id
            </div>
        </div>
    @endif
</div>

<script>
    function workScheduleCard(startTime, endTime, isToday) {
        return {
            currentTime: '--:--',
            progressPercent: 0,
            intervalId: null,
            init() {
                this.updateProgress();
                if (isToday) {
                    // Update setiap 10 detik untuk hari ini
                    this.intervalId = setInterval(() => {
                        this.updateProgress();
                    }, 10000);
                }
            },
            updateProgress() {
                const now = new Date();
                const hours = now.getHours().toString().padStart(2, '0');
                const minutes = now.getMinutes().toString().padStart(2, '0');
                this.currentTime = `${hours}:${minutes}`;

                const [startH, startM] = startTime.split(':').map(Number);
                const [endH, endM] = endTime.split(':').map(Number);

                const startMinutes = startH * 60 + startM;
                const endMinutes = endH * 60 + endM;
                const currentMinutes = now.getHours() * 60 + now.getMinutes();

                if (currentMinutes < startMinutes) {
                    // Belum mulai
                    this.progressPercent = 0;
                } else if (currentMinutes >= endMinutes) {
                    // Sudah selesai
                    this.progressPercent = 100;
                } else {
                    // Sedang berjalan
                    const totalDuration = endMinutes - startMinutes;
                    const elapsed = currentMinutes - startMinutes;
                    this.progressPercent = (elapsed / totalDuration) * 100;
                }
            },
            destroy() {
                if (this.intervalId) {
                    clearInterval(this.intervalId);
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
        
        // Re-init lucide icons setiap 1 detik untuk icon dinamis
        setInterval(() => {
            if (window.lucide) lucide.createIcons();
        }, 1000);
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