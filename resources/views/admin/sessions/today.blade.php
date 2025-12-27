@extends('layouts.app')

@section('title', 'جلسات اليوم')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-day me-2 text-success"></i>
                            جلسات اليوم
                            <span class="badge bg-success ms-2">{{ today()->format('Y/m/d') }}</span>
                        </h5>
                        <small class="text-muted">{{ today()->locale('ar')->translatedFormat('l') }} - {{ $sessions->count() }} جلسة</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.sessions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-1"></i> كل الجلسات
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="row mb-3">
            @php
                $scheduled = $sessions->where('status', 'scheduled')->count();
                $completed = $sessions->where('status', 'completed')->count();
                $postponed = $sessions->where('status', 'postponed')->count();
                $cancelled = $sessions->where('status', 'cancelled')->count();
            @endphp
            <div class="col-6 col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body py-3 text-center">
                        <h3 class="mb-0">{{ $scheduled }}</h3>
                        <small>مجدولة</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body py-3 text-center">
                        <h3 class="mb-0">{{ $completed }}</h3>
                        <small>تمت</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body py-3 text-center">
                        <h3 class="mb-0">{{ $postponed }}</h3>
                        <small>مؤجلة</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body py-3 text-center">
                        <h3 class="mb-0">{{ $cancelled }}</h3>
                        <small>ملغاة</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sessions Table -->
        <div class="card">
            <div class="card-body p-0">
                @if($sessions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>الطالب</th>
                                <th>نوع الجلسة</th>
                                <th>الوقت</th>
                                <th>الأخصائي</th>
                                <th width="12%">الحالة</th>
                                <th width="10%" class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $index => $session)
                            <tr id="session-row-{{ $session->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.students.show', $session->student_id) }}" class="text-decoration-none">
                                        <strong>{{ $session->student->name ?? '-' }}</strong>
                                    </a>
                                    <br><small class="text-muted">{{ $session->student->code ?? '' }}</small>
                                </td>
                                <td>{{ $session->package->therapySession->name ?? '-' }}</td>
                                <td>
                                    <span class="fw-bold" style="color: #063973;">{{ $session->formatted_time }}</span>
                                    <br><small class="text-muted">{{ $session->duration ?? 30 }} دقيقة</small>
                                </td>
                                <td>{{ $session->specialist->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $session->status_color }}" id="status-badge-{{ $session->id }}">
                                        {{ $session->status_text }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" onclick="openEditModal({{ $session->id }})" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if($session->status === 'scheduled')
                                        <button type="button" class="btn btn-outline-success" onclick="quickComplete({{ $session->id }})" title="تمت">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-calendar-check fa-4x mb-3 opacity-25"></i>
                    <p class="mb-0">لا توجد جلسات مجدولة لليوم</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Session Modal -->
<div class="modal fade" id="editSessionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: #063973;">
                <h5 class="modal-title text-white"><i class="fas fa-edit me-2"></i>تعديل الجلسة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="editSessionLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                </div>
                <form id="editSessionForm" style="display: none;">
                    <input type="hidden" id="editSessionId">

                    <!-- معلومات الطالب -->
                    <div class="alert alert-light mb-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: #063973;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <strong id="editStudentName">-</strong>
                                <br><small class="text-muted" id="editSessionType">-</small>
                            </div>
                        </div>
                    </div>

                    <!-- الأخصائي -->
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fas fa-user-md me-1"></i> الأخصائي <span class="text-danger">*</span></label>
                        <select name="specialist_id" id="editSpecialistId" class="form-select" required>
                            <option value="">-- اختر الأخصائي --</option>
                            @foreach($specialists as $specialist)
                            <option value="{{ $specialist->id }}">{{ $specialist->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- التاريخ والوقت -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold"><i class="fas fa-calendar me-1"></i> التاريخ <span class="text-danger">*</span></label>
                            <input type="date" name="session_date" id="editSessionDate" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold"><i class="fas fa-clock me-1"></i> الوقت <span class="text-danger">*</span></label>
                            <input type="time" name="session_time" id="editSessionTime" class="form-control" required>
                        </div>
                    </div>

                    <!-- الحالة -->
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fas fa-toggle-on me-1"></i> الحالة <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusScheduled" value="scheduled">
                                <label class="form-check-label" for="statusScheduled">
                                    <span class="badge bg-primary">مجدولة</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusCompleted" value="completed">
                                <label class="form-check-label" for="statusCompleted">
                                    <span class="badge bg-success">تمت</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusPostponed" value="postponed">
                                <label class="form-check-label" for="statusPostponed">
                                    <span class="badge bg-warning">مؤجلة</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusCancelled" value="cancelled">
                                <label class="form-check-label" for="statusCancelled">
                                    <span class="badge bg-secondary">ملغاة</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- الملاحظات -->
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-sticky-note me-1"></i> ملاحظات</label>
                        <textarea name="notes" id="editSessionNotes" class="form-control" rows="2" placeholder="أي ملاحظات..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> إلغاء
                </button>
                <button type="button" class="btn text-white" style="background: #063973;" id="btnUpdateSession">
                    <i class="fas fa-save me-1"></i> حفظ التغييرات
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentSessionId = null;
const editSessionModal = new bootstrap.Modal(document.getElementById('editSessionModal'));

const statusColors = {
    'scheduled': 'primary',
    'completed': 'success',
    'postponed': 'warning',
    'cancelled': 'secondary'
};

const statusTexts = {
    'scheduled': 'مجدولة',
    'completed': 'تمت',
    'postponed': 'مؤجلة',
    'cancelled': 'ملغاة'
};

function openEditModal(sessionId) {
    currentSessionId = sessionId;
    document.getElementById('editSessionLoading').style.display = 'block';
    document.getElementById('editSessionForm').style.display = 'none';

    editSessionModal.show();

    fetch(`{{ url('admin/sessions') }}/${sessionId}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const session = data.session;
            document.getElementById('editSessionId').value = session.id;
            document.getElementById('editStudentName').textContent = session.student?.name || '-';
            document.getElementById('editSessionType').textContent = session.package?.therapy_session?.name || '-';
            document.getElementById('editSpecialistId').value = session.specialist_id;
            document.getElementById('editSessionDate').value = session.session_date.split('T')[0];
            document.getElementById('editSessionTime').value = session.session_time;
            document.getElementById('editSessionNotes').value = session.notes || '';

            // تحديد الحالة
            document.querySelectorAll('input[name="status"]').forEach(radio => {
                radio.checked = radio.value === session.status;
            });

            document.getElementById('editSessionLoading').style.display = 'none';
            document.getElementById('editSessionForm').style.display = 'block';
        } else {
            alert(data.message || 'حدث خطأ');
            editSessionModal.hide();
        }
    })
    .catch(error => {
        console.error(error);
        alert('حدث خطأ في الاتصال');
        editSessionModal.hide();
    });
}

// إكمال سريع للجلسة
function quickComplete(sessionId) {
    if (!confirm('هل تريد تحديد هذه الجلسة كـ "تمت"؟')) return;

    fetch(`{{ url('admin/sessions') }}/${sessionId}`, {
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            status: 'completed',
            specialist_id: null, // will be ignored if not changed
            session_date: null,
            session_time: null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        console.error(error);
        alert('حدث خطأ في الاتصال');
    });
}

// حفظ التغييرات
document.getElementById('btnUpdateSession').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...';

    const status = document.querySelector('input[name="status"]:checked')?.value;
    if (!status) {
        alert('يرجى اختيار حالة الجلسة');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ التغييرات';
        return;
    }

    const formData = {
        specialist_id: document.getElementById('editSpecialistId').value,
        session_date: document.getElementById('editSessionDate').value,
        session_time: document.getElementById('editSessionTime').value,
        status: status,
        notes: document.getElementById('editSessionNotes').value
    };

    fetch(`{{ url('admin/sessions') }}/${currentSessionId}`, {
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            editSessionModal.hide();
            window.location.reload();
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ التغييرات';
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        console.error(error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ التغييرات';
        alert('حدث خطأ في الاتصال');
    });
});

// إعادة تعيين Modal
document.getElementById('editSessionModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('editSessionForm').reset();
    document.getElementById('btnUpdateSession').disabled = false;
    document.getElementById('btnUpdateSession').innerHTML = '<i class="fas fa-save me-1"></i> حفظ التغييرات';
});
</script>
@endpush
