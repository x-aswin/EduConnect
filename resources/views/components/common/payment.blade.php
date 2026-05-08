@props(['enrollment'])

@php
    $amount = (float) ($enrollment->total_amount ?? $enrollment->course->price ?? 0);
    $amountDisplay = $amount > 0 ? '₹' . number_format($amount, 2) : 'Free';
    $courseName = $enrollment->course->title ?? 'COURSE';
    $collegeName = $enrollment->course->college?->user?->name ?? 'COLLEGE';

    $makeInitials = function ($str, $limit = 3) {
        $str = trim(preg_replace('/[^A-Za-z0-9 ]+/', '', (string) $str));
        if ($str === '') return 'NA';
        $parts = preg_split('/\s+/', $str);
        $initials = '';
        foreach ($parts as $p) {
            $initials .= strtoupper(substr($p, 0, 1));
            if (strlen($initials) >= $limit) break;
        }
        return $initials ?: strtoupper(substr($str, 0, $limit));
    };

    $courseCode = $makeInitials($courseName, 3);
    $collegeCode = $makeInitials($collegeName, 3);
    $enrollmentCode = $enrollment->id ? '#ENR-' . $courseCode . '-' . $collegeCode . '-' . now()->format('ymd') . '-' . $enrollment->id : '#ENR-NA';
    $qrAmount = number_format($amount, 2, '.', '');
    $qrData = rawurlencode("upi://pay?pa=educonnect1@icici&pn=EduConnect&am={$qrAmount}&cu=INR");
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={$qrData}";
@endphp

<!-- ========== MAIN CONTENT ========== -->
<main class="container py-4">
    <div class="mb-4">
        <h1 class="fw-bold mb-1"><i class="bi bi-credit-card me-2 text-primary"></i>Complete Payment</h1>
        <p class="text-secondary mb-0">Secure UPI payment for your course enrollment.</p>
    </div>

    <div class="payment-card">
        <div class="row align-items-center g-4">
            <!-- Left: QR Code -->
            <div class="col-md-5 text-center">
                <div class="qr-image d-inline-block">
                    <img src="{{ $qrUrl }}" 
                         alt="Payment QR Code" 
                         class="img-fluid rounded-3" 
                         style="width: 220px; height: 220px;">
                </div>
                <p class="small text-muted mt-2 mb-0">Scan this QR using any UPI app</p>
            </div>

            <!-- Right: Payment details & form -->
            <div class="col-md-7">
                <div class="mb-4">
                    <h5 class="fw-bold mb-1">{{ $enrollment->course->title ?? 'Course Title' }}</h5>
                    <small class="text-secondary"><i class="bi bi-building me-1"></i> {{ $enrollment->course->college?->institution_name ?? 'College' }}</small>
                    <div class="mt-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary">Student Only</span>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <span class="text-secondary small">Course Price</span>
                        <h4 class="fw-bold text-primary mb-0">{{ $amountDisplay }}</h4>
                    </div>
                    <div class="col-6">
                        <span class="text-secondary small">Enrollment ID</span>
                        <h5 class="fw-bold mb-0">{{ $enrollmentCode }}</h5>
                    </div>
                </div>

                <hr>

                <div class="mt-3">
                    <label class="form-label fw-semibold">Enter your UPI ID (or leave blank)</label>
                    <input type="text" class="form-control rounded-pill py-3" placeholder="yourname@upi" id="upiInput">
                    <div class="form-text">We will not charge you – this is a demo payment.</div>
                </div>
                <form id="payForm" method="POST" action="{{ route('student.enrollment.pay', $enrollment) }}">
                    @csrf
                    <input type="hidden" name="amount" value="{{ $amount }}">
                    <div class="mt-3">
                        <button class="btn btn-primary btn-pay mt-3 w-100" id="payButton" type="submit">
                            <i class="bi bi-shield-check me-2"></i> Pay {{ $amountDisplay }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body text-center p-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Payment Successful!</h4>
                    <p class="text-secondary">Your enrollment has been confirmed. You now have full access to the course.</p>
                    <a href="#" class="btn btn-primary rounded-pill px-4 mt-3" id="goToEnrollments">
                        <i class="bi bi-journal-check me-2"></i> Go to My Enrollments
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7ff 0%, #eef1fa 100%);
            padding-top: 80px;
            min-height: 100vh;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
            font-size: 1.6rem;
            background: linear-gradient(135deg, #1e3ce0, #4f6ef6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        } */
        .payment-card {
            background: white;
            border-radius: 2rem;
            padding: 2rem;
            box-shadow: 0 12px 32px rgba(0,0,0,0.05);
        }
        .qr-image {
            border: 2px dashed #d1d5db;
            border-radius: 1.5rem;
            padding: 1rem;
            background: #f9fafb;
        }
        .btn-pay {
            background: #2563eb;
            border: none;
            font-weight: 600;
            border-radius: 50px;
            padding: 0.8rem 2rem;
            box-shadow: 0 8px 18px rgba(37,99,235,0.3);
            transition: all 0.2s;
        }
        .btn-pay:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var payForm = document.getElementById('payForm');
            var payButton = document.getElementById('payButton');
            var successModalEl = document.getElementById('successModal');
            var goToEnrollments = document.getElementById('goToEnrollments');

            if (payForm && payButton) {
                payForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    payButton.disabled = true;
                    payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';

                    // Simulate server payment processing (replace with real fetch to server)
                    setTimeout(function () {
                        if (successModalEl && typeof bootstrap !== 'undefined') {
                            var modal = new bootstrap.Modal(successModalEl);
                            modal.show();
                        } else if (successModalEl) {
                            successModalEl.style.display = 'block';
                        }
                        payButton.disabled = false;
                        payButton.innerHTML = '<i class="bi bi-shield-check me-2"></i> Pay {{ $amountDisplay }}';
                    }, 900);
                });
            }

            if (goToEnrollments) {
                goToEnrollments.addEventListener('click', function (e) {
                    e.preventDefault();
                    window.location.href = '{{ route('student.my.enrollments') }}';
                });
            }
        });
    </script>
@endpush
