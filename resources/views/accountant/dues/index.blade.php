@extends('layouts.app')

@section('title', 'قائمة المستحقات')

@section('content')
<div class="row">
    <!-- إحصائيات -->
    <div class="col-md-4 mb-3">
        <div class="card border-0 bg-light-danger">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar bg-danger text-white">
                            <i class="ti text-white ti-users fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1 text-muted small">إجمالي المستحقات</p>
                        <h4 class="mb-0 text-danger">{{ number_format($stats['total_dues'], 2) }} <small class="fs-6">د.ل</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-0 bg-light-warning">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar bg-warning text-white">
                            <i class="ti ti-users fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1 text-muted small">عدد الطلاب المدينين</p>
                        <h4 class="mb-0 text-warning">{{ $stats['total_students'] }} <small class="fs-6">طالب</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-0 bg-light-info">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar bg-info text-white">
                            <i class="ti ti-file-invoice fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1 text-muted small">متوسط المستحقات</p>
                        <h4 class="mb-0 text-info">{{ $stats['total_students'] > 0 ? number_format($stats['total_dues'] / $stats['total_students'], 2) : '0.00' }} <small class="fs-6">د.ل</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلترة -->
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header py-2">
                <a class="text-dark text-decoration-none d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#filterCollapse">
                    <span><i class="ti ti-filter me-2"></i>البحث والفلترة</span>
                    <i class="ti ti-chevron-down"></i>
                </a>
            </div>
            <div class="collapse {{ request()->hasAny(['search', 'status', 'min_balance', 'max_balance']) ? 'show' : '' }}" id="filterCollapse">
                <div class="card-body py-3">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">بحث</label>
                            <input type="text" name="search" class="form-control" placeholder="الاسم، الكود، الهاتف..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="">الكل</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>جديد</option>
                                <option value="under_assessment" {{ request('status') === 'under_assessment' ? 'selected' : '' }}>تحت التقييم</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">الحد الأدنى</label>
                            <input type="number" name="min_balance" class="form-control" placeholder="0.00" value="{{ request('min_balance') }}" step="0.01">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">الحد الأقصى</label>
                            <input type="number" name="max_balance" class="form-control" placeholder="0.00" value="{{ request('max_balance') }}" step="0.01">
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search me-1"></i> بحث
                            </button>
                            @if(request()->hasAny(['search', 'status', 'min_balance', 'max_balance']))
                                <a href="{{ route('accountant.dues.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-x"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول المستحقات -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="ti ti-file-invoice me-2"></i>
                    قائمة المستحقات
                    <span class="badge bg-secondary ms-2">{{ $students->total() }}</span>
                </h6>
                <a href="{{ route('accountant.dues.print', request()->all()) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-printer me-1"></i> طباعة الكشف
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th width="10%">الكود</th>
                                <th width="22%">الطالب</th>
                                <th width="18%">ولي الأمر</th>
                                <th width="15%">الهاتف</th>
                                <th width="10%" class="text-center">الفواتير</th>
                                <th width="15%" class="text-center">المستحقات</th>
                                <th width="10%" class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $student->code }}</span></td>
                                    <td>
                                        <div class="fw-bold">{{ $student->name }}</div>
                                        <span class="badge bg-{{ $student->status_color }} small">{{ $student->status_text }}</span>
                                    </td>
                                    <td>{{ $student->guardian_name ?: '-' }}</td>
                                    <td>{{ $student->phone ?: '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $student->invoices->count() }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-danger fs-6">{{ number_format($student->total_dues, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('accountant.dues.show', $student) }}" class="btn btn-success btn-sm">
                                            <i class="ti ti-cash me-1"></i> تحصيل
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="ti ti-check-circle fs-1 d-block mb-2 text-success"></i>
                                        لا يوجد طلاب عليهم مستحقات
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($students->hasPages())
            <div class="card-footer">
                {{ $students->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
