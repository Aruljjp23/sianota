document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && sidebar && sidebarOverlay) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        });

        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }

    window.addEventListener('resize', function () {
        if (window.innerWidth > 800 && sidebar && sidebarOverlay) {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        }
    });

    const profileToggle = document.getElementById('profileToggle');
    const profileMenu = document.getElementById('profileMenu');

    if (profileToggle && profileMenu) {
        profileToggle.addEventListener('click', function (event) {
            event.stopPropagation();

            profileMenu.classList.toggle('show');
            profileToggle.classList.toggle('active');
        });

        document.addEventListener('click', function (event) {
            if (
                !profileMenu.contains(event.target) &&
                !profileToggle.contains(event.target)
            ) {
                profileMenu.classList.remove('show');
                profileToggle.classList.remove('active');
            }
        });
    }

    const notificationWrapper = document.querySelector(
        '.notification-wrapper[data-notifications-url]'
    );

    const notificationToggle = document.getElementById('notificationToggle');
    const notificationMenu = document.getElementById('notificationMenu');
    const notificationList = document.getElementById('notificationList');
    const notificationDot = document.getElementById('notificationDot');
    const notificationCount = document.getElementById('notificationCount');
    const notificationReadAll = document.getElementById('notificationReadAll');

    if (
        notificationWrapper &&
        notificationToggle &&
        notificationMenu &&
        notificationList
    ) {
        const notificationsUrl = notificationWrapper.dataset.notificationsUrl;
        const readUrl = notificationWrapper.dataset.readUrl;
        const readAllUrl = notificationWrapper.dataset.readAllUrl;

        let notificationLoaded = false;
        let notificationLoading = false;

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value ?? '';
            return div.innerHTML;
        }

        function renderNotifications(data) {
            const notifications = data.notifications || [];
            const unread = Number(data.unread || 0);

            if (notificationCount) {
                notificationCount.textContent = `${unread} belum dibaca`;
            }

            if (notificationDot) {
                notificationDot.classList.toggle('hidden', unread === 0);
            }

            if (notifications.length === 0) {
                notificationList.innerHTML = `
                    <div class="notification-empty">
                        <i class="fa-regular fa-bell-slash"></i>
                        <strong>Belum ada notifikasi</strong>
                        <span>Notifikasi baru akan muncul di sini.</span>
                    </div>
                `;
                return;
            }

            notificationList.innerHTML = notifications.map(function (notification) {
                const unreadClass = notification.read ? '' : 'unread';

                return `
                    <button
                        type="button"
                        class="notification-item ${unreadClass}"
                        data-id="${escapeHtml(notification.id)}"
                        data-url="${escapeHtml(notification.url || '')}"
                    >
                        <div class="notification-icon">
                            <i class="fa-solid ${escapeHtml(notification.icon || 'fa-bell')}"></i>
                        </div>

                        <div class="notification-content">
                            <span class="notification-title">
                                ${escapeHtml(notification.title || 'Notifikasi')}
                            </span>

                            <span class="notification-message">
                                ${escapeHtml(notification.message || '')}
                            </span>

                            <span class="notification-time">
                                ${escapeHtml(notification.time || '')}
                            </span>
                        </div>
                    </button>
                `;
            }).join('');
        }

        async function loadNotifications() {
            if (notificationLoading) return;

            notificationLoading = true;

            try {
                const response = await fetch(notificationsUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error('Gagal mengambil notifikasi.');
                }

                const data = await response.json();
                renderNotifications(data);
                notificationLoaded = true;
            } catch (error) {
                console.error('Gagal memuat notifikasi:', error);

                if (!notificationLoaded) {
                    notificationList.innerHTML = `
                        <div class="notification-empty">
                            <strong>Notifikasi gagal dimuat</strong>
                            <span>Silakan coba lagi.</span>
                        </div>
                    `;
                }
            } finally {
                notificationLoading = false;
            }
        }

        async function markNotificationAsRead(notificationId) {
            if (!readUrl || !notificationId) return false;

            try {
                const response = await fetch(
                    readUrl.replace('__ID__', encodeURIComponent(notificationId)),
                    {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN':
                                document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        credentials: 'same-origin'
                    }
                );

                if (!response.ok) {
                    throw new Error('Gagal menandai notifikasi.');
                }

                return true;
            } catch (error) {
                console.error('Gagal menandai notifikasi:', error);
                return false;
            }
        }

        notificationToggle.addEventListener('click', function (event) {
            event.stopPropagation();

            const isOpen = notificationMenu.classList.toggle('show');
            notificationToggle.classList.toggle('active', isOpen);

            if (isOpen) {
                loadNotifications();
            }
        });

        document.addEventListener('click', function (event) {
            if (
                !notificationWrapper.contains(event.target)
            ) {
                notificationMenu.classList.remove('show');
                notificationToggle.classList.remove('active');
            }
        });

        notificationList.addEventListener('click', async function (event) {
            const item = event.target.closest('.notification-item');

            if (!item) return;

            const notificationId = item.dataset.id;
            const targetUrl = item.dataset.url;

            if (item.classList.contains('unread')) {
                const success = await markNotificationAsRead(notificationId);

                if (!success) return;
            }

            await loadNotifications();

            if (targetUrl) {
                window.location.href = targetUrl;
            }
        });

        if (notificationReadAll) {
            notificationReadAll.addEventListener('click', async function (event) {
                event.preventDefault();
                event.stopPropagation();

                if (!readAllUrl) return;

                try {
                    const response = await fetch(readAllUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN':
                                document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        throw new Error('Gagal menandai semua notifikasi.');
                    }

                    await loadNotifications();
                } catch (error) {
                    console.error('Gagal menandai semua notifikasi:', error);
                }
            });
        }

        loadNotifications();
        window.setInterval(loadNotifications, 30000);
    }

    const loginLoading = document.getElementById('loginLoading');

    if (loginLoading) {
        setTimeout(function () {
            loginLoading.classList.add('is-hidden');

            setTimeout(function () {
                loginLoading.remove();
            }, 400);
        }, 1000);
    }
});