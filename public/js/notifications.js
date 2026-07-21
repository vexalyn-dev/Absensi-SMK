// Notifications client script (moved out of Blade to avoid editor diagnostics)
(function(){
    const cfgEl = document.getElementById('laravel-config');
    const UNREAD_URL = cfgEl ? cfgEl.dataset.unreadUrl : '/teacher/notifications/api/unread';
    const USER_ID = cfgEl ? cfgEl.dataset.userId : null;

    // Initialize Lucide icons
    function initIcons() {
        if (window.lucide) try { lucide.createIcons(); } catch(e){}
    }

    // Check saved theme on load
    try {
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    } catch(e){}

    // Toggle Dark Mode
    window.toggleDarkMode = function() {
        const html = document.documentElement;
        html.classList.toggle('dark');
        localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
        initIcons();
    };

    document.addEventListener('DOMContentLoaded', () => initIcons());
    document.addEventListener('alpine:initialized', () => initIcons());

    // Mark notification as read (simple reload on success)
    window.markAsRead = function(notificationId) {
        fetch(`/teacher/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => {
            if (response.ok) {
                setTimeout(() => window.location.reload(), 400);
            }
        });
    };

    // Mark single notification as read (update UI)
    window.markNotifRead = function(btn, id, type) {
        const url = type === 'admin' ? `/notifications/${id}/read` : `/teacher/notifications/${id}/read`;
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(res => {
            if (res.ok) {
                const wrapper = btn.closest('.group');
                if (wrapper) {
                    const boldText = wrapper.querySelector('.font-semibold');
                    if (boldText) boldText.classList.add('opacity-60');

                    btn.outerHTML = `<div class="shrink-0 self-center mr-3 p-1.5"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-green-400"></i></div>`;
                    initIcons();

                    const badge = document.querySelector('.notification-badge, [data-notif-count]');
                    if (badge) {
                        const count = Number.parseInt(badge.textContent || '0', 10);
                        if (Number.isFinite(count) && count > 1) badge.textContent = count - 1;
                        else badge.remove();
                    }
                    setTimeout(checkNotifications, 150);
                }
            }
        });
    };

    // Mark ALL notifications as read
    window.markAllNotifRead = function(type) {
        const url = type === 'teacher' ? '/teacher/notifications/read-all' : '/notifications/mark-all-read';
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(res => {
            if (res.ok) {
                document.querySelectorAll('.notif-check-wrap').forEach(wrap => {
                    const single = wrap.querySelector('.notif-check-single');
                    const double = wrap.querySelector('.notif-check-double');
                    if (single) single.classList.add('hidden');
                    if (double) double.classList.remove('hidden');
                });

                document.querySelectorAll('.notif-text').forEach(el => {
                    el.classList.remove('font-bold', 'font-semibold');
                    el.classList.add('font-medium', 'opacity-60');
                });

                const btn = document.querySelector('[onclick*="markAllNotifRead"]');
                if (btn) btn.remove();

                document.querySelectorAll('.notification-badge, [data-notif-count]').forEach(b => b.remove());
            }
        });
    };

    // Alpine x-data helper
    window.notificationDropdown = function() {
        return {
            open: false,
            markRead() {
                // deliberate no-op: keep manual behavior for badge
            },
            init() {
                this.$watch('open', value => this.markRead());
            }
        };
    };
    window.notificationDropdownAdmin = window.notificationDropdown;

    // --- Notification polling ---
    let lastNotificationIds = new Set();
    let notificationsSeeded = false;

    function initNotificationSound() {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const duration = 0.5;
        const now = audioContext.currentTime;
        const osc = audioContext.createOscillator();
        const gain = audioContext.createGain();
        osc.connect(gain);
        gain.connect(audioContext.destination);
        osc.frequency.setValueAtTime(800, now);
        osc.frequency.setValueAtTime(1000, now + 0.1);
        gain.gain.setValueAtTime(0.3, now);
        gain.gain.exponentialRampToValueAtTime(0.01, now + duration);
        osc.start(now);
        osc.stop(now + duration);
    }

    function playNotificationSound() {
        try { initNotificationSound(); } catch (e) { console.log('Audio API not available:', e.message); }
    }

    function showNotificationToast(notification) {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-6 right-6 p-4 rounded-lg shadow-lg animate-bounce-in flex items-start gap-3 max-w-sm z-50 ${notification.bg_color || 'bg-blue-100 text-blue-600'} transition-all duration-300`;
        toast.innerHTML = `
            <div class="flex-1">
                <h3 class="font-semibold text-sm">${notification.title}</h3>
                <p class="text-xs opacity-90 mt-1">${notification.message}</p>
                <p class="text-xs opacity-75 mt-2">${notification.created_at}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="flex-shrink-0 opacity-50 hover:opacity-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        `;
        document.body.appendChild(toast);
        initIcons();
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            setTimeout(() => toast.remove(), 300);
        }, 6000);
    }

    async function checkNotifications() {
        try {
            const response = await fetch(UNREAD_URL, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (!data || !data.success) return;

            // Badge: always scope to the bell button to avoid duplicates or mis-positioned badges
            const dropdownRoot = document.querySelector('[x-data*="notificationDropdown"]');
            const bellBtn = dropdownRoot ? dropdownRoot.querySelector('button') : null;
            let notifBadge = bellBtn ? bellBtn.querySelector('.notification-badge') : document.querySelector('.notification-badge');

            if (data.unreadCount > 0) {
                if (!notifBadge && bellBtn) {
                    notifBadge = document.createElement('span');
                    notifBadge.className = 'notification-badge absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full animate-pulse';
                    notifBadge.setAttribute('aria-label', `${data.unreadCount} notifikasi belum dibaca`);
                    // ensure the bell button is the positioning context
                    if (getComputedStyle(bellBtn).position === 'static') bellBtn.style.position = 'relative';
                    bellBtn.appendChild(notifBadge);
                } else if (notifBadge) {
                    // if it's present elsewhere, move it into the bell button to keep layout consistent
                    if (bellBtn && notifBadge.closest('[x-data*="notificationDropdown"]') !== dropdownRoot) {
                        notifBadge.remove();
                        bellBtn.appendChild(notifBadge);
                        if (getComputedStyle(bellBtn).position === 'static') bellBtn.style.position = 'relative';
                    }
                    notifBadge.style.display = 'block';
                    notifBadge.setAttribute('aria-label', `${data.unreadCount} notifikasi belum dibaca`);
                }
            } else if (notifBadge) {
                notifBadge.style.display = 'none';
            }

            const dropdown = document.querySelector('[x-data*="notificationDropdown"]');
            const notifsList = dropdown ? dropdown.querySelector('.divide-y') : null;

            // First poll: seed known ids to avoid toasting existing notifications
            if (!notificationsSeeded) {
                (data.notifications || []).forEach(n => lastNotificationIds.add(n.id));
                notificationsSeeded = true;
            }

            // Re-render dropdown list entirely so UI always matches backend
            if (notifsList) {
                // Clear existing items
                notifsList.innerHTML = '';

                if (!data.notifications || data.notifications.length === 0) {
                    // Empty state
                    notifsList.innerHTML = `
                        <div class="p-8 text-center">
                            <i data-lucide="bell-off" class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-2"></i>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Tidak ada notifikasi</p>
                        </div>
                    `;
                } else {
                    // Build list from latest notifications
                    data.notifications.forEach(notification => {
                        // Show toast only for newly added notifications
                        if (!lastNotificationIds.has(notification.id)) {
                            lastNotificationIds.add(notification.id);
                            playNotificationSound();
                            showNotificationToast(notification);
                            if (typeof window.refreshLeaveRequests === 'function') {
                                window.refreshLeaveRequests();
                            }
                            window.dispatchEvent(new CustomEvent('notifications:new', { detail: notification }));
                        }

                        const item = document.createElement('div');
                        item.className = 'flex items-start hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors notif-item';
                        const isRead = !!notification.is_read;
                        item.innerHTML = `
                            <a href="${notification.action_url || '#'}" class="flex-1 p-3 min-w-0">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-8 h-8 rounded-lg ${notification.bg_color || 'bg-blue-100 text-blue-600'} flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="${notification.icon || 'bell'}" class="w-4 h-4"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-navy-800 dark:text-white line-clamp-1 notif-text ${isRead ? 'font-medium opacity-60' : 'font-semibold'}">${notification.title}</p>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2 leading-relaxed">${notification.message}</p>
                                        <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-1">${notification.created_at}</p>
                                    </div>
                                </div>
                            </a>
                            <div class="shrink-0 self-center mr-3 notif-check-wrap">
                                ${isRead ? `
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/></svg>
                                ` : `
                                    <svg class="notif-check-single" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                `}
                            </div>
                        `;
                        notifsList.appendChild(item);
                    });
                    initIcons();
                }
            }

        } catch (error) {
            console.error('Error checking notifications:', error);
        }
    }

    // Start polling on page load
    document.addEventListener('DOMContentLoaded', function() {
        checkNotifications();
        setInterval(checkNotifications, 3000);
        window.addEventListener('focus', checkNotifications);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) checkNotifications();
        });
    });
})();
