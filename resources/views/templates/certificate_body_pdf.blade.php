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

                    @if(isset($firmName))
                            This is to certify that
                            <span class="highlight">{{ $student->name }}</span>,
                            a participant from <span class="highlight">{{ $firmName }}</span>,
                            has successfully completed the specialised corporate training program in
                            <span class="course-highlight">"{{ $course->title ?? 'Course Name' }}"</span>,
                            curated and provided by
                            <span class="highlight">{{ $course->college->institution_name ?? 'Partner Institution' }}</span>.
                            The program was conducted from
                            <span class="gold-highlight">{{ isset($course->start_date) ? \Carbon\Carbon::parse($course->start_date)->format('F d, Y') : 'Start Date' }}</span> through
                            <span class="gold-highlight">{{ isset($course->end_date) ? \Carbon\Carbon::parse($course->end_date)->format('F d, Y') : 'End Date' }}</span>
                            offline, at the designated venue.
                        @else
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
                        curated and provided by 
                        <span class="highlight">{{ $course->college->institution_name ?? 'Partner Institution' }}</span>. 
                        The program was conducted from 
                        <span class="gold-highlight">{{ isset($course->start_date) ? \Carbon\Carbon::parse($course->start_date)->format('F d, Y') : 'Start Date' }}</span> through 
                        <span class="gold-highlight">{{ isset($course->end_date) ? \Carbon\Carbon::parse($course->end_date)->format('F d, Y') : 'End Date' }}</span> 
                        offline, at the designated campus venue.
                        @endif
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