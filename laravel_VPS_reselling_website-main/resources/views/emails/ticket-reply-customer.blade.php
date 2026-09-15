<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Ticket Reply</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0c0217;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #e2e8f0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #16072b;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #2e1065;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.6);
        }
        .header {
            background: linear-gradient(135deg, #4c1d95, #673de6);
            padding: 32px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 8px 0 0 0;
            color: #e9d5ff;
            font-size: 15px;
        }
        .content {
            padding: 32px 30px;
        }
        .greeting {
            font-size: 16px;
            color: #f8fafc;
            margin-bottom: 16px;
        }
        .ticket-info {
            background-color: #0f0521;
            border: 1px solid #2e1065;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .reply-box {
            background-color: #1e0b38;
            border-left: 4px solid #a855f7;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 24px;
            color: #f1f5f9;
            font-size: 14px;
            white-space: pre-wrap;
            line-height: 1.6;
        }
        .staff-badge {
            display: inline-block;
            background-color: #673de6;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-container {
            text-align: center;
            margin: 28px 0 16px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #673de6, #7c3aed);
            color: #ffffff !important;
            text-decoration: none;
            padding: 13px 32px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 15px;
            box-shadow: 0 4px 14px rgba(103, 61, 230, 0.45);
        }
        .footer {
            background-color: #0c0217;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #2e1065;
        }
        .footer a {
            color: #a855f7;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💬 Support Ticket Response</h1>
            <p>Ticket {{ $ticket->formatted_id }}: {{ $ticket->subject }}</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello <strong>{{ $user->name }}</strong>,
            </div>
            <p style="color: #cbd5e1; font-size: 14px; margin-bottom: 16px;">
                Our cloud support team has responded to your ticket. Here is the response:
            </p>

            <div class="reply-box">
                <span class="staff-badge">Support Staff Response</span><br>
                {{ $reply->message }}
            </div>

            <div class="ticket-info">
                <strong style="color: #cbd5e1;">Ticket ID:</strong> {{ $ticket->formatted_id }} &bull; 
                <strong style="color: #cbd5e1;">Department:</strong> {{ ucfirst($ticket->department) }} &bull; 
                <strong style="color: #cbd5e1;">Priority:</strong> {{ ucfirst($ticket->priority) }}
            </div>

            <div class="btn-container">
                <a href="{{ url('/customer/support-tickets/' . $ticket->id) }}" class="btn">
                    View Ticket & Reply in Portal &rarr;
                </a>
            </div>

            <p style="color: #64748b; font-size: 12px; text-align: center; margin-top: 16px;">
                You can reply directly inside your Customer Portal to continue this conversation.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>VortexCloud Cloud Infrastructure Support Team</p>
        </div>
    </div>
</body>
</html>
