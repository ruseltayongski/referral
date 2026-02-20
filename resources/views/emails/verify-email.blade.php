<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #eef2f1;
            margin: 0;
            padding: 24px 12px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 4px;
            border: 1px solid #dbe3e1;
            overflow: hidden;
        }
        .header {
            background: #f7faf9;
            color: #1f3a35;
            padding: 20px 24px;
            border-bottom: 1px solid #e1e9e7;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }
        .content {
            padding: 28px 24px;
            color: #333;
            line-height: 1.6;
        }
        .content p {
            margin: 0 0 20px 0;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background-color: #2f8f76;
            color: #ffffff !important;
            padding: 12px 28px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 15px;
        }
        .button:hover {
            background-color: #267660;
        }
        .footer {
            background-color: #fafcfc;
            padding: 24px;
            border-top: 1px solid #e6eceb;
            font-size: 13px;
            color: #666;
            text-align: center;
        }
        .divider {
            border: none;
            border-top: 1px solid #e9efee;
            margin: 28px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ $greeting }}</h1>
        </div>
        
        <div class="content">
            <p>Thank you for registering as a patient.</p>
            
            <p>Please click the button below to verify your email address and activate your account.</p>
            
            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="button" style="color: #ffffff !important;">Verify Email Address</a>
            </div>
            
            <p style="color: #888; font-size: 14px;">This verification link will expire in 60 minutes.</p>
            
            <hr class="divider">
            
            <p>If you did not create an account, no further action is required.</p>
        </div>
        
        <div class="footer">
            {{ $salutation }}<br><br>
            <img src="https://cvchd7.com/resources/img/doh.png" alt="DOH Logo" style="width: 60px; height: 60px; margin-top: 15px; display: inline-block;">
        </div>
    </div>
</body>
</html>
