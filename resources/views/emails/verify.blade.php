<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Verifikasi Akun - {{ env('APP_NAME', 'InterStudi') }}</title>
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

        <p style="font-weight: bold;">Hi {{ $user->name }},</p>
        <p>
            Terima kasih telah mendaftar di platform pembelajaran InterStudi.
            Untuk melanjutkan proses aktivasi akun Anda, silakan klik tombol
            verifikasi di bawah ini.
        </p>

        <a href="#" class="button" target="_blank">Verifikasi Alamat Email</a>

        <p>
            Jika Anda mengalami kesulitan saat mengklik tombol "Verifikasi Alamat
            Email", salin dan tempel URL di bawah ini ke peramban web Anda:
        </p>

        <a href="#" target="_blank">
            {{ route('api.auth.verify', ['id' => $user->id]) }}
        </a>

        <p style="margin-top: 24px;">
            Jika Anda tidak merasa melakukan pendaftaran atau tidak mengenali email
            ini, silakan abaikan atau hubungi tim dukungan kami.
        </p>

        <p style="margin-top: 36px;">Salam hangat,</p>
        <p style="color: #00897b; font-weight: 600;">Tim {{ env('APP_NAME', 'InterStudi') }}</p>

        <div class="footer">
            Delivered by {{ env('APP_NAME', 'InterStudi') }}, Jl. Bulungan I No. 6 Kebayoran Baru, Jakarta Selatan
        </div>
    </div>
</body>

</html>
