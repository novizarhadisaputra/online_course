<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>InterStudi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 32px;
            border: 1px solid #e5e7eb;
            box-sizing: border-box;
        }

        .logo {
            width: 200px;
            margin-bottom: 28px;
        }

        .button {
            padding: 12px 24px;
            background-color: #00897b;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin: 20px 0;
            font-size: 16px;
            font-weight: bold;
        }

        .footer {
            font-size: 12px;
            color: #6b7280;
            margin-top: 40px;
            text-align: left;
        }

        a {
            color: #00897b;
            text-decoration: none;
            word-break: break-word;
        }

        @media only screen and (max-width: 600px) {
            .container {
                padding: 20px;
            }

            .logo {
                width: 100px;
            }

            .button {
                width: 100%;
                font-size: 15px;
                padding: 14px;
                text-align: center;
                box-sizing: border-box;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="logo_interstudi.png" alt="InterStudi Logo" class="logo" />

        <p style="font-weight: 600;">Hi {{ $user->name }},</p>
        <p>Kami ingin menginformasikan bahwa password akun InterStudi Anda telah berhasil diperbarui.</p>
        <p>Jika Anda merasa tidak melakukan perubahan ini, segera hubungi tim dukungan kami untuk menjaga keamanan akun
            Anda.</p>

        <p>Terima kasih telah menggunakan InterStudi sebagai platform pembelajaran Anda.</p>
        <p style="margin-top: 36px;">Salam hangat</p>
        <p style="color: #00897B; font-weight: 600;">Tim Interstudi</p>

        <div class="footer">
            Delivered by InterStudi, Jl. Bulungan I No. 6 Kebayoran Baru, Jakarta Selatan
        </div>
    </div>
</body>

</html>
