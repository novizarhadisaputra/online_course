<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beautiful Email Template</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background-color: #1d72b8;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 28px;
        }

        .email-body {
            padding: 20px;
            color: #555555;
        }

        .email-body h2 {
            color: #333333;
            font-size: 24px;
            margin-top: 0;
        }

        .email-body p {
            line-height: 1.6;
            font-size: 16px;
        }

        .email-button {
            display: inline-block;
            background-color: #1d72b8;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }

        .email-footer {
            background-color: #f7f7f7;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #888888;
        }

        .email-footer a {
            color: #1d72b8;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Welcome to Our Service!</h1>
        </div>
        <div class="email-body">
            <h2>Hi {{ $user->name }},</h2>
            <p>Thank you for signing up for our service! We're excited to have you onboard. To get started, click the
                button below:</p>
            <a href="{{ route('api.auth.verify', ['id' => $user->id]) }}" class="email-button">Get Started</a>
            <p>If you have any questions, feel free to reply to this email or visit our <a href="#">help
                    center</a>.</p>
        </div>
        <div class="email-footer">
            <p>&copy; 2025 Our Company. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
