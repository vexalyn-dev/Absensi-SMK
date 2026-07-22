@extends('layouts.app')

@section('page-title', 'Data Kelas')

@section('content')
    <div class="fade-in space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                    <i data-lucide="school" class="w-6 h-6 text-white dark:text-navy-900"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-navy-800 dark:text-white tracking-tight">Data Kelas</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola data kelas dan QR Code</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap justify-end">
                <!-- Tombol Regenerate Semua QR -->
                <form action="{{ route('classrooms.qr.regenerate-all') }}" method="POST"
                      onsubmit="return confirm('Regenerate QR Code semua kelas ke format JSON baru? Proses ini mungkin butuh beberapa saat.')">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-bold transition-all shadow hover:shadow-md hover:-translate-y-0.5">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        Regenerate Semua QR
                    </button>
                </form>
                <a href="{{ route('classrooms.create') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Kelas
                </a>
            </div>
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

        <!-- Class Level Sections -->
        @php
            $classLevels = ['X', 'XI', 'XII', 'Ruangan Bersama'];
            $levelColors = [
                'X' => 'from-blue-500 to-cyan-500',
                'XI' => 'from-violet-500 to-purple-500',
                'XII' => 'from-emerald-500 to-teal-500',
                'Ruangan Bersama' => 'from-purple-500 to-indigo-500'
            ];
        @endphp

        @foreach($classLevels as $level)
            @if(isset($classrooms[$level]) && $classrooms[$level]->count() > 0)
                <div class="space-y-4">
                    <!-- Section Header -->
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-8 bg-gradient-to-b {{ $levelColors[$level] }} rounded-full"></div>
                        <h2 class="text-xl font-bold text-navy-800 dark:text-white">{{ $level === 'Ruangan Bersama' ? 'Ruangan Bersama' : 'Kelas ' . $level }}</h2>
                        <span
                            class="px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-full text-xs font-bold">
                            {{ $classrooms[$level]->count() }} {{ $level === 'Ruangan Bersama' ? 'ruangan' : 'kelas' }}
                        </span>
                    </div>

                    <!-- Classroom Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($classrooms[$level] as $classroom)
                            <div class="card p-5 hover:shadow-lg transition-all group">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-12 h-12 bg-gradient-to-br {{ $classroom->type === 'shared' ? 'from-purple-600 to-indigo-600' : 'from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500' }} rounded-xl flex items-center justify-center shadow-lg">
                                            @if($classroom->type === 'shared')
                                                <i data-lucide="building-2" class="w-5 h-5 text-white"></i>
                                            @else
                                                <span
                                                    class="text-white dark:text-navy-900 font-bold text-sm">{{ substr($classroom->name, 0, 2) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-base font-bold text-navy-800 dark:text-white">{{ $classroom->name }}</h3>
                                                @if($classroom->type === 'shared')
                                                    <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                                        Shared
                                                    </span>
                                                @endif
                                            </div>
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
                                        class="flex-1 px-3 py-2.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-lg text-xs font-bold text-center hover:-translate-y-0.5 transition-all flex items-center justify-center gap-1.5">
                                        <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
                                        QR Code
                                    </a>
                                    <form action="{{ route('classrooms.qr.regenerate', $classroom) }}" method="POST" class="contents">
                                        @csrf
                                        <button type="submit"
                                            class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-lg transition-all flex items-center justify-center border border-amber-200 dark:border-amber-800"
                                            title="Regenerate QR Code">
                                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('classrooms.edit', $classroom) }}"
                                        class="w-10 h-10 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-lg text-xs font-semibold transition-all flex items-center justify-center border border-slate-200 dark:border-slate-600"
                                        title="Edit Kelas">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <button onclick="openDeleteModal({{ $classroom->id }}, '{{ $classroom->name }}')"
                                        class="w-10 h-10 bg-slate-100 dark:bg-slate-700 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 hover:border-red-200 dark:hover:border-red-800 text-slate-600 dark:text-slate-300 rounded-lg text-xs font-semibold transition-all flex items-center justify-center border border-slate-200 dark:border-slate-600"
                                        title="Hapus Kelas">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        <!-- Empty State -->
        @if($classrooms->isEmpty())
            <div class="card p-12 text-center">
                <div
                    class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="school" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                </div>
                <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-2">Belum Ada Kelas</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-4">Tidak ada data kelas untuk
                    ditampilkan. Tambahkan kelas terlebih dahulu.</p>
                <a href="{{ route('classrooms.create') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Kelas Pertama
                </a>
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl transform transition-all">
            <div class="flex items-center gap-4 mb-5">
                <div
                    class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-7 h-7 text-red-600 dark:text-red-400"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-navy-800 dark:text-white">Hapus Kelas?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 mb-5">
                <p class="text-sm text-slate-700 dark:text-slate-300" id="delete-message">
                    Yakin ingin menghapus kelas ini? Data yang dihapus tidak dapat dikembalikan.
                </p>
            </div>

            <div class="flex gap-3">
                <button onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all">
                    Batal
                </button>
                <button onclick="confirmDelete()"
                    class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-red-500/30">
                    <i data-lucide="trash-2" class="w-4 h-4 inline mr-1"></i>
                    Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Form (Hidden) -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        let deleteUrl = '';

        function openDeleteModal(classroomId, className) {
            deleteUrl = `/classrooms/${classroomId}`;
            document.getElementById('delete-message').textContent = `Yakin ingin menghapus kelas "${className}"? Data yang dihapus tidak dapat dikembalikan.`;
            document.getElementById('delete-modal').classList.remove('hidden');
            if (window.lucide) lucide.createIcons();
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
            deleteUrl = '';
        }

        function confirmDelete() {
            if (deleteUrl) {
                const form = document.getElementById('delete-form');
                form.action = deleteUrl;
                form.submit();
            }
        }

        // Close modal when clicking outside
        document.getElementById('delete-modal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });

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