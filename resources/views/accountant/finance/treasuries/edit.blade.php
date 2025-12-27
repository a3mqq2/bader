@extends('layouts.app')

@section('title', 'تعديل الخزينة')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">تعديل الخزينة</h6>
                <a href="{{ route('accountant.finance.treasuries.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-right me-1"></i> رجوع
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('accountant.finance.treasuries.update', $treasury) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">اسم الخزينة <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $treasury->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">الحالة</label>
                            <select name="is_active" class="form-select">
                                <option value="1" {{ old('is_active', $treasury->is_active) ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ !old('is_active', $treasury->is_active) ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">المستخدمين المخولين</label>
                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                <div class="row g-2">
                                    @foreach($users as $user)
                                        <div class="col-md-4 col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="authorized_users[]" value="{{ $user->id }}" id="user_{{ $user->id }}" {{ $treasury->authorizedUsers->contains($user->id) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="user_{{ $user->id }}">{{ $user->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="bg-light rounded p-3">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <small class="text-muted d-block">الرصيد الافتتاحي</small>
                                        <strong>{{ number_format($treasury->opening_balance, 2) }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">الرصيد الحالي</small>
                                        <strong class="{{ $treasury->current_balance >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($treasury->current_balance, 2) }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">عدد الحركات</small>
                                        <strong>{{ $treasury->transactions()->count() }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('accountant.finance.treasuries.index') }}" class="btn btn-light">إلغاء</a>
                        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
