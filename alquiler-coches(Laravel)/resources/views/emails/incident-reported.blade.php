<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Incident Reported</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #92400e 0%, #b45309 100%); padding: 32px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: 0.5px;">
                                NEUVO — Admin
                            </h1>
                            <p style="margin: 8px 0 0; color: #fde68a; font-size: 14px;">
                                ⚠ New Incident Reported
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 40px;">

                            {{-- Alert --}}
                            <p style="margin: 0 0 24px; font-size: 16px; color: #334155;">
                                A customer has reported a new incident. Please review the details below:
                            </p>

                            {{-- Details card --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #fffbeb; border-radius: 8px; border: 1px solid #fde68a; margin-bottom: 32px;">
                                <tr>
                                    <td style="padding: 20px 24px; border-bottom: 1px solid #fde68a;">
                                        <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Incident ID</span><br>
                                        <span style="font-size: 16px; color: #0f172a; font-weight: 600;">#{{ $incident->id }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 24px; border-bottom: 1px solid #fde68a;">
                                        <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Type</span><br>
                                        <span style="font-size: 16px; color: #b45309; font-weight: 600; text-transform: capitalize;">{{ $incident->type }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 24px; border-bottom: 1px solid #fde68a;">
                                        <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Description</span><br>
                                        <span style="font-size: 15px; color: #0f172a; font-weight: 500; line-height: 1.5;">{{ $incident->description }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 24px; border-bottom: 1px solid #fde68a;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="50%">
                                                    <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Reservation</span><br>
                                                    <span style="font-size: 15px; color: #0f172a; font-weight: 500;">#{{ $incident->reservation_id }}</span>
                                                </td>
                                                <td width="50%">
                                                    <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Vehicle</span><br>
                                                    <span style="font-size: 15px; color: #0f172a; font-weight: 500;">{{ $incident->car->name ?? 'N/A' }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 24px; border-bottom: 1px solid #fde68a;">
                                        <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Customer Email</span><br>
                                        <span style="font-size: 15px; color: #0f172a; font-weight: 500;">{{ $incident->reservation->customer_email ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Reported At</span><br>
                                        <span style="font-size: 15px; color: #0f172a; font-weight: 500;">{{ $incident->created_at->format('M d, Y — H:i') }}</span>
                                    </td>
                                </tr>
                            </table>

                            {{-- Action --}}
                            <p style="margin: 0; font-size: 14px; color: #64748b; line-height: 1.6;">
                                Please review this incident in the admin dashboard and take appropriate action.
                            </p>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 13px; color: #94a3b8; line-height: 1.5;">
                                This is an internal notification for NEUVO administrators.<br>
                                Do not forward this email to customers.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
