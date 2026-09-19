<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Bazaario Verification Code</title>
    <!--[if mso]><noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript><![endif]-->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: #f5f3ee;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            -webkit-font-smoothing: antialiased;
            color: #1b1c19;
        }

        .email-wrapper {
            background-color: #f5f3ee;
            padding: 40px 16px 60px;
        }

        .email-container {
            max-width: 560px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(15, 23, 42, 0.12);
        }

        /* ── Header ── */
        .header {
            background: linear-gradient(135deg, #0F172A 0%, #1e293b 100%);
            padding: 36px 40px 32px;
            position: relative;
            overflow: hidden;
        }
        .header::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(245,166,35,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: -40px;
            left: -40px;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(245,166,35,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .header-inner { position: relative; z-index: 1; }
        .brand-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(245,166,35,0.15);
            border: 1px solid rgba(245,166,35,0.3);
            padding: 4px 12px;
            border-radius: 100px;
            margin-bottom: 16px;
        }
        .brand-dot {
            width: 6px;
            height: 6px;
            background: #F5A623;
            border-radius: 50%;
        }
        .brand-text {
            font-family: 'Inter', sans-serif;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #F5A623;
        }
        .header-title {
            font-family: 'Space Grotesk', 'Inter', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        .header-subtitle {
            font-size: 14px;
            color: rgba(255,255,255,0.55);
            margin-top: 6px;
            font-weight: 400;
        }

        /* ── Body ── */
        .body {
            padding: 40px 40px 32px;
        }
        .greeting {
            font-size: 15px;
            color: #1b1c19;
            margin-bottom: 10px;
            font-weight: 500;
        }
        .message {
            font-size: 14px;
            color: #45464d;
            line-height: 1.7;
            margin-bottom: 32px;
        }

        /* ── OTP Box ── */
        .otp-container {
            background: linear-gradient(135deg, #fffdf8 0%, #fef9ee 100%);
            border: 1.5px solid rgba(245,166,35,0.25);
            border-radius: 16px;
            padding: 28px 24px;
            text-align: center;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }
        .otp-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #F5A623, #f5c623, #F5A623);
        }
        .otp-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #76777d;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .otp-label-line {
            flex: 1;
            height: 1px;
            background: rgba(15,23,42,0.08);
            max-width: 40px;
        }
        .otp-digits {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-bottom: 14px;
        }
        .otp-digit-box {
            width: 48px;
            height: 58px;
            background: #ffffff;
            border: 1.5px solid rgba(245,166,35,0.4);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Space Grotesk', 'Inter', monospace;
            font-size: 26px;
            font-weight: 700;
            color: #0F172A;
            box-shadow: 0 2px 8px rgba(245,166,35,0.1);
        }
        .otp-sep {
            width: 10px;
            height: 2px;
            background: rgba(15,23,42,0.15);
            border-radius: 2px;
            margin: 0 2px;
        }
        .otp-expiry {
            font-size: 12px;
            color: #76777d;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .otp-expiry-dot {
            width: 6px;
            height: 6px;
            background: #F5A623;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        /* ── Info cards ── */
        .info-grid {
            display: flex;
            gap: 12px;
            margin-bottom: 28px;
        }
        .info-card {
            flex: 1;
            background: #f5f3ee;
            border-radius: 12px;
            padding: 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .info-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .info-icon.yellow { background: rgba(245,166,35,0.15); }
        .info-icon.red    { background: rgba(186,26,26,0.10); }
        .info-card-title {
            font-size: 11px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 2px;
        }
        .info-card-text {
            font-size: 11px;
            color: #45464d;
            line-height: 1.5;
        }

        /* ── Footer ── */
        .footer {
            background: #f5f3ee;
            padding: 20px 40px 24px;
            border-top: 1px solid rgba(15,23,42,0.06);
        }
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 10px;
        }
        .footer-link {
            font-size: 11px;
            color: #76777d;
            text-decoration: none;
            font-weight: 500;
        }
        .footer-copy {
            text-align: center;
            font-size: 11px;
            color: #c6c6cd;
        }

        @media (max-width: 560px) {
            .body { padding: 28px 20px 24px; }
            .header { padding: 28px 20px 24px; }
            .footer { padding: 16px 20px 20px; }
            .otp-digit-box { width: 40px; height: 50px; font-size: 22px; }
            .info-grid { flex-direction: column; }
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="email-container">

        <!-- Header -->
        <div class="header">
            <div class="header-inner">
                <div class="brand-pill">
                    <div class="brand-dot"></div>
                    <span class="brand-text">Bazaario Marketplace</span>
                </div>
                <div class="header-title">Verify your email address</div>
                <div class="header-subtitle">One-Time Verification Code</div>
            </div>
        </div>

        <!-- Body -->
        <div class="body">
            <p class="greeting">Hi there 👋</p>
            <p class="message">
                You're just one step away from joining <strong>Bazaario Marketplace</strong>.
                Enter the 6-digit code below to verify your email and complete your registration.
            </p>

            <!-- OTP Box -->
            <div class="otp-container">
                <div class="otp-label">
                    <span class="otp-label-line"></span>
                    Your Verification Code
                    <span class="otp-label-line"></span>
                </div>

                <!-- Render each digit as its own box -->
                <div class="otp-digits">
                    @php $digits = str_split($otp); @endphp
                    <div class="otp-digit-box">{{ $digits[0] ?? '' }}</div>
                    <div class="otp-digit-box">{{ $digits[1] ?? '' }}</div>
                    <div class="otp-digit-box">{{ $digits[2] ?? '' }}</div>
                    <div class="otp-sep"></div>
                    <div class="otp-digit-box">{{ $digits[3] ?? '' }}</div>
                    <div class="otp-digit-box">{{ $digits[4] ?? '' }}</div>
                    <div class="otp-digit-box">{{ $digits[5] ?? '' }}</div>
                </div>

                <div class="otp-expiry">
                    <span class="otp-expiry-dot"></span>
                    Expires in <strong>10 minutes</strong>
                </div>
            </div>

            <!-- Info cards -->
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-icon yellow">⏱</div>
                    <div>
                        <div class="info-card-title">Time sensitive</div>
                        <div class="info-card-text">This code is valid for 10 minutes only.</div>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-icon red">🔒</div>
                    <div>
                        <div class="info-card-title">Keep it private</div>
                        <div class="info-card-text">Never share this code with anyone.</div>
                    </div>
                </div>
            </div>

            <p style="font-size:13px; color:#76777d; line-height:1.6;">
                If you didn't create a Bazaario account, you can safely ignore this email.
                Someone may have entered your email address by mistake.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-links">
                <a href="#" class="footer-link">Privacy Policy</a>
                <a href="#" class="footer-link">Terms of Service</a>
                <a href="#" class="footer-link">Help Center</a>
            </div>
            <div class="footer-copy">© {{ date('Y') }} Bazaario Marketplace · All rights reserved</div>
        </div>

    </div>
</div>
</body>
</html>
