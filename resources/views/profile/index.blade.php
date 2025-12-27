@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>
                        الملف الشخصي
                    </h5>
                </div>
            </div>
            <div class="card-body">
                @include('layouts.messages')

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- معلومات أساسية -->
                        <div class="col-12">
                            <h6 class="mb-3 text-primary">
                                <i class="fas fa-info-circle me-1"></i>
                                المعلومات الأساسية
                            </h6>
                        </div>

                        <!-- الاسم -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-1 text-muted"></i>
                                الاسم <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- رقم الهاتف -->
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone me-1 text-muted"></i>
                                رقم الهاتف <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" required inputmode="numeric">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الكود -->
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">
                                <i class="fas fa-hashtag me-1 text-muted"></i>
                                الكود
                            </label>
                            <input type="text" class="form-control" value="{{ $user->code }}" disabled>
                            <small class="text-muted">الكود لا يمكن تغييره</small>
                        </div>

                        <!-- الدور -->
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">
                                <i class="fas fa-user-tag me-1 text-muted"></i>
                                الدور
                            </label>
                            <input type="text" class="form-control" value="{{ $user->role_text }}" disabled>
                        </div>

                        <div class="col-12">
                            <hr class="my-4">
                            <h6 class="mb-3 text-primary">
                                <i class="fas fa-lock me-1"></i>
                                تغيير كلمة المرور
                                <small class="text-muted">(اختياري)</small>
                            </h6>
                        </div>

                        <!-- كلمة المرور الحالية -->
                        <div class="col-md-4 mb-3">
                            <label for="current_password" class="form-label">
                                <i class="fas fa-key me-1 text-muted"></i>
                                كلمة المرور الحالية
                            </label>
                            <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- كلمة المرور الجديدة -->
                        <div class="col-md-4 mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1 text-muted"></i>
                                كلمة المرور الجديدة
                            </label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">يجب أن تكون 8 أحرف على الأقل</small>
                        </div>

                        <!-- تأكيد كلمة المرور -->
                        <div class="col-md-4 mb-3">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock me-1 text-muted"></i>
                                تأكيد كلمة المرور
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        </div>

                        <div class="col-12">
                            <hr class="my-4">
                            <h6 class="mb-3 text-primary">
                                <i class="fas fa-info-circle me-1"></i>
                                معلومات إضافية
                            </h6>
                        </div>

                        <!-- معلومات إضافية -->
                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <small class="text-muted">الحالة:</small>
                                            <div>
                                                @if($user->is_active)
                                                    <span class="badge bg-success">نشط</span>
                                                @else
                                                    <span class="badge bg-danger">غير نشط</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($user->salary)
                                        <div class="col-md-3">
                                            <small class="text-muted">الراتب:</small>
                                            <div>{{ number_format($user->salary, 2) }} د.ل</div>
                                        </div>
                                        @endif
                                        <div class="col-md-3">
                                            <small class="text-muted">تاريخ الإنشاء:</small>
                                            <div>{{ $user->created_at->format('Y/m/d H:i') }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">آخر تحديث:</small>
                                            <div>{{ $user->updated_at->format('Y/m/d H:i') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
