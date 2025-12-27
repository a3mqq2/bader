@extends('layouts.app')

@section('title', 'تقرير دراسات الحالة')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- فلتر التاريخ -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="ti ti-filter me-2"></i>
                    فلترة التقرير
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">حالة الدراسة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>جاري</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-search me-1"></i> بحث
                        </button>
                        <a href="{{ route('admin.reports.cases') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-refresh"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- التقرير -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="ti ti-file-description me-2"></i>
                    تقرير دراسات الحالة
                    <span class="badge bg-info ms-2">{{ $cases->count() }}</span>
                </h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reports.cases', array_merge(request()->query(), ['print' => 1])) }}"
                       class="btn btn-outline-info btn-sm" target="_blank">
                        <i class="ti ti-printer me-1"></i> طباعة
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-right me-1"></i> رجوع
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- إحصائيات -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="bg-light rounded p-3 text-center">
                            <h4 class="mb-1 text-primary">{{ $stats['total'] }}</h4>
                            <small class="text-muted">إجمالي الحالات</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light rounded p-3 text-center">
                            <h4 class="mb-1 text-warning">{{ $stats['pending'] }}</h4>
                            <small class="text-muted">قيد الانتظار</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light rounded p-3 text-center">
                            <h4 class="mb-1 text-info">{{ $stats['in_progress'] }}</h4>
                            <small class="text-muted">جاري</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light rounded p-3 text-center">
                            <h4 class="mb-1 text-success">{{ $stats['completed'] }}</h4>
                            <small class="text-muted">مكتمل</small>
                        </div>
                    </div>
                </div>

                <!-- جدول البيانات -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-info">
                            <tr>
                                <th>#</th>
                                <th>الطالب</th>
                                <th>الكود</th>
                                <th>حالة الدراسة</th>
                                <th>عدد التقييمات</th>
                                <th>المبلغ الإجمالي</th>
                                <th>المدفوع</th>
                                <th>المتبقي</th>
                                <th>تاريخ البدء</th>
                                <th>منشئ الدراسة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cases as $index => $case)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $case->student->name ?? '-' }}</td>
                                <td><span class="badge bg-secondary">{{ $case->student->code ?? '-' }}</span></td>
                                <td>
                                    @php
                                        $statusColors = ['pending' => 'warning', 'in_progress' => 'info', 'completed' => 'success'];
                                        $statusTexts = ['pending' => 'قيد الانتظار', 'in_progress' => 'جاري', 'completed' => 'مكتمل'];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$case->status] ?? 'secondary' }}">
                                        {{ $statusTexts[$case->status] ?? $case->status }}
                                    </span>
                                </td>
                                <td>{{ $case->invoice?->items?->count() ?? 0 }}</td>
                                <td>{{ number_format($case->invoice?->total ?? 0, 2) }} د.ل</td>
                                <td class="text-success">{{ number_format($case->invoice?->paid ?? 0, 2) }} د.ل</td>
                                <td class="text-danger">{{ number_format(($case->invoice?->total ?? 0) - ($case->invoice?->paid ?? 0), 2) }} د.ل</td>
                                <td>{{ $case->created_at->format('Y/m/d') }}</td>
                                <td>{{ $case->creator->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="ti ti-file-off fa-2x mb-2 d-block"></i>
                                    لا توجد بيانات
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($cases->count() > 0)
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="5" class="text-start">الإجمالي</td>
                                <td>{{ number_format($cases->sum(fn($c) => $c->invoice?->total ?? 0), 2) }} د.ل</td>
                                <td class="text-success">{{ number_format($cases->sum(fn($c) => $c->invoice?->paid ?? 0), 2) }} د.ل</td>
                                <td class="text-danger">{{ number_format($cases->sum(fn($c) => ($c->invoice?->total ?? 0) - ($c->invoice?->paid ?? 0)), 2) }} د.ل</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
