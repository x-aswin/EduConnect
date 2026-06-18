<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion - EduConnect</title>
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
    </style>
</head>
<body>

    <div class="certificate-sheet">
        
        <div class="accent-panel">
            <div class="accent-text-container">
                <div class="accent-text-top">E<br>D<br>U<br>C<br>O<br>N<br>N<br>E<br>C<br>T</div>
                <div class="accent-text-bottom">V<br>E<br>R<br>I<br>F<br>I<br>E<br>D</div>
            </div>
        </div>

        <div class="gold-accent-top"></div>
        <div class="gold-accent-bottom"></div>

        <table class="main-layout-table">
            <tr>
                <td>
                    
                    <table class="top-section-table">
                        <tr>
                            <td class="icon-cell">
                                <!-- <svg width="48" height="48" viewBox="0 0 100 100">
                                    <polygon points="50,3 93,25 93,75 50,97 7,75 7,25" fill="#0f172a"/>
                                    <path d="M50,28 L85,42 L50,56 L15,42 Z" fill="#c9a84c"/>
                                    <path d="M25,47.5 L25,68 C25,72 35,76 50,76 C65,76 75,72 75,68 L75,47.5 L50,59.5 Z" fill="#c9a84c"/>
                                    <path d="M80,44.5 L80,68 L83,68 L83,46 Z" fill="#c9a84c"/>
                                </svg> -->
                                <img src="{{ public_path('storage/images/logo.png') }}" alt="EduConnect Logo" style="height: 80px; width: auto; display: block;">
                            </td>
                            <td style="vertical-align: middle; padding-left: 12px;">
                                <div class="brand-name">EduConnect</div>
                                <div class="cert-label">Certificate of Participation</div>
                            </td>
                        </tr>
                    </table>

                    <div class="central-text">
                        @php
                            $genderLower = strtolower($gender ?? '');
                            $prefix = '';
                            if ($genderLower === 'male') $prefix = 'Mr. ';
                            elseif ($genderLower === 'female') $prefix = 'Ms. ';
                            
                            $pronoun = ($genderLower === 'male') ? 'his' : (($genderLower === 'female') ? 'her' : 'their');
                        @endphp
                        
                        This is to certify that 
                        <span class="highlight">{{ $prefix }}{{ $student->name ?? 'Student Name' }}</span> 
                        has successfully fulfilled {{ $pronoun }} academic requirements and completed the specialized training program in 
                        <span class="course-highlight">"{{ $course->title ?? 'Course Name' }}"</span>, 
                        curated and provided in partnership with 
                        <span class="highlight">{{ $course->college->institution_name ?? 'Partner Institution' }}</span>. 
                        The program was dynamically conducted from 
                        <span class="gold-highlight">{{ isset($course->start_date) ? \Carbon\Carbon::parse($course->start_date)->format('F d, Y') : 'Start Date' }}</span> through 
                        <span class="gold-highlight">{{ isset($course->end_date) ? \Carbon\Carbon::parse($course->end_date)->format('F d, Y') : 'End Date' }}</span> 
                        via the EduConnect Unified Platform verification ledger.
                    </div>

                    @php
                        $sigList = $signatories ?? [];
                        $sigCount = count($sigList);
                    @endphp
                    <table class="footer-layout-table">
                        <tr>
                            <td style="width: 42%; vertical-align: bottom; text-align: left;">
                                <a href="{{ $verificationUrl }}" style="text-decoration: none;">
                                <div class="verify-module-box">
                                    <table style="width:100%; border-collapse:collapse;">
                                        <tr>
                                            <td class="qr-code-cell">
                                                @if(!empty($qrCode))
                                                    <img src="{{ $qrCode }}" alt="QR Verification Ledger">
                                                @else
                                                    <div style="width:55px; height:55px; background:#0f172a; border-radius:4px;"></div>
                                                @endif
                                            </td>
                                            <td style="vertical-align: middle; padding-left: 10px;">
                                                <div class="verify-code">{{ $verificationCode ?? 'EDU-2026-X78K' }}</div>
                                                <div class="verify-hint">Tap or Scan to verify authenticity on the EduConnect ledger.</div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                </a>
                            </td>
                            
                            <td style="width: 58%; vertical-align: bottom;">
                                <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                                    <tr>
                                        @if($sigCount === 0)
                                            <td class="signature-col" style="width: 50%;">
                                                <div class="signature-img-wrap"></div>
                                                <div class="signature-line"></div>
                                                <div class="signature-name">Course Coordinator</div>
                                                <div class="signature-role">EduConnect Representative</div>
                                            </td>
                                            <td class="signature-col" style="width: 50%;">
                                                <div class="signature-img-wrap"></div>
                                                <div class="signature-line"></div>
                                                <div class="signature-name">Institution Head</div>
                                                <div class="signature-role">College Authority</div>
                                            </td>
                                        @else
                                            @foreach($sigList as $sig)
                                                <td class="signature-col" style="width: {{ 100 / $sigCount }}%;">
                                                    <div class="signature-img-wrap">
                                                        @if(!empty($sig['signature_image']))
                                                            <img src="{{ public_path('storage/' . $sig['signature_image']) }}" alt="Signature Box Image">
                                                        @endif
                                                    </div>
                                                    <div class="signature-line"></div>
                                                    <div class="signature-name">{{ $sig['name'] }}</div>
                                                    <div class="signature-role">{{ $sig['designation'] ?? 'Authority Reference' }}</div>
                                                </td>
                                            @endforeach
                                        @endif
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>

    </div>

</body>
</html>