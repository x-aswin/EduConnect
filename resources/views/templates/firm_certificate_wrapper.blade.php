<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificates – {{ $firm->org_name ?? 'Firm' }}</title>
    <style>
        /* 1. Register local TTF assets safely */
        @font-face {
            font-family: 'Inter';
            src: url("{{ storage_path('app/fonts/Inter-Regular.ttf') }}") format('truetype');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Inter';
            src: url("{{ storage_path('app/fonts/Inter-Bold.ttf') }}") format('truetype');
            font-weight: 700;
            font-style: normal;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        body {
            background: #ffffff;
            font-family: 'Inter', Helvetica, Arial, sans-serif;
            color: #0f172a;
            width: 297mm;
            height: 210mm;
            -webkit-font-smoothing: antialiased;
        }

        .certificate-sheet {
            width: 297mm;
            height: 210mm;
            background: #ffffff;
            position: relative;
        }

        /* Left Column Panel Strip */
        .accent-panel {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 22mm;
            background: #0f172a;
        }
        
        .accent-text-container {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .accent-text-top {
            position: absolute;
            top: 25mm;
            left: 0;
            right: 0;
            text-align: center;
            color: rgba(255, 255, 255, 0.2);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        .accent-text-bottom {
            position: absolute;
            bottom: 25mm;
            left: 0;
            right: 0;
            text-align: center;
            color: rgba(201, 168, 76, 0.4);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        /* Solid Color Accent Lines (Safer fallback for DomPDF gradients) */
        .gold-accent-top {
            position: absolute;
            top: 18mm;
            left: 22mm;
            right: 18mm;
            height: 2px;
            background: #c9a84c;
        }
        .gold-accent-bottom {
            position: absolute;
            bottom: 18mm;
            left: 22mm;
            right: 18mm;
            height: 2px;
            background: #c9a84c;
        }

        /* Unified Document Layout Canvas Grid */
        .main-layout-table {
            position: absolute;
            top: 26mm;
            left: 38mm;
            width: 241mm;
            border-collapse: collapse;
            table-layout: fixed;
        }

        /* Branding Segment Header */
        .top-section-table {
            width: 100%;
            border-collapse: collapse;
        }
        .icon-cell {
            width: 52px;
            vertical-align: middle;
        }
        .brand-name {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
        }
        .cert-label {
            font-size: 11px;
            font-weight: 700;
            color: #c9a84c;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Body Statement Formatting */
        .central-text {
            font-size: 16px;
            line-height: 2;
            color: #334155;
            text-align: justify;
            margin-top: 10mm;
            margin-bottom: 12mm;
            padding-right: 6mm;
        }
        .central-text .highlight { 
            font-weight: 700; 
            color: #0f172a; 
        }
        .central-text .course-highlight { 
            font-weight: 700; 
            color: #3b82f6; 
        }
        .central-text .gold-highlight { 
            font-weight: 700; 
            color: #b8860b; 
        }

        /* Footer Alignment Matrix */
        .footer-layout-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        /* Verification Container Box Styling */
        .verify-module-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            width: 76mm;
        }
        .qr-code-cell {
            width: 55px;
            height: 55px;
            vertical-align: middle;
        }
        .qr-code-cell img {
            width: 55px;
            height: 55px;
            display: block;
        }
        .verify-code {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
        }
        .verify-hint {
            font-size: 8px;
            color: #64748b;
            margin-top: 3px;
            line-height: 1.3;
        }

        /* Signature Component Styling */
        .signature-col {
            text-align: center;
            vertical-align: bottom;
            padding: 0 4mm;
        }
        .signature-img-wrap {
            height: 45px;
            margin-bottom: 4px;
            text-align: center;
        }
        .signature-img-wrap img {
            max-height: 45px;
            max-width: 130px;
        }
        .signature-line {
            border-top: 1.5px solid #0f172a;
            margin-bottom: 2mm;
        }
        .signature-name {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .signature-role {
            font-size: 9px;
            color: #64748b;
            font-weight: 400;
        }
        @font-face { … }
        @page { size: A4 landscape; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
    </style>
</head>
<body>
    @foreach($participantPages as $index => $page)
        @include('templates.certificate_body_pdf', [
            'student'          => $page['student'],
            'course'           => $course,
            'signatories'      => $signatories,
            'verificationCode' => $page['verificationCode'],
            'qrCode'           => $page['qrCode'],
            'verificationUrl'  => $page['verificationUrl'],
            'firmName'         => $page['firmName'] ?? null,
        ])
        @if(!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach
</body>
</html>