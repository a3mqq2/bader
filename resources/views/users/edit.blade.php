@extends('layouts.app')

@section('title', 'تعديل المستخدم')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>
                        تعديل المستخدم: {{ $user->name }}
                    </h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i> رجوع للقائمة
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- الاسم -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-1 text-muted"></i>
                                اسم المستخدم <span class="text-danger">*</span>
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

                        <!-- كلمة المرور -->
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1 text-muted"></i>
                                كلمة المرور الجديدة
                            </label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">اتركها فارغة إذا لم ترد تغييرها</small>
                        </div>

                        <!-- تأكيد كلمة المرور -->
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock me-1 text-muted"></i>
                                تأكيد كلمة المرور
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        </div>

                        <!-- الراتب -->
                        <div class="col-md-6 mb-3">
                            <label for="salary" class="form-label">
                                <i class="fas fa-money-bill-wave me-1 text-muted"></i>
                                الراتب (د.ل)
                            </label>
                            <input type="number" name="salary" id="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary', $user->salary) }}" step="0.01" min="0">
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
                                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}" {{ $user->roles->contains('name', $role->name) ? 'checked' : '' }}>
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

                        <!-- معلومات إضافية -->
                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-3">
                                        <i class="fas fa-info-circle me-1"></i>
                                        معلومات إضافية
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <small class="text-muted">الحالة:</small>
                                            <div>
                                                @if($user->is_active)
                                                    <span class="badge bg-success">نشط</span>
                                                @else
                                                    <span class="badge bg-danger">غير نشط</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">تاريخ الإنشاء:</small>
                                            <div>{{ $user->created_at->format('Y/m/d H:i') }}</div>
                                        </div>
                                        <div class="col-md-4">
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
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> إلغاء
                        </a>
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
