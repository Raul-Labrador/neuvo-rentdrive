<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Status Update</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%); padding: 32px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: 0.5px;">
                                NEUVO
                            </h1>
                            <p style="margin: 8px 0 0; color: #93c5fd; font-size: 14px;">
                                Incident Status Update
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 40px;">

                            {{-- Greeting --}}
                            <p style="margin: 0 0 24px; font-size: 16px; color: #334155;">
                                Hello <strong>{{ $incident->reservation->customer_name ?? 'Customer' }}</strong>,
                            </p>

                            {{-- Status message --}}
                            @if($newStatus === 'in_review')
                                <p style="margin: 0 0 32px; font-size: 15px; color: #475569; line-height: 1.6;">
                                    We are currently <strong style="color: #2563eb;">reviewing your incident report</strong>. Our team will get back to you shortly.
                                </p>
                            @elseif($newStatus === 'resolved')
                                <p style="margin: 0 0 32px; font-size: 15px; color: #475569; line-height: 1.6;">
                                    Your incident has been <strong style="color: #16a34a;">resolved</strong>. We hope the issue has been addressed to your satisfaction.
                                </p>
                            @elseif($newStatus === 'dismissed')
                                <p style="margin: 0 0 32px; font-size: 15px; color: #475569; line-height: 1.6;">
                                    Your incident report has been <strong style="color: #64748b;">closed</strong>. If you believe this was in error, please contact us.
                                </p>
                            @else
                                <p style="margin: 0 0 32px; font-size: 15px; color: #475569; line-height: 1.6;">
                                    The status of your incident has been updated.
                                </p>
                            @endif

                            {{-- Details card --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0f9ff; border-radius: 8px; border: 1px solid #bfdbfe; margin-bottom: 32px;">
                                <tr>
                                    <td style="padding: 20px 24px; border-bottom: 1px solid #bfdbfe;">
                                        <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Incident ID</span><br>
                                        <span style="font-size: 16px; color: #0f172a; font-weight: 600;">#{{ $incident->id }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 24px; border-bottom: 1px solid #bfdbfe;">
                                        <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Type</span><br>
                                        <span style="font-size: 15px; color: #0f172a; font-weight: 500; text-transform: capitalize;">{{ $incident->type }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 24px; border-bottom: 1px solid #bfdbfe;">
                                        <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Status Change</span><br>
                                        <span style="font-size: 15px; color: #64748b; font-weight: 500; text-transform: capitalize;">{{ str_replace('_', ' ', $oldStatus) }}</span>
                                        <span style="font-size: 15px; color: #94a3b8; padding: 0 8px;">→</span>
                                        @if($newStatus === 'resolved')
                                            <span style="font-size: 15px; color: #16a34a; font-weight: 700; text-transform: capitalize;">{{ str_replace('_', ' ', $newStatus) }}</span>
                                        @elseif($newStatus === 'in_review')
                                            <span style="font-size: 15px; color: #2563eb; font-weight: 700; text-transform: capitalize;">{{ str_replace('_', ' ', $newStatus) }}</span>
                                        @elseif($newStatus === 'dismissed')
                                            <span style="font-size: 15px; color: #64748b; font-weight: 700; text-transform: capitalize;">{{ str_replace('_', ' ', $newStatus) }}</span>
                                        @else
                                            <span style="font-size: 15px; color: #0f172a; font-weight: 700; text-transform: capitalize;">{{ str_replace('_', ' ', $newStatus) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 24px; border-bottom: 1px solid #bfdbfe;">
                                        <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Vehicle</span><br>
                                        <span style="font-size: 15px; color: #0f172a; font-weight: 500;">{{ $incident->car->name ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="50%">
                                                    <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Check-in</span><br>
                                                    <span style="font-size: 15px; color: #0f172a; font-weight: 500;">{{ \Carbon\Carbon::parse($incident->reservation->start_date)->format('M d, Y') }}</span>
                                                </td>
                                                <td width="50%">
                                                    <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Check-out</span><br>
                                                    <span style="font-size: 15px; color: #0f172a; font-weight: 500;">{{ \Carbon\Carbon::parse($incident->reservation->end_date)->format('M d, Y') }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Reservation ID --}}
                            <p style="margin: 0 0 8px; font-size: 13px; color: #94a3b8;">
                                Reservation ID: <strong style="color: #64748b;">#{{ $incident->reservation_id }}</strong>
                            </p>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 13px; color: #94a3b8; line-height: 1.5;">
                                Thank you for choosing NEUVO.<br>
                                If you have any questions, contact us at support@neuvo.com
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
