<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified - DOH Telemedicine</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #eef2f1;
            min-height: 100vh;
            padding: 24px 14px;
        }

        .verification-layout {
            max-width: 980px;
            margin: 0 auto;
        }

        .page-header {
            background: #59ab91;
            border-radius: 6px;
            border: 1px solid #dbe3e1;
            overflow: hidden;
        }

        .page-header img {
            width: 100%;
            height: auto;
            display: block;
        }

        .verification-card {
            background: white;
            border-radius: 6px;
            border: 1px solid #dbe3e1;
            max-width: 540px;
            width: 100%;
            margin: 16px auto 0;
            padding: 32px;
        }

        .success-icon {
            width: fit-content;
            margin: 0 0 18px;
            padding: 4px 10px;
            border-radius: 999px;
            background: #edf7f3;
            color: #267660;
            font-size: 13px;
            font-weight: 600;
        }

        .verification-card h1 {
            color: #1f3a35;
            font-size: 26px;
            margin-bottom: 12px;
        }

        .verification-card p {
            color: #555;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .btn-login {
            background: #2f8f76;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-login:hover {
            background: #267660;
        }

        @media (max-width: 480px) {
            .verification-card {
                padding: 24px 18px;
            }

            .verification-card h1 {
                font-size: 22px;
            }

            .verification-card p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="verification-layout">
        <header class="page-header">
            <img src="{{ asset('resources/img/banner_referral2023v1-01.png') }}" alt="DOH Telemedicine Header">
        </header>

        <div class="verification-card">
            <div class="success-icon">Email verified</div>
            <h1>Email Verified Successfully!</h1>
            <p>
                Your email has been verified. Your account is now active and you can access all features of the DOH Telemedicine portal.
            </p>
            <a href="{{ url('/login') }}" class="btn-login">
                Go to Login
            </a>
        </div>
    </div>
</body>
</html>