@extends('layouts.app')

@section('title', 'تعديل بيانات الطالب')

@section('content')
<form action="{{ route('admin.students.update', $student) }}" method="POST">
    @csrf
    @method('PUT')

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
                        <label class="form-label">كود الطالب</label>
                        <input type="text" class="form-control" value="{{ $student->code }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">اسم الطالب <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $student->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">تاريخ الميلاد <span class="text-danger">*</span></label>
                        <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date', $student->birth_date->format('Y-m-d')) }}" required>
                        @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الجنس <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="male" value="male" {{ old('gender', $student->gender) == 'male' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="male">ذكر</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="female" value="female" {{ old('gender', $student->gender) == 'female' ? 'checked' : '' }}>
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
                            <option value="new" {{ old('status', $student->status) == 'new' ? 'selected' : '' }}>جديد</option>
                            <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>نشط (مسجل مسبقاً)</option>
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
                        <input type="text" name="guardian_name" class="form-control @error('guardian_name') is-invalid @enderror" value="{{ old('guardian_name', $student->guardian_name) }}" required>
                        @error('guardian_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">رقم الهاتف الأساسي <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $student->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">رقم هاتف بديل</label>
                        <input type="text" name="phone_alt" class="form-control @error('phone_alt') is-invalid @enderror" value="{{ old('phone_alt', $student->phone_alt) }}">
                        @error('phone_alt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label">العنوان</label>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $student->address) }}">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- ملاحظات -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-sticky-note me-1"></i>
                        ملاحظات
                    </h6>
                </div>
                <div class="card-body">
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4" placeholder="أي ملاحظات إضافية عن الطالب...">{{ old('notes', $student->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- معلومات إضافية -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        معلومات إضافية
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">العمر:</td>
                            <td class="fw-bold">{{ $student->age }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">تاريخ التسجيل:</td>
                            <td>{{ $student->created_at->format('Y/m/d H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">آخر تحديث:</td>
                            <td>{{ $student->updated_at->format('Y/m/d H:i') }}</td>
                        </tr>
                        @if($student->creator)
                        <tr>
                            <td class="text-muted">أضيف بواسطة:</td>
                            <td>{{ $student->creator->name }}</td>
                        </tr>
                        @endif
                    </table>
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
                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-info">
                                <i class="fas fa-eye me-1"></i> عرض
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> حفظ التعديلات
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
