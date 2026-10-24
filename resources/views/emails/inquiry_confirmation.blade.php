<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry Confirmation</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 40px 20px;
            color: #334155;
        }
        .card {
            max-width: 580px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 30px;
            color: white;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.25);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
            text-transform: uppercase;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
        }
        .info-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        .info-table td.label {
            color: #64748b;
            font-weight: 500;
            width: 35%;
        }
        .info-table td.value {
            color: #0f172a;
            font-weight: 600;
        }
        .message-box {
            background: #f8fafc;
            border-left: 4px solid #4f46e5;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 14px;
            color: #1e293b;
        }
        .footer {
            background: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>Thank You for Reaching Out!</h1>
            <div class="badge">Ticket #{{ $inquiry['ticket_id'] }}</div>
        </div>
        <div class="content">
            <p>Hi <strong>{{ $inquiry['name'] }}</strong>,</p>
            <p>Your inquiry has been successfully received by our support team. This email was queued through <strong>Redis Queue Worker</strong> and processed asynchronously!</p>

            <table class="info-table">
                <tr>
                    <td class="label">Ticket ID:</td>
                    <td class="value">#{{ $inquiry['ticket_id'] }}</td>
                </tr>
                <tr>
                    <td class="label">Priority Level:</td>
                    <td class="value" style="color: {{ $inquiry['priority'] === 'urgent' ? '#ef4444' : ($inquiry['priority'] === 'high' ? '#f59e0b' : '#10b981') }}; text-transform: uppercase;">
                        {{ $inquiry['priority'] }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Subject:</td>
                    <td class="value">{{ $inquiry['subject'] }}</td>
                </tr>
                <tr>
                    <td class="label">Received At:</td>
                    <td class="value">{{ $inquiry['created_at'] }}</td>
                </tr>
            </table>

            <p style="font-weight: 600; margin-bottom: 6px; font-size: 14px;">Your Message Details:</p>
            <div class="message-box">
                "{{ $inquiry['message'] }}"
            </div>

            <p style="font-size: 13px; color: #64748b;">Our support team will review your request and get back to you shortly. You do not need to reply to this automated email.</p>
        </div>
        <div class="footer">
            Sent automatically via Laravel Docker + Redis Worker • Powered by Mailpit Local Inbox
        </div>
    </div>
</body>
</html>
