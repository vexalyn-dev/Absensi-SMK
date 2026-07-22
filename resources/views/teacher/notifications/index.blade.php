@extends('layouts.teacher')

@section('page-title', 'Notifikasi')

@section('content')
<div class="fade-in space-y-4 sm:space-y-6" x-data="notificationManager()">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 flex-shrink-0 relative">
                <i data-lucide="bell" class="w-5 h-5 sm:w-6 sm:h-6 text-white dark:text-navy-900"></i>
                @if(auth()->user()->unreadCount() > 0)
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">
                    {{ auth()->user()->unreadCount() > 9 ? '9+' : auth()->user()->unreadCount() }}
                </span>
                @endif
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white">Notifikasi</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                    <span x-show="!selectionMode">
                        @if(auth()->user()->unreadCount() > 0)
                            {{ auth()->user()->unreadCount() }} belum dibaca
                        @else
                            Semua sudah dibaca
                        @endif
                    </span>
                    <span x-show="selectionMode" x-text="`${selectedItems.length} dipilih`"></span>
                </p>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex items-center gap-2">
            <!-- Bulk Delete Button (Selection Mode) -->
            <button x-show="selectionMode && selectedItems.length > 0" 
                    @click="bulkDelete()"
                    class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-semibold transition-all hover:shadow-lg flex items-center gap-2">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                <span>Hapus (<span x-text="selectedItems.length"></span>)</span>
            </button>
            
            <!-- Cancel Selection Button -->
            <button x-show="selectionMode" 
                    @click="cancelSelection()"
                    class="px-4 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all flex items-center gap-2">
                <i data-lucide="x" class="w-4 h-4"></i>
                <span>Batal</span>
            </button>
            
            <!-- Mark All Read Button -->
            @if(auth()->user()->unreadCount() > 0)
            <form x-show="!selectionMode" action="{{ route('teacher.notifications.read-all') }}" method="POST" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl text-sm font-semibold transition-all hover:shadow-lg hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Tandai Semua Dibaca
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Stats Bar -->
    @php
        $totalNotif  = $notifications->total();
        $unreadCount = auth()->user()->unreadCount();
        $readCount   = $totalNotif - $unreadCount;
    @endphp
    <div class="grid grid-cols-3 gap-3">
        <div class="card p-3 sm:p-4 flex items-center gap-2 sm:gap-3">
            <div class="w-8 h-8 sm:w-9 sm:h-9 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="bell" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Total</p>
                <p class="text-base sm:text-lg font-bold text-navy-800 dark:text-white">{{ $totalNotif }}</p>
            </div>
        </div>
        <div class="card p-3 sm:p-4 flex items-center gap-2 sm:gap-3">
            <div class="w-8 h-8 sm:w-9 sm:h-9 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="bell-ring" class="w-4 h-4 text-red-500 dark:text-red-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Belum Baca</p>
                <p class="text-base sm:text-lg font-bold text-navy-800 dark:text-white">{{ $unreadCount }}</p>
            </div>
        </div>
        <div class="card p-3 sm:p-4 flex items-center gap-2 sm:gap-3">
            <div class="w-8 h-8 sm:w-9 sm:h-9 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Dibaca</p>
                <p class="text-base sm:text-lg font-bold text-navy-800 dark:text-white">{{ $readCount }}</p>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="space-y-2 sm:space-y-3">
        @forelse($notifications as $notif)
        <div class="relative group" x-data="{ swiping: false, swipeDistance: 0 }"
             @touchstart="handleTouchStart($event, '{{ $notif->id }}')"
             @touchmove="handleTouchMove($event)"
             @touchend="handleTouchEnd($event, '{{ $notif->id }}')"
             @contextmenu.prevent="addToSelection('{{ $notif->id }}')">
            
            <!-- Main Card -->
            <div class="card overflow-hidden relative transition-all"
                 :style="`transform: translateX(${swipeDistance}px)`"
                 :class="{ 
                     'border-2 border-navy-800 dark:border-gold-400': selectedItems.includes('{{ $notif->id }}'),
                     '{{ !$notif->is_read ? 'border-l-4 border-l-blue-500 bg-blue-50/50 dark:bg-blue-900/10' : '' }}'
                 }">
                
                <a :href="selectionMode ? 'javascript:void(0)' : '{{ $notif->action_url ?? '#' }}'" 
                   @click="selectionMode ? toggleSelection('{{ $notif->id }}') : markAsRead('{{ $notif->id }}')"
                   class="block p-4 sm:p-5 hover:shadow-lg transition-all">
                    <div class="flex items-start gap-3 sm:gap-4">
                        <!-- Checkbox (Selection Mode) -->
                        <div x-show="selectionMode" class="flex-shrink-0 pt-1">
                            <input type="checkbox" 
                                   :checked="selectedItems.includes('{{ $notif->id }}')"
                                   @change="toggleSelection('{{ $notif->id }}')"
                                   class="w-5 h-5 rounded border-2 border-slate-300 dark:border-slate-600 text-navy-800 dark:text-gold-400 focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-400">
                        </div>
                        
                        <!-- Icon -->
                        <div x-show="!selectionMode" class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl {{ $notif->color }} flex items-center justify-center flex-shrink-0 relative">
                            <i data-lucide="{{ $notif->icon }}" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            @if(!$notif->is_read)
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 rounded-full border-2 border-white dark:border-slate-800"></span>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h3 class="text-sm sm:text-base font-bold text-navy-800 dark:text-white leading-snug">{{ $notif->title }}</h3>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    @if(!$notif->is_read)
                                    <span class="px-1.5 py-0.5 bg-blue-500 text-white rounded-full text-[9px] sm:text-[10px] font-bold whitespace-nowrap">Baru</span>
                                    @endif
                                    <!-- Desktop Delete Button -->
                                    <button x-show="!selectionMode" 
                                            @click.stop.prevent="deleteNotification('{{ $notif->id }}')"
                                            class="hidden sm:flex opacity-0 group-hover:opacity-100 w-8 h-8 items-center justify-center bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-all">
                                        <i data-lucide="trash-2" class="w-4 h-4 text-red-500"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mb-1.5 line-clamp-2">{{ $notif->message }}</p>
                            <p class="text-[10px] sm:text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                {{ $notif->created_at->locale('id')->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Swipe Delete Button (Mobile) -->
            <div class="absolute top-0 right-0 h-full w-20 bg-red-500 flex items-center justify-center rounded-r-xl"
                 :class="{ 'block': swipeDistance < -50, 'hidden': swipeDistance >= -50 }">
                <button @click="deleteNotification('{{ $notif->id }}')" class="text-white">
                    <i data-lucide="trash-2" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="card p-10 sm:p-12 text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                <i data-lucide="bell-off" class="w-8 h-8 sm:w-10 sm:h-10 text-slate-400 dark:text-slate-500"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold text-navy-800 dark:text-white mb-2">Tidak Ada Notifikasi</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada notifikasi untuk ditampilkan</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
    <div class="card p-3 sm:p-4">
        {{ $notifications->links() }}
    </div>
    @endif

</div>

{{-- ====================================================== --}}
{{-- DELETE NOTIFICATION MODAL --}}
{{-- ====================================================== --}}
<div id="deleteNotifModal"
     style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:1rem;">
    
    {{-- Backdrop --}}
    <div id="deleteNotifModalBackdrop"
         style="position:absolute; inset:0; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px);">
    </div>

    {{-- Modal Card --}}
    <div id="deleteNotifModalBox"
         style="position:relative; z-index:1; background:white; border-radius:1rem; box-shadow:0 25px 60px rgba(0,0,0,0.25); border:1px solid #e2e8f0; width:100%; max-width:380px; transition:transform 0.25s ease, opacity 0.25s ease; transform:scale(0.9); opacity:0;">
        
        {{-- Body --}}
        <div style="padding:1.75rem 1.5rem 1.25rem; display:flex; flex-direction:column; align-items:center; text-align:center;">
            <div style="width:60px; height:60px; background:#FEF2F2; border-radius:1rem; display:flex; align-items:center; justify-content:center; margin-bottom:1.25rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                    <path d="M12 9v4"/><path d="M12 17h.01"/>
                </svg>
            </div>
            <h3 style="font-size:1.25rem; font-weight:800; color:#0F172A; margin:0 0 0.625rem;">Konfirmasi Hapus</h3>
            <p style="font-size:0.8125rem; color:#64748B; line-height:1.5; margin:0;">
                <span id="deleteNotifMessage"></span><br>
                Tindakan ini <strong>permanen</strong> dan tidak dapat dibatalkan.
            </p>
        </div>

        {{-- Actions --}}
        <div style="padding:0.875rem 1.5rem 1.75rem; display:flex; flex-direction:column; gap:0.625rem;">
            <button type="button" id="deleteNotifSubmitBtn"
                    style="width:100%; padding:0.75rem 1.25rem; background:#DC2626; color:white; font-size:0.8125rem; font-weight:700; border:none; border-radius:0.75rem; cursor:pointer; box-shadow:0 6px 16px rgba(220,38,38,0.3); transition:all 0.2s;">
                Ya, Hapus Notifikasi
            </button>
            <button type="button" id="deleteNotifCancelBtn"
                    style="width:100%; padding:0.75rem 1.25rem; background:white; color:#374151; font-size:0.8125rem; font-weight:700; border:2px solid #E5E7EB; border-radius:0.75rem; cursor:pointer; transition:all 0.2s;">
                Batalkan
            </button>
        </div>
    </div>
</div>

{{-- Dark mode styles for modal --}}
<style>
    .dark #deleteNotifModalBox { background: #1E293B !important; border-color: #334155 !important; }
    .dark #deleteNotifModalBox h3 { color: #F8FAFC !important; }
    .dark #deleteNotifModalBox p { color: #94A3B8 !important; }
    .dark #deleteNotifCancelBtn { background: #1E293B !important; color: #CBD5E1 !important; border-color: #475569 !important; }
    .dark #deleteNotifModalBox > div:first-child > div:first-child { background: rgba(220,38,38,0.15) !important; }
    #deleteNotifSubmitBtn:hover { background: #B91C1C !important; transform: scale(1.02); }
    #deleteNotifCancelBtn:hover { background: #F8FAFC !important; }
    .dark #deleteNotifCancelBtn:hover { background: #0F172A !important; }
    
    /* Responsive adjustments for mobile */
    @media (max-width: 640px) {
        #deleteNotifModalBox {
            max-width: calc(100vw - 2rem) !important;
            border-radius: 1rem !important;
        }
        #deleteNotifModalBox > div:first-child {
            padding: 1.5rem 1.25rem 1rem !important;
        }
        #deleteNotifModalBox > div:first-child > div:first-child {
            width: 56px !important;
            height: 56px !important;
            margin-bottom: 1rem !important;
        }
        #deleteNotifModalBox > div:first-child > div:first-child svg {
            width: 28px !important;
            height: 28px !important;
        }
        #deleteNotifModalBox h3 {
            font-size: 1.125rem !important;
            margin-bottom: 0.5rem !important;
        }
        #deleteNotifModalBox p {
            font-size: 0.75rem !important;
            line-height: 1.4 !important;
        }
        #deleteNotifModalBox > div:last-child {
            padding: 0.75rem 1.25rem 1.5rem !important;
            gap: 0.5rem !important;
        }
        #deleteNotifSubmitBtn, #deleteNotifCancelBtn {
            padding: 0.625rem 1rem !important;
            font-size: 0.75rem !important;
        }
    }
</style>

<script>
    function notificationManager() {
        return {
            selectionMode: false,
            selectedItems: [],
            longPressTimer: null,
            longPressDelay: 500,
            touchStartX: 0,
            touchStartY: 0,
            deleteNotifId: null,
            isBulkDelete: false,
            
            toggleSelectionMode() {
                this.selectionMode = !this.selectionMode;
                if (!this.selectionMode) {
                    this.selectedItems = [];
                }
            },
            
            toggleSelection(id) {
                const index = this.selectedItems.indexOf(id);
                if (index > -1) {
                    this.selectedItems.splice(index, 1);
                } else {
                    this.selectedItems.push(id);
                }
            },
            
            addToSelection(id) {
                if (!this.selectedItems.includes(id)) {
                    this.selectedItems.push(id);
                }
                this.selectionMode = true;
            },
            
            cancelSelection() {
                this.selectionMode = false;
                this.selectedItems = [];
            },
            
            handleTouchStart(event, id) {
                this.touchStartX = event.touches[0].clientX;
                this.touchStartY = event.touches[0].clientY;
                
                this.longPressTimer = setTimeout(() => {
                    if (navigator.vibrate) {
                        navigator.vibrate(50);
                    }
                    this.addToSelection(id);
                }, this.longPressDelay);
            },
            
            handleTouchMove(event) {
                if (this.longPressTimer) {
                    const touchX = event.touches[0].clientX;
                    const touchY = event.touches[0].clientY;
                    const deltaX = Math.abs(touchX - this.touchStartX);
                    const deltaY = Math.abs(touchY - this.touchStartY);
                    
                    if (deltaX > 10 || deltaY > 10) {
                        clearTimeout(this.longPressTimer);
                        this.longPressTimer = null;
                    }
                    
                    if (!this.selectionMode && deltaX > 10 && deltaY < 30) {
                        const distance = touchX - this.touchStartX;
                        event.target.closest('[x-data]').swipeDistance = Math.min(0, distance);
                    }
                }
            },
            
            handleTouchEnd(event, id) {
                if (this.longPressTimer) {
                    clearTimeout(this.longPressTimer);
                    this.longPressTimer = null;
                }
                
                const element = event.target.closest('[x-data]');
                if (element && element.swipeDistance > -80) {
                    element.swipeDistance = 0;
                }
            },
            
            deleteNotification(id) {
                this.deleteNotifId = id;
                this.isBulkDelete = false;
                showDeleteNotifModal('Apakah Anda yakin ingin menghapus notifikasi ini?');
            },
            
            bulkDelete() {
                if (this.selectedItems.length === 0) return;
                this.isBulkDelete = true;
                showDeleteNotifModal(`Apakah Anda yakin ingin menghapus <strong style="color:#DC2626;">${this.selectedItems.length} notifikasi</strong>?`);
            },
            
            confirmDelete() {
                if (this.isBulkDelete) {
                    this.executeBulkDelete();
                } else {
                    this.executeSingleDelete();
                }
            },
            
            executeSingleDelete() {
                fetch(`/teacher/notifications/${this.deleteNotifId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeDeleteNotifModal();
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            },
            
            executeBulkDelete() {
                fetch('/teacher/notifications/bulk-delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ ids: this.selectedItems })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeDeleteNotifModal();
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    }
    
    // Modal functions
    function showDeleteNotifModal(message) {
        const modal = document.getElementById('deleteNotifModal');
        const box = document.getElementById('deleteNotifModalBox');
        const messageEl = document.getElementById('deleteNotifMessage');
        
        messageEl.innerHTML = message;
        modal.style.display = 'flex';
        
        setTimeout(() => {
            box.style.transform = 'scale(1)';
            box.style.opacity = '1';
        }, 10);
    }
    
    function closeDeleteNotifModal() {
        const modal = document.getElementById('deleteNotifModal');
        const box = document.getElementById('deleteNotifModalBox');
        
        box.style.transform = 'scale(0.9)';
        box.style.opacity = '0';
        
        setTimeout(() => {
            modal.style.display = 'none';
        }, 250);
    }
    
    // Event listeners
    document.addEventListener('DOMContentLoaded', () => {
        const submitBtn = document.getElementById('deleteNotifSubmitBtn');
        const cancelBtn = document.getElementById('deleteNotifCancelBtn');
        const backdrop = document.getElementById('deleteNotifModalBackdrop');
        
        if (submitBtn) {
            submitBtn.addEventListener('click', () => {
                // Alpine v3 compatible: gunakan Alpine.$data() bukan __x.$data
                const managerEl = document.querySelector('[x-data="notificationManager()"]');
                if (managerEl && window.Alpine) {
                    try {
                        const data = window.Alpine.$data(managerEl);
                        if (data && typeof data.confirmDelete === 'function') {
                            data.confirmDelete();
                        }
                    } catch (e) {
                        console.error('Alpine $data error:', e);
                    }
                }
            });
        }
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeDeleteNotifModal);
        }
        
        if (backdrop) {
            backdrop.addEventListener('click', closeDeleteNotifModal);
        }
        
        // ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeDeleteNotifModal();
            }
        });
        
        if (window.lucide) lucide.createIcons();
    });
    
    function markAsRead(notificationId) {
        fetch(`/teacher/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => {
            if (response.ok) {
                setTimeout(() => window.location.reload(), 500);
            }
        });
    }
</script>

<style>
    .fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
