@extends('layouts.app')

@section('title', 'تقرير الأخصائيين')

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
                        <label class="form-label">الأخصائي</label>
                        <select name="specialist_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($allSpecialists as $spec)
                                <option value="{{ $spec->id }}" {{ request('specialist_id') == $spec->id ? 'selected' : '' }}>
                                    {{ $spec->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-search me-1"></i> بحث
                        </button>
                        <a href="{{ route('admin.reports.specialists') }}" class="btn btn-outline-secondary">
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
                    <i class="ti ti-user-check me-2"></i>
                    تقرير أداء الأخصائيين
                </h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reports.specialists', array_merge(request()->query(), ['print' => 1])) }}"
                       class="btn btn-outline-info btn-sm" target="_blank">
                        <i class="ti ti-printer me-1"></i> طباعة
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-right me-1"></i> رجوع
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- ملخص الأداء -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="bg-primary bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-primary">{{ $stats['total_specialists'] }}</h4>
                            <small class="text-muted">عدد الأخصائيين</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-success bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-success">{{ $stats['total_sessions'] }}</h4>
                            <small class="text-muted">إجمالي الجلسات</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-info bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-info">{{ $stats['completed_sessions'] }}</h4>
                            <small class="text-muted">جلسات مكتملة</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-warning bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-warning">{{ number_format($stats['avg_completion_rate'], 1) }}%</h4>
                            <small class="text-muted">متوسط نسبة الإنجاز</small>
                        </div>
                    </div>
                </div>

                <!-- جدول الأخصائيين -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-secondary">
                            <tr>
                                <th>#</th>
                                <th>الأخصائي</th>
                                <th>عدد الطلاب</th>
                                <th>إجمالي الجلسات</th>
                                <th>مكتملة</th>
                                <th>مجدولة</th>
                                <th>غياب</th>
                                <th>نسبة الإنجاز</th>
                                <th>نسبة الحضور</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($specialists as $index => $specialist)
                            @php
                                $completionRate = $specialist->total_sessions > 0
                                    ? ($specialist->completed_sessions / $specialist->total_sessions) * 100
                                    : 0;
                                $attendanceRate = ($specialist->completed_sessions + $specialist->absent_sessions) > 0
                                    ? ($specialist->completed_sessions / ($specialist->completed_sessions + $specialist->absent_sessions)) * 100
                                    : 0;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:35px;height:35px;font-size:12px;">
                                            {{ mb_substr($specialist->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <span class="fw-bold">{{ $specialist->name }}</span>
                                            <br><small class="text-muted">{{ $specialist->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-primary">{{ $specialist->students_count }}</span></td>
                                <td>{{ $specialist->total_sessions }}</td>
                                <td class="text-success">{{ $specialist->completed_sessions }}</td>
                                <td class="text-info">{{ $specialist->scheduled_sessions }}</td>
                                <td class="text-danger">{{ $specialist->absent_sessions }}</td>
                                <td>
                                    <div class="progress" style="height: 20px; min-width: 80px;">
                                        <div class="progress-bar bg-{{ $completionRate >= 80 ? 'success' : ($completionRate >= 50 ? 'warning' : 'danger') }}"
                                             style="width: {{ $completionRate }}%">
                                            {{ number_format($completionRate, 0) }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px; min-width: 80px;">
                                        <div class="progress-bar bg-{{ $attendanceRate >= 90 ? 'success' : ($attendanceRate >= 70 ? 'warning' : 'danger') }}"
                                             style="width: {{ $attendanceRate }}%">
                                            {{ number_format($attendanceRate, 0) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="ti ti-users fa-2x mb-2 d-block"></i>
                                    لا يوجد أخصائيين
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($specialists->count() > 0)
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="2">الإجمالي</td>
                                <td><span class="badge bg-primary">{{ $specialists->sum('students_count') }}</span></td>
                                <td>{{ $specialists->sum('total_sessions') }}</td>
                                <td class="text-success">{{ $specialists->sum('completed_sessions') }}</td>
                                <td class="text-info">{{ $specialists->sum('scheduled_sessions') }}</td>
                                <td class="text-danger">{{ $specialists->sum('absent_sessions') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

                <!-- أفضل الأخصائيين أداءً -->
                @if($topPerformers->count() > 0)
                <div class="mt-4 pt-4 border-top">
                    <h6 class="mb-3">
                        <i class="ti ti-trophy text-warning me-2"></i>
                        أفضل الأخصائيين أداءً
                    </h6>
                    <div class="row">
                        @foreach($topPerformers->take(4) as $index => $performer)
                        @php
                            $performerRate = $performer->total_sessions > 0
                                ? ($performer->completed_sessions / $performer->total_sessions) * 100
                                : 0;
                            $medals = ['🥇', '🥈', '🥉', '🏅'];
                        @endphp
                        <div class="col-md-3 mb-3">
                            <div class="card border-warning h-100">
                                <div class="card-body text-center p-3">
                                    <div class="fs-2 mb-2">{{ $medals[$index] ?? '⭐' }}</div>
                                    <h6 class="text-primary mb-1">{{ $performer->name }}</h6>
                                    <p class="text-muted mb-2 small">{{ $performer->completed_sessions }} جلسة مكتملة</p>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-success" style="width: {{ $performerRate }}%"></div>
                                    </div>
                                    <small class="text-success">{{ number_format($performerRate, 0) }}% إنجاز</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- تفاصيل كل أخصائي -->
                @if(request('specialist_id') && $specialistDetails)
                <div class="mt-4 pt-4 border-top">
                    <h6 class="mb-3">
                        <i class="ti ti-chart-bar text-info me-2"></i>
                        تفاصيل أداء: {{ $specialistDetails->name }}
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">الجلسات حسب نوع العلاج</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>نوع الجلسة</th>
                                                <th>العدد</th>
                                                <th>المكتملة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($specialistSessionsByType as $type)
                                            <tr>
                                                <td>{{ $type->therapy_name ?? 'غير محدد' }}</td>
                                                <td>{{ $type->total }}</td>
                                                <td class="text-success">{{ $type->completed }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">طلاب الأخصائي</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>الطالب</th>
                                                <th>الجلسات</th>
                                                <th>المتبقية</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($specialistStudents as $student)
                                            <tr>
                                                <td>{{ $student->name }}</td>
                                                <td>{{ $student->total_sessions }}</td>
                                                <td>{{ $student->remaining_sessions }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
