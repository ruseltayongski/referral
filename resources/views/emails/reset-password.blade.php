<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset Request</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f9;font-family:-apple-system,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f9;padding:30px 15px;">
    <tr>
        <td align="center">
            <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;background:#ffffff;border-radius:10px;overflow:hidden;">

                <!-- Header -->
                <tr>
                    <td style="background-color:#1e3c72;padding:24px 32px;text-align:center;">
                        <span style="color:#ffffff;font-size:18px;font-weight:600;">{{ $appName }}</span>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:32px;color:#2d3748;font-size:15px;line-height:1.6;">
                        <h2 style="margin:0 0 16px;font-size:20px;color:#1e3c72;">Password Reset Request</h2>

                        <p style="margin:0 0 16px;">Good day,</p>
                        <p style="margin:0 0 16px;">
                            We received a request to reset the password for your account.
                            Click the button below to choose a new password.
                        </p>

                        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:24px auto;">
                            <tr>
                                <td align="center" style="background-color:#2a5298;border-radius:8px;">
                                    <a href="{{ $url }}"
                                       style="display:inline-block;padding:13px 32px;color:#ffffff;font-size:15px;font-weight:600;text-decoration:none;">
                                        Reset Password
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0 0 16px;">
                            This link will expire in <strong>{{ $expire }} minutes</strong>.
                        </p>
                        <p style="margin:0 0 16px;color:#718096;font-size:13px;">
                            If you did not request a password reset, no further action is required and your
                            password will remain unchanged. For your security, do not share this email or
                            link with anyone.
                        </p>

                        <p style="margin:24px 0 0;">Respectfully,<br><strong>{{ $appName }}</strong></p>
                    </td>
                </tr>

                <!-- Fallback link -->
                <tr>
                    <td style="padding:20px 32px;background-color:#f7fafc;border-top:1px solid #e2e8f0;color:#718096;font-size:12px;line-height:1.5;">
                        If the button doesn't work, copy and paste this link into your browser:<br>
                        <a href="{{ $url }}" style="color:#2a5298;word-break:break-all;">{{ $url }}</a>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="padding:16px 32px;text-align:center;color:#a0aec0;font-size:12px;">
                        &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>