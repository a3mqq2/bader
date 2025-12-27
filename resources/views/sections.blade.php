{{-- resources/views/sections.blade.php --}}
@extends('layouts.auth')

@section('title', 'الرئيسية')

@push('styles')
<style>
    .auth-form {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .roles-container {
        padding: 2rem;
        max-width: 100%;
        height: 100vh;
        overflow-y: auto;
    }

    .page-header {
        text-align: center;
        margin-bottom: 2rem;
        position: relative;
        padding: 1.5rem 0;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 2px;
        background: linear-gradient(90deg, transparent, #8B1538, transparent);
        border-radius: 1px;
    }

    .page-title {
        font-family: 'Changa', sans-serif;
        font-size: 1.8rem;
        font-weight: 700;
        color: #8B1538;
        margin-bottom: 1rem;
        text-shadow: 0 1px 3px rgba(139, 21, 56, 0.1);
        position: relative;
    }

    .welcome-text {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        padding: 0.8rem 1.5rem;
        border-radius: 25px;
        display: inline-block;
        color: #6c757d;
        font-size: 0.95rem;
        font-weight: 500;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(139, 21, 56, 0.1);
        font-family: 'Changa', sans-serif;
    }

    .role-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(139, 21, 56, 0.15);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        height: fit-content;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .role-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(139, 21, 56, 0.05), transparent);
        transition: left 0.5s ease;
    }

    .role-card:hover::before {
        left: 100%;
    }

    .role-card:hover {
        transform: translateY(-3px) scale(1.01);
        box-shadow: 0 8px 25px rgba(139, 21, 56, 0.12);
        border-color: rgba(139, 21, 56, 0.3);
    }

    .role-icon {
        width: 55px;
        height: 55px;
        background: linear-gradient(135deg, #063973 0%, #429bff 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        box-shadow: 0 6px 15px rgba(139, 21, 56, 0.25);
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }

    .role-card:hover .role-icon {
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(139, 21, 56, 0.35);
    }

    .role-name {
        font-family: 'Changa', sans-serif;
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.4rem;
        line-height: 1.3;
    }

    .role-description {
        color: #7f8c8d;
        font-size: 0.85rem;
        font-weight: 400;
        letter-spacing: 0.3px;
        font-family: 'Changa', sans-serif;
    }

    .role-card {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.5s ease forwards;
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .roles-container {
            padding: 1.5rem 1rem;
        }

        .page-title {
            font-size: 1.8rem;
        }

        .role-card {
            padding: 1.5rem 1.2rem;
        }

        .role-icon {
            width: 55px;
            height: 55px;
            font-size: 1.5rem;
        }

        .role-name {
            font-size: 1.1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="roles-container">

   <div class="head-page">
      <h3 style="font-weight: bold;color: #063973;">مرحباً بعودتك {{ auth()->user()->name }}</h3>
      <div class="prayer font-weight-bold mb-4">
         اللهم بارك لي في وقتي ورزقي وجهدي وجسدي ومالي وعملي وارزقني البركة في كل شيء.. اللهم اجعلني مباركاً اينما كنت.
      </div>
   </div>

    <div class="row g-3">
        @if(auth()->user()->hasRole('admin'))
        <div class="col-md-4">
            <a href="{{ route('admin.dashboard') }}">
                <div class="role-card">
                    <div class="role-icon">
                        <i class="ph-duotone ph-crown"></i>
                    </div>
                    <h3 class="role-name">لوحة التحكم</h3>
                    <p class="role-description">إدارة النظام</p>
                </div>
            </a>
        </div>
        @endif

        @if(auth()->user()->hasRole('specialist'))
        <div class="col-md-4">
            <a href="{{ route('specialist.dashboard') }}">
                <div class="role-card">
                    <div class="role-icon">
                        <i class="ph-duotone ph-user-gear"></i>
                    </div>
                    <h3 class="role-name">الأخصائي</h3>
                    <p class="role-description">إدارة الجلسات</p>
                </div>
            </a>
        </div>
        @endif

        @if(auth()->user()->hasRole('supervisor'))
        <div class="col-md-4">
            <a href="{{ route('supervisor.attendance.index') }}">
                <div class="role-card">
                    <div class="role-icon">
                        <i class="ph-duotone ph-scan"></i>
                    </div>
                    <h3 class="role-name">المشرف</h3>
                    <p class="role-description">حضور الموظفين</p>
                </div>
            </a>
        </div>
        @endif

        @if(auth()->user()->hasRole('accountant'))
        <div class="col-md-4">
            <a href="{{ route('accountant.dashboard') }}">
                <div class="role-card">
                    <div class="role-icon">
                        <i class="ph-duotone ph-wallet"></i>
                    </div>
                    <h3 class="role-name">المحاسب</h3>
                    <p class="role-description">النظام المالي</p>
                </div>
            </a>
        </div>
        @endif
    </div>

    <div class="text-center mt-4">
        <form action="{{ route('logout') }}" method="POST" id="logout-form-sections">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="ph-duotone ph-sign-out me-2"></i>
                تسجيل الخروج
            </button>
        </form>
    </div>

</div>
@endsection
