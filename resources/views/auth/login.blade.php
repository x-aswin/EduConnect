{{-- <x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}
<x-guest.layout title="Login - EduConnect" active="login" :hideButtons="true">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6"> {{-- Wider card --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-mortarboard-fill fs-1 text-primary mb-3"></i>
                            <h4 class="fw-bold mb-1">Welcome Back</h4>
                            <p class="text-muted small">Sign in to your account to continue</p>
                        </div>

                        <!-- Session Status -->
                        @if(session('status'))
                            <div class="alert alert-success py-2 rounded-3 small">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="row">
                                <!-- Email Address -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold small text-secondary">Email Address</label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                           class="form-control rounded-pill py-2 @error('email') is-invalid @enderror"
                                           required autofocus autocomplete="username" placeholder="you@example.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label fw-semibold small text-secondary">Password</label>
                                    <input type="password" id="password" name="password"
                                           class="form-control rounded-pill py-2 @error('password') is-invalid @enderror"
                                           required autocomplete="current-password" placeholder="••••••••">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember_me" {{ old('remember', true) ? 'checked' : '' }}>
                                    <label class="form-check-label small text-muted" for="remember_me">
                                        Remember me
                                    </label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-decoration-none small text-primary fw-semibold">
                                        Forgot password?
                                    </a>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Log in
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <span class="text-muted small">Don't have an account? </span>
                            <a href="{{ route('register') }}" class="text-decoration-none small fw-semibold text-primary">
                                Create one here
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest.layout>