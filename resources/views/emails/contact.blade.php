<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #0f172a; color: #e2e8f0; padding: 40px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #1e293b; border: 1px solid rgba(234,179,8,0.3); border-radius: 12px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #1e293b, #0f172a); padding: 30px; text-align: center; border-bottom: 2px solid rgba(234,179,8,0.3); }
        .header h1 { color: #eab308; font-size: 22px; margin: 0; letter-spacing: 2px; text-transform: uppercase; }
        .body { padding: 30px; }
        .field { margin-bottom: 20px; }
        .label { color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin-bottom: 6px; }
        .value { color: #f1f5f9; font-size: 15px; line-height: 1.6; }
        .message-box { background-color: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 20px; margin-top: 10px; }
        .footer { padding: 20px 30px; text-align: center; border-top: 1px solid #334155; }
        .footer p { color: #64748b; font-size: 12px; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚡ Contact Transmission</h1>
        </div>
        <div class="body">
            <div class="field">
                <div class="label">From</div>
                <div class="value">{{ $senderName }} &lt;{{ $senderEmail }}&gt;</div>
            </div>
            <div class="field">
                <div class="label">Subject</div>
                <div class="value">{{ $messageSubject }}</div>
            </div>
            <div class="field">
                <div class="label">Message</div>
                <div class="message-box">
                    <div class="value">{!! nl2br(e($body)) !!}</div>
                </div>
            </div>
        </div>
        <div class="footer">
            <p>This message was sent via the Adept's Armoury contact form.</p>
        </div>
    </div>
</body>
</html>
