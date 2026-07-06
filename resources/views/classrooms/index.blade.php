@extends('layouts.app')

@section('page-title', 'Data Kelas')

@section('content')
    <div class="fade-in space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg">
                    <i data-lucide="school" class="w-6 h-6 text-white dark:text-navy-900"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-navy-800 dark:text-white">Data Kelas</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola data kelas dan QR Code</p>
                </div>
            </div>
            <a href="{{ route('classrooms.create') }}" class="btn-primary flex items-center gap-2 w-fit">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Kelas
            </a>
        </div>

        <!-- Alert -->
        @if(session('success'))
            <div
                class="card p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800">
                <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                    <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Classroom Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($classrooms as $classroom)
                <div class="card p-5 hover:shadow-lg transition-all group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg">
                                <span
                                    class="text-white dark:text-navy-900 font-bold text-sm">{{ substr($classroom->name, 0, 2) }}</span>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-navy-800 dark:text-white">{{ $classroom->name }}</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Kode: {{ $classroom->code }}</p>
                            </div>
                        </div>
                        <span
                            class="px-2 py-1 text-[10px] font-bold rounded-full {{ $classroom->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-slate-100 text-slate-500' }}">
                            {{ $classroom->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="space-y-2 mb-4 text-xs">
                        
                        <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                            <i data-lucide="calendar-clock" class="w-3.5 h-3.5"></i>
                            <span>{{ $classroom->teaching_schedules_count }} jadwal mengajar</span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('classrooms.qr', $classroom) }}"
                            class="flex-1 px-3 py-2 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-lg text-xs font-bold text-center hover:-translate-y-0.5 transition-all">
                            <i data-lucide="qr-code" class="w-3.5 h-3.5 inline mr-1"></i>
                            QR Code
                        </a>
                        <a href="{{ route('classrooms.edit', $classroom) }}"
                            class="px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-all">
                            <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i data-lucide="school" class="w-16 h-16 text-slate-300 dark:text-slate-600 mx-auto mb-4"></i>
                    <p class="text-slate-500 dark:text-slate-400">Belum ada data kelas</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>
@endsection