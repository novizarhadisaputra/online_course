<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Certificate</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: DejaVu Sans, sans-serif;
            background: #fff;
        }

        .certificate {
            width: 100%;
            height: 100%;
            min-width: 297mm;
            min-height: 210mm;
            box-sizing: border-box;
            padding: 0;
            position: relative;
        }

        .logo {
            position: absolute;
            top: 26mm;
            left: 38mm;
            width: 80px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 70%;
            opacity: 0.03;
            transform: translate(-50%, -50%);
            z-index: 0;
        }

        .content {
            text-align: center;
            margin-top: 28mm;
            margin-bottom: 22mm;
            position: relative;
            z-index: 1;
        }

        .title {
            font-size: 28pt;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 12px;
        }

        .subtitle {
            font-size: 14pt;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .name {
            font-size: 24pt;
            font-weight: bold;
            color: #111827;
            margin: 12px 0 8px 0;
            text-transform: uppercase;
        }

        .course {
            font-size: 14pt;
            font-style: italic;
            color: #374151;
            margin-bottom: 3px;
        }

        .highlight {
            width: 50px;
            height: 4px;
            background: #eab308;
            margin: 10px auto 0;
        }

        .footer {
            text-align: center;
            font-size: 10pt;
            color: #6b7280;
            margin-top: 18mm;
        }

        .signature-table {
            width: 65%;
            margin: 0 auto;
            margin-top: 18mm;
            margin-bottom: 6mm;
            table-layout: fixed;
        }

        .signature-cell {
            text-align: center;
            vertical-align: bottom;
            width: 50%;
        }

        .signature-img {
            height: 40px;
            margin-bottom: 5px;
        }

        .signature-line {
            border-top: 1px solid #999;
            width: 120px;
            margin: 8px auto 2px auto;
        }

        @media print {
            .certificate {
                border: 4mm solid #2563eb;
            }
        }
    </style>
</head>

<body>
    <div class="certificate">
        {{-- Watermark --}}
        @if (file_exists(public_path('images/logo-big.png')))
            <img src="{{ public_path('images/logo-big.png') }}" class="watermark" alt="Watermark">
        @endif
        {{-- Logo --}}
        @if (file_exists(public_path('images/logo.png')))
            <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo">
        @endif

        <div class="content">
            <div class="title">Certificate of Completion</div>
            <div class="subtitle">This is to proudly certify that</div>
            <div class="name">{{ $name }}</div>
            <div class="subtitle">has successfully completed the course:</div>
            <div class="course">“{{ $certificate_name }}”</div>
            <div class="highlight"></div>
        </div>

        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    @if ($instructor_signature)
                        <img src="{{ $instructor_signature }}" alt="Instructor Signature" class="signature-img">
                    @endif
                    <div class="signature-line"></div>
                    <div>Instructor</div>
                </td>
                <td class="signature-cell">
                    @if ($director_signature)
                        <img src="{{ $director_signature }}" alt="Director Signature" class="signature-img">
                    @endif
                    <div class="signature-line"></div>
                    <div>Director</div>
                </td>
            </tr>
        </table>

        <div class="footer">
            Issued on {{ $date }} &nbsp;|&nbsp; Certificate ID: {{ $certificate_number }}
        </div>
    </div>
</body>

</html>
