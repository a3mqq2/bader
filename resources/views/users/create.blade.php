@extends('layouts.app')

@section('title', 'إضافة مستخدم جديد')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        إضافة مستخدم جديد
                    </h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i> رجوع للقائمة
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- الاسم -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-1 text-muted"></i>
                                اسم المستخدم <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
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
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required inputmode="numeric">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- كلمة المرور -->
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1 text-muted"></i>
                                كلمة المرور <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">يجب أن تكون 8 أحرف على الأقل</small>
                        </div>

                        <!-- تأكيد كلمة المرور -->
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock me-1 text-muted"></i>
                                تأكيد كلمة المرور <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>

                        <!-- الراتب -->
                        <div class="col-md-6 mb-3">
                            <label for="salary" class="form-label">
                                <i class="fas fa-money-bill-wave me-1 text-muted"></i>
                                الراتب (د.ل)
                            </label>
                            <input type="number" name="salary" id="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary') }}" step="0.01" min="0">
                            @error('salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الأدوار -->
                        <div class="col-12 mb-3">
                            <label class="form-label">
                                <i class="fas fa-user-tag me-1 text-muted"></i>
                                الأدوار
                            </label>
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <div class="row">
                                        @foreach($roles as $role)
                                            <div class="col-md-4 col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}" {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                                        {{ $role->display_name ?? $role->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> حفظ المستخدم
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
