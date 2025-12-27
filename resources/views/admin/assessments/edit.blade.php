@extends('layouts.app')

@section('title', 'تعديل اختبار/تقييم/مقياس')

@section('content')
<form action="{{ route('admin.assessments.update', $assessment) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-clipboard-list me-1"></i>
                        تعديل بيانات الاختبار/التقييم/المقياس
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $assessment->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">السعر <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $assessment->price) }}" step="0.01" min="0" required>
                            <span class="input-group-text">د.ل</span>
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $assessment->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $assessment->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">مفعل</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-cog me-1"></i>
                        الإجراءات
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> حفظ التعديلات
                        </button>
                        <a href="{{ route('admin.assessments.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i> رجوع للقائمة
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        معلومات إضافية
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted">تاريخ الإنشاء:</td>
                            <td>{{ $assessment->created_at->format('Y/m/d H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">آخر تحديث:</td>
                            <td>{{ $assessment->updated_at->format('Y/m/d H:i') }}</td>
                        </tr>
                        @if($assessment->creator)
                        <tr>
                            <td class="text-muted">أضيف بواسطة:</td>
                            <td>{{ $assessment->creator->name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
