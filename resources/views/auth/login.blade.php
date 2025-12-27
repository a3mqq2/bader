@extends('layouts.auth')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="card my-5">
    <div class="card-body">
        <div class="text-center">
            <a href="{{ route('login') }}">
                <img src="{{ asset('logo-primary.png') }}"  width="350"  alt="مؤسسة البدر التعليمية" />
            </a>
        </div>
        <h4 class="text-center f-w-500 mb-3">مرحباً بعودتك </h4>
        <p class="text-center text-muted mb-4">يرجى تسجيل الدخول للوصول إلى حسابك</p>
        
       
        @include('layouts.messages')

        <form method="POST" action="{{ route('login.submit') }}" dir="rtl" style="direction: rtl !important;">
            @csrf
            <div class="mb-3">
                <label for="phone" class="form-label">رقم الهاتف</label>
                <input
                    type="text"
                    class="form-control @error('phone') is-invalid @enderror"
                    id="phone"
                    name="phone"
                    value="{{ old('phone') }}"
                    placeholder="أدخل رقم هاتفك"
                    required
                    autofocus
                    inputmode="numeric"
                />
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">كلمة المرور</label>
                <div class="input-group">
                    <input 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        id="password" 
                        name="password"
                        placeholder="أدخل كلمة المرور" 
                        required
                    />
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="d-flex mt-1 justify-content-between align-items-center">
                <div class="form-check">
                    <input 
                        class="form-check-input input-primary" 
                        type="checkbox" 
                        id="remember" 
                        name="remember"
                        {{ old('remember') ? 'checked' : '' }}
                    />
                    <label class="form-check-label text-muted" for="remember">
                        تذكرني
                    </label>
                </div>
            </div>
            
            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="ph-duotone ph-sign-in me-2"></i>
                    تسجيل الدخول
                </button>
            </div>
        </form>

        <hr class="my-4">
        
        <div class="text-center">
            <p class="text-muted small mb-0">
                © {{ date('Y') }}      مؤسسة البدر التعليمية . جميع الحقوق محفوظة.
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    
    if (togglePassword && passwordField && eyeIcon) {
        togglePassword.addEventListener('click', function() {
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.className = 'ph-duotone ph-eye-slash';
            } else {
                passwordField.type = 'password';
                eyeIcon.className = 'ph-duotone ph-eye';
            }
        });
    }
    
    // Auto dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endsection