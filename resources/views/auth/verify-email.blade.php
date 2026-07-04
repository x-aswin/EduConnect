<x-guest.layout title="Verify Email - EduConnect" active="login" :hideButtons="true">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-envelope-check-fill fs-1 text-primary mb-3"></i>
                            <h4 class="fw-bold mb-1">Verify Your Email</h4>
                            <p class="text-muted small mb-0">Click the link we sent before continuing</p>
                        </div>

                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success py-2 rounded-3 small">
                                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                            </div>
                        @endif

                        <p class="text-muted small mb-4">
                            {{ __('Thanks for signing up! Before getting started, verify your email address using the link we sent. If you did not receive it, we can send another one.') }}
                        </p>

                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-between align-items-sm-center">
                            <form method="POST" action="{{ route('verification.send') }}" class="w-100">
                                @csrf

                                <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold shadow-sm">
                                    <i class="bi bi-arrow-repeat me-2"></i> Resend Verification Email
                                </button>
                            </form>

                            <form method="POST" action="{{ route('logout') }}" class="w-100 text-center text-sm-start">
                                @csrf

                                <button type="submit" class="btn btn-link text-decoration-none small fw-semibold text-primary p-0">
                                    Log out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest.layout>
