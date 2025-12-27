@extends('layouts.app')

@section('title', 'سجل الحضور - ' . $subscription->student->name)

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">
                            <i class="fas fa-sun me-2" style="color: #063973;"></i>
                            سجل الحضور - الرعاية النهارية
                        </h5>
                        <span class="badge bg-secondary me-2">{{ $subscription->student->name }}</span>
                        <span class="badge bg-{{ $subscription->status_color }}">{{ $subscription->status_text }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.daycare.print', $subscription) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-print me-1"></i> طباعة
                        </a>
                        @if($subscription->invoice)
                        <a href="{{ route('admin.invoices.print', $subscription->invoice) }}" target="_blank" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-invoice me-1"></i> الفاتورة
                        </a>
                        @endif
                        <a href="{{ route('admin.students.show', $subscription->student) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i> رجوع للطالب
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- معلومات الاشتراك -->
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-header text-white" style="background: #063973;">
                        <h6 class="mb-0 text-white"><i class="fas fa-info-circle me-2"></i>معلومات الاشتراك</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-tag me-1"></i> نوع الرعاية</td>
                                    <td class="fw-bold">{{ $subscription->daycareType->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-user-tie me-1"></i> المشرف</td>
                                    <td>{{ $subscription->supervisor->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-calendar-alt me-1"></i> من تاريخ</td>
                                    <td>{{ $subscription->start_date->format('Y/m/d') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-calendar-check me-1"></i> إلى تاريخ</td>
                                    <td>{{ $subscription->end_date->format('Y/m/d') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-money-bill me-1"></i> السعر</td>
                                    <td class="fw-bold" style="color: #063973;">{{ $subscription->formatted_price }}</td>
                                </tr>
                                @if($subscription->invoice)
                                <tr>
                                    <td class="text-muted"><i class="fas fa-file-invoice me-1"></i> الفاتورة</td>
                                    <td>
                                        <span class="badge bg-{{ $subscription->invoice->status_color }}">{{ $subscription->invoice->status_text }}</span>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- إحصائيات الحضور -->
            <div class="col-md-8 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>إحصائيات الحضور</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-3">
                                <div class="p-3 rounded" style="background: #f8f9fa;">
                                    <h2 class="mb-1" style="color: #063973;">{{ $subscription->attendances->count() }}</h2>
                                    <small class="text-muted">إجمالي الأيام</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-3 rounded bg-success bg-opacity-10">
                                    <h2 class="mb-1 text-success">{{ $subscription->present_count }}</h2>
                                    <small class="text-muted">حضور</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-3 rounded bg-danger bg-opacity-10">
                                    <h2 class="mb-1 text-danger">{{ $subscription->absent_count }}</h2>
                                    <small class="text-muted">غياب</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-3 rounded bg-warning bg-opacity-10">
                                    <h2 class="mb-1 text-warning">{{ $subscription->pending_count }}</h2>
                                    <small class="text-muted">انتظار</small>
                                </div>
                            </div>
                        </div>

                        @if($subscription->attendances->count() > 0)
                        @php
                            $total = $subscription->attendances->count();
                            $presentPercent = ($subscription->present_count / $total) * 100;
                            $absentPercent = ($subscription->absent_count / $total) * 100;
                            $pendingPercent = ($subscription->pending_count / $total) * 100;
                        @endphp
                        <div class="progress mt-3" style="height: 25px;">
                            <div class="progress-bar bg-success" style="width: {{ $presentPercent }}%">
                                @if($presentPercent > 10) {{ number_format($presentPercent, 0) }}% @endif
                            </div>
                            <div class="progress-bar bg-danger" style="width: {{ $absentPercent }}%">
                                @if($absentPercent > 10) {{ number_format($absentPercent, 0) }}% @endif
                            </div>
                            <div class="progress-bar bg-warning" style="width: {{ $pendingPercent }}%">
                                @if($pendingPercent > 10) {{ number_format($pendingPercent, 0) }}% @endif
                            </div>
                        </div>
                        <div class="d-flex justify-content-center gap-3 mt-2">
                            <small><span class="badge bg-success">حضور</span></small>
                            <small><span class="badge bg-danger">غياب</span></small>
                            <small><span class="badge bg-warning">انتظار</span></small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول الحضور -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        سجل الحضور اليومي
                    </h6>
                    <small class="text-muted">اضغط على الأزرار لتغيير حالة الحضور</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>التاريخ</th>
                                <th>اليوم</th>
                                <th class="text-center" width="15%">الحالة</th>
                                <th width="20%" class="text-center">تغيير الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscription->attendances->sortBy('date') as $index => $attendance)
                            <tr id="attendance-row-{{ $attendance->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $attendance->date->format('Y/m/d') }}</span>
                                </td>
                                <td>{{ $attendance->day_name }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $attendance->status_color }} fs-6" id="status-badge-{{ $attendance->id }}">
                                        <i class="fas fa-{{ $attendance->status_icon }} me-1"></i>
                                        {{ $attendance->status_text }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" id="btn-group-{{ $attendance->id }}">
                                        <button type="button"
                                                class="btn btn-{{ $attendance->status === 'present' ? 'success' : 'outline-success' }}"
                                                onclick="setAttendance({{ $attendance->id }}, 'present')"
                                                title="حاضر">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-{{ $attendance->status === 'pending' ? 'warning' : 'outline-warning' }}"
                                                onclick="setAttendance({{ $attendance->id }}, 'pending')"
                                                title="قيد الانتظار">
                                            <i class="fas fa-clock"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-{{ $attendance->status === 'absent' ? 'danger' : 'outline-danger' }}"
                                                onclick="setAttendance({{ $attendance->id }}, 'absent')"
                                                title="غائب">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function setAttendance(attendanceId, status) {
    const badge = document.getElementById(`status-badge-${attendanceId}`);
    const btnGroup = document.getElementById(`btn-group-${attendanceId}`);

    // تعطيل الأزرار مؤقتاً
    btnGroup.querySelectorAll('button').forEach(btn => btn.disabled = true);

    fetch(`/admin/daycare-attendance/${attendanceId}/toggle`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const statusConfig = {
                'present': { color: 'success', icon: 'check-circle', text: 'حاضر' },
                'absent': { color: 'danger', icon: 'times-circle', text: 'غائب' },
                'pending': { color: 'warning', icon: 'clock', text: 'قيد الانتظار' }
            };

            const config = statusConfig[status];

            // تحديث الشارة
            badge.className = `badge bg-${config.color} fs-6`;
            badge.innerHTML = `<i class="fas fa-${config.icon} me-1"></i>${config.text}`;

            // تحديث الأزرار
            btnGroup.innerHTML = `
                <button type="button" class="btn btn-${status === 'present' ? 'success' : 'outline-success'}" onclick="setAttendance(${attendanceId}, 'present')" title="حاضر">
                    <i class="fas fa-check"></i>
                </button>
                <button type="button" class="btn btn-${status === 'pending' ? 'warning' : 'outline-warning'}" onclick="setAttendance(${attendanceId}, 'pending')" title="قيد الانتظار">
                    <i class="fas fa-clock"></i>
                </button>
                <button type="button" class="btn btn-${status === 'absent' ? 'danger' : 'outline-danger'}" onclick="setAttendance(${attendanceId}, 'absent')" title="غائب">
                    <i class="fas fa-times"></i>
                </button>
            `;
        } else {
            alert(data.message || 'حدث خطأ');
            btnGroup.querySelectorAll('button').forEach(btn => btn.disabled = false);
        }
    })
    .catch(error => {
        alert('حدث خطأ في الاتصال');
        console.error(error);
        btnGroup.querySelectorAll('button').forEach(btn => btn.disabled = false);
    });
}
</script>
@endpush
