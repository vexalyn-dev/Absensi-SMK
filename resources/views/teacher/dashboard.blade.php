@extends('layouts.teacher')

@section('page-title', 'Dashboard')

@section('content')
<div class="fade-in space-y-6">
    
    <!-- Welcome Card -->
    <div class="card p-6 bg-gradient-to-br from-navy-800 via-navy-900 to-slate-900 dark:from-gold-400 dark:to-gold-400 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-white/80 dark:text-navy-900/80">Semangat mengajar hari ini. Anda memiliki {{ $todaySchedules->count() }} jadwal mengajar.</p>
            </div>
            <div class="hidden sm:block">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i data-lucide="school" class="w-10 h-10 text-white dark:text-navy-900"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="card p-3 sm:p-5 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i data-lucide="calendar-check" class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Hadir Bulan Ini</p>
                    <h3 class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white mt-0.5">{{ $stats['hadir'] }}</h3>
                    <p class="text-[10px] text-blue-500 mt-0.5">Bulan ini</p>
                </div>
            </div>
        </div>

        <div class="card p-3 sm:p-5 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i data-lucide="clock" class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Terlambat</p>
                    <h3 class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white mt-0.5">{{ $stats['terlambat'] }}</h3>
                    <p class="text-[10px] text-yellow-600 mt-0.5">Perlu perbaikan</p>
                </div>
            </div>
        </div>

        <div class="card p-3 sm:p-5 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i data-lucide="file-text" class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Izin/Sakit</p>
                    <h3 class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white mt-0.5">{{ $stats['izin'] }}</h3>
                    <p class="text-[10px] text-green-500 mt-0.5">Hari</p>
                </div>
            </div>
        </div>

        <div class="card p-3 sm:p-5 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i data-lucide="x-circle" class="w-5 h-5 sm:w-6 sm:h-6 text-red-600 dark:text-red-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Alpha</p>
                    <h3 class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white mt-0.5">{{ $stats['alpha'] }}</h3>
                    <p class="text-[10px] text-red-500 mt-0.5">Tanpa keterangan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Mengajar Hari Ini dengan Progress -->
    <div class="card p-5 sm:p-6">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-navy-800 dark:text-white">Jadwal Mengajar Hari Ini</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
            </div>
            <span class="px-3 py-1.5 bg-navy-100 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 rounded-full text-xs font-bold">
                {{ $todaySchedules->count() }} Kelas
            </span>
        </div>

        @if($todaySchedules->count() > 0)
            @php
                $totalClasses = $todaySchedules->count();
                $completedClasses = $todaySchedules->filter(function($schedule) {
                    return $schedule->classAttendances->where('user_id', auth()->id())->whereNotNull('check_out_time')->count() > 0;
                })->count();
                $progress = $totalClasses > 0 ? ($completedClasses / $totalClasses) * 100 : 0;
            @endphp
            
            <!-- Progress Bar -->
            <div class="mb-5">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">Progress Mengajar</p>
                    <p class="text-xs font-bold text-navy-800 dark:text-white">{{ round($progress) }}%</p>
                </div>
                <div class="w-full h-3 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-400 rounded-full transition-all duration-500" 
                         style="width: {{ $progress }}%"></div>
                </div>
                <div class="flex items-center justify-between mt-2 text-xs">
                    <span class="text-green-600 dark:text-green-400 font-semibold">
                        <i data-lucide="check-circle" class="w-3 h-3 inline mr-1"></i>
                        {{ $completedClasses }} Selesai
                    </span>
                    <span class="text-orange-600 dark:text-orange-400 font-semibold">
                        <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                        {{ $totalClasses - $completedClasses }} Belum
                    </span>
                </div>
            </div>

            <!-- List Jadwal -->
            <div class="space-y-3">
                @foreach($todaySchedules as $schedule)
                    @php
                        $attendance = $schedule->classAttendances->where('user_id', auth()->id())->first();
                        $isCompleted = $attendance && $attendance->check_out_time;
                        $isInProgress = $attendance && $attendance->check_in_time && !$attendance->check_out_time;
                    @endphp
                    <div class="p-4 rounded-xl border-2 transition-all
                        {{ $isCompleted 
                            ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' 
                            : ($isInProgress 
                                ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' 
                                : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700') }}">
                        
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                    {{ $isCompleted 
                                        ? 'bg-green-100 dark:bg-green-900/30' 
                                        : ($isInProgress 
                                            ? 'bg-blue-100 dark:bg-blue-900/30' 
                                            : 'bg-slate-200 dark:bg-slate-600') }}">
                                    @if($isCompleted)
                                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                                    @elseif($isInProgress)
                                        <i data-lucide="clock" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                                    @else
                                        <i data-lucide="circle" class="w-5 h-5 text-slate-500 dark:text-slate-400"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-navy-800 dark:text-white">
                                        {{ $schedule->classroom->name ?? '-' }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ $schedule->subject->name ?? '-' }} • Jam ke-{{ $schedule->period }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex-shrink-0">
                                @if($isCompleted)
                                    <span class="px-2.5 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-bold">
                                        Selesai
                                    </span>
                                @elseif($isInProgress)
                                    <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-xs font-bold">
                                        Berlangsung
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-400 rounded-full text-xs font-bold">
                                        Belum
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="calendar-x" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                </div>
                <p class="text-sm font-semibold text-navy-800 dark:text-white mb-1">Tidak ada jadwal mengajar hari ini</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Nikmati hari libur Anda!</p>
            </div>
        @endif
    </div>

    <!-- JADWAL KERJA (Work Schedule) -->
    <div class="card p-5 sm:p-6">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="briefcase" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-navy-800 dark:text-white">Jadwal Kerja</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Jam kerja mingguan Anda</p>
                </div>
            </div>
            <span class="px-3 py-1.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full text-xs font-bold">
                {{ $workSchedule->count() }} Hari
            </span>
        </div>

        @if($workSchedule->count() > 0)
            <div class="space-y-3">
                @foreach($workSchedule as $work)
                @php
                    $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    $isToday = $work->day_of_week === now()->dayOfWeek;
                @endphp
                <div class="p-4 rounded-xl border-2 transition-all
                    {{ $isToday 
                        ? 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-800' 
                        : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700' }}">
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                {{ $isToday 
                                    ? 'bg-purple-100 dark:bg-purple-900/30' 
                                    : 'bg-slate-200 dark:bg-slate-600' }}">
                                <i data-lucide="calendar" class="w-5 h-5 {{ $isToday ? 'text-purple-600 dark:text-purple-400' : 'text-slate-500 dark:text-slate-400' }}"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-navy-800 dark:text-white">
                                    {{ $dayNames[$work->day_of_week] }}
                                    @if($isToday)
                                    <span class="ml-2 px-2 py-0.5 bg-purple-500 text-white rounded-full text-[10px]">Hari Ini</span>
                                    @endif
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ \Carbon\Carbon::parse($work->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($work->end_time)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <p class="text-xs font-semibold text-navy-800 dark:text-white">
                                {{ \Carbon\Carbon::parse($work->start_time)->diffInHours(\Carbon\Carbon::parse($work->end_time)) }} Jam
                            </p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">
                                Total kerja
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Summary -->
                <div class="mt-4 p-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl border border-purple-200 dark:border-purple-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-purple-600 dark:text-purple-400 font-semibold">Total Jam Kerja Mingguan</p>
                            <p class="text-2xl font-bold text-purple-800 dark:text-purple-300">
                                {{ $workSchedule->sum(fn($w) => \Carbon\Carbon::parse($w->start_time)->diffInHours(\Carbon\Carbon::parse($w->end_time))) }} Jam
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                            <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="briefcase" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                </div>
                <p class="text-sm font-semibold text-navy-800 dark:text-white mb-1">Belum ada jadwal kerja</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Hubungi admin untuk mengatur jadwal kerja Anda</p>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });
</script>

<style>
    .fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection