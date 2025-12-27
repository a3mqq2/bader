@extends('layouts.app')

@section('title', 'تقرير التقييمات')

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
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-search me-1"></i> بحث
                        </button>
                        <a href="{{ route('admin.reports.assessments') }}" class="btn btn-outline-secondary">
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
                    <i class="ti ti-checklist me-2"></i>
                    تقرير استخدام التقييمات
                </h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reports.assessments', array_merge(request()->query(), ['print' => 1])) }}"
                       class="btn btn-outline-info btn-sm" target="_blank">
                        <i class="ti ti-printer me-1"></i> طباعة
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-right me-1"></i> رجوع
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- إحصائيات عامة -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="bg-primary bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-primary">{{ $stats['total_assessments'] }}</h4>
                            <small class="text-muted">إجمالي التقييمات المتاحة</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-success bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-success">{{ $stats['total_usage'] }}</h4>
                            <small class="text-muted">إجمالي الاستخدامات</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-info bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-info">{{ number_format($stats['total_revenue'], 2) }} د.ل</h4>
                            <small class="text-muted">إجمالي الإيرادات</small>
                        </div>
                    </div>
                </div>

                <!-- جدول التقييمات -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>اسم التقييم</th>
                                <th>السعر</th>
                                <th>الحالة</th>
                                <th>عدد الاستخدامات</th>
                                <th>إجمالي الإيرادات</th>
                                <th>نسبة الاستخدام</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assessments as $index => $assessment)
                            @php
                                $usagePercent = $stats['total_usage'] > 0 ? ($assessment->usage_count / $stats['total_usage']) * 100 : 0;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold">{{ $assessment->name }}</span>
                                    @if($assessment->description)
                                        <br><small class="text-muted">{{ Str::limit($assessment->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ number_format($assessment->price, 2) }} د.ل</td>
                                <td>
                                    <span class="badge bg-{{ $assessment->is_active ? 'success' : 'secondary' }}">
                                        {{ $assessment->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $assessment->usage_count }}</span>
                                </td>
                                <td class="text-success fw-bold">{{ number_format($assessment->total_revenue, 2) }} د.ل</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $usagePercent }}%">
                                            {{ number_format($usagePercent, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="ti ti-checklist fa-2x mb-2 d-block"></i>
                                    لا توجد تقييمات
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($assessments->count() > 0)
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="4" class="text-start">الإجمالي</td>
                                <td><span class="badge bg-info">{{ $stats['total_usage'] }}</span></td>
                                <td class="text-success">{{ number_format($stats['total_revenue'], 2) }} د.ل</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

                <!-- أكثر التقييمات استخداماً -->
                @if($topAssessments->count() > 0)
                <div class="mt-4 pt-4 border-top">
                    <h6 class="mb-3">
                        <i class="ti ti-trending-up text-success me-2"></i>
                        أكثر التقييمات استخداماً
                    </h6>
                    <div class="row">
                        @foreach($topAssessments->take(5) as $assessment)
                        <div class="col-md-4 col-lg-2 mb-3">
                            <div class="card border h-100">
                                <div class="card-body text-center p-3">
                                    <h5 class="text-primary mb-1">{{ $assessment->usage_count }}</h5>
                                    <small class="text-muted">{{ Str::limit($assessment->name, 20) }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
