@extends('layouts.app')

@section('title', 'تقرير الطلاب حسب الخدمات')

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
                        <a href="{{ route('admin.reports.students-services') }}" class="btn btn-outline-secondary">
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
                    <i class="ti ti-category me-2"></i>
                    تقرير الطلاب حسب الخدمات
                </h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reports.students-services', array_merge(request()->query(), ['print' => 1])) }}"
                       class="btn btn-outline-info btn-sm" target="_blank">
                        <i class="ti ti-printer me-1"></i> طباعة
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-right me-1"></i> رجوع
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- إحصائيات حسب الخدمة -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-success bg-opacity-10 border-success">
                            <div class="card-body text-center">
                                <i class="ti ti-clock fa-2x text-success mb-2"></i>
                                <h3 class="text-success mb-1">{{ $stats['with_sessions'] }}</h3>
                                <p class="text-muted mb-0">طلاب لديهم جلسات</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info bg-opacity-10 border-info">
                            <div class="card-body text-center">
                                <i class="ti ti-home-heart fa-2x text-info mb-2"></i>
                                <h3 class="text-info mb-1">{{ $stats['with_daycare'] }}</h3>
                                <p class="text-muted mb-0">طلاب لديهم رعاية نهارية</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning bg-opacity-10 border-warning">
                            <div class="card-body text-center">
                                <i class="ti ti-clipboard-check fa-2x text-warning mb-2"></i>
                                <h3 class="text-warning mb-1">{{ $stats['with_cases'] }}</h3>
                                <p class="text-muted mb-0">طلاب لديهم دراسة حالة</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- طلاب الجلسات -->
                <h6 class="mb-3 border-bottom pb-2">
                    <i class="ti ti-clock text-success me-2"></i>
                    طلاب الجلسات العلاجية
                    <span class="badge bg-success">{{ $studentsWithSessions->count() }}</span>
                </h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm">
                        <thead class="table-success">
                            <tr>
                                <th>#</th>
                                <th>الكود</th>
                                <th>اسم الطالب</th>
                                <th>عدد الباقات</th>
                                <th>إجمالي الجلسات</th>
                                <th>الجلسات المكتملة</th>
                                <th>الجلسات المتبقية</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($studentsWithSessions as $index => $student)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge bg-secondary">{{ $student->code }}</span></td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->sessionPackages->count() }}</td>
                                <td>{{ $student->sessionPackages->sum('total_sessions') }}</td>
                                <td class="text-success">{{ $student->sessionPackages->flatMap->sessions->where('status', 'completed')->count() }}</td>
                                <td class="text-warning">{{ $student->sessionPackages->flatMap->sessions->where('status', 'scheduled')->count() }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">لا يوجد طلاب</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- طلاب الرعاية النهارية -->
                <h6 class="mb-3 border-bottom pb-2">
                    <i class="ti ti-home-heart text-info me-2"></i>
                    طلاب الرعاية النهارية
                    <span class="badge bg-info">{{ $studentsWithDaycare->count() }}</span>
                </h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm">
                        <thead class="table-info">
                            <tr>
                                <th>#</th>
                                <th>الكود</th>
                                <th>اسم الطالب</th>
                                <th>عدد الاشتراكات</th>
                                <th>الاشتراك الحالي</th>
                                <th>أيام الحضور</th>
                                <th>أيام الغياب</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($studentsWithDaycare as $index => $student)
                            @php
                                $activeSub = $student->daycareSubscriptions->where('status', 'active')->first();
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge bg-secondary">{{ $student->code }}</span></td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->daycareSubscriptions->count() }}</td>
                                <td>
                                    @if($activeSub)
                                        <span class="badge bg-success">{{ $activeSub->daycareType->name ?? '-' }}</span>
                                    @else
                                        <span class="badge bg-secondary">لا يوجد</span>
                                    @endif
                                </td>
                                <td class="text-success">{{ $student->daycareSubscriptions->flatMap->attendances->where('status', 'present')->count() }}</td>
                                <td class="text-danger">{{ $student->daycareSubscriptions->flatMap->attendances->where('status', 'absent')->count() }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">لا يوجد طلاب</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- طلاب دراسة الحالة -->
                <h6 class="mb-3 border-bottom pb-2">
                    <i class="ti ti-clipboard-check text-warning me-2"></i>
                    طلاب دراسة الحالة
                    <span class="badge bg-warning">{{ $studentsWithCases->count() }}</span>
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-warning">
                            <tr>
                                <th>#</th>
                                <th>الكود</th>
                                <th>اسم الطالب</th>
                                <th>حالة الدراسة</th>
                                <th>عدد التقييمات</th>
                                <th>تاريخ البدء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($studentsWithCases as $index => $student)
                            @php
                                $currentCase = $student->currentCase;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge bg-secondary">{{ $student->code }}</span></td>
                                <td>{{ $student->name }}</td>
                                <td>
                                    @if($currentCase)
                                        <span class="badge bg-{{ $currentCase->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ $currentCase->status === 'completed' ? 'مكتمل' : 'جاري' }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $currentCase?->invoice?->items?->count() ?? 0 }}</td>
                                <td>{{ $currentCase?->created_at?->format('Y/m/d') ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">لا يوجد طلاب</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
