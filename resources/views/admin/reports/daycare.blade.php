@extends('layouts.app')

@section('title', 'تقرير الرعاية النهارية')

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
                        <label class="form-label">نوع الرعاية</label>
                        <select name="daycare_type_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($daycareTypes as $type)
                                <option value="{{ $type->id }}" {{ request('daycare_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-search me-1"></i> بحث
                        </button>
                        <a href="{{ route('admin.reports.daycare') }}" class="btn btn-outline-secondary">
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
                    <i class="ti ti-home-heart me-2"></i>
                    تقرير الرعاية النهارية
                </h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reports.daycare', array_merge(request()->query(), ['print' => 1])) }}"
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
                        <div class="bg-primary bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-primary">{{ $stats['total_subscriptions'] }}</h4>
                            <small class="text-muted">إجمالي الاشتراكات</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-success bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-success">{{ $stats['active'] }}</h4>
                            <small class="text-muted">اشتراكات نشطة</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-info bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-info">{{ number_format($stats['attendance_rate'], 1) }}%</h4>
                            <small class="text-muted">نسبة الحضور</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-warning bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-warning">{{ number_format($stats['total_revenue'], 2) }}</h4>
                            <small class="text-muted">الإيرادات (د.ل)</small>
                        </div>
                    </div>
                </div>

                <!-- الاشتراكات النشطة -->
                <h6 class="mb-3 border-bottom pb-2">
                    <i class="ti ti-check text-success me-2"></i>
                    الاشتراكات النشطة
                    <span class="badge bg-success">{{ $activeSubscriptions->count() }}</span>
                </h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-success">
                            <tr>
                                <th>#</th>
                                <th>الطالب</th>
                                <th>نوع الرعاية</th>
                                <th>تاريخ البدء</th>
                                <th>تاريخ الانتهاء</th>
                                <th>أيام الحضور</th>
                                <th>أيام الغياب</th>
                                <th>نسبة الحضور</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeSubscriptions as $index => $sub)
                            @php
                                $presentDays = $sub->attendances->where('status', 'present')->count();
                                $absentDays = $sub->attendances->where('status', 'absent')->count();
                                $totalDays = $presentDays + $absentDays;
                                $attendanceRate = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold">{{ $sub->student?->name ?? '-' }}</span>
                                    <br><small class="text-muted">{{ $sub->student?->code ?? '' }}</small>
                                </td>
                                <td>{{ $sub->daycareType?->name ?? '-' }}</td>
                                <td>{{ $sub->start_date?->format('Y/m/d') ?? '-' }}</td>
                                <td>{{ $sub->end_date?->format('Y/m/d') ?? '-' }}</td>
                                <td class="text-success">{{ $presentDays }}</td>
                                <td class="text-danger">{{ $absentDays }}</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $attendanceRate >= 80 ? 'success' : ($attendanceRate >= 50 ? 'warning' : 'danger') }}"
                                             style="width: {{ $attendanceRate }}%">
                                            {{ number_format($attendanceRate, 0) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">لا توجد اشتراكات نشطة</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- الطلاب المنقطعون -->
                @if($discontinuedStudents->count() > 0)
                <h6 class="mb-3 border-bottom pb-2">
                    <i class="ti ti-alert-triangle text-danger me-2"></i>
                    طلاب منقطعون (غياب متتالي)
                    <span class="badge bg-danger">{{ $discontinuedStudents->count() }}</span>
                </h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-danger">
                            <tr>
                                <th>#</th>
                                <th>الطالب</th>
                                <th>نوع الرعاية</th>
                                <th>آخر حضور</th>
                                <th>أيام الغياب المتتالية</th>
                                <th>هاتف ولي الأمر</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($discontinuedStudents as $index => $student)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold">{{ $student['name'] }}</span>
                                    <br><small class="text-muted">{{ $student['code'] }}</small>
                                </td>
                                <td>{{ $student['daycare_type'] }}</td>
                                <td>{{ $student['last_attendance'] }}</td>
                                <td>
                                    <span class="badge bg-danger">{{ $student['absent_days'] }} يوم</span>
                                </td>
                                <td>
                                    <a href="tel:{{ $student['phone'] }}" class="text-decoration-none">
                                        {{ $student['phone'] }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                <!-- إحصائيات حسب نوع الرعاية -->
                @if($subscriptionsByType->count() > 0)
                <div class="mt-4 pt-4 border-top">
                    <h6 class="mb-3">
                        <i class="ti ti-chart-pie text-primary me-2"></i>
                        توزيع الاشتراكات حسب النوع
                    </h6>
                    <div class="row">
                        @foreach($subscriptionsByType as $type)
                        <div class="col-md-3 mb-3">
                            <div class="card border h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-primary mb-2">{{ $type->daycareType?->name ?? 'غير محدد' }}</h6>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">عدد الاشتراكات:</span>
                                        <span class="fw-bold">{{ $type->total }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">الإيرادات:</span>
                                        <span class="fw-bold text-success">{{ number_format($type->revenue ?? 0, 2) }} د.ل</span>
                                    </div>
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
