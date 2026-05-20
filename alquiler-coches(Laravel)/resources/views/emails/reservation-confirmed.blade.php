<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Confirmed</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 32px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: 0.5px;">
                                NEUVO
                            </h1>
                            <p style="margin: 8px 0 0; color: #94a3b8; font-size: 14px;">
                                Reservation Confirmation
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 40px;">

                            {{-- Greeting --}}
                            <p style="margin: 0 0 24px; font-size: 16px; color: #334155;">
                                Hello <strong>{{ $reservation->customer_name }}</strong>,
                            </p>
                            <p style="margin: 0 0 32px; font-size: 15px; color: #475569; line-height: 1.6;">
                                Your reservation has been confirmed. Here are the details:
                            </p>

                            {{-- Details card --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 32px;">
                                <tr>
                                    <td style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0;">
                                        <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Vehicle</span><br>
                                        <span style="font-size: 16px; color: #0f172a; font-weight: 600;">{{ $carName }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="50%">
                                                    <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Check-in</span><br>
                                                    <span style="font-size: 15px; color: #0f172a; font-weight: 500;">{{ \Carbon\Carbon::parse($reservation->start_date)->format('M d, Y') }}</span>
                                                </td>
                                                <td width="50%">
                                                    <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Check-out</span><br>
                                                    <span style="font-size: 15px; color: #0f172a; font-weight: 500;">{{ \Carbon\Carbon::parse($reservation->end_date)->format('M d, Y') }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0;">
                                        <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Duration</span><br>
                                        <span style="font-size: 15px; color: #0f172a; font-weight: 500;">{{ $days }} {{ $days === 1 ? 'day' : 'days' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Total</span><br>
                                        <span style="font-size: 22px; color: #0f172a; font-weight: 700;">€{{ number_format($reservation->total_price, 2) }}</span>
                                    </td>
                                </tr>
                            </table>

                            {{-- Reservation ID --}}
                            <p style="margin: 0 0 8px; font-size: 13px; color: #94a3b8;">
                                Reservation ID: <strong style="color: #64748b;">#{{ $reservation->id }}</strong>
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
