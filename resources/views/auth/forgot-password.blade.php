<x-guest.layout title="Forgot Password - EduConnect" active="login" :hideButtons="true">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock-fill fs-1 text-primary mb-3"></i>
                            <h4 class="fw-bold mb-1">Reset Your Password</h4>
                            <p class="text-muted small mb-0">Enter your email and we will send you a reset link</p>
                        </div>

                        @if(session('status'))
                            <div class="alert alert-success py-2 rounded-3 small">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold small text-secondary">Email Address</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                       class="form-control rounded-pill py-2 @error('email') is-invalid @enderror"
                                       required autofocus autocomplete="username" placeholder="you@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-envelope-paper me-2"></i> Email Password Reset Link
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <a href="{{ route('login') }}" class="text-decoration-none small fw-semibold text-primary">
                                Back to login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest.layout>
