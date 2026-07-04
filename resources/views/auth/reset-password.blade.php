<x-guest.layout title="Reset Password - EduConnect" active="login" :hideButtons="true">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-key-fill fs-1 text-primary mb-3"></i>
                            <h4 class="fw-bold mb-1">Choose a New Password</h4>
                            <p class="text-muted small mb-0">Enter your email and new password below</p>
                        </div>

                        <form method="POST" action="{{ route('password.store') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold small text-secondary">Email Address</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $request->email) }}"
                                       class="form-control rounded-pill py-2 @error('email') is-invalid @enderror"
                                       required autofocus autocomplete="username" placeholder="you@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold small text-secondary">Password</label>
                                <input type="password" id="password" name="password"
                                       class="form-control rounded-pill py-2 @error('password') is-invalid @enderror"
                                       required autocomplete="new-password" placeholder="••••••••">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold small text-secondary">Confirm Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="form-control rounded-pill py-2 @error('password_confirmation') is-invalid @enderror"
                                       required autocomplete="new-password" placeholder="••••••••">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-arrow-repeat me-2"></i> Reset Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest.layout>
