(function () {
    function initLeaveRealtime() {
        const list = document.getElementById('leave-requests-list');
        if (!list || !list.dataset.latestUrl) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        }[char]));

        const statusClass = status => ({
            pending: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
            approved: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            rejected: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        }[status] || 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400');

        const actionButtons = leave => leave.status === 'pending' ? `
            <form action="${escapeHtml(leave.approve_url)}" method="POST" class="inline">
                <input type="hidden" name="_token" value="${escapeHtml(csrf)}">
                <input type="hidden" name="admin_notes" value="Disetujui oleh admin">
                <button type="submit" class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    Setujui
                </button>
            </form>
            <form action="${escapeHtml(leave.reject_url)}" method="POST" class="inline">
                <input type="hidden" name="_token" value="${escapeHtml(csrf)}">
                <input type="hidden" name="admin_notes" value="Ditolak oleh admin">
                <button type="submit" onclick="return confirm('Yakin ingin menolak pengajuan ini?')" class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    Tolak
                </button>
            </form>
        ` : '';

        const renderLeave = leave => {
            const photo = leave.teacher_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(leave.teacher_name || 'Guru')}`;

            return `
                <div class="card p-5 hover:shadow-lg transition-all" data-leave-id="${escapeHtml(leave.id)}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-4 flex-1">
                            <img src="${escapeHtml(photo)}" class="w-12 h-12 rounded-xl object-cover border-2 border-slate-200 dark:border-slate-700">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <h3 class="text-base font-bold text-navy-800 dark:text-white">${escapeHtml(leave.teacher_name)}</h3>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${statusClass(leave.status)}">${escapeHtml(leave.status_text)}</span>
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-full text-[10px] font-bold">${escapeHtml(leave.type_text)}</span>
                                </div>
                                <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400 mb-2 flex-wrap">
                                    <div class="flex items-center gap-1">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                        <span>${escapeHtml(leave.start_date)} - ${escapeHtml(leave.end_date)}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                        <span>${escapeHtml(leave.duration)} Hari</span>
                                    </div>
                                </div>
                                <p class="text-sm text-slate-600 dark:text-slate-400">${escapeHtml(leave.reason)}</p>
                                ${leave.admin_notes ? `<div class="mt-2 p-2 bg-slate-50 dark:bg-slate-700/50 rounded-lg"><p class="text-xs text-slate-500 dark:text-slate-400"><strong>Catatan:</strong> ${escapeHtml(leave.admin_notes)}</p></div>` : ''}
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            ${actionButtons(leave)}
                            <a href="${escapeHtml(leave.show_url)}" class="px-3 py-2 bg-navy-800 dark:bg-gold-400 hover:bg-navy-900 dark:hover:bg-gold-500 text-white dark:text-navy-900 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            `;
        };

        const renderEmpty = () => `
            <div class="card p-12 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="file-text" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                </div>
                <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-2">Belum Ada Pengajuan</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada pengajuan izin atau sakit</p>
            </div>
        `;

        async function refreshLeaveRequests() {
            try {
                const url = new URL(list.dataset.latestUrl, window.location.origin);
                url.searchParams.set('_', Date.now().toString());

                const response = await fetch(url.toString(), {
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'Cache-Control': 'no-cache',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const data = await response.json();
                if (!response.ok || !data.success) return;

                Object.entries(data.stats || {}).forEach(([key, value]) => {
                    const stat = document.querySelector(`[data-leave-stat="${key}"]`);
                    if (stat) stat.textContent = value;
                });

                list.innerHTML = data.leaves?.length ? data.leaves.map(renderLeave).join('') : renderEmpty();
                if (window.lucide) lucide.createIcons();
            } catch (error) {
                refreshLeaveRequestsFromPage();
            }
        }

        async function refreshLeaveRequestsFromPage() {
            try {
                const url = new URL(window.location.href);
                url.searchParams.set('_', Date.now().toString());

                const response = await fetch(url.toString(), {
                    cache: 'no-store',
                    headers: {
                        'Accept': 'text/html',
                        'Cache-Control': 'no-cache',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const freshList = doc.getElementById('leave-requests-list');
                if (!response.ok || !freshList) return;

                list.innerHTML = freshList.innerHTML;
                ['total', 'pending', 'approved', 'rejected'].forEach(key => {
                    const stat = document.querySelector(`[data-leave-stat="${key}"]`);
                    const freshStat = doc.querySelector(`[data-leave-stat="${key}"]`);
                    if (stat && freshStat) stat.textContent = freshStat.textContent;
                });
                if (window.lucide) lucide.createIcons();
            } catch (error) {
                console.error('Error refreshing leave requests:', error);
            }
        }

        window.refreshLeaveRequests = refreshLeaveRequestsFromPage;
        refreshLeaveRequestsFromPage();
        setInterval(refreshLeaveRequestsFromPage, 2000);
        window.addEventListener('focus', refreshLeaveRequestsFromPage);
        window.addEventListener('notifications:new', refreshLeaveRequestsFromPage);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) refreshLeaveRequestsFromPage();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLeaveRealtime);
    } else {
        initLeaveRealtime();
    }
})();
