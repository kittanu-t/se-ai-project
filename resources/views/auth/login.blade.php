<x-guest-layout>
    <div class="container-fluid min-vh-100 d-flex align-items-stretch p-0" style="background:#F4F6F8;">

        {{-- ซ้าย 40% : พื้นหลังรูปภาพ --}}
        <div class="col-12 col-md-5 d-none d-md-flex align-items-center justify-content-center text-center text-white position-relative"
             style="background:url('{{ asset('images/login-bg.jpg') }}') center/cover no-repeat;">
            <div class="position-absolute top-0 start-0 w-100 h-100"
                 style="background:rgba(0,0,0,0.45);"></div>
            <div class="position-relative px-4">
                <h1 class="fw-bold display-6 mb-3" style="color:#FFB900;">Welcome Back!</h1>
                <p class="lead fw-medium" style="color:#F4F6F8;">เข้าสู่ระบบเพื่อจองสนามกีฬาของคุณ</p>
            </div>
        </div>

        {{-- ขวา 60% : ฟอร์มเข้าสู่ระบบ --}}
        <div class="col-12 col-md-7 d-flex align-items-center justify-content-center py-5 py-md-0 bg-white">
            <div class="w-100" style="max-width:400px;">
                <div class="text-center mb-4">
                    <h2 class="fw-semibold mt-3" style="color:#212529;">เข้าสู่ระบบ</h2>
                    <p class="text-secondary mb-0">Sports Field Booking System</p>
                </div>

                <x-validation-errors class="mb-3" />

                @if (session('status'))
                    <div class="alert alert-success py-2 small mb-3">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email" class="form-control mt-1" type="email" name="email"
                                 :value="old('email')" required autofocus autocomplete="username" />
                    </div>

                    <div class="mb-3">
                        <x-label for="password" value="{{ __('Password') }}" />
                        <x-input id="password" class="form-control mt-1" type="password" name="password"
                                 required autocomplete="current-password" />
                    </div>

                    <div class="form-check mb-3">
                        <x-checkbox id="remember_me" name="remember" class="form-check-input" />
                        <label for="remember_me" class="form-check-label text-secondary ms-1">
                            {{ __('Remember me') }}
                        </label>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-4">
                        @if (Route::has('password.request'))
                            <a class="text-decoration-none small" href="{{ route('password.request') }}"
                               style="color:#6C757D;">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif

                        <x-button class="btn btn-primary border-0 px-4 py-2 fw-semibold"
                                  style="background:#E54D42;border-radius:.5rem;">
                            {{ __('Log in') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
