<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel Docker - Redis & Pusher Live Demo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <style>
        :root {
            --bg: #090d16;
            --surface: rgba(18, 24, 38, 0.7);
            --surface-border: rgba(255, 255, 255, 0.08);
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --primary-glow: rgba(99, 102, 241, 0.25);
            --redis: #ef4444;
            --redis-glow: rgba(239, 68, 68, 0.25);
            --pusher: #06b6d4;
            --pusher-glow: rgba(6, 182, 212, 0.25);
            --success: #10b981;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --radius-lg: 16px;
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
            background: radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.12) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(239, 68, 68, 0.1) 0%, transparent 40%),
                        var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            padding: 2.5rem 1rem;
            display: flex;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 1000px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 2rem;
            background: var(--surface);
            backdrop-filter: blur(12px);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-lg);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .brand-title {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .badge-laravel {
            background: linear-gradient(135deg, #ff2d20, #e01e12);
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            text-transform: uppercase;
        }

        .status-strip {
            display: flex;
            gap: 1rem;
        }

        .status-chip {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            padding: 0.4rem 0.85rem;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--surface-border);
            border-radius: 999px;
            font-weight: 500;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #64748b;
        }

        .status-dot.online {
            background: var(--success);
            box-shadow: 0 0 10px var(--success);
        }

        .status-dot.warning {
            background: #f59e0b;
            box-shadow: 0 0 10px #f59e0b;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: var(--surface);
            backdrop-filter: blur(12px);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--surface-border);
            padding-bottom: 0.85rem;
        }

        .card-title {
            font-size: 1.05rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .icon-redis { color: var(--redis); }
        .icon-pusher { color: var(--pusher); }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        label {
            font-size: 0.825rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        input[type="text"] {
            width: 100%;
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-md);
            padding: 0.8rem 1rem;
            color: var(--text-main);
            font-size: 0.95rem;
            font-family: inherit;
            outline: none;
            transition: all 0.2s;
        }

        input[type="text"]:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .btn-group {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        button {
            cursor: pointer;
            border: none;
            outline: none;
            font-family: inherit;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 15px var(--primary-glow);
            flex: 1;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-main);
            border: 1px solid var(--surface-border);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .feed-container {
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-md);
            height: 320px;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            font-family: 'JetBrains Mono', monospace;
        }

        .empty-feed {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--text-muted);
            font-size: 0.85rem;
            gap: 0.5rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .feed-item {
            background: rgba(255, 255, 255, 0.04);
            border-left: 3px solid var(--pusher);
            padding: 0.75rem 1rem;
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
            animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .feed-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .feed-msg {
            color: #e2e8f0;
            font-size: 0.9rem;
            word-break: break-word;
        }

        .code-box {
            font-family: 'JetBrains Mono', monospace;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-sm);
            padding: 0.75rem;
            font-size: 0.8rem;
            color: #cbd5e1;
            white-space: pre-wrap;
            word-break: break-all;
        }

        .toast {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius-sm);
            display: none;
        }
        .toast.success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }
        .toast.error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Top Header -->
    <header>
        <div>
            <div class="brand-title">
                Laravel Docker Stack
                <span class="badge-laravel">Sail</span>
            </div>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">
                Real-Time Event Broadcasting with Redis & Pusher
            </p>
        </div>

        <div class="status-strip">
            <div class="status-chip">
                <span class="status-dot online"></span>
                <span>Redis: <strong>6379</strong></span>
            </div>
            <div class="status-chip">
                <span id="pusher-dot" class="status-dot warning"></span>
                <span>Pusher: <strong id="pusher-status">Connecting...</strong></span>
            </div>
        </div>
    </header>

    <!-- Main Grid -->
    <div class="grid">
        <!-- Redis Card -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <span class="icon-redis">⚡</span> Redis Cache & Queue
                </div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Connection: redis</span>
            </div>

            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">
                Test Redis read/write cache operations and check Redis connectivity inside Docker container network.
            </p>

            <div class="form-group">
                <label>Test Key Value</label>
                <input type="text" id="redis-value" placeholder="Enter test value to store in Redis..." value="Laravel & Redis working perfectly!">
            </div>

            <div class="btn-group">
                <button class="btn-primary" onclick="testRedis()" id="btn-redis">
                    <span>⚡ Store & Read Redis</span>
                </button>
            </div>

            <div id="redis-toast" class="toast"></div>

            <div class="form-group">
                <label>Redis Response Log</label>
                <div id="redis-output" class="code-box">Waiting for test action...</div>
            </div>
        </div>

        <!-- Pusher Live Event Broadcaster -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <span class="icon-pusher">📡</span> Broadcast Event
                </div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Channel: chat-channel</span>
            </div>

            <div class="form-group">
                <label>Message To Broadcast</label>
                <input type="text" id="broadcast-message" placeholder="Type a message to send..." value="Hello from Laravel via Pusher!">
            </div>

            <div class="btn-group">
                <button class="btn-primary" onclick="sendMessage(false)" id="btn-direct">
                    <span>🚀 Broadcast (Immediate)</span>
                </button>
                <button class="btn-secondary" onclick="sendMessage(true)" id="btn-queued" title="Requires 'php artisan queue:work'">
                    <span>⏳ Broadcast (Redis Queue)</span>
                </button>
            </div>

            <div id="broadcast-toast" class="toast"></div>

            <div style="font-size: 0.8rem; color: var(--text-muted); background: rgba(255,255,255,0.03); padding: 0.75rem; border-radius: 8px;">
                💡 <strong>Tip:</strong> Open this page in two separate browser tabs! When you click <em>Broadcast</em> in one tab, you will see it arrive live in real-time in both tabs.
            </div>
        </div>
    </div>

    <!-- Live Event Feed Full Width -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span>🔴</span> Live Real-Time Feed (Pusher WebSocket Stream)
            </div>
            <button class="btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;" onclick="clearFeed()">
                Clear Feed
            </button>
        </div>

        <div class="feed-container" id="feed-box">
            <div class="empty-feed" id="empty-feed">
                <span>Listening for Pusher events on <code>chat-channel</code>...</span>
                <span>Send a message above to see real-time updates!</span>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const pusherAppKey = "{{ config('broadcasting.connections.pusher.key') }}";
    const pusherCluster = "{{ config('broadcasting.connections.pusher.options.cluster') }}";

    // Initialize Pusher Client
    const pusher = new Pusher(pusherAppKey, {
        cluster: pusherCluster,
        forceTLS: true
    });

    const pusherDot = document.getElementById('pusher-dot');
    const pusherStatus = document.getElementById('pusher-status');

    pusher.connection.bind('state_change', function(states) {
        pusherStatus.textContent = states.current.charAt(0).toUpperCase() + states.current.slice(1);
        if (states.current === 'connected') {
            pusherDot.className = 'status-dot online';
        } else if (states.current === 'connecting') {
            pusherDot.className = 'status-dot warning';
        } else {
            pusherDot.className = 'status-dot';
        }
    });

    // Subscribe to channel
    const channel = pusher.subscribe('chat-channel');

    channel.bind('message.sent', function(data) {
        appendFeedItem(data);
    });

    function appendFeedItem(data) {
        const feedBox = document.getElementById('feed-box');
        const emptyFeed = document.getElementById('empty-feed');
        if (emptyFeed) {
            emptyFeed.remove();
        }

        const item = document.createElement('div');
        item.className = 'feed-item';
        item.innerHTML = `
            <div class="feed-meta">
                <span style="color: var(--pusher); font-weight: 600;">event: message.sent</span>
                <span>${data.timestamp || new Date().toLocaleTimeString()}</span>
            </div>
            <div class="feed-msg">${escapeHtml(data.message)}</div>
        `;
        feedBox.prepend(item);
    }

    function clearFeed() {
        const feedBox = document.getElementById('feed-box');
        feedBox.innerHTML = `
            <div class="empty-feed" id="empty-feed">
                <span>Listening for Pusher events on <code>chat-channel</code>...</span>
            </div>
        `;
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Ajax calls
    async function testRedis() {
        const val = document.getElementById('redis-value').value;
        const out = document.getElementById('redis-output');
        const toast = document.getElementById('redis-toast');

        out.textContent = "Connecting to Redis...";

        try {
            const res = await fetch('/test-redis', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ value: val })
            });

            const data = await res.json();
            out.textContent = JSON.stringify(data, null, 2);

            toast.className = 'toast success';
            toast.textContent = data.message || 'Redis test successful!';
            toast.style.display = 'block';
        } catch (err) {
            out.textContent = "Error: " + err.message;
            toast.className = 'toast error';
            toast.textContent = 'Redis request failed!';
            toast.style.display = 'block';
        }
    }

    async function sendMessage(queued) {
        const input = document.getElementById('broadcast-message');
        const toast = document.getElementById('broadcast-toast');
        const message = input.value.trim();

        if (!message) return;

        const url = queued ? '/send-queued-message' : '/send-message';
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ message: message })
            });

            const data = await res.json();
            toast.className = 'toast success';
            toast.textContent = data.status || 'Broadcast dispatched!';
            toast.style.display = 'block';
            setTimeout(() => { toast.style.display = 'none'; }, 4000);
        } catch (err) {
            toast.className = 'toast error';
            toast.textContent = 'Broadcast failed: ' + err.message;
            toast.style.display = 'block';
        }
    }
</script>

</body>
</html>
