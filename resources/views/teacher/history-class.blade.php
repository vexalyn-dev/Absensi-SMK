@extends('layouts.teacher')

@section('page-title', 'Riwayat Presensi Kelas')

@section('content')
<div class="fade-in space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="history" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Riwayat Presensi Kelas</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Lihat riwayat presensi per kelas</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('teacher.history', ['type' => 'daily', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="px-4 py-2 {{ $type === 'daily' ? 'bg-navy-800 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }} rounded-lg text-sm font-semibold transition-all">
                Presensi Harian
            </a>
            <a href="{{ route('teacher.history', ['type' => 'class', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="px-4 py-2 {{ $type === 'class' ? 'bg-navy-800 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }} rounded-lg text-sm font-semibold transition-all">
                Presensi Kelas
            </a>
        </div>
    </div>

    <!-- Filter & Export -->
    <div class="card p-5">
        <form method="GET" action="{{ route('teacher.history') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="type" value="{{ $type }}">
            
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all hover:shadow-lg flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Filter
                </button>
            </div>

            <div class="flex items-end">
                <a href="{{ route('teacher.history.export', ['type' => $type, 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                   class="w-full px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Export CSV
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card p-4 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="file-text" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Total Presensi</p>
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>

        <div class="card p-4 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Selesai</p>
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white">{{ $stats['completed'] }}</h3>
                </div>
            </div>
        </div>

        <div class="card p-4 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="check" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Hadir</p>
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white">{{ $stats['hadir'] }}</h3>
                </div>
            </div>
        </div>

        <div class="card p-4 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="clock" class="w-5 h-5 text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Terlambat</p>
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white">{{ $stats['terlambat'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Mapel</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Masuk</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Keluar</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($classAttendances as $att)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-navy-800 dark:text-white">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($att->date)->locale('id')->isoFormat('dddd') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-navy-800 dark:text-white">{{ $att->classroom->name ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-slate-700 dark:text-slate-300">
                                {{ $att->teachingSchedule?->subject?->name ?? $att->subject?->name ?? '-' }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-mono text-slate-700 dark:text-slate-300">Jam {{ $att->period }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($att->check_in_time)
                            <div class="flex items-center gap-2">
                                <i data-lucide="clock" class="w-4 h-4 text-green-500"></i>
                                <span class="text-sm font-mono text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($att->check_in_time)->format('H:i') }}</span>
                            </div>
                            @else
                            <span class="text-sm text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($att->check_out_time)
                            <div class="flex items-center gap-2">
                                <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i>
                                <span class="text-sm font-mono text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($att->check_out_time)->format('H:i') }}</span>
                            </div>
                            @else
                            <span class="text-sm text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                                {{ ($att->status === 'Hadir' || $att->status === 'Tepat Waktu' || $att->status === 'Selesai') ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                {{ $att->status === 'Terlambat' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                {{ ($att->status === 'Izin' || $att->status === 'Sakit') ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : '' }}
                                {{ $att->status === 'Alpha' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                {{ $att->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i data-lucide="inbox" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada data presensi kelas</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($classAttendances->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-700">
            {{ $classAttendances->links() }}
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
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection