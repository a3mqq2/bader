@extends('layouts.app')

@section('title', 'الخزائن المالية')

@section('content')
<div class="row">
    <!-- الفلترة -->
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-body py-3">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small">بحث</label>
                        <input type="text" name="search" class="form-control" placeholder="اسم الخزينة..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-search me-1"></i> بحث
                        </button>
                    </div>
                    @if(request()->hasAny(['search', 'status']))
                    <div class="col-md-2">
                        <a href="{{ route('accountant.finance.treasuries.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="ti ti-x me-1"></i> إلغاء
                        </a>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- جدول الخزائن -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">الخزائن المالية</h6>
                <a href="{{ route('accountant.finance.treasuries.create') }}" class="btn btn-primary btn-sm">
                    <i class="ti ti-plus me-1"></i> إضافة خزينة
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>الخزينة</th>
                                <th class="text-center">الرصيد الافتتاحي</th>
                                <th class="text-center">الرصيد الحالي</th>
                                <th class="text-center">الحركات</th>
                                <th class="text-center">الحالة</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($treasuries as $treasury)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $treasury->name }}</div>
                                        <small class="text-muted">{{ $treasury->created_at->format('Y/m/d') }}</small>
                                    </td>
                                    <td class="text-center">{{ number_format($treasury->opening_balance, 2) }}</td>
                                    <td class="text-center">
                                        <span class="fw-bold {{ $treasury->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($treasury->current_balance, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $treasury->transactions_count ?? $treasury->transactions()->count() }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $treasury->is_active ? 'success' : 'danger' }}">
                                            {{ $treasury->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('accountant.finance.transactions.index', ['treasury_id' => $treasury->id]) }}" class="btn btn-sm btn-outline-info">
                                                <i class="ti ti-list me-1"></i> الحركات
                                            </a>
                                            <a href="{{ route('accountant.finance.treasuries.edit', $treasury) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="ti ti-edit me-1"></i> تعديل
                                            </a>
                                            @if(($treasury->transactions_count ?? $treasury->transactions()->count()) == 0)
                                                <form action="{{ route('accountant.finance.treasuries.destroy', $treasury) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخزينة؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="ti ti-trash me-1"></i> حذف
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="ti ti-wallet fs-1 d-block mb-2"></i>
                                        لا توجد خزائن مالية
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($treasuries->hasPages())
            <div class="card-footer">
                {{ $treasuries->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
