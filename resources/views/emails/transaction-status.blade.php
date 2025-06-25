<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Transaction Status Update</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Mobile-first styles */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                padding: 15px !important;
            }

            .button {
                display: block !important;
                width: 100% !important;
                text-align: center !important;
            }

            .order-summary th,
            .order-summary td {
                display: block;
                width: 100% !important;
                text-align: left !important;
            }

            .order-summary {
                border: none !important;
            }
        }
    </style>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: 'Segoe UI', sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table class="email-container" width="600" cellpadding="0" cellspacing="0"
                    style="max-width: 600px; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
                    <tr style="background-color: #3182ce; color: white;">
                        <td style="padding: 20px; text-align: center;">
                            <h2 style="margin: 0;">🛒 Transaction Status Update</h2>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px;">
                            <p style="margin: 0;">Hello <strong>{{ $transaction->user->name }}</strong>,</p>
                            <p>Your transaction <strong>#{{ $transaction->code }}</strong> status has been updated to:
                            </p>

                            <div
                                style="background-color: #e2e8f0; padding: 15px; text-align: center; border-radius: 6px; margin: 20px 0;">
                                <span
                                    style="font-size: 20px; font-weight: bold; color: #2d3748;">{{ strtoupper($transaction->status) }}</span>
                            </div>

                            @if (isset($transaction->details) && count($transaction->details))
                                <h4 style="margin-top: 30px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">
                                    Transaction Summary</h4>
                                <table class="order-summary" width="100%" cellpadding="10" cellspacing="0"
                                    style="border-collapse: collapse;">
                                    <thead style="background-color: #f7fafc;">
                                        <tr>
                                            <th align="left">Item</th>
                                            <th align="center">Qty</th>
                                            <th align="right">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transaction->details as $item)
                                            <tr style="border-bottom: 1px solid #edf2f7;">
                                                <td>{{ $model->name }}</td>
                                                <td align="center">{{ $item->qty }}</td>
                                                <td align="right">${{ number_format($item->price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            <div style="text-align: center; margin-top: 30px;">
                                <a href="{{ env('APP_URL_WEBSITE') . "/transaction-history/$transaction->id" }}" class="button"
                                    style="background-color: #3182ce; color: white; padding: 14px 28px; text-decoration: none; border-radius: 6px; display: inline-block;">
                                    View Transaction Details
                                </a>
                            </div>

                            <p style="font-size: 14px; color: #718096; margin-top: 30px;">
                                Have any questions? <a href="{{ env('APP_URL_WEBSITE') . '/contactus' }}" style="color: #3182ce;">Contact our
                                    support</a>.
                            </p>

                            <p style="margin-top: 10px;">Thanks,<br><strong>{{ config('app.name') }}</strong></p>
                        </td>
                    </tr>

                    <tr>
                        <td
                            style="background-color: #edf2f7; text-align: center; padding: 15px; font-size: 12px; color: #718096;">
                            © {{ now()->year }} {{ config('app.name') }}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>
