<x-guest.layout title="Certificate Verification - EduConnect" active="verify">
    @push('styles')
    <style>
        .verify-card {
            background: white;
            border-radius: 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        }
        .verify-badge {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        .verify-badge.valid {
            background: #dcfce7;
        }
        .verify-badge.invalid {
            background: #fee2e2;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .detail-value {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            text-align: right;
        }
        .detail-value.highlight {
            color: #2563eb;
        }
        .status-banner {
            border-radius: 1rem;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .status-banner.valid {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }
        .status-banner.invalid {
            background: #fef2f2;
            border: 1px solid #fecaca;
        }
        .issuer-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 50px;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #0f172a;
        }
    </style>
    @endpush

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                {{-- Verification Status Header --}}
                <div class="text-center mb-4">
                    @if($valid)
                        <div class="verify-badge valid mb-3">
                            <i class="bi bi-check-circle-fill fs-1" style="color: #16a34a;"></i>
                        </div>
                        <h2 class="fw-bold text-success mb-1">Certificate Verified</h2>
                        <p class="text-muted">This is an authentic EduConnect certificate</p>
                    @else
                        <div class="verify-badge invalid mb-3">
                            <i class="bi bi-x-circle-fill fs-1" style="color: #dc2626;"></i>
                        </div>
                        <h2 class="fw-bold text-danger mb-1">Verification Failed</h2>
                        <p class="text-muted">{{ $errorMessage ?? 'This certificate could not be verified' }}</p>
                    @endif
                </div>

                {{-- Certificate Details Card --}}
                @if($valid && isset($certificate))
                <div class="verify-card p-4 p-lg-5">
                    
                    {{-- Status Banner --}}
                    <div class="status-banner valid mb-4">
                        <i class="bi bi-shield-check fs-4" style="color: #16a34a;"></i>
                        <div>
                            <strong style="color: #166534;">Authentic Certificate</strong>
                            <p class="mb-0 small text-muted">Issued through the EduConnect Unified Platform</p>
                        </div>
                    </div>

                    {{-- Certificate Details --}}
                    <div class="detail-row">
                        <span class="detail-label">Student Name</span>
                        <span class="detail-value highlight">{{ $certificate['student_name'] }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Course</span>
                        <span class="detail-value">{{ $certificate['course_title'] }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Institution</span>
                        <span class="detail-value">{{ $certificate['college_name'] }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Duration</span>
                        <span class="detail-value">
                            {{ $certificate['start_date'] }} – {{ $certificate['end_date'] }}
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Certificate ID</span>
                        <span class="detail-value" style="font-family: 'Courier New', monospace; font-size: 0.9rem;">
                            {{ $certificate['code'] }}
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Issued On</span>
                        <span class="detail-value">{{ $certificate['issued_date'] ?? $certificate['end_date'] }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status</span>
                        <span class="detail-value">
                            <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                                <i class="bi bi-check-circle-fill me-1"></i> Valid
                            </span>
                        </span>
                    </div>

                    {{-- Issuer Info --}}
                    <div class="text-center mt-4 pt-3 border-top">
                        <div class="issuer-badge mx-auto d-inline-flex">
                            <i class="bi bi-mortarboard-fill text-primary"></i>
                            <span>EduConnect Platform</span>
                        </div>
                        <p class="small text-muted mt-2 mb-0">
                            This certificate was digitally issued and verified through EduConnect.<br>
                            For questions, contact the issuing institution directly.
                        </p>
                    </div>
                </div>
                @endif

                {{-- Invalid certificate message --}}
                @if(!$valid)
                <div class="verify-card p-4 p-lg-5 text-center">
                    <div class="status-banner invalid mb-4">
                        <i class="bi bi-exclamation-triangle-fill fs-4" style="color: #dc2626;"></i>
                        <div>
                            <strong style="color: #991b1b;">Unable to Verify</strong>
                            <p class="mb-0 small text-muted">The verification code is invalid or the certificate has been revoked</p>
                        </div>
                    </div>
                    
                    <p class="text-muted mb-4">
                        {{ $errorMessage ?? 'The code you provided does not match any certificate in our system. Please double-check the URL or contact the issuing institution.' }}
                    </p>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="bi bi-house me-1"></i> Home
                        </a>
                        <a href="{{ route('explore') }}" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-compass me-1"></i> Explore Courses
                        </a>
                    </div>
                </div>
                @endif

                {{-- Footer note --}}
                <div class="text-center mt-4">
                    <p class="small text-muted mb-0">
                        <i class="bi bi-lock-fill me-1"></i> 
                        Verification powered by EduConnect Secure Ledger
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest.layout>