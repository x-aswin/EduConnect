<x-guest.layout title="Register Institution - EduConnect" active="register" :hideButtons="true">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-building-fill fs-1 text-primary mb-3"></i>
                            <h4 class="fw-bold mb-1">Partner as a College</h4>
                            <p class="text-muted small">List your courses and reach professional learners</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Hidden role field -->
                            <input type="hidden" name="role" value="college">

                            <!-- College / Institution Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold small text-secondary">College / Institution Acronym</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}"
                                       class="form-control rounded-pill py-2 @error('name') is-invalid @enderror"
                                       required autofocus autocomplete="organization" placeholder="e.g., DiST, CUSAT, IITM">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold small text-secondary">Official Email Address</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                       class="form-control rounded-pill py-2 @error('email') is-invalid @enderror"
                                       required autocomplete="username" placeholder="info@cochinuniv.ac.in">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label fw-semibold small text-secondary">Password</label>
                                    <input type="password" id="password" name="password"
                                           class="form-control rounded-pill py-2 @error('password') is-invalid @enderror"
                                           required autocomplete="new-password" placeholder="••••••••">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label fw-semibold small text-secondary">Confirm Password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           class="form-control rounded-pill py-2 @error('password_confirmation') is-invalid @enderror"
                                           required autocomplete="new-password" placeholder="••••••••">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-building-add me-2"></i> Register Institution
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <span class="text-muted small">Already a partner? </span>
                            <a href="{{ route('login') }}" class="text-decoration-none small fw-semibold text-primary">
                                Sign in
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest.layout>