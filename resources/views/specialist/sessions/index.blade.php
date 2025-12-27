@extends('layouts.app')

@section('title', 'جلساتي')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-check me-2" style="color: #063973;"></i>
                            جلساتي
                        </h5>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($date)->locale('ar')->translatedFormat('l j F Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($search) && $search)
        @if(isset($studentNotFound) && $studentNotFound)
        <!-- رسالة عدم العثور على الطالب -->
        <div class="alert alert-danger d-flex justify-content-between align-items-center mb-3">
            <div>
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>لم يتم العثور على الطالب</strong>
                <span class="ms-2">- الكود: {{ $search }}</span>
            </div>
            <a href="{{ route('specialist.sessions.index', ['date' => $date]) }}" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-times me-1"></i> إغلاق
            </a>
        </div>
        @endif

        @if(isset($searchedStudent) && $searchedStudent)
        <!-- بطاقة الطالب المبحوث عنه -->
        <div class="card border-primary mb-4 shadow">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-user-check me-2"></i>
                        <strong>جلسة اليوم للطالب</strong>
                    </div>
                    <span class="badge bg-light text-primary">{{ $searchedStudent->student->code ?? '' }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 80px; height: 80px; background: #e3f2fd;">
                            <i class="fas fa-user fa-2x text-primary"></i>
                        </div>
                        <h5 class="mb-1">{{ $searchedStudent->student->name ?? '-' }}</h5>
                        <span class="badge bg-secondary">{{ $searchedStudent->package->therapySession->name ?? '-' }}</span>
                    </div>
                    <div class="col-md-8">
                        <div class="row g-3 mb-3">
                            <div class="col-4 text-center">
                                <div class="p-2 rounded" style="background: #f8f9fa;">
                                    <i class="fas fa-clock text-primary"></i>
                                    <div class="small text-muted">الوقت</div>
                                    <strong>{{ $searchedStudent->formatted_time }}</strong>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="p-2 rounded" style="background: #f8f9fa;">
                                    <i class="fas fa-hourglass-half text-primary"></i>
                                    <div class="small text-muted">المدة</div>
                                    <strong>{{ $searchedStudent->duration ?? 30 }} د</strong>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="p-2 rounded" style="background: #f8f9fa;">
                                    <i class="fas fa-info-circle text-primary"></i>
                                    <div class="small text-muted">الحالة</div>
                                    <span class="badge bg-{{ $searchedStudent->status_color }}">{{ $searchedStudent->status_text }}</span>
                                </div>
                            </div>
                        </div>

                        @if($searchedStudent->status === 'scheduled')
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success flex-fill" onclick="quickComplete({{ $searchedStudent->id }})">
                                <i class="fas fa-check me-1"></i> تمت الجلسة
                            </button>
                            <button type="button" class="btn btn-danger flex-fill" onclick="quickAbsent({{ $searchedStudent->id }})">
                                <i class="fas fa-user-slash me-1"></i> غائب
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="openSessionModal({{ $searchedStudent->id }})">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        @else
                        <div class="text-center">
                            <button type="button" class="btn btn-outline-primary" onclick="openSessionModal({{ $searchedStudent->id }})">
                                <i class="fas fa-eye me-1"></i> عرض التفاصيل
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <form action="{{ route('specialist.sessions.index') }}" method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small mb-1">التاريخ</label>
                            <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">الحالة</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">الكل</option>
                                @foreach($statuses as $key => $value)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">البحث</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="اسم الطالب..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-sm text-white flex-grow-1" style="background: #063973;">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                <a href="{{ route('specialist.sessions.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- Sessions Cards -->
        @if($sessions->count() > 0)
        <div class="row">
            @foreach($sessions as $session)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 border-0 shadow-sm session-card" id="session-card-{{ $session->id }}">
                    <!-- Card Header with Status -->
                    @php
                        $headerColors = [
                            'scheduled' => '#063973',
                            'completed' => '#198754',
                            'absent' => '#dc3545',
                            'postponed' => '#ffc107',
                            'cancelled' => '#6c757d'
                        ];
                    @endphp
                    <div class="card-header border-0 py-2 d-flex justify-content-between align-items-center"
                         style="background: {{ $headerColors[$session->status] ?? '#6c757d' }};">
                        <div>
                            @if($searchingStudent ?? false)
                            <span class="badge bg-white text-dark me-1">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $session->session_date->format('m/d') }}
                            </span>
                            @endif
                            <span class="badge bg-white text-dark">
                                <i class="fas fa-clock me-1"></i>
                                {{ $session->formatted_time }}
                            </span>
                        </div>
                        <span class="badge bg-white bg-opacity-25 text-white" id="status-text-{{ $session->id }}">
                            {{ $session->status_text }}
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- Student Info -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 50px; height: 50px; background: linear-gradient(135deg, #063973 0%, #0a5299 100%);">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                @if($session->student)
                                <a href="{{ route('specialist.students.show', $session->student) }}" class="text-decoration-none">
                                    <h6 class="mb-0 fw-bold text-dark">{{ $session->student->name }}</h6>
                                </a>
                                @else
                                <h6 class="mb-0 fw-bold">-</h6>
                                @endif
                                <small class="text-muted">{{ $session->student->code ?? '' }}</small>
                            </div>
                        </div>

                        <!-- Session Type -->
                        <div class="mb-3">
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-tag me-1"></i>
                                {{ $session->package->therapySession->name ?? '-' }}
                            </span>
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-hourglass-half me-1"></i>
                                {{ $session->duration ?? 30 }} دقيقة
                            </span>
                        </div>

                        <!-- Notes Preview -->
                        @if($session->notes)
                        <div class="alert alert-light mb-0 py-2 small">
                            <i class="fas fa-sticky-note me-1 text-muted"></i>
                            {{ Str::limit($session->notes, 60) }}
                        </div>
                        @endif
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary flex-grow-1" onclick="openSessionModal({{ $session->id }})">
                                <i class="fas fa-edit me-1"></i> تفاصيل
                            </button>
                            @if($session->status === 'scheduled')
                            <button type="button" class="btn btn-sm btn-success" onclick="quickComplete({{ $session->id }})" id="complete-btn-{{ $session->id }}">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="quickAbsent({{ $session->id }})" id="absent-btn-{{ $session->id }}">
                                <i class="fas fa-user-slash"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: #f8f9fa;">
                        <i class="fas fa-calendar-check fa-2x text-muted"></i>
                    </div>
                </div>
                <h5 class="text-muted mb-2">لا توجد جلسات</h5>
                <p class="text-muted small mb-0">لا توجد جلسات مجدولة لهذا اليوم</p>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Session Details Modal -->
<div class="modal fade" id="sessionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: #063973;">
                <h5 class="modal-title text-white"><i class="fas fa-calendar-check me-2"></i>تفاصيل الجلسة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="sessionLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                </div>
                <div id="sessionContent" style="display: none;">
                    <input type="hidden" id="modalSessionId">
                    <input type="hidden" id="modalSessionStatus">

                    <!-- Student Info -->
                    <div class="card mb-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width: 50px; height: 50px; background: #063973;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <a href="#" id="modalStudentLink" class="text-decoration-none">
                                        <h6 class="mb-0 fw-bold text-dark" id="modalStudentName">-</h6>
                                    </a>
                                    <small class="text-muted" id="modalSessionType">-</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Session Info -->
                    <div class="row mb-3">
                        <div class="col-4">
                            <div class="text-center p-2 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-calendar text-primary mb-1"></i>
                                <div class="small text-muted">التاريخ</div>
                                <strong id="modalSessionDate">-</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-2 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-clock text-primary mb-1"></i>
                                <div class="small text-muted">الوقت</div>
                                <strong id="modalSessionTime">-</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-2 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-info-circle text-primary mb-1"></i>
                                <div class="small text-muted">الحالة</div>
                                <span id="modalStatusBadge" class="badge">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fas fa-sticky-note me-1"></i> ملاحظات الجلسة</label>
                        <textarea id="modalSessionNotes" class="form-control" rows="4" placeholder="اكتب ملاحظاتك عن الجلسة هنا..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> إغلاق
                </button>
                <button type="button" class="btn btn-outline-primary" id="btnSaveNotes">
                    <i class="fas fa-save me-1"></i> حفظ الملاحظات
                </button>
                <button type="button" class="btn btn-danger" id="btnAbsentSession" style="display: none;">
                    <i class="fas fa-user-slash me-1"></i> غائب
                </button>
                <button type="button" class="btn btn-success" id="btnCompleteSession" style="display: none;">
                    <i class="fas fa-check me-1"></i> تمت
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const sessionModal = new bootstrap.Modal(document.getElementById('sessionModal'));
let currentSessionId = null;
let currentSessionStatus = null;

const statusColors = {
    'scheduled': '#063973',
    'completed': '#198754',
    'absent': '#dc3545',
    'postponed': '#ffc107',
    'cancelled': '#6c757d'
};

const statusBgColors = {
    'scheduled': 'bg-primary',
    'completed': 'bg-success',
    'absent': 'bg-danger',
    'postponed': 'bg-warning',
    'cancelled': 'bg-secondary'
};

const statusTexts = {
    'scheduled': 'مجدولة',
    'completed': 'تمت',
    'absent': 'غائب',
    'postponed': 'مؤجلة',
    'cancelled': 'ملغاة'
};

function openSessionModal(sessionId) {
    currentSessionId = sessionId;
    document.getElementById('sessionLoading').style.display = 'block';
    document.getElementById('sessionContent').style.display = 'none';

    sessionModal.show();

    fetch(`{{ url('specialist/sessions') }}/${sessionId}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const session = data.session;
            currentSessionStatus = session.status;

            document.getElementById('modalSessionId').value = session.id;
            document.getElementById('modalSessionStatus').value = session.status;
            document.getElementById('modalStudentName').textContent = session.student?.name || '-';
            document.getElementById('modalStudentLink').href = session.student ? `{{ url('specialist/students') }}/${session.student.id}` : '#';
            document.getElementById('modalSessionType').textContent = session.package?.therapy_session?.name || '-';
            document.getElementById('modalSessionDate').textContent = new Date(session.session_date).toLocaleDateString('ar-SA');
            document.getElementById('modalSessionTime').textContent = session.session_time?.substring(0, 5) || '-';
            document.getElementById('modalSessionNotes').value = session.notes || '';

            // عرض الحالة كـ badge
            const statusBadge = document.getElementById('modalStatusBadge');
            statusBadge.className = `badge ${statusBgColors[session.status]}`;
            statusBadge.textContent = statusTexts[session.status];

            // إظهار/إخفاء أزرار "تمت" و"غائب" حسب الحالة
            const completeBtn = document.getElementById('btnCompleteSession');
            const absentBtn = document.getElementById('btnAbsentSession');
            if (session.status === 'scheduled') {
                completeBtn.style.display = 'inline-block';
                absentBtn.style.display = 'inline-block';
            } else {
                completeBtn.style.display = 'none';
                absentBtn.style.display = 'none';
            }

            document.getElementById('sessionLoading').style.display = 'none';
            document.getElementById('sessionContent').style.display = 'block';
        } else {
            alert(data.message || 'حدث خطأ');
            sessionModal.hide();
        }
    })
    .catch(error => {
        console.error(error);
        alert('حدث خطأ في الاتصال');
        sessionModal.hide();
    });
}

function quickComplete(sessionId) {
    if (!confirm('هل تريد تحديد هذه الجلسة كـ "تمت"؟')) return;

    const btn = document.getElementById(`complete-btn-${sessionId}`);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    }

    fetch(`{{ url('specialist/sessions') }}/${sessionId}/complete`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCardStatus(sessionId, 'completed');
        } else {
            alert(data.message || 'حدث خطأ');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i>';
            }
        }
    })
    .catch(error => {
        console.error(error);
        alert('حدث خطأ في الاتصال');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i>';
        }
    });
}

function quickAbsent(sessionId) {
    if (!confirm('هل تريد تحديد الطالب كـ "غائب"؟')) return;

    const btn = document.getElementById(`absent-btn-${sessionId}`);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    }

    fetch(`{{ url('specialist/sessions') }}/${sessionId}`, {
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: 'absent' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCardStatus(sessionId, 'absent');
        } else {
            alert(data.message || 'حدث خطأ');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-user-slash"></i>';
            }
        }
    })
    .catch(error => {
        console.error(error);
        alert('حدث خطأ في الاتصال');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-user-slash"></i>';
        }
    });
}

function updateCardStatus(sessionId, status) {
    const card = document.getElementById(`session-card-${sessionId}`);
    if (card) {
        const header = card.querySelector('.card-header');
        if (header) header.style.background = statusColors[status];

        const statusText = document.getElementById(`status-text-${sessionId}`);
        if (statusText) statusText.textContent = statusTexts[status];

        // إزالة أزرار الإجراء
        const completeBtn = document.getElementById(`complete-btn-${sessionId}`);
        const absentBtn = document.getElementById(`absent-btn-${sessionId}`);
        if (completeBtn) completeBtn.remove();
        if (absentBtn) absentBtn.remove();
    }
}

// حفظ الملاحظات فقط
document.getElementById('btnSaveNotes').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...';

    fetch(`{{ url('specialist/sessions') }}/${currentSessionId}`, {
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            notes: document.getElementById('modalSessionNotes').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            sessionModal.hide();
            // تحديث الملاحظات في البطاقة إذا كانت موجودة
            alert('تم حفظ الملاحظات بنجاح');
        } else {
            alert(data.message || 'حدث خطأ');
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ الملاحظات';
    })
    .catch(error => {
        console.error(error);
        alert('حدث خطأ في الاتصال');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ الملاحظات';
    });
});

// تحديد الجلسة كمكتملة من المودال
document.getElementById('btnCompleteSession').addEventListener('click', function() {
    if (!confirm('هل تريد تحديد هذه الجلسة كـ "تمت"؟')) return;

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    // حفظ الملاحظات + تحديد كمكتملة
    fetch(`{{ url('specialist/sessions') }}/${currentSessionId}`, {
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            status: 'completed',
            notes: document.getElementById('modalSessionNotes').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCardStatus(currentSessionId, 'completed');
            sessionModal.hide();
        } else {
            alert(data.message || 'حدث خطأ');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> تمت';
        }
    })
    .catch(error => {
        console.error(error);
        alert('حدث خطأ في الاتصال');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check me-1"></i> تمت';
    });
});

// تحديد الطالب كغائب من المودال
document.getElementById('btnAbsentSession').addEventListener('click', function() {
    if (!confirm('هل تريد تحديد الطالب كـ "غائب"؟')) return;

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    // حفظ الملاحظات + تحديد كغائب
    fetch(`{{ url('specialist/sessions') }}/${currentSessionId}`, {
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            status: 'absent',
            notes: document.getElementById('modalSessionNotes').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCardStatus(currentSessionId, 'absent');
            sessionModal.hide();
        } else {
            alert(data.message || 'حدث خطأ');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-user-slash me-1"></i> غائب';
        }
    })
    .catch(error => {
        console.error(error);
        alert('حدث خطأ في الاتصال');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-user-slash me-1"></i> غائب';
    });
});

// إعادة تعيين Modal
document.getElementById('sessionModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('modalSessionNotes').value = '';
    document.getElementById('btnSaveNotes').disabled = false;
    document.getElementById('btnSaveNotes').innerHTML = '<i class="fas fa-save me-1"></i> حفظ الملاحظات';
    document.getElementById('btnCompleteSession').disabled = false;
    document.getElementById('btnCompleteSession').innerHTML = '<i class="fas fa-check me-1"></i> تمت';
    document.getElementById('btnAbsentSession').disabled = false;
    document.getElementById('btnAbsentSession').innerHTML = '<i class="fas fa-user-slash me-1"></i> غائب';
});
</script>
@endpush
