<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Submit Support Ticket | Customer Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
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
            background: radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(239, 68, 68, 0.12) 0%, transparent 40%),
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
            max-width: 860px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Top Bar */
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
            background: linear-gradient(135deg, var(--primary), #a855f7);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            box-shadow: 0 0 15px var(--primary-glow);
        }

        .brand-text h1 {
            font-size: 1.05rem;
            font-weight: 700;
        }

        .brand-text span {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .nav-links {
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
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        .nav-btn.primary {
            background: linear-gradient(135deg, var(--primary), #4f46e5);
            border-color: transparent;
            box-shadow: 0 4px 12px var(--primary-glow);
        }

        .nav-btn.admin-link {
            background: rgba(6, 182, 212, 0.12);
            border-color: rgba(6, 182, 212, 0.35);
            color: #67e8f9;
        }

        .nav-btn.admin-link:hover {
            background: rgba(6, 182, 212, 0.22);
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.3);
        }

        /* Banner Card */
        .info-strip {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.1) 0%, rgba(239, 68, 68, 0.08) 50%, rgba(6, 182, 212, 0.1) 100%);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-md);
            padding: 1rem 1.4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.85rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .flow-steps {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .pill {
            padding: 0.25rem 0.65rem;
            border-radius: 20px;
            font-size: 0.74rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .pill-form { background: rgba(99, 102, 241, 0.2); color: #a5b4fc; }
        .pill-pusher { background: rgba(6, 182, 212, 0.2); color: #67e8f9; }
        .pill-redis { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
        .pill-mail { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; }

        /* Main Form Card */
        .form-card {
            background: var(--surface);
            backdrop-filter: blur(14px);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-lg);
            padding: 2.2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
        }

        .form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--pusher), var(--redis));
        }

        .form-header {
            margin-bottom: 1.8rem;
        }

        .form-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .form-header p {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
            margin-bottom: 1.2rem;
        }

        @media (max-width: 640px) {
            .form-grid { grid-template-columns: 1fr; }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        input, select, textarea {
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid var(--surface-border);
            color: var(--text-main);
            padding: 0.75rem 1rem;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 0.92rem;
            transition: all 0.2s ease;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
            background: rgba(0, 0, 0, 0.5);
        }

        textarea {
            resize: vertical;
            min-height: 110px;
        }

        /* Priority Radio Group */
        .priority-options {
            display: flex;
            gap: 0.75rem;
            margin-top: 0.2rem;
        }

        .priority-label {
            flex: 1;
            cursor: pointer;
            position: relative;
        }

        .priority-label input {
            position: absolute;
            opacity: 0;
        }

        .priority-box {
            border: 1px solid var(--surface-border);
            background: rgba(0,0,0,0.25);
            padding: 0.6rem 0.5rem;
            border-radius: var(--radius-sm);
            text-align: center;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .priority-label input:checked + .priority-box.low {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
        }

        .priority-label input:checked + .priority-box.medium {
            border-color: #f59e0b;
            background: rgba(245, 158, 11, 0.15);
            color: #fcd34d;
        }

        .priority-label input:checked + .priority-box.urgent {
            border-color: #ef4444;
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
        }

        /* Submit Button */
        .submit-btn {
            background: linear-gradient(135deg, var(--primary) 0%, #4f46e5 100%);
            border: none;
            color: white;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            padding: 0.95rem 1.8rem;
            border-radius: var(--radius-md);
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            box-shadow: 0 4px 20px var(--primary-glow);
            transition: all 0.2s ease;
            margin-top: 0.5rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(99, 102, 241, 0.45);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Result Alert */
        .result-box {
            display: none;
            margin-top: 1.5rem;
            padding: 1.2rem;
            border-radius: var(--radius-md);
            border: 1px solid rgba(16, 185, 129, 0.3);
            background: rgba(16, 185, 129, 0.1);
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .result-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6ee7b7;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .result-steps {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.6rem;
            padding-top: 0.6rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .step-check {
            color: #10b981;
            font-weight: 700;
        }

        .result-links {
            display: flex;
            gap: 0.75rem;
            margin-top: 0.8rem;
            flex-wrap: wrap;
        }

        .btn-link {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.08);
            color: white;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.2s;
        }

        .btn-link:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        /* Spinner */
        .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Top Nav -->
        <nav class="top-nav">
            <a href="/" class="brand">
                <div class="brand-icon">⚡</div>
                <div class="brand-text">
                    <h1>Support Ticket System</h1>
                    <span>Docker • Redis Queue • Pusher Realtime</span>
                </div>
            </a>
            <div class="nav-links">
                <a href="/admin" target="_blank" class="nav-btn admin-link">
                    <span>📡 Open Live Admin Dashboard</span>
                    <span style="font-size: 0.7rem; background: var(--pusher); color: #000; padding: 1px 6px; border-radius: 10px; font-weight: 800;">Pusher</span>
                </a>
                <a href="http://localhost:8025" target="_blank" class="nav-btn">
                    <span>📬 Mailpit Inbox</span>
                </a>
                <a href="/" class="nav-btn">
                    <span>🧪 Old Demo</span>
                </a>
            </div>
        </nav>

        <!-- Workflow Visual Strip -->
        <div class="info-strip">
            <div style="font-weight: 600; color: #e2e8f0;">⚡ Form Submission Pipeline:</div>
            <div class="flow-steps">
                <span class="pill pill-form">1. Form Submit</span>
                <span>➔</span>
                <span class="pill pill-pusher">2. Pusher Direct Broadcast</span>
                <span>➔</span>
                <span class="pill pill-redis">3. Redis Queue Job</span>
                <span>➔</span>
                <span class="pill pill-mail">4. Mailpit HTML Email</span>
            </div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <div class="form-header">
                <h2>Submit an Inquiry / Support Request</h2>
                <p>Fill out the form below. Once submitted, it will trigger an instant Pusher WebSocket notification to the <strong>Admin Dashboard</strong> and dispatch an async confirmation email to the <strong>Redis Queue</strong>.</p>
            </div>

            <form id="ticketForm" onsubmit="handleFormSubmit(event)">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" required placeholder="e.g. Awais Khan" value="Awais Khan">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address (Receives Queued Mail)</label>
                        <input type="email" id="email" name="email" required placeholder="e.g. awais@example.com" value="awais@example.com">
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required placeholder="e.g. Redis Queue & Pusher Integration" value="Need help with Laravel Docker & Redis Queue">
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select id="department" name="department">
                            <option value="Technical Support">Technical Support</option>
                            <option value="Billing & Sales">Billing & Sales</option>
                            <option value="DevOps & Cloud">DevOps & Cloud</option>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label>Priority Level</label>
                        <div class="priority-options">
                            <label class="priority-label">
                                <input type="radio" name="priority" value="normal">
                                <div class="priority-box low">🟢 Normal</div>
                            </label>
                            <label class="priority-label">
                                <input type="radio" name="priority" value="high" checked>
                                <div class="priority-box medium">🟡 High</div>
                            </label>
                            <label class="priority-label">
                                <input type="radio" name="priority" value="urgent">
                                <div class="priority-box urgent">🔴 Urgent</div>
                            </label>
                        </div>
                    </div>
                    <div class="form-group full-width">
                        <label for="message">Detailed Message</label>
                        <textarea id="message" name="message" required placeholder="Describe what you need assistance with...">Testing asynchronous email delivery via Redis queue worker and instant real-time notification on Admin page using Pusher!</textarea>
                    </div>
                </div>

                <button type="submit" id="submitBtn" class="submit-btn">
                    <div class="spinner" id="spinner"></div>
                    <span id="btnText">🚀 Submit Ticket & Dispatch to Pipeline</span>
                </button>
            </form>

            <!-- Result Box -->
            <div id="resultBox" class="result-box">
                <div class="result-title">
                    <span>✅ Ticket Dispatched Successfully!</span>
                </div>
                <div id="resultMsg" style="font-size: 0.9rem; color: #e2e8f0;"></div>
                <div class="result-steps">
                    <div class="step-item">
                        <span class="step-check">✔</span>
                        <span><strong>Pusher Broadcast:</strong> Event broadcasted to <code>inquiry-channel</code>. Check the <a href="/admin" target="_blank" style="color: #67e8f9; text-decoration: underline;">Admin Dashboard</a> tab!</span>
                    </div>
                    <div class="step-item">
                        <span class="step-check">✔</span>
                        <span><strong>Redis Queue:</strong> <code>ProcessInquiryJob</code> pushed to Redis. Handled by <code>queue:work</code>.</span>
                    </div>
                    <div class="step-item">
                        <span class="step-check">✔</span>
                        <span><strong>Mailpit Inbox:</strong> HTML email generated and delivered to Mailpit!</span>
                    </div>
                </div>
                <div class="result-links">
                    <a href="/admin" target="_blank" class="btn-link" style="border-color: rgba(6, 182, 212, 0.4); color: #67e8f9;">
                        👁 View Realtime Admin Dashboard
                    </a>
                    <a href="http://localhost:8025" target="_blank" class="btn-link" style="border-color: rgba(16, 185, 129, 0.4); color: #6ee7b7;">
                        📬 Open Mailpit Inbox (Port 8025)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function handleFormSubmit(event) {
            event.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const btnText = document.getElementById('btnText');
            const resultBox = document.getElementById('resultBox');
            const resultMsg = document.getElementById('resultMsg');

            submitBtn.disabled = true;
            spinner.style.display = 'inline-block';
            btnText.textContent = 'Processing Pipeline...';
            resultBox.style.display = 'none';

            const payload = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                subject: document.getElementById('subject').value,
                department: document.getElementById('department').value,
                priority: document.querySelector('input[name="priority"]:checked').value,
                message: document.getElementById('message').value
            };

            try {
                const response = await fetch('/submit-inquiry', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    resultMsg.innerHTML = `Ticket <strong>#${data.ticket_id}</strong> created for <strong>${data.inquiry.name}</strong> (${data.inquiry.email}).`;
                    resultBox.style.display = 'block';
                } else {
                    alert('Error: ' + (data.message || 'Something went wrong.'));
                }
            } catch (err) {
                alert('Request failed: ' + err.message);
            } finally {
                submitBtn.disabled = false;
                spinner.style.display = 'none';
                btnText.textContent = '🚀 Submit Ticket & Dispatch to Pipeline';
            }
        }
    </script>
</body>
</html>
