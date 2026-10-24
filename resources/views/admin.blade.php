<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Notification Center | Real-time Pusher & Redis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <style>
        :root {
            --bg: #090d16;
            --surface: rgba(18, 24, 38, 0.75);
            --surface-border: rgba(255, 255, 255, 0.08);
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --primary-glow: rgba(99, 102, 241, 0.3);
            --redis: #ef4444;
            --pusher: #06b6d4;
            --success: #10b981;
            --urgent: #ef4444;
            --high: #f59e0b;
            --normal: #10b981;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --radius-lg: 18px;
            --radius-md: 12px;
            --radius-sm: 8px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at 80% 20%, rgba(6, 182, 212, 0.12) 0%, transparent 40%),
                        radial-gradient(circle at 20% 80%, rgba(99, 102, 241, 0.12) 0%, transparent 40%),
                        var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            width: 100%;
            max-width: 1000px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Nav */
        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--surface);
            backdrop-filter: blur(12px);
            border: 1px solid var(--surface-border);
            padding: 0.85rem 1.5rem;
            border-radius: var(--radius-md);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: inherit;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--pusher), #3b82f6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.35);
        }

        .brand-text h1 {
            font-size: 1.05rem;
            font-weight: 700;
        }

        .brand-text span {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .nav-actions {
            display: flex;
            gap: 0.6rem;
            align-items: center;
        }

        .nav-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--surface-border);
            color: var(--text-main);
            padding: 0.5rem 0.9rem;
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        .nav-btn.form-btn {
            background: rgba(99, 102, 241, 0.15);
            border-color: rgba(99, 102, 241, 0.4);
            color: #a5b4fc;
        }

        /* Status Badge */
        .status-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.9rem;
            border-radius: 20px;
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--surface-border);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #94a3b8;
            box-shadow: 0 0 8px currentColor;
            transition: all 0.3s ease;
        }

        .status-dot.online {
            background: #10b981;
            color: #10b981;
        }

        .status-dot.warning {
            background: #f59e0b;
            color: #f59e0b;
        }

        /* Stat Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 1rem;
        }

        .stat-card {
            background: var(--surface);
            backdrop-filter: blur(12px);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            overflow: hidden;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .stat-icon.blue { background: rgba(6, 182, 212, 0.15); color: #06b6d4; }
        .stat-icon.purple { background: rgba(99, 102, 241, 0.15); color: #818cf8; }
        .stat-icon.red { background: rgba(239, 68, 68, 0.15); color: #f87171; }
        .stat-icon.green { background: rgba(16, 185, 129, 0.15); color: #34d399; }

        .stat-details h3 {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .stat-details p {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 0.2rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        /* Main Stream Card */
        .feed-card {
            background: var(--surface);
            backdrop-filter: blur(14px);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-lg);
            padding: 1.8rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .feed-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.8rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--surface-border);
        }

        .feed-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .feed-actions {
            display: flex;
            gap: 0.5rem;
        }

        /* Notification Feed List */
        .notifications-list {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
            max-height: 600px;
            overflow-y: auto;
            padding-right: 0.25rem;
        }

        .notification-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-md);
            padding: 1.2rem;
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            animation: slideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .notification-item.new-arrival {
            border-color: rgba(6, 182, 212, 0.5);
            background: rgba(6, 182, 212, 0.08);
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.15);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-12px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .item-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .item-author {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--pusher));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: white;
            text-transform: uppercase;
        }

        .author-details h4 {
            font-size: 0.95rem;
            font-weight: 700;
        }

        .author-details span {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .badges-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .badge-pill {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-urgent { background: rgba(239, 68, 68, 0.2); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.3); }
        .badge-high { background: rgba(245, 158, 11, 0.2); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.3); }
        .badge-normal { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.3); }
        .badge-ticket { background: rgba(255, 255, 255, 0.08); color: #cbd5e1; font-family: 'JetBrains Mono', monospace; }

        .item-body {
            font-size: 0.88rem;
            line-height: 1.5;
            color: #e2e8f0;
            background: rgba(0, 0, 0, 0.25);
            padding: 0.8rem 1rem;
            border-radius: var(--radius-sm);
            border-left: 3px solid var(--pusher);
        }

        .item-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.76rem;
            color: var(--text-muted);
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .pipeline-status {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .p-step {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            color: #94a3b8;
        }

        .p-step.done {
            color: #10b981;
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 3.5rem 1rem;
            color: var(--text-muted);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.8rem;
        }

        .empty-icon {
            font-size: 2.5rem;
            opacity: 0.5;
        }

        /* Floating Toast Container */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 9999;
            pointer-events: none;
        }

        .toast-popup {
            background: rgba(18, 24, 38, 0.95);
            border: 1px solid rgba(6, 182, 212, 0.4);
            border-left: 4px solid var(--pusher);
            backdrop-filter: blur(16px);
            padding: 1rem 1.25rem;
            border-radius: var(--radius-md);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            color: white;
            width: 320px;
            pointer-events: auto;
            animation: toastPop 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes toastPop {
            from { transform: translateX(50px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .toast-top {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            color: #67e8f9;
            font-weight: 700;
            margin-bottom: 0.3rem;
        }

        .toast-title {
            font-size: 0.92rem;
            font-weight: 700;
            margin-bottom: 0.2rem;
        }

        .toast-desc {
            font-size: 0.78rem;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <div class="toast-container" id="toastContainer"></div>

    <div class="container">
        <!-- Top Nav -->
        <nav class="top-nav">
            <a href="/admin" class="brand">
                <div class="brand-icon">📡</div>
                <div class="brand-text">
                    <h1>Real-time Admin Notifications</h1>
                    <span>Pusher WebSocket Channel: <code style="color: var(--pusher);">inquiry-channel</code></span>
                </div>
            </a>
            <div class="nav-actions">
                <div class="status-badge">
                    <div class="status-dot" id="statusDot"></div>
                    <span id="statusText">Connecting...</span>
                </div>
                <a href="/form" target="_blank" class="nav-btn form-btn">
                    <span>📝 Open Customer Form</span>
                </a>
                <a href="http://localhost:8025" target="_blank" class="nav-btn">
                    <span>📬 Mailpit</span>
                </a>
            </div>
        </nav>

        <!-- Stats Bar -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">⚡</div>
                <div class="stat-details">
                    <h3 id="statTotal">0</h3>
                    <p>Total Live Inquiries</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">🔔</div>
                <div class="stat-details">
                    <h3 id="statUnread">0</h3>
                    <p>Unread Alerts</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">🔴</div>
                <div class="stat-details">
                    <h3 id="statUrgent">0</h3>
                    <p>Urgent Priority</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">📬</div>
                <div class="stat-details">
                    <h3 id="statQueued">0</h3>
                    <p>Redis Emails Queued</p>
                </div>
            </div>
        </div>

        <!-- Notification Feed -->
        <div class="feed-card">
            <div class="feed-header">
                <h2>
                    <span>⚡ Live Stream</span>
                    <span id="feedCounter" style="font-size: 0.8rem; background: rgba(255,255,255,0.08); padding: 2px 8px; border-radius: 12px; font-weight: 600;">0 items</span>
                </h2>
                <div class="feed-actions">
                    <button class="nav-btn" onclick="soundEnabled = !soundEnabled; this.textContent = soundEnabled ? '🔊 Sound On' : '🔇 Muted';">
                        🔊 Sound On
                    </button>
                    <button class="nav-btn" onclick="clearFeed()">
                        🗑 Clear
                    </button>
                </div>
            </div>

            <div class="notifications-list" id="notificationsList">
                <div class="empty-state" id="emptyState">
                    <div class="empty-icon">📡</div>
                    <p style="font-size: 1rem; font-weight: 600; color: #e2e8f0;">Listening for real-time events...</p>
                    <p style="font-size: 0.85rem; max-width: 420px;">
                        Open <a href="/form" target="_blank" style="color: #67e8f9; text-decoration: underline;">Customer Form</a> in another tab and submit a ticket. You will instantly see it appear here without refreshing the page!
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const pusherKey = "{{ config('broadcasting.connections.pusher.key') }}";
        const pusherCluster = "{{ config('broadcasting.connections.pusher.options.cluster') }}";

        let totalCount = 0;
        let unreadCount = 0;
        let urgentCount = 0;
        let soundEnabled = true;

        // Initialize Pusher Client
        const pusher = new Pusher(pusherKey, {
            cluster: pusherCluster,
            forceTLS: true
        });

        const statusDot = document.getElementById('statusDot');
        const statusText = document.getElementById('statusText');

        pusher.connection.bind('state_change', function(states) {
            statusText.textContent = states.current.charAt(0).toUpperCase() + states.current.slice(1);
            if (states.current === 'connected') {
                statusDot.className = 'status-dot online';
            } else if (states.current === 'connecting') {
                statusDot.className = 'status-dot warning';
            } else {
                statusDot.className = 'status-dot';
            }
        });

        // Subscribe to inquiry-channel
        const channel = pusher.subscribe('inquiry-channel');

        channel.bind('inquiry.received', function(data) {
            handleNewInquiry(data.inquiry);
        });

        function handleNewInquiry(inquiry) {
            totalCount++;
            unreadCount++;
            if (inquiry.priority === 'urgent') urgentCount++;

            updateStats();
            playChimeSound();
            showToast(inquiry);
            renderNotificationCard(inquiry);
        }

        function updateStats() {
            document.getElementById('statTotal').textContent = totalCount;
            document.getElementById('statUnread').textContent = unreadCount;
            document.getElementById('statUrgent').textContent = urgentCount;
            document.getElementById('statQueued').textContent = totalCount;
            document.getElementById('feedCounter').textContent = `${totalCount} items`;
        }

        function renderNotificationCard(inquiry) {
            const list = document.getElementById('notificationsList');
            const emptyState = document.getElementById('emptyState');
            if (emptyState) emptyState.remove();

            const card = document.createElement('div');
            card.className = 'notification-item new-arrival';

            const initials = inquiry.name.split(' ').map(n => n[0]).join('').substring(0, 2);
            const priorityClass = inquiry.priority === 'urgent' ? 'badge-urgent' : (inquiry.priority === 'high' ? 'badge-high' : 'badge-normal');

            card.innerHTML = `
                <div class="item-top">
                    <div class="item-author">
                        <div class="avatar">${initials}</div>
                        <div class="author-details">
                            <h4>${escapeHtml(inquiry.name)}</h4>
                            <span>${escapeHtml(inquiry.email)} • ${escapeHtml(inquiry.department || 'Support')}</span>
                        </div>
                    </div>
                    <div class="badges-row">
                        <span class="badge-pill ${priorityClass}">${escapeHtml(inquiry.priority)}</span>
                        <span class="badge-pill badge-ticket">#${escapeHtml(inquiry.ticket_id)}</span>
                    </div>
                </div>
                <div style="font-weight: 600; font-size: 0.95rem; color: #f8fafc;">
                    📌 ${escapeHtml(inquiry.subject)}
                </div>
                <div class="item-body">
                    "${escapeHtml(inquiry.message)}"
                </div>
                <div class="item-footer">
                    <div class="pipeline-status">
                        <span class="p-step done">✔ Pusher WebSocket</span>
                        <span>•</span>
                        <span class="p-step done">✔ Redis Queued</span>
                        <span>•</span>
                        <span class="p-step done">✔ Email Dispatched</span>
                    </div>
                    <span>${inquiry.created_at || new Date().toLocaleTimeString()}</span>
                </div>
            `;

            list.prepend(card);

            // Remove highlighted arrival effect after 3s
            setTimeout(() => {
                card.classList.remove('new-arrival');
            }, 3000);
        }

        // Floating Toast Popup
        function showToast(inquiry) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'toast-popup';
            toast.innerHTML = `
                <div class="toast-top">
                    <span>⚡ NEW LIVE INQUIRY</span>
                    <span>#${escapeHtml(inquiry.ticket_id)}</span>
                </div>
                <div class="toast-title">${escapeHtml(inquiry.name)}</div>
                <div class="toast-desc">${escapeHtml(inquiry.subject)}</div>
            `;
            container.appendChild(toast);

            setTimeout(() => {
                toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(50px)';
                setTimeout(() => toast.remove(), 400);
            }, 4500);
        }

        // Web Audio Synthesizer Chime
        function playChimeSound() {
            if (!soundEnabled) return;
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                
                const now = ctx.currentTime;
                const osc1 = ctx.createOscillator();
                const osc2 = ctx.createOscillator();
                const gain = ctx.createGain();

                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, now); // D5
                osc1.frequency.exponentialRampToValueAtTime(880, now + 0.15); // A5

                osc2.type = 'triangle';
                osc2.frequency.setValueAtTime(880, now + 0.15);
                osc2.frequency.exponentialRampToValueAtTime(1174.66, now + 0.3); // D6

                gain.gain.setValueAtTime(0.15, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.6);

                osc1.connect(gain);
                osc2.connect(gain);
                gain.connect(ctx.destination);

                osc1.start(now);
                osc2.start(now + 0.15);
                osc1.stop(now + 0.3);
                osc2.stop(now + 0.6);
            } catch(e) {
                // Audio not allowed yet
            }
        }

        function clearFeed() {
            const list = document.getElementById('notificationsList');
            list.innerHTML = `
                <div class="empty-state" id="emptyState">
                    <div class="empty-icon">📡</div>
                    <p style="font-size: 1rem; font-weight: 600; color: #e2e8f0;">Listening for real-time events...</p>
                    <p style="font-size: 0.85rem; max-width: 420px;">
                        Open <a href="/form" target="_blank" style="color: #67e8f9; text-decoration: underline;">Customer Form</a> in another tab and submit a ticket.
                    </p>
                </div>
            `;
            unreadCount = 0;
            updateStats();
        }

        function escapeHtml(text) {
            if (!text) return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Fetch previous inquiries on load
        async function loadPreviousInquiries() {
            try {
                const res = await fetch('/api/inquiries');
                const data = await res.json();
                if (data && data.length > 0) {
                    data.reverse().forEach(item => {
                        handleNewInquiry(item);
                    });
                    unreadCount = 0; // reset unread on initial load
                    updateStats();
                }
            } catch(e) {}
        }

        loadPreviousInquiries();
    </script>
</body>
</html>
