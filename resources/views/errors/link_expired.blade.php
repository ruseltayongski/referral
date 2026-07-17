<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Expired</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f4f5f7;
        }
        .modal-overlay {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-box {
            background: #fff;
            border-radius: 12px;
            padding: 32px 28px;
            max-width: 380px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        .icon-wrap { margin-bottom: 12px; }
        h2 { margin: 0 0 12px; font-size: 1.25rem; color: #222; }
        p { margin: 0 0 8px; color: #555; font-size: 0.95rem; line-height: 1.4; }
        .hint { font-size: 0.85rem; color: #888; margin-bottom: 0; }
    </style>
</head>
<body>
    <div class="modal-overlay">
        <div class="modal-box">
            <div class="icon-wrap">
                <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#e74c3c" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="13" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
            </div>

            <h2>Link Expired or Invalid</h2>
            <p>{{ $message ?? 'Expired or invalid access link' }}</p>
            <p class="hint">
                This can happen if the link is old or has already been used.
                Please contact your care provider for a new access link.
            </p>
        </div>
    </div>
</body>
</html>