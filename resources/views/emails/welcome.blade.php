<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Welcome to Bazaario</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
                <o:AllowPNG/>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <style>
        table, td, div, p, a { font-family: Arial, sans-serif !important; }
    </style>
    <![endif]-->
    <style>
        body, table, td { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        body { margin: 0; padding: 0; width: 100% !important; height: 100% !important; }
        a { color: #F5A623; text-decoration: none; }

        @media screen and (max-width: 600px) {
            .email-container { width: 100% !important; }
            .stack-col { display: block !important; width: 100% !important; padding: 0 0 12px 0 !important; }
            .px-mobile { padding-left: 24px !important; padding-right: 24px !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#FFFDF8;">

    <!-- Preheader -->
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">
        Your Bazaario account is ready — start browsing nearby stalls or set up your own shop.
    </div>
    <div style="display:none; max-height:0; overflow:hidden;">&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#FFFDF8;">
        <tr>
            <td align="center" style="padding: 40px 16px 60px;">

                <table role="presentation" class="email-container" width="600" cellpadding="0" cellspacing="0" border="0"
                       style="width:600px; max-width:600px; background-color:#FFFFFF; border-radius:14px; overflow:hidden; border:1px solid rgba(15,23,42,0.08);">

                    <!-- ============ HEADER ============ -->
                    <tr>
                        <td class="px-mobile" style="background-color:#0F172A; padding: 36px 40px 32px; background-image: linear-gradient(135deg, #0F172A 0%, #1e293b 100%);">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;">
                                            <tr>
                                                <td style="background-color:rgba(245,166,35,0.15); border:1px solid rgba(245,166,35,0.35); border-radius:100px; padding:5px 14px;">
                                                    <span style="font-family:'JetBrains Mono','Courier New',monospace; font-size:10px; font-weight:500; letter-spacing:0.08em; text-transform:uppercase; color:#F5A623;">
                                                        &#9679;&nbsp; Bazaario Marketplace
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>

                                        <div style="font-family:'Space Grotesk','Segoe UI',Arial,sans-serif; font-size:26px; font-weight:700; color:#FFFFFF; letter-spacing:-0.02em; line-height:1.25;">
                                            Welcome to the market{{ '' }}{{-- avoid trailing space collapse --}}, {{ $name ?? 'there' }}.
                                        </div>
                                        <div style="font-family:'Inter',Arial,sans-serif; font-size:14px; color:rgba(255,253,248,0.60); margin-top:6px; font-weight:400;">
                                            Your account is verified and ready to go
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ============ BODY INTRO ============ -->
                    <tr>
                        <td class="px-mobile" style="padding: 40px 40px 8px;">
                            <p style="font-family:'Inter',Arial,sans-serif; font-size:15px; color:#1b1c19; font-weight:500; margin:0 0 10px;">
                                Hi {{ $name ?? 'there' }},
                            </p>
                            <p style="font-family:'Inter',Arial,sans-serif; font-size:14px; color:rgba(15,23,42,0.70); line-height:1.7; margin:0 0 28px;">
                                Your Bazaario account has been created successfully. One cart, every shop —
                                you're all set to start
                                @if (($role ?? 'customer') === 'seller')
                                    setting up your stall.
                                @else
                                    browsing the market.
                                @endif
                            </p>
                        </td>
                    </tr>

                    {{-- ============ ACCOUNT SUMMARY CARD ============ --}}
                    <tr>
                        <td class="px-mobile" style="padding: 0 40px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                   style="background-color:#FFFDF8; border:1.5px solid rgba(15,23,42,0.10); border-radius:14px;">
                                <tr>
                                    <td style="background-color:#F5A623; height:3px; line-height:3px; font-size:1px; border-radius:14px 14px 0 0;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="padding: 22px 24px;">
                                        <div style="font-family:'JetBrains Mono','Courier New',monospace; font-size:10px; font-weight:500; letter-spacing:0.1em; text-transform:uppercase; color:#76777d; margin-bottom:14px;">
                                            Account Details
                                        </div>

                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding: 6px 0; font-family:'Inter',Arial,sans-serif; font-size:13px; color:#76777d;">Name</td>
                                                <td align="right" style="padding: 6px 0; font-family:'Inter',Arial,sans-serif; font-size:13px; color:#0F172A; font-weight:600;">{{ $name ?? '—' }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border-top:1px solid rgba(15,23,42,0.06); line-height:1px; font-size:1px;">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-family:'Inter',Arial,sans-serif; font-size:13px; color:#76777d;">Email</td>
                                                <td align="right" style="padding: 6px 0; font-family:'Inter',Arial,sans-serif; font-size:13px; color:#0F172A; font-weight:600;">{{ $email ?? '—' }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border-top:1px solid rgba(15,23,42,0.06); line-height:1px; font-size:1px;">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-family:'Inter',Arial,sans-serif; font-size:13px; color:#76777d;">Account type</td>
                                                <td align="right" style="padding: 6px 0;">
                                                    <span style="font-family:'JetBrains Mono','Courier New',monospace; font-size:11px; background-color:rgba(22,163,74,0.10); color:#16A34A; border-radius:100px; padding:3px 10px; font-weight:600;">
                                                        {{ ucfirst($role ?? 'customer') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ============ PRIMARY CTA ============ -->
                    <tr>
                        <td class="px-mobile" align="center" style="padding: 0 40px 32px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="background-color:#F5A623; border-radius:14px;">
                                        <a href="{{ $ctaUrl ?? '#' }}"
                                           style="display:inline-block; padding:14px 32px; font-family:'Inter',Arial,sans-serif; font-size:15px; font-weight:600; color:#0F172A; text-decoration:none;">
                                            @if (($role ?? 'customer') === 'seller')
                                                Set up your stall
                                            @else
                                                Start browsing the market
                                            @endif
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ============ WHAT'S NEXT — 3 STEPS ============ -->
                    <tr>
                        <td class="px-mobile" style="padding: 0 40px 28px;">
                            <div style="font-family:'Space Grotesk','Segoe UI',Arial,sans-serif; font-size:16px; font-weight:700; color:#0F172A; margin-bottom:16px;">
                                @if (($role ?? 'customer') === 'seller')
                                    Getting your stall live
                                @else
                                    Getting started
                                @endif
                            </div>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                @if (($role ?? 'customer') === 'seller')
                                    <tr>
                                        <td valign="top" width="32" style="padding-bottom:16px;">
                                            <table role="presentation" width="24" height="24" cellpadding="0" cellspacing="0" border="0" style="background-color:rgba(245,166,35,0.15); border-radius:100px;">
                                                <tr><td align="center" valign="middle" style="font-family:'JetBrains Mono',monospace; font-size:11px; font-weight:700; color:#F5A623;">1</td></tr>
                                            </table>
                                        </td>
                                        <td valign="top" style="padding-bottom:16px; padding-left:10px;">
                                            <div style="font-family:'Inter',Arial,sans-serif; font-size:13px; font-weight:600; color:#0F172A;">Complete your shop profile</div>
                                            <div style="font-family:'Inter',Arial,sans-serif; font-size:12px; color:rgba(15,23,42,0.60); margin-top:2px;">Add your shop name, location, and a short description.</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td valign="top" width="32" style="padding-bottom:16px;">
                                            <table role="presentation" width="24" height="24" cellpadding="0" cellspacing="0" border="0" style="background-color:rgba(245,166,35,0.15); border-radius:100px;">
                                                <tr><td align="center" valign="middle" style="font-family:'JetBrains Mono',monospace; font-size:11px; font-weight:700; color:#F5A623;">2</td></tr>
                                            </table>
                                        </td>
                                        <td valign="top" style="padding-bottom:16px; padding-left:10px;">
                                            <div style="font-family:'Inter',Arial,sans-serif; font-size:13px; font-weight:600; color:#0F172A;">List your first product</div>
                                            <div style="font-family:'Inter',Arial,sans-serif; font-size:12px; color:rgba(15,23,42,0.60); margin-top:2px;">Photos, price, and stock — takes about two minutes.</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td valign="top" width="32">
                                            <table role="presentation" width="24" height="24" cellpadding="0" cellspacing="0" border="0" style="background-color:rgba(245,166,35,0.15); border-radius:100px;">
                                                <tr><td align="center" valign="middle" style="font-family:'JetBrains Mono',monospace; font-size:11px; font-weight:700; color:#F5A623;">3</td></tr>
                                            </table>
                                        </td>
                                        <td valign="top" style="padding-left:10px;">
                                            <div style="font-family:'Inter',Arial,sans-serif; font-size:13px; font-weight:600; color:#0F172A;">Go live and start selling</div>
                                            <div style="font-family:'Inter',Arial,sans-serif; font-size:12px; color:rgba(15,23,42,0.60); margin-top:2px;">Your stall appears in the market once approved.</div>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td valign="top" width="32" style="padding-bottom:16px;">
                                            <table role="presentation" width="24" height="24" cellpadding="0" cellspacing="0" border="0" style="background-color:rgba(245,166,35,0.15); border-radius:100px;">
                                                <tr><td align="center" valign="middle" style="font-family:'JetBrains Mono',monospace; font-size:11px; font-weight:700; color:#F5A623;">1</td></tr>
                                            </table>
                                        </td>
                                        <td valign="top" style="padding-bottom:16px; padding-left:10px;">
                                            <div style="font-family:'Inter',Arial,sans-serif; font-size:13px; font-weight:600; color:#0F172A;">Browse the market</div>
                                            <div style="font-family:'Inter',Arial,sans-serif; font-size:12px; color:rgba(15,23,42,0.60); margin-top:2px;">Explore stalls and products from independent sellers near you.</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td valign="top" width="32" style="padding-bottom:16px;">
                                            <table role="presentation" width="24" height="24" cellpadding="0" cellspacing="0" border="0" style="background-color:rgba(245,166,35,0.15); border-radius:100px;">
                                                <tr><td align="center" valign="middle" style="font-family:'JetBrains Mono',monospace; font-size:11px; font-weight:700; color:#F5A623;">2</td></tr>
                                            </table>
                                        </td>
                                        <td valign="top" style="padding-bottom:16px; padding-left:10px;">
                                            <div style="font-family:'Inter',Arial,sans-serif; font-size:13px; font-weight:600; color:#0F172A;">Add items to your cart</div>
                                            <div style="font-family:'Inter',Arial,sans-serif; font-size:12px; color:rgba(15,23,42,0.60); margin-top:2px;">Mix and match from different sellers in a single order.</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td valign="top" width="32">
                                            <table role="presentation" width="24" height="24" cellpadding="0" cellspacing="0" border="0" style="background-color:rgba(245,166,35,0.15); border-radius:100px;">
                                                <tr><td align="center" valign="middle" style="font-family:'JetBrains Mono',monospace; font-size:11px; font-weight:700; color:#F5A623;">3</td></tr>
                                            </table>
                                        </td>
                                        <td valign="top" style="padding-left:10px;">
                                            <div style="font-family:'Inter',Arial,sans-serif; font-size:13px; font-weight:600; color:#0F172A;">Check out and track your order</div>
                                            <div style="font-family:'Inter',Arial,sans-serif; font-size:12px; color:rgba(15,23,42,0.60); margin-top:2px;">Follow each seller's shipment right from your account.</div>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <!-- ============ SECURITY NOTE ============ -->
                    <tr>
                        <td class="px-mobile" style="padding: 0 40px 36px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#F5F3EE; border-radius:12px;">
                                <tr>
                                    <td style="padding:14px 16px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td width="28" valign="top">
                                                    <table role="presentation" width="28" height="28" cellpadding="0" cellspacing="0" border="0" style="background-color:rgba(15,23,42,0.08); border-radius:8px;">
                                                        <tr><td align="center" valign="middle" style="font-size:13px;">&#128274;</td></tr>
                                                    </table>
                                                </td>
                                                <td width="10">&nbsp;</td>
                                                <td valign="top">
                                                    <div style="font-family:'Inter',Arial,sans-serif; font-size:12px; color:rgba(15,23,42,0.70); line-height:1.6;">
                                                        Wasn't you? If you didn't create this account, please
                                                        <a href="{{ $supportUrl ?? '#' }}" style="color:#F5A623; font-weight:600;">contact support</a>
                                                        right away.
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ============ FOOTER ============ -->
                    <tr>
                        <td class="px-mobile" style="background-color:#F5F3EE; padding: 20px 40px 24px; border-top:1px solid rgba(15,23,42,0.06);">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom:10px;">
                                        <a href="#" style="font-family:'Inter',Arial,sans-serif; font-size:11px; color:#76777d; font-weight:500; text-decoration:none; padding:0 10px;">Privacy Policy</a>
                                        <span style="color:#c6c6cd;">&middot;</span>
                                        <a href="#" style="font-family:'Inter',Arial,sans-serif; font-size:11px; color:#76777d; font-weight:500; text-decoration:none; padding:0 10px;">Terms of Service</a>
                                        <span style="color:#c6c6cd;">&middot;</span>
                                        <a href="#" style="font-family:'Inter',Arial,sans-serif; font-size:11px; color:#76777d; font-weight:500; text-decoration:none; padding:0 10px;">Help Center</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <div style="font-family:'Inter',Arial,sans-serif; font-size:11px; color:#c6c6cd;">
                                            &copy; {{ date('Y') }} Bazaario Marketplace &middot; All rights reserved
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>

                <table role="presentation" class="email-container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px; max-width:600px;">
                    <tr>
                        <td align="center" style="padding: 20px 20px 0;">
                            <div style="font-family:'JetBrains Mono','Courier New',monospace; font-size:10px; letter-spacing:0.06em; color:rgba(15,23,42,0.35); text-transform:uppercase;">
                                Sent securely by Bazaario &middot; Do not reply to this email
                            </div>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>
