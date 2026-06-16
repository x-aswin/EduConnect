<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion</title>
    <style>
        /* 1. Dedicated DomPDF Font Face Registrations */
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
        @font-face {
            font-family: 'Space Grotesk';
            src: url("{{ storage_path('app/fonts/SpaceGrotesk-Bold.ttf') }}") format('truetype');
            font-weight: 700;
            font-style: normal;
        }

        /* 2. Document Setup & Color Variables */
        :root {
            --navy: #0f172a;
            --gold: #c9a84c;
            --white: #ffffff;
            --slate: #64748b;
            --blue-accent: #3b82f6;
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
            font-family: 'Inter', sans-serif;
            width: 297mm;
            height: 210mm;
        }

        .certificate-sheet {
            width: 297mm;
            height: 210mm;
            background: var(--white);
            position: relative;
            overflow: hidden;
        }

        /* Left Accent Panel (Using DomPDF friendly vertical character stacking) */
        .accent-panel {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 22mm;
            background: #0f172a;
            padding-top: 22mm;
            text-align: center;
        }

        /* Border Fallbacks (Gradients warp in DomPDF print context) */
        .gold-accent-top {
            position: absolute;
            top: 18mm;
            left: 22mm;
            right: 18mm;
            height: 1.5px;
            background: var(--gold);
        }
        .gold-accent-bottom {
            position: absolute;
            bottom: 18mm;
            left: 22mm;
            right: 18mm;
            height: 1.5px;
            background: var(--gold);
        }

        /* Primary Content Canvas Mapping via Native Tables */
        .content-area-table {
            position: absolute;
            top: 24mm;
            left: 38mm;
            right: 18mm;
            bottom: 42mm; /* Raised slightly to explicitly protect the absolute footer spacing */
            width: 241mm;
            height: 144mm;
            border-collapse: collapse;
            z-index: 1;
        }
        .content-inner-cell {
            vertical-align: middle;
        }

        /* Top Header Component */
        .top-section-table {
            width: 100%;
            margin-bottom: 6mm;
        }
        .icon-cell {
            width: 56px;
            vertical-align: middle;
        }
        .brand-name {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: -0.02em;
        }
        .cert-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--gold);
            letter-spacing: 0.25em;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Centralized Certificate Body Statement */
        .central-text {
            font-family: 'Inter', sans-serif;
            font-size: 15.5px;
            font-weight: 400;
            line-height: 2;
            color: #334155;
            text-align: justify;
            padding-right: 6mm;
        }
        .central-text .highlight { font-weight: 700; color: var(--navy); }
        .central-text .course-highlight { font-weight: 700; color: var(--blue-accent); }
        /* Removed white-space: nowrap to prevent text alignment rendering gaps */
        .central-text .gold-highlight { font-weight: 700; color: #b8860b; }

        /* Absolute Bottom Anchor for Signatures & Verification blocks relative to the sheet */
        .bottom-layout-table {
            position: absolute;
            bottom: 24mm; /* Aligns perfectly above the global bottom gold accent line */
            left: 38mm;
            right: 18mm;
            width: 241mm;
            border-collapse: collapse;
            z-index: 2;
        }
        .signature-col {
            text-align: center;
            vertical-align: bottom;
            padding: 0 4mm;
        }
        .signature-img-wrap {
            height: 45px;
            margin-bottom: 2px;
            text-align: center;
        }
        .signature-img-wrap img {
            max-height: 100%;
            max-width: 140px;
        }
        .signature-line {
            border-top: 1.5px solid var(--navy);
            margin-top: 2px;
            margin-bottom: 2mm;
        }
        .signature-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--navy);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .signature-role {
            font-size: 9px;
            color: var(--slate);
            font-weight: 500;
        }

        /* Secure Verification Unit */
        .verify-module-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 4mm;
            width: 75mm;
            text-align: left;
        }
        .qr-code-cell {
            width: 18mm;
            height: 18mm;
            padding-right: 3mm;
        }
        .qr-code-cell img {
            width: 100%;
            height: 100%;
        }
        .verify-code {
            font-family: 'Space Grotesk', monospace;
            font-size: 11px;
            font-weight: 600;
            color: var(--navy);
            letter-spacing: 0.05em;
        }
        .verify-hint {
            font-size: 8px;
            color: var(--slate);
            margin-top: 1px;
            line-height: 1.2;
        }
    </style>
</head>
<body>

    <div class="certificate-sheet">
        
        {{-- Side Accent Badge Stack --}}
        <div class="accent-panel">
            <div style="color: rgba(255,255,255,0.18); font-family: 'Space Grotesk', sans-serif; font-size: 9px; font-weight: 600; text-transform: uppercase; line-height: 1.6; letter-spacing: 2px;">
                E<br>D<br>U<br>C<br>O<br>N<br>N<br>E<br>C<br>T
            </div>
            <div style="height: 25mm; width: 1px; border-left: 1px dashed rgba(255,255,255,0.1); margin: 10mm auto;"></div>
            <div style="color: var(--gold); opacity: 0.4; font-family: 'Space Grotesk', sans-serif; font-size: 9px; font-weight: 600; text-transform: uppercase; line-height: 1.6; letter-spacing: 2px;">
                V<br>E<br>R<br>I<br>F<br>I<br>E<br>D
            </div>
        </div>

        <div class="gold-accent-top"></div>
        <div class="gold-accent-bottom"></div>

        {{-- Background Vector Cap Watermark (Baked-in faint hex color fallback to avoid CSS opacity dependency) --}}
        <div class="watermark" style="position: absolute; top: 22%; left: 38%; width: 320px; z-index: 0;">
            <svg viewBox="0 0 16 16" fill="#f4f6f8" width="100%" height="100%">
                <path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 0 .906l7.5 3.5a.5.5 0 0 0 .422 0l7.5-3.5a.5.5 0 0 0 0-.906z"/>
                <path d="M4.176 9.032a.5.5 0 0 0-.656.327 7 7 0 0 0-.17 2.029c.115 1.082.722 2.018 1.436 2.518C5.504 14.407 6.741 15 8 15s2.496-.593 3.214-1.094c.714-.5 1.32-1.436 1.435-2.518a7 7 0 0 0-.172-2.03.5.5 0 0 0-.656-.327.5.5 0 0 0-.328.656 6 6 0 0 1 .142 1.728c-.087.815-.526 1.503-1.055 1.875C9.972 13.78 8.974 14 8 14s-1.972-.22-2.58-.646c-.53-.372-.968-1.06-1.055-1.875a6 6 0 0 1 .142-1.728.5.5 0 0 0-.33-.656z"/>
            </svg>
        </div>

        {{-- Main Content Canvas (Converted from Div to Semantic Table Structure) --}}
        <table class="content-area-table">
            <tr>
                <td class="content-inner-cell">
                    
                    {{-- Primary Identity branding --}}
                    <table class="top-section-table">
                        <tr>
                            <td class="icon-cell">
                                <svg width="52" height="52" viewBox="0 0 100 100" style="vertical-align: middle;">
                                    <polygon points="50,3 93,25 93,75 50,97 7,75 7,25" fill="#0f172a"/>
                                    <path d="M50,28 L85,42 L50,56 L15,42 Z" fill="#c9a84c"/>
                                    <path d="M25,47.5 L25,68 C25,72 35,76 50,76 C65,76 75,72 75,68 L75,47.5 L50,59.5 Z" fill="#c9a84c"/>
                                    <path d="M80,44.5 L80,68 L83,68 L83,46 Z" fill="#c9a84c"/>
                                </svg>
                            </td>
                            <td style="vertical-align: middle; padding-left: 10px;">
                                <div class="brand-name">EduConnect</div>
                                <div class="cert-label">Certificate of Participation</div>
                            </td>
                        </tr>
                    </table>

                    {{-- Main Token Statement Block --}}
                    <div class="central-text">
                        @php
                            $genderLower = strtolower($student->gender ?? '');
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

                </td>
            </tr>
        </table>

        {{-- Dynamic Signatory & Verification Footer Alignment (Anchored directly to sheet root context) --}}
        @php
            $sigList = $signatories ?? [];
            $sigCount = count($sigList);
        @endphp
        <table class="bottom-layout-table">
            <tr>
                <td style="width: 42%; vertical-align: bottom; text-align: left;">
                    <div class="verify-module-box">
                        <table style="width:100%; border-collapse:collapse;">
                            <tr>
                                <td class="qr-code-cell">
                                    @if(!empty($qrCode))
                                        <img src="{{ $qrCode }}" alt="Verification QR">
                                    @else
                                        {{-- Vector barcode emulation replacing unstable CSS repeating gradients --}}
                                        <svg width="100%" height="100%" viewBox="0 0 50 50" preserveAspectRatio="none">
                                            <rect x="0" y="0" width="3" height="50" fill="#0f172a"/>
                                            <rect x="5" y="0" width="1" height="50" fill="#0f172a"/>
                                            <rect x="8" y="0" width="4" height="50" fill="#0f172a"/>
                                            <rect x="14" y="0" width="2" height="50" fill="#0f172a"/>
                                            <rect x="18" y="0" width="1" height="50" fill="#0f172a"/>
                                            <rect x="21" y="0" width="5" height="50" fill="#0f172a"/>
                                            <rect x="28" y="0" width="2" height="50" fill="#0f172a"/>
                                            <rect x="32" y="0" width="3" height="50" fill="#0f172a"/>
                                            <rect x="37" y="0" width="1" height="50" fill="#0f172a"/>
                                            <rect x="40" y="0" width="4" height="50" fill="#0f172a"/>
                                            <rect x="46" y="0" width="2" height="50" fill="#0f172a"/>
                                        </svg>
                                    @endif
                                </td>
                                <td style="vertical-align: middle;">
                                    <div class="verify-code">{{ $verificationCode ?? 'EDU-2026-X78K' }}</div>
                                    <div class="verify-hint">Scan to verify authenticity on the EduConnect ledger.</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td style="width: 58%; vertical-align: bottom;">
                    <table style="width: 100%; border-collapse: collapse;">
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
                            @elseif($sigCount === 1)
                                <td style="width: 40%;"></td>
                                <td class="signature-col" style="width: 60%;">
                                    <div class="signature-img-wrap">
                                        @if(!empty($sigList[0]['signature_image']))
                                            <img src="{{ public_path('storage/' . $sigList[0]['signature_image']) }}" alt="Signature">
                                        @endif
                                    </div>
                                    <div class="signature-line"></div>
                                    <div class="signature-name">{{ $sigList[0]['name'] }}</div>
                                    <div class="signature-role">{{ $sigList[0]['designation'] ?? 'Authority' }}</div>
                                </td>
                            @else
                                @foreach($sigList as $sig)
                                    <td class="signature-col" style="width: {{ 100 / $sigCount }}%;">
                                        <div class="signature-img-wrap">
                                            @if(!empty($sig['signature_image']))
                                                <img src="{{ public_path('storage/' . $sig['signature_image']) }}" alt="Signature">
                                            @endif
                                        </div>
                                        <div class="signature-line"></div>
                                        <div class="signature-name">{{ $sig['name'] }}</div>
                                        <div class="signature-role">{{ $sig['designation'] ?? 'Authority' }}</div>
                                    </td>
                                @endforeach
                            @endif
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </div>

</body>
</html>