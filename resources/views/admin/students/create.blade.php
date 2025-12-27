@extends('layouts.app')

@section('title', 'إضافة طالب جديد')

@section('content')
<form action="{{ route('admin.students.store') }}" method="POST">
    @csrf

    <div class="row">
        <!-- معلومات الطالب -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-user-graduate me-1"></i>
                        معلومات الطالب
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">اسم الطالب <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="أدخل اسم الطالب الكامل" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">تاريخ الميلاد <span class="text-danger">*</span></label>
                        <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}" required>
                        @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الجنس <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="male" value="male" {{ old('gender') == 'male' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="male">ذكر</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="female" value="female" {{ old('gender') == 'female' ? 'checked' : '' }}>
                                <label class="form-check-label" for="female">أنثى</label>
                            </div>
                        </div>
                        @error('gender')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label">حالة الطالب <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="new" {{ old('status', 'new') == 'new' ? 'selected' : '' }}>جديد</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط (مسجل مسبقاً)</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات ولي الأمر -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-user-friends me-1"></i>
                        معلومات ولي الأمر
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">اسم ولي الأمر <span class="text-danger">*</span></label>
                        <input type="text" name="guardian_name" class="form-control @error('guardian_name') is-invalid @enderror" value="{{ old('guardian_name') }}" placeholder="أدخل اسم ولي الأمر" required>
                        @error('guardian_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">رقم الهاتف الأساسي <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="مثال: 0912345678" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">رقم هاتف بديل</label>
                        <input type="text" name="phone_alt" class="form-control @error('phone_alt') is-invalid @enderror" value="{{ old('phone_alt') }}" placeholder="اختياري">
                        @error('phone_alt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label">العنوان</label>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" placeholder="اختياري">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- ملاحظات -->
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-sticky-note me-1"></i>
                        ملاحظات
                    </h6>
                </div>
                <div class="card-body">
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="أي ملاحظات إضافية عن الطالب...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- الأزرار -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i> رجوع للقائمة
                        </a>
                        <div class="d-flex gap-2">
                            <button type="reset" class="btn btn-light">
                                <i class="fas fa-redo me-1"></i> إعادة تعيين
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> حفظ الطالب
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
