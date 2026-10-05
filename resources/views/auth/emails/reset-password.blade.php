<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password — Bazaario</title>
</head>
<body style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #FFFDF8; margin: 0; padding: 40px 20px; color: #0F172A;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 560px; background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);">
        <tr>
            <td style="padding: 32px 32px 24px; text-align: center; background-color: #0F172A;">
                <h1 style="color: #F5A623; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;">BAZAARIO</h1>
                <p style="color: #94A3B8; margin: 4px 0 0; font-size: 12px; font-family: monospace; text-transform: uppercase; letter-spacing: 1px;">Marketplace Security</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 36px 32px;">
                <h2 style="font-size: 20px; font-weight: 700; color: #0F172A; margin: 0 0 16px;">Password Reset Request</h2>
                <p style="font-size: 14px; line-height: 1.6; color: #475569; margin: 0 0 20px;">
                    Hello {{ $user->name }},<br><br>
                    You are receiving this email because we received a password reset request for your Bazaario account. Click the button below to choose a new password.
                </p>
                <div style="text-align: center; margin: 32px 0;">
                    <a href="{{ $url }}" style="background-color: #F5A623; color: #0F172A; text-decoration: none; padding: 14px 28px; border-radius: 12px; font-weight: 700; font-size: 14px; display: inline-block;">Reset Password</a>
                </div>
                <p style="font-size: 12px; line-height: 1.5; color: #64748B; margin: 0 0 16px;">
                    This password reset link will expire in 60 minutes. If you did not request a password reset, no further action is required.
                </p>
                <hr style="border: none; border-top: 1px solid #E2E8F0; margin: 24px 0;">
                <p style="font-size: 11px; color: #94A3B8; margin: 0; word-break: break-all;">
                    If you're having trouble clicking the button, copy and paste the URL below into your web browser:<br>
                    <a href="{{ $url }}" style="color: #F5A623;">{{ $url }}</a>
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px 32px; background-color: #FAF8F4; text-align: center; border-top: 1px solid #E2E8F0; font-size: 11px; color: #94A3B8;">
                &copy; {{ date('Y') }} Bazaario Marketplace. All rights reserved.
            </td>
        </tr>
    </table>
</body>
</html>
