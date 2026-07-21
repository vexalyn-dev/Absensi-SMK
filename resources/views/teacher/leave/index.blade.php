@extends('layouts.teacher')

@section('page-title', 'Pengajuan Izin/Sakit')

@section('content')
<div class="fade-in space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 flex-shrink-0">
                <i data-lucide="file-text" class="w-5 h-5 sm:w-6 sm:h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white">Pengajuan Izin/Sakit</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Ajukan izin atau sakit kepada admin</p>
            </div>
        </div>
        <a href="{{ route('teacher.leave.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:px-5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 w-full sm:w-auto">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Ajukan Izin
        </a>
    </div>

    <!-- Stat Cards -->
    @php
        $total    = $leaveRequests->total();
        $pending  = $leaveRequests->getCollection()->where('status','pending')->count();
        $approved = $leaveRequests->getCollection()->where('status','approved')->count();
        $rejected = $leaveRequests->getCollection()->where('status','rejected')->count();
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="card p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="file-text" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Total</p>
                <p class="text-lg font-bold text-navy-800 dark:text-white">{{ $leaveRequests->total() }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="clock" class="w-4 h-4 text-yellow-600 dark:text-yellow-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Pending</p>
                <p class="text-lg font-bold text-navy-800 dark:text-white">{{ $pending }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Disetujui</p>
                <p class="text-lg font-bold text-navy-800 dark:text-white">{{ $approved }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="x-circle" class="w-4 h-4 text-red-600 dark:text-red-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Ditolak</p>
                <p class="text-lg font-bold text-navy-800 dark:text-white">{{ $rejected }}</p>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2000)" x-show="show"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="card p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800">
        <div class="flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
            <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="card p-4 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800">
        <div class="flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
            <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Leave Requests List -->
    <div class="space-y-4">
        @forelse($leaveRequests as $leave)
        <div class="card p-4 sm:p-5 hover:shadow-lg transition-all">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3 sm:gap-4 flex-1 min-w-0">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center flex-shrink-0
                        {{ $leave->type === 'sakit' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-blue-100 dark:bg-blue-900/30' }}">
                        <i data-lucide="{{ $leave->type === 'sakit' ? 'thermometer' : 'calendar-off' }}" 
                           class="w-5 h-5 sm:w-6 sm:h-6 {{ $leave->type === 'sakit' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400' }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <h3 class="text-sm sm:text-base font-bold text-navy-800 dark:text-white">
                                {{ $leave->type_text }}
                            </h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $leave->status_badge }}">
                                {{ $leave->status_text }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mb-2 line-clamp-2">{{ $leave->reason }}</p>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 text-xs text-slate-500 dark:text-slate-400">
                            <div class="flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 flex-shrink-0"></i>
                                <span>{{ $leave->start_date->format('d M Y') }} – {{ $leave->end_date->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 flex-shrink-0"></i>
                                    {{ $leave->duration }} hari
                                </span>
                                <span class="hidden sm:flex items-center gap-1">
                                    <i data-lucide="calendar-clock" class="w-3.5 h-3.5 flex-shrink-0"></i>
                                    {{ $leave->created_at->format('d M Y H:i') }}
                                </span>
                            </div>
                        </div>
                        @if($leave->status === 'approved' && $leave->admin_notes)
                        <div class="mt-3 p-2.5 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <p class="text-xs text-green-700 dark:text-green-300">
                                <strong>Catatan:</strong> {{ $leave->admin_notes }}
                            </p>
                        </div>
                        @endif
                        @if($leave->status === 'rejected' && $leave->admin_notes)
                        <div class="mt-3 p-2.5 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                            <p class="text-xs text-red-700 dark:text-red-300">
                                <strong>Ditolak:</strong> {{ $leave->admin_notes }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-2 flex-shrink-0">
                    <a href="{{ route('teacher.leave.show', $leave) }}" 
                       class="p-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-lg transition-all" 
                       title="Lihat Detail">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </a>
                    @if($leave->status === 'pending')
                    <button type="button"
                            class="delete-trigger p-2 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 rounded-lg transition-all" 
                            data-leave-id="{{ $leave->id }}"
                            data-leave-type="{{ $leave->type_text }}"
                            title="Batalkan Pengajuan">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="card p-10 sm:p-12 text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center mx-auto mb-5">
                <i data-lucide="file-text" class="w-8 h-8 sm:w-10 sm:h-10 text-slate-400 dark:text-slate-500"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold text-navy-800 dark:text-white mb-2">Belum Ada Pengajuan</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-4">Anda belum memiliki pengajuan izin atau sakit.</p>
            <a href="{{ route('teacher.leave.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Buat Pengajuan Pertama
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($leaveRequests->hasPages())
    <div class="card p-4">
        {{ $leaveRequests->links() }}
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl transform transition-all">
        <div class="flex items-center gap-4 mb-5">
            <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                <i data-lucide="alert-triangle" class="w-7 h-7 text-red-600 dark:text-red-400"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-navy-800 dark:text-white">Batalkan Pengajuan?</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Tindakan ini tidak dapat dibatalkan</p>
            </div>
        </div>
        
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 mb-5">
            <p class="text-sm text-slate-700 dark:text-slate-300" id="delete-message">
                Yakin ingin membatalkan pengajuan ini?
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
                Ya, Batalkan
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

    function openDeleteModal(leaveId, leaveType) {
        deleteUrl = `/teacher/leave/${leaveId}`;
        document.getElementById('delete-message').textContent = `Yakin ingin membatalkan pengajuan ${leaveType} ini? Data yang dihapus tidak dapat dikembalikan.`;
        const modal = document.getElementById('delete-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        if (window.lucide) lucide.createIcons();
    }

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('.delete-trigger');
        if (!trigger) {
            return;
        }

        const leaveId = trigger.dataset.leaveId;
        const leaveType = trigger.dataset.leaveType;
        openDeleteModal(leaveId, leaveType);
    });

    function closeDeleteModal() {
        const modal = document.getElementById('delete-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        deleteUrl = '';
    }

    function confirmDelete() {
        if (deleteUrl) {
            const form = document.getElementById('delete-form');
            form.action = deleteUrl;
            form.submit();
        }
    }

    document.getElementById('delete-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    document.addEventListener('keydown', function(e) {
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
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection