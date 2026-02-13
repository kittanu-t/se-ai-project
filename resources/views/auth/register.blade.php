<x-guest-layout>
    <div class="container-fluid min-vh-100 d-flex align-items-stretch p-0" style="background:#F4F6F8;">

        {{-- ซ้าย 40% : พื้นหลังรูปภาพ + ข้อความต้อนรับ --}}
        <div class="col-12 col-md-5 d-none d-md-flex align-items-center justify-content-center text-center text-white position-relative"
             style="background:url('{{ asset('images/login-bg.jpg') }}') center/cover no-repeat;">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background:rgba(0,0,0,0.45);"></div>
            <div class="position-relative px-4">
                <h1 class="fw-bold display-6 mb-3" style="color:#FFB900;">Create Your Account</h1>
                <p class="lead fw-medium" style="color:#F4F6F8;">สมัครสมาชิกเพื่อเริ่มต้นจองสนามกีฬา</p>
            </div>
        </div>

        {{-- ขวา 60% : ฟอร์มสมัครสมาชิก --}}
        <div class="col-12 col-md-7 d-flex align-items-center justify-content-center py-5 py-md-0 bg-white">
            <div class="w-100" style="max-width:460px;">

                {{-- โลโก้ + หัวเรื่อง --}}
                <div class="text-center mb-4">
                    <h2 class="fw-semibold mt-3" style="color:#212529;">สมัครสมาชิก</h2>
                    <p class="text-secondary mb-0">Sports Field Booking System</p>
                </div>

                <x-validation-errors class="mb-3" />

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <x-label for="name" value="{{ __('Name') }}" />
                        <x-input id="name" class="form-control mt-1" type="text" name="name"
                                 :value="old('name')" required autofocus autocomplete="name" />
                    </div>

                    <div class="mb-3">
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email" class="form-control mt-1" type="email" name="email"
                                 :value="old('email')" required autocomplete="username" />
                    </div>

                    <div class="mb-3">
                        <x-label for="phone" value="Phone" />
                        <x-input id="phone" class="form-control mt-1" type="text" name="phone"
                                 :value="old('phone')" />
                    </div>

                    <div class="mb-3">
                        <x-label for="password" value="{{ __('Password') }}" />
                        <x-input id="password" class="form-control mt-1" type="password" name="password"
                                 required autocomplete="new-password" />
                    </div>

                    <div class="mb-3">
                        <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                        <x-input id="password_confirmation" class="form-control mt-1" type="password"
                                 name="password_confirmation" required autocomplete="new-password" />
                    </div>

                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div class="mb-3">
                            <x-label for="terms">
                                <div class="form-check">
                                    <x-checkbox name="terms" id="terms" class="form-check-input" required />
                                    <div class="ms-2 form-check-label">
                                        {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                            'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-decoration-underline small" style="color:#6C757D;">'.__('Terms of Service').'</a>',
                                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-decoration-underline small" style="color:#6C757D;">'.__('Privacy Policy').'</a>',
                                        ]) !!}
                                    </div>
                                </div>
                            </x-label>
                        </div>
                    @endif

                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <a class="text-decoration-none small" href="{{ route('login') }}" style="color:#6C757D;">
                            {{ __('Already registered?') }}
                        </a>

                        <x-button class="btn btn-primary border-0 px-4 py-2 fw-semibold"
                                  style="background:#E54D42;border-radius:.5rem;">
                            {{ __('Register') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
