/**
 * TrustedU ERP — Notification System
 * WhatsApp-style sound + Browser Push Notifications
 */
(function () {
    'use strict';

    const POLL_INTERVAL = 15000; // 15 seconds
    const SOUND_ENABLED_KEY = 'trustedu_notification_sound';
    const PUSH_ENABLED_KEY = 'trustedu_notification_push';
    const LAST_COUNT_KEY = 'trustedu_last_unread_count';

    // ── Audio Context for WhatsApp-like notification sound ──
    let audioCtx = null;

    function getAudioContext() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        return audioCtx;
    }

    function playNotificationSound() {
        if (localStorage.getItem(SOUND_ENABLED_KEY) === 'false') return;

        try {
            const ctx = getAudioContext();
            const now = ctx.currentTime;

            // WhatsApp-style double tone
            // First tone
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(880, now);        // A5
            osc1.frequency.setValueAtTime(1046.5, now + 0.08); // C6
            gain1.gain.setValueAtTime(0.3, now);
            gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.15);
            osc1.start(now);
            osc1.stop(now + 0.15);

            // Second tone (slight delay)
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(1046.5, now + 0.18); // C6
            osc2.frequency.setValueAtTime(1318.5, now + 0.26); // E6
            gain2.gain.setValueAtTime(0, now);
            gain2.gain.setValueAtTime(0.25, now + 0.18);
            gain2.gain.exponentialRampToValueAtTime(0.01, now + 0.4);
            osc2.start(now + 0.18);
            osc2.stop(now + 0.4);

        } catch (e) {
            console.warn('Notification sound failed:', e);
        }
    }

    // ── Browser Push Notification ──
    function requestPushPermission() {
        if (!('Notification' in window)) return;
        if (Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }

    function showBrowserNotification(title, body, url) {
        if (localStorage.getItem(PUSH_ENABLED_KEY) === 'false') return;
        if (!('Notification' in window) || Notification.permission !== 'granted') return;

        const notification = new Notification(title, {
            body: body || '',
            icon: '/assets/images/logo-color.png',
            badge: '/favicon.png',
            tag: 'trustedu-notification-' + Date.now(),
            requireInteraction: false,
            silent: true, // We play our own sound
        });

        notification.onclick = function () {
            window.focus();
            if (url) window.location.href = url;
            notification.close();
        };

        setTimeout(() => notification.close(), 8000);
    }

    // ── Polling for new notifications ──
    let lastUnreadCount = parseInt(localStorage.getItem(LAST_COUNT_KEY) || '0', 10);

    async function checkForNewNotifications() {
        try {
            const response = await fetch('/admin/api/notification-count', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) return;

            const data = await response.json();
            const newCount = data.unread_count || 0;

            if (newCount > lastUnreadCount && lastUnreadCount >= 0) {
                // New notification arrived!
                playNotificationSound();

                if (data.latest) {
                    showBrowserNotification(
                        data.latest.title || 'New Notification',
                        data.latest.message || '',
                        data.latest.action_url || '/admin/notifications'
                    );
                }

                // Update badge in topbar
                updateTopbarBadge(newCount);
            }

            lastUnreadCount = newCount;
            localStorage.setItem(LAST_COUNT_KEY, newCount.toString());

        } catch (e) {
            // Silently fail
        }
    }

    function updateTopbarBadge(count) {
        const badges = document.querySelectorAll('[data-notification-badge]');
        badges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count > 9 ? '9+' : count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        });
    }

    // ── Initialize on Page Load ──
    function init() {
        // Request push permission on first user interaction
        document.addEventListener('click', function initAudio() {
            getAudioContext();
            requestPushPermission();
            document.removeEventListener('click', initAudio);
        }, { once: true });

        // Start polling
        setInterval(checkForNewNotifications, POLL_INTERVAL);

        // Check immediately after 3 seconds
        setTimeout(checkForNewNotifications, 3000);
    }

    // ── Global API for testing ──
    window.TrustedUNotifications = {
        playSound: playNotificationSound,
        showPush: showBrowserNotification,
        isSoundEnabled: () => localStorage.getItem(SOUND_ENABLED_KEY) !== 'false',
        isPushEnabled: () => localStorage.getItem(PUSH_ENABLED_KEY) !== 'false',
        toggleSound: (enabled) => {
            localStorage.setItem(SOUND_ENABLED_KEY, enabled ? 'true' : 'false');
            return enabled;
        },
        togglePush: (enabled) => {
            localStorage.setItem(PUSH_ENABLED_KEY, enabled ? 'true' : 'false');
            if (enabled) requestPushPermission();
            return enabled;
        },
        testNotification: () => {
            playNotificationSound();
            showBrowserNotification('Test Notification', 'This is a test notification from TrustedU ERP');
        },
    };

    // Boot
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
