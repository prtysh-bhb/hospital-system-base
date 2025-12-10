<!DOCTYPE html>
<html lang="en">

<body style="margin:0; padding:0; background:#eef1f5; font-family: 'Arial', Helvetica, sans-serif;">

    <!-- Outer wrapper -->
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0; background:#eef1f5;">
        <tr>
            <td align="center">

                <!-- Card -->
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:10px; box-shadow:0 4px 18px rgba(0,0,0,0.07); overflow:hidden; border:1px solid #e6e9ef;">

                    <!-- HEADER -->
                    <tr>
                        <td style="padding:28px; text-align:center; background:#ffffff;">
                            <div style="font-size:22px; font-weight:700; color:#2d2d2d; letter-spacing:-0.2px;">
                                MediCare HMS
                            </div>
                            <div style="font-size:13px; color:#6a6a6a; margin-top:4px;">
                                Hospital Management System
                            </div>
                        </td>
                    </tr>

                    <!-- Subtle separator -->
                    <tr>
                        <td style="height:2px; background:linear-gradient(to right, #ffffff, #dfe3ea, #ffffff);"></td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td style="padding:32px 30px; font-size:15px; line-height:1.7; color:#333;">

                            <h2 style="margin:0 0 18px 0; font-size:20px; font-weight:600; color:#222;">
                                Password Reset Request
                            </h2>

                            <p style="margin:0 0 18px 0;">
                                We received a request to reset the password associated with your account.
                                Click the button below to reset your password.
                            </p>

                            <!-- CTA Button -->
                            <div style="text-align:center; margin:28px 0;">
                                <a href="{{ $url }}"
                                    style="background:#0ea5e9; padding:14px 32px; color:white; text-decoration:none; border-radius:6px; font-weight:600; display:inline-block; font-size:15px;">
                                    Reset Password
                                </a>
                            </div>

                            <!-- Highlight Box -->
                            <div
                                style="
                                background:#fef3c7;
                                border-left:4px solid #f59e0b;
                                padding:14px 16px;
                                margin:24px 0;
                                border-radius:4px;
                                font-size:14px;
                                color:#92400e;
                                ">
                                <strong>Note:</strong> This link will expire in 60 minutes. If you did not request a
                                password reset, please ignore this email or contact support if you have concerns.
                            </div>

                            <p style="margin:18px 0 0 0; font-size:13px; color:#666;">
                                If the button above doesn't work, copy and paste the following link into your browser:
                            </p>
                            <p style="margin:8px 0 0 0; font-size:12px; color:#0ea5e9; word-break:break-all;">
                                {{ $url }}
                            </p>

                            <p style="margin:28px 0 0 0;">
                                Regards,<br>
                                <strong>MediCare HMS Team</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- FOOTER SEPARATOR -->
                    <tr>
                        <td style="height:1px; background:#e8e8e8;"></td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="padding:18px; text-align:center; font-size:12px; color:#9a9a9a; background:#fafafa;">
                            © {{ date('Y') }} MediCare HMS. All rights reserved.<br>
                            This is an automated email — please do not reply.
                        </td>
                    </tr>

                </table>
                <!-- End Card -->

            </td>
        </tr>
    </table>

</body>

</html>
