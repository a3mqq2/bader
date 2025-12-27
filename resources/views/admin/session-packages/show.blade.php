@extends('layouts.app')

@section('title', 'تفاصيل باقة الجلسات')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ $package->therapySession->name ?? 'باقة جلسات' }}</h5>
                        <span class="badge bg-info me-2">{{ $package->sessions_count }} جلسة</span>
                        <span class="badge bg-success">{{ number_format($package->total_price, 2) }} د.ل</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.session-packages.print', $package) }}" target="_blank" class="btn btn-sm btn-primary">
                            <i class="fas fa-print me-1"></i> طباعة
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deletePackageModal">
                            <i class="fas fa-trash me-1"></i> حذف
                        </button>
                        <a href="{{ route('admin.students.show', $package->student) }}#sessions" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i> رجوع للطالب
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- معلومات الباقة -->
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-header" style="background: #063973;">
                        <h6 class="mb-0 text-white"><i class="fas fa-info-circle me-1"></i> معلومات الباقة</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted" width="40%"><i class="fas fa-user me-1"></i> الطالب</td>
                                    <td class="fw-semibold">
                                        <a href="{{ route('admin.students.show', $package->student) }}">{{ $package->student->name }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-tag me-1"></i> نوع الجلسة</td>
                                    <td>{{ $package->therapySession->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-user-md me-1"></i> الأخصائي</td>
                                    <td>{{ $package->specialist->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-calendar me-1"></i> من تاريخ</td>
                                    <td>{{ $package->start_date->format('Y/m/d') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-calendar me-1"></i> إلى تاريخ</td>
                                    <td>{{ $package->end_date->format('Y/m/d') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-clock me-1"></i> وقت الجلسة</td>
                                    <td>{{ date('h:i A', strtotime($package->session_time)) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-hourglass me-1"></i> مدة الجلسة</td>
                                    <td>{{ $package->session_duration }} دقيقة</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-calendar-week me-1"></i> الأيام</td>
                                    <td>{{ $package->days_text }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- إحصائيات -->
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-header" style="background: #063973;">
                        <h6 class="mb-0 text-white"><i class="fas fa-chart-pie me-1"></i> إحصائيات الجلسات</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $scheduledCount = $package->sessions->where('status', 'scheduled')->count();
                            $completedCount = $package->sessions->where('status', 'completed')->count();
                            $cancelledCount = $package->sessions->where('status', 'cancelled')->count();
                            $absentCount = $package->sessions->where('status', 'absent')->count();
                            $totalSessions = $package->sessions->count();
                            $completedPercent = $totalSessions > 0 ? ($completedCount / $totalSessions) * 100 : 0;
                        @endphp

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>التقدم</span>
                                <span class="fw-bold">{{ $completedCount }}/{{ $totalSessions }}</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completedPercent }}%"></div>
                            </div>
                        </div>

                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="p-2 rounded" style="background: #cce5ff;">
                                    <div class="fs-4 fw-bold text-primary">{{ $scheduledCount }}</div>
                                    <small class="text-primary">مجدولة</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-2 rounded" style="background: #d4edda;">
                                    <div class="fs-4 fw-bold text-success">{{ $completedCount }}</div>
                                    <small class="text-success">مكتملة</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded" style="background: #f8d7da;">
                                    <div class="fs-4 fw-bold text-danger">{{ $absentCount }}</div>
                                    <small class="text-danger">غائب</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded" style="background: #e2e3e5;">
                                    <div class="fs-4 fw-bold text-secondary">{{ $cancelledCount }}</div>
                                    <small class="text-secondary">ملغاة</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات إضافية -->
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-header" style="background: #063973;">
                        <h6 class="mb-0 text-white"><i class="fas fa-money-bill me-1"></i> المعلومات المالية</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">سعر الجلسة الواحدة</span>
                            <span class="fw-bold">{{ $package->therapySession->formatted_price ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">عدد الجلسات</span>
                            <span class="fw-bold">{{ $package->sessions_count }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">الإجمالي</span>
                            <span class="fs-5 fw-bold" style="color: #063973;">{{ number_format($package->total_price, 2) }} د.ل</span>
                        </div>

                        @if($package->notes)
                        <hr>
                        <div>
                            <small class="text-muted"><i class="fas fa-sticky-note me-1"></i> ملاحظات:</small>
                            <p class="mb-0 mt-1 small">{{ $package->notes }}</p>
                        </div>
                        @endif

                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-user-plus me-1"></i> أنشئت بواسطة: {{ $package->creator->name ?? '-' }}<br>
                            <i class="fas fa-calendar-plus me-1"></i> {{ $package->created_at->format('Y/m/d h:i A') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول الجلسات -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-list me-1"></i> جدول الجلسات</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>اليوم</th>
                                <th>التاريخ</th>
                                <th>الوقت</th>
                                <th>الحالة</th>
                                <th>ملاحظات</th>
                                <th width="15%" class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($package->sessions->sortBy('session_date') as $index => $session)
                            <tr id="session-row-{{ $session->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $session->day_name }}</td>
                                <td>{{ $session->session_date->format('Y/m/d') }}</td>
                                <td>{{ $session->formatted_time }}</td>
                                <td>
                                    <span class="badge bg-{{ $session->status_color }}">{{ $session->status_text }}</span>
                                </td>
                                <td>
                                    @if($session->notes)
                                    <small class="text-muted">{{ Str::limit($session->notes, 30) }}</small>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="editSession({{ $session->id }}, '{{ $session->session_date->format('Y-m-d') }}', '{{ $session->session_time }}', '{{ $session->status }}', '{{ addslashes($session->notes ?? '') }}')" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSession({{ $session->id }})" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

<!-- Edit Session Modal -->
<div class="modal fade" id="editSessionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: #063973;">
                <h5 class="modal-title text-white"><i class="fas fa-edit me-2"></i>تعديل الجلسة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editSessionForm">
                    <input type="hidden" id="editSessionId">

                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fas fa-calendar-alt me-1"></i> التاريخ <span class="text-danger">*</span></label>
                        <input type="date" id="editSessionDate" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fas fa-clock me-1"></i> الوقت <span class="text-danger">*</span></label>
                        <input type="time" id="editSessionTime" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fas fa-flag me-1"></i> الحالة <span class="text-danger">*</span></label>
                        <select id="editSessionStatus" class="form-select" required>
                            <option value="scheduled">مجدولة</option>
                            <option value="completed">مكتملة</option>
                            <option value="cancelled">ملغاة</option>
                            <option value="absent">غائب</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-sticky-note me-1"></i> ملاحظات</label>
                        <textarea id="editSessionNotes" class="form-control" rows="2" placeholder="أي ملاحظات..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> إلغاء
                </button>
                <button type="button" class="btn text-white" style="background: #063973;" id="btnUpdateSession">
                    <i class="fas fa-save me-1"></i> تحديث
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Session Modal -->
<div class="modal fade" id="deleteSessionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title"><i class="fas fa-exclamation-triangle me-1"></i> تأكيد حذف الجلسة</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-3">
                <p class="mb-0">هل أنت متأكد من حذف هذه الجلسة؟</p>
            </div>
            <div class="modal-footer justify-content-center py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-sm btn-danger" id="btnConfirmDeleteSession">
                    <i class="fas fa-trash me-1"></i> حذف
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Package Modal -->
<div class="modal fade" id="deletePackageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title"><i class="fas fa-exclamation-triangle me-1"></i> تأكيد حذف الباقة</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-3">
                <p class="mb-1">هل أنت متأكد من حذف هذه الباقة؟</p>
                <small class="text-danger">سيتم حذف جميع الجلسات المرتبطة بها</small>
            </div>
            <div class="modal-footer justify-content-center py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-sm btn-danger" id="btnConfirmDeletePackage">
                    <i class="fas fa-trash me-1"></i> حذف
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let deleteSessionId = null;

// تعديل جلسة
function editSession(id, date, time, status, notes) {
    document.getElementById('editSessionId').value = id;
    document.getElementById('editSessionDate').value = date;
    document.getElementById('editSessionTime').value = time;
    document.getElementById('editSessionStatus').value = status;
    document.getElementById('editSessionNotes').value = notes || '';
    new bootstrap.Modal(document.getElementById('editSessionModal')).show();
}

// تحديث الجلسة
document.getElementById('btnUpdateSession').addEventListener('click', function() {
    const sessionId = document.getElementById('editSessionId').value;
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    fetch(`{{ url('admin/student-sessions') }}/${sessionId}`, {
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            session_date: document.getElementById('editSessionDate').value,
            session_time: document.getElementById('editSessionTime').value,
            status: document.getElementById('editSessionStatus').value,
            notes: document.getElementById('editSessionNotes').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> تحديث';
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        console.error(error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> تحديث';
        alert('حدث خطأ في الاتصال');
    });
});

// حذف جلسة
function deleteSession(id) {
    deleteSessionId = id;
    new bootstrap.Modal(document.getElementById('deleteSessionModal')).show();
}

document.getElementById('btnConfirmDeleteSession').addEventListener('click', function() {
    if (!deleteSessionId) return;

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    fetch(`{{ url('admin/student-sessions') }}/${deleteSessionId}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-trash me-1"></i> حذف';
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        console.error(error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash me-1"></i> حذف';
        alert('حدث خطأ في الاتصال');
    });
});

// حذف الباقة
document.getElementById('btnConfirmDeletePackage').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    fetch(`{{ route('admin.session-packages.destroy', $package) }}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route('admin.students.show', $package->student) }}#sessions';
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-trash me-1"></i> حذف';
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        console.error(error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash me-1"></i> حذف';
        alert('حدث خطأ في الاتصال');
    });
});
</script>
@endpush
