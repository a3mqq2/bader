@extends('layouts.app')

@section('title', 'إضافة خزينة جديدة')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        إضافة خزينة جديدة
                    </h5>
                    <a href="{{ route('accountant.finance.treasuries.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i> رجوع
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('accountant.finance.treasuries.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-vault me-1 text-muted"></i>
                                اسم الخزينة <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="opening_balance" class="form-label">
                                <i class="fas fa-coins me-1 text-muted"></i>
                                الرصيد الافتتاحي <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="opening_balance" id="opening_balance" class="form-control @error('opening_balance') is-invalid @enderror" value="{{ old('opening_balance', 0) }}" step="0.01" min="0" required>
                                <span class="input-group-text">د.ل</span>
                            </div>
                            @error('opening_balance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">سيتم إنشاء حركة رصيد افتتاحي تلقائياً</small>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">
                                <i class="fas fa-users me-1 text-muted"></i>
                                المستخدمين المخولين
                            </label>
                            <div class="card bg-light">
                                <div class="card-body py-2" style="max-height: 200px; overflow-y: auto;">
                                    <div class="row">
                                        @foreach($users as $user)
                                            <div class="col-md-4 col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="authorized_users[]" value="{{ $user->id }}" id="user_{{ $user->id }}" {{ in_array($user->id, old('authorized_users', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="user_{{ $user->id }}">
                                                        {{ $user->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">حدد المستخدمين الذين يمكنهم استخدام هذه الخزينة</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('accountant.finance.treasuries.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> حفظ الخزينة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
