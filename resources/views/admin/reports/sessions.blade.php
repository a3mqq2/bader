@extends('layouts.app')

@section('title', 'تقرير الجلسات')

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
                    <div class="col-md-2">
                        <label class="form-label">حالة الجلسة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                            <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>غياب</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">نوع الجلسة</label>
                        <select name="therapy_session_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($therapySessions as $session)
                                <option value="{{ $session->id }}" {{ request('therapy_session_id') == $session->id ? 'selected' : '' }}>
                                    {{ $session->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-search me-1"></i> بحث
                        </button>
                        <a href="{{ route('admin.reports.sessions') }}" class="btn btn-outline-secondary">
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
                    <i class="ti ti-clock me-2"></i>
                    تقرير الجلسات العلاجية
                    <span class="badge bg-success ms-2">{{ $sessions->count() }}</span>
                </h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reports.sessions', array_merge(request()->query(), ['print' => 1])) }}"
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
                    <div class="col-md-2">
                        <div class="bg-primary bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-primary">{{ $stats['total'] }}</h4>
                            <small class="text-muted">إجمالي الجلسات</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="bg-info bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-info">{{ $stats['scheduled'] }}</h4>
                            <small class="text-muted">مجدولة</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="bg-success bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-success">{{ $stats['completed'] }}</h4>
                            <small class="text-muted">مكتملة</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="bg-danger bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-danger">{{ $stats['absent'] }}</h4>
                            <small class="text-muted">غياب</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="bg-secondary bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-secondary">{{ $stats['cancelled'] }}</h4>
                            <small class="text-muted">ملغية</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="bg-warning bg-opacity-10 rounded p-3 text-center">
                            <h4 class="mb-1 text-warning">{{ number_format($stats['completion_rate'], 1) }}%</h4>
                            <small class="text-muted">نسبة الإكمال</small>
                        </div>
                    </div>
                </div>

                <!-- جدول البيانات -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-success">
                            <tr>
                                <th>#</th>
                                <th>الطالب</th>
                                <th>نوع الجلسة</th>
                                <th>الأخصائي</th>
                                <th>التاريخ</th>
                                <th>الوقت</th>
                                <th>الحالة</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $index => $session)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold">{{ $session->package?->student?->name ?? '-' }}</span>
                                    <br><small class="text-muted">{{ $session->package?->student?->code ?? '' }}</small>
                                </td>
                                <td>{{ $session->package?->therapySession?->name ?? '-' }}</td>
                                <td>{{ $session->package?->specialist?->name ?? '-' }}</td>
                                <td>{{ $session->session_date?->format('Y/m/d') ?? '-' }}</td>
                                <td>{{ $session->session_time ?? '-' }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'scheduled' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'secondary',
                                            'absent' => 'danger'
                                        ];
                                        $statusTexts = [
                                            'scheduled' => 'مجدولة',
                                            'completed' => 'مكتملة',
                                            'cancelled' => 'ملغية',
                                            'absent' => 'غياب'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$session->status] ?? 'secondary' }}">
                                        {{ $statusTexts[$session->status] ?? $session->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($session->notes)
                                        <small>{{ Str::limit($session->notes, 30) }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="ti ti-calendar-off fa-2x mb-2 d-block"></i>
                                    لا توجد جلسات
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- إحصائيات حسب نوع الجلسة -->
                @if($sessionsByType->count() > 0)
                <div class="mt-4 pt-4 border-top">
                    <h6 class="mb-3">
                        <i class="ti ti-chart-pie text-primary me-2"></i>
                        توزيع الجلسات حسب النوع
                    </h6>
                    <div class="row">
                        @foreach($sessionsByType as $type)
                        <div class="col-md-3 mb-3">
                            <div class="card border h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-primary mb-2">{{ $type->therapySession?->name ?? 'غير محدد' }}</h6>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">عدد الجلسات:</span>
                                        <span class="fw-bold">{{ $type->total }}</span>
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
