@extends('layouts.app')

@section('title', 'تقرير الغياب')

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
                        <label class="form-label">نوع الغياب</label>
                        <select name="absence_type" class="form-select">
                            <option value="">الكل</option>
                            <option value="sessions" {{ request('absence_type') == 'sessions' ? 'selected' : '' }}>جلسات</option>
                            <option value="daycare" {{ request('absence_type') == 'daycare' ? 'selected' : '' }}>رعاية نهارية</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-search me-1"></i> بحث
                        </button>
                        <a href="{{ route('admin.reports.absence') }}" class="btn btn-outline-secondary">
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
                    <i class="ti ti-calendar-off me-2"></i>
                    تقرير الغياب
                </h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reports.absence', array_merge(request()->query(), ['print' => 1])) }}"
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
                        <div class="bg-danger bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-danger">{{ $stats['total_absences'] }}</h4>
                            <small class="text-muted">إجمالي الغياب</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-warning bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-warning">{{ $stats['session_absences'] }}</h4>
                            <small class="text-muted">غياب الجلسات</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-info bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-info">{{ $stats['daycare_absences'] }}</h4>
                            <small class="text-muted">غياب الرعاية النهارية</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-success bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-success">{{ $stats['excused_absences'] }}</h4>
                            <small class="text-muted">غياب بإذن</small>
                        </div>
                    </div>
                </div>

                <!-- غياب الجلسات -->
                @if(!request('absence_type') || request('absence_type') == 'sessions')
                <h6 class="mb-3 border-bottom pb-2">
                    <i class="ti ti-clock text-warning me-2"></i>
                    غياب الجلسات العلاجية
                    <span class="badge bg-warning">{{ $sessionAbsences->count() }}</span>
                </h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-warning">
                            <tr>
                                <th>#</th>
                                <th>الطالب</th>
                                <th>نوع الجلسة</th>
                                <th>الأخصائي</th>
                                <th>التاريخ</th>
                                <th>غياب بإذن</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessionAbsences as $index => $session)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold">{{ $session->package?->student?->name ?? '-' }}</span>
                                    <br><small class="text-muted">{{ $session->package?->student?->code ?? '' }}</small>
                                </td>
                                <td>{{ $session->package?->therapySession?->name ?? '-' }}</td>
                                <td>{{ $session->package?->specialist?->name ?? '-' }}</td>
                                <td>{{ $session->session_date?->format('Y/m/d') ?? '-' }}</td>
                                <td>
                                    @if($session->is_excused)
                                        <span class="badge bg-success">نعم</span>
                                    @else
                                        <span class="badge bg-danger">لا</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($session->notes, 30) ?: '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">لا يوجد غياب</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif

                <!-- غياب الرعاية النهارية -->
                @if(!request('absence_type') || request('absence_type') == 'daycare')
                <h6 class="mb-3 border-bottom pb-2">
                    <i class="ti ti-home-off text-info me-2"></i>
                    غياب الرعاية النهارية
                    <span class="badge bg-info">{{ $daycareAbsences->count() }}</span>
                </h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-info">
                            <tr>
                                <th>#</th>
                                <th>الطالب</th>
                                <th>نوع الرعاية</th>
                                <th>التاريخ</th>
                                <th>غياب بإذن</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($daycareAbsences as $index => $attendance)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold">{{ $attendance->subscription?->student?->name ?? '-' }}</span>
                                    <br><small class="text-muted">{{ $attendance->subscription?->student?->code ?? '' }}</small>
                                </td>
                                <td>{{ $attendance->subscription?->daycareType?->name ?? '-' }}</td>
                                <td>{{ $attendance->date?->format('Y/m/d') ?? '-' }}</td>
                                <td>
                                    @if($attendance->is_excused)
                                        <span class="badge bg-success">نعم</span>
                                    @else
                                        <span class="badge bg-danger">لا</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($attendance->notes, 30) ?: '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">لا يوجد غياب</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif

                <!-- الطلاب الأكثر غياباً -->
                @if($topAbsentStudents->count() > 0)
                <div class="mt-4 pt-4 border-top">
                    <h6 class="mb-3">
                        <i class="ti ti-alert-triangle text-danger me-2"></i>
                        الطلاب الأكثر غياباً
                    </h6>
                    <div class="row">
                        @foreach($topAbsentStudents->take(6) as $student)
                        <div class="col-md-2 mb-3">
                            <div class="card border-danger h-100">
                                <div class="card-body text-center p-3">
                                    <h5 class="text-danger mb-1">{{ $student->total_absences }}</h5>
                                    <small class="text-muted d-block">{{ Str::limit($student->name, 15) }}</small>
                                    <small class="badge bg-secondary mt-1">{{ $student->code }}</small>
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
