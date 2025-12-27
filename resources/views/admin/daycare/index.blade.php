@extends('layouts.app')

@section('title', 'الرعاية النهارية')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header with Date Picker -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="mb-1">
                            <i class="fas fa-sun me-2" style="color: #063973;"></i>
                            الرعاية النهارية
                        </h5>
                        <small class="text-muted">إدارة حضور الطلاب اليومي</small>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeDate(-1)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <input type="date" id="dateInput" class="form-control" value="{{ $date }}" style="width: 160px;" onchange="goToDate()">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeDate(1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="goToToday()">
                            <i class="fas fa-calendar-day me-1"></i> اليوم
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-3">
            <div class="col-md-3 col-6 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center py-3">
                        <h2 class="mb-1" style="color: #063973;">{{ $stats['total'] }}</h2>
                        <small class="text-muted">إجمالي الطلاب</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="card h-100 border-0 shadow-sm bg-success bg-opacity-10">
                    <div class="card-body text-center py-3">
                        <h2 class="mb-1 text-success">{{ $stats['present'] }}</h2>
                        <small class="text-muted">حاضر</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="card h-100 border-0 shadow-sm bg-danger bg-opacity-10">
                    <div class="card-body text-center py-3">
                        <h2 class="mb-1 text-danger">{{ $stats['absent'] }}</h2>
                        <small class="text-muted">غائب</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="card h-100 border-0 shadow-sm bg-warning bg-opacity-10">
                    <div class="card-body text-center py-3">
                        <h2 class="mb-1 text-warning">{{ $stats['pending'] }}</h2>
                        <small class="text-muted">قيد الانتظار</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Info -->
        <div class="alert alert-info d-flex align-items-center mb-3">
            <i class="fas fa-calendar-alt fa-lg me-3"></i>
            <div>
                <strong>{{ \Carbon\Carbon::parse($date)->locale('ar')->translatedFormat('l j F Y') }}</strong>
                @if($date == now()->format('Y-m-d'))
                    <span class="badge bg-primary ms-2">اليوم</span>
                @endif
            </div>
        </div>

        <!-- Students Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        قائمة الطلاب
                    </h6>
                    <small class="text-muted">اضغط على الأزرار لتغيير حالة الحضور</small>
                </div>
            </div>
            <div class="card-body p-0">
                @if($attendances->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>الطالب</th>
                                <th>نوع الرعاية</th>
                                <th>المشرف</th>
                                <th class="text-center" width="12%">الحالة</th>
                                <th width="18%" class="text-center">تغيير الحالة</th>
                                <th width="20%">ملاحظات المشرف</th>
                                <th width="6%" class="text-center">تفاصيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $index => $attendance)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.students.show', $attendance->subscription->student) }}" class="text-decoration-none">
                                        <strong>{{ $attendance->subscription->student->name }}</strong>
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $attendance->subscription->student->code }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $attendance->subscription->daycareType->name ?? '-' }}</span>
                                </td>
                                <td>{{ $attendance->subscription->supervisor->name ?? '-' }}</td>
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
                                <td>
                                    @if($attendance->notes)
                                    <small class="text-muted">
                                        <i class="fas fa-comment text-primary me-1"></i>
                                        {{ $attendance->notes }}
                                    </small>
                                    @else
                                    <small class="text-muted opacity-50">-</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.daycare.show', $attendance->subscription) }}" class="btn btn-sm btn-outline-primary" title="سجل الحضور">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-sun fa-4x mb-3 opacity-25"></i>
                    <p class="mb-0">لا يوجد طلاب مسجلين في الرعاية النهارية لهذا اليوم</p>
                    <small>
                        @if(\Carbon\Carbon::parse($date)->isFriday() || \Carbon\Carbon::parse($date)->isSaturday())
                            هذا اليوم عطلة نهاية الأسبوع
                        @else
                            جرب اختيار تاريخ آخر
                        @endif
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function changeDate(days) {
    const dateInput = document.getElementById('dateInput');
    const currentDate = new Date(dateInput.value);
    currentDate.setDate(currentDate.getDate() + days);
    dateInput.value = currentDate.toISOString().split('T')[0];
    goToDate();
}

function goToDate() {
    const date = document.getElementById('dateInput').value;
    window.location.href = `{{ route('admin.daycare.index') }}?date=${date}`;
}

function goToToday() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('dateInput').value = today;
    goToDate();
}

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

            // تحديث الإحصائيات (إعادة تحميل الصفحة بسيط)
            // يمكن تحسينه لاحقاً بدون إعادة تحميل
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
