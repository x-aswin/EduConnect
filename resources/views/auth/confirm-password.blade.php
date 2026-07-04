<x-guest.layout title="Confirm Password - EduConnect" active="login" :hideButtons="true">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock-fill fs-1 text-primary mb-3"></i>
                            <h4 class="fw-bold mb-1">Confirm Your Password</h4>
                            <p class="text-muted small mb-0">Enter your password to continue</p>
                        </div>

                        <p class="text-muted small mb-4">
                            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                        </p>

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold small text-secondary">Password</label>
                                <input type="password" id="password" name="password"
                                       class="form-control rounded-pill py-2 @error('password') is-invalid @enderror"
                                       required autocomplete="current-password" placeholder="••••••••">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-check2-circle me-2"></i> Confirm
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest.layout>
