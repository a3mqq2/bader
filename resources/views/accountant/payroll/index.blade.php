@extends('layouts.app')

@section('title', 'كشوفات المرتبات')

@section('content')
<div class="row">
    <!-- إحصائيات -->
    <div class="col-md-3 mb-3">
        <div class="card border-0 bg-light-primary">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar bg-primary text-white">
                            <i class="ti ti-file-invoice fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1 text-muted small">إجمالي الكشوفات</p>
                        <h4 class="mb-0">{{ $payrolls->total() }} <small class="fs-6 text-muted">كشف</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0 bg-light-success">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar bg-success text-white">
                            <i class="ti ti-check fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1 text-muted small">تم التنفيذ</p>
                        <h4 class="mb-0 text-success">{{ $payrolls->where('status', 'executed')->count() }} <small class="fs-6">كشف</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0 bg-light-warning">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar bg-warning text-white">
                            <i class="ti ti-clock fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1 text-muted small">مسودات</p>
                        <h4 class="mb-0 text-warning">{{ $payrolls->where('status', 'draft')->count() }} <small class="fs-6">كشف</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0 bg-light-info">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar bg-info text-white">
                            <i class="ti ti-calendar fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1 text-muted small">السنة الحالية</p>
                        <h4 class="mb-0 text-info">{{ date('Y') }}</h4>
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
            <div class="collapse {{ request()->hasAny(['year', 'status']) ? 'show' : '' }}" id="filterCollapse">
                <div class="card-body py-3">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">السنة</label>
                            <select name="year" class="form-select">
                                <option value="">كل السنوات</option>
                                @foreach($years as $y)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة</option>
                                <option value="executed" {{ request('status') === 'executed' ? 'selected' : '' }}>تم التنفيذ</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search me-1"></i> بحث
                            </button>
                            @if(request()->hasAny(['year', 'status']))
                                <a href="{{ route('accountant.payroll.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-x"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- كشوفات المرتبات -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="ti ti-file-invoice me-2"></i>
                    كشوفات المرتبات
                    <span class="badge bg-secondary ms-2">{{ $payrolls->total() }}</span>
                </h6>
                <a href="{{ route('accountant.payroll.show') }}" class="btn btn-primary btn-sm">
                    <i class="ti ti-plus me-1"></i> كشف جديد
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($payrolls as $payroll)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 shadow-sm border-0 overflow-hidden">
                                <!-- Header -->
                                <div class="card-header bg-{{ $payroll->status === 'executed' ? 'success' : 'warning' }} bg-opacity-10 border-0 py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avtar avtar-s bg-{{ $payroll->status === 'executed' ? 'success' : 'warning' }} text-white">
                                                <i class="ti ti-{{ $payroll->status === 'executed' ? 'check' : 'clock' }} fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $payroll->period_text }}</h6>
                                                <small class="text-muted">{{ $payroll->year }}</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-{{ $payroll->status === 'executed' ? 'success' : 'warning' }}">
                                            {{ $payroll->status_text }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Body -->
                                <div class="card-body">
                                    <div class="row text-center mb-3">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <p class="text-muted small mb-1">عدد الموظفين</p>
                                                <h4 class="mb-0 text-primary">{{ $payroll->items_count }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-muted small mb-1">إجمالي الصافي</p>
                                            <h4 class="mb-0 text-success">{{ number_format($payroll->total_net, 2) }}</h4>
                                        </div>
                                    </div>

                                    <div class="bg-light rounded p-2 small">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="ti ti-user me-2 text-muted"></i>
                                            <span class="text-muted">أنشئ بواسطة:</span>
                                            <span class="ms-auto fw-medium">{{ $payroll->creator->name ?? '-' }}</span>
                                        </div>
                                        @if($payroll->executed_at)
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-calendar-check me-2 text-success"></i>
                                                <span class="text-muted">تاريخ التنفيذ:</span>
                                                <span class="ms-auto fw-medium text-success">{{ $payroll->executed_at->format('Y/m/d') }}</span>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-calendar me-2 text-muted"></i>
                                                <span class="text-muted">تاريخ الإنشاء:</span>
                                                <span class="ms-auto fw-medium">{{ $payroll->created_at->format('Y/m/d') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="card-footer bg-transparent border-0 pt-0">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('accountant.payroll.show', ['year' => $payroll->year, 'month' => $payroll->month]) }}" class="btn btn-primary btn-sm flex-fill">
                                            <i class="ti ti-eye me-1"></i> عرض التفاصيل
                                        </a>
                                        @if($payroll->status === 'executed')
                                            <a href="{{ route('accountant.payroll.print', $payroll) }}" target="_blank" class="btn btn-outline-secondary btn-sm" title="طباعة">
                                                <i class="ti ti-printer"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <div class="avtar avtar-xl bg-light-primary mx-auto mb-3">
                                    <i class="ti ti-file-invoice fs-1 text-primary"></i>
                                </div>
                                <h5 class="text-muted mb-2">لا توجد كشوفات مرتبات</h5>
                                <p class="text-muted small mb-3">ابدأ بإنشاء كشف مرتبات جديد</p>
                                <a href="{{ route('accountant.payroll.show') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-1"></i> إنشاء كشف جديد
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
            @if($payrolls->hasPages())
            <div class="card-footer">
                {{ $payrolls->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
