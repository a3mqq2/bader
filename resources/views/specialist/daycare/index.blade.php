@extends('layouts.app')

@section('title', 'الرعاية النهارية')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header with Date Picker -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">
                            <i class="fas fa-sun me-2" style="color: #063973;"></i>
                            الرعاية النهارية
                        </h5>
                        <small class="text-muted d-none d-md-inline">تسجيل حضور وغياب الطلاب المسندين إليك</small>
                    </div>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <!-- زر قارئ الباركود -->
                        <button type="button" class="btn btn-info btn-sm" onclick="openBarcodeScanner()" title="مسح باركود">
                            <i class="fas fa-qrcode"></i>
                            <span class="d-none d-sm-inline ms-1">مسح باركود</span>
                        </button>
                        <div class="d-flex gap-1 align-items-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeDate(-1)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <input type="date" id="dateInput" class="form-control form-control-sm" value="{{ $date }}" style="width: 130px;" onchange="goToDate()">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeDate(1)">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" onclick="goToToday()">
                            <i class="fas fa-calendar-day"></i>
                            <span class="d-none d-sm-inline ms-1">اليوم</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-3 g-2">
            <div class="col-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center py-2 px-1">
                        <h3 class="mb-0" style="color: #063973;">{{ $stats['total'] }}</h3>
                        <small class="text-muted" style="font-size: 0.7rem;">الكل</small>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card h-100 border-0 shadow-sm bg-success bg-opacity-10">
                    <div class="card-body text-center py-2 px-1">
                        <h3 class="mb-0 text-success" id="stats-present">{{ $stats['present'] }}</h3>
                        <small class="text-muted" style="font-size: 0.7rem;">حاضر</small>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card h-100 border-0 shadow-sm bg-danger bg-opacity-10">
                    <div class="card-body text-center py-2 px-1">
                        <h3 class="mb-0 text-danger" id="stats-absent">{{ $stats['absent'] }}</h3>
                        <small class="text-muted" style="font-size: 0.7rem;">غائب</small>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card h-100 border-0 shadow-sm bg-warning bg-opacity-10">
                    <div class="card-body text-center py-2 px-1">
                        <h3 class="mb-0 text-warning" id="stats-pending">{{ $stats['pending'] }}</h3>
                        <small class="text-muted" style="font-size: 0.7rem;">انتظار</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Info -->
        <div class="alert alert-info d-flex align-items-center mb-3 py-2">
            <i class="fas fa-calendar-alt me-2"></i>
            <div>
                <strong>{{ \Carbon\Carbon::parse($date)->locale('ar')->translatedFormat('l j F Y') }}</strong>
                @if($date == now()->format('Y-m-d'))
                    <span class="badge bg-primary ms-1">اليوم</span>
                @endif
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
            <a href="{{ route('specialist.daycare.index', ['date' => $date]) }}" class="btn btn-outline-danger btn-sm">
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
                        <strong>حضور اليوم للطالب</strong>
                    </div>
                    <span class="badge bg-light text-primary">{{ $searchedStudent->subscription->student->code }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 80px; height: 80px; background: #e3f2fd;">
                            <i class="fas fa-user fa-2x text-primary"></i>
                        </div>
                        <h5 class="mb-1">{{ $searchedStudent->subscription->student->name }}</h5>
                        <span class="badge bg-secondary">{{ $searchedStudent->subscription->daycareType->name ?? '-' }}</span>
                    </div>
                    <div class="col-md-8">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">حالة الحضور:</label>
                                <div class="d-flex gap-2" id="searched-btn-group-{{ $searchedStudent->id }}">
                                    <button type="button"
                                            class="btn btn-{{ $searchedStudent->status === 'present' ? 'success' : 'outline-success' }} flex-fill py-2"
                                            onclick="setAttendance({{ $searchedStudent->id }}, 'present')">
                                        <i class="fas fa-check me-1"></i> حاضر
                                    </button>
                                    <button type="button"
                                            class="btn btn-{{ $searchedStudent->status === 'absent' ? 'danger' : 'outline-danger' }} flex-fill py-2"
                                            onclick="setAttendance({{ $searchedStudent->id }}, 'absent')">
                                        <i class="fas fa-times me-1"></i> غائب
                                    </button>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">ملاحظات:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searched-notes-{{ $searchedStudent->id }}"
                                           value="{{ $searchedStudent->notes }}" placeholder="أضف ملاحظة...">
                                    <button class="btn btn-primary" type="button" onclick="saveNotes({{ $searchedStudent->id }})" title="حفظ">
                                        <i class="fas fa-save me-1"></i> حفظ
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif

        <!-- حقل البحث -->
        @if($attendances->count() > 0)
        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control border-start-0"
                           placeholder="ابحث باسم الطالب أو رقمه..."
                           oninput="filterStudents()">
                    <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()" id="clearSearchBtn" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Students Cards Grid -->
        @if($attendances->count() > 0)
        <div class="row g-3">
            @foreach($attendances as $attendance)
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100 shadow-sm student-card" id="card-{{ $attendance->id }}" data-code="{{ $attendance->subscription->student->code }}">
                    <div class="card-body p-3">
                        <!-- رأس البطاقة: اسم الطالب والحالة -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $attendance->subscription->student->name }}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-id-badge me-1"></i>{{ $attendance->subscription->student->code }}
                                </small>
                            </div>
                            <span class="badge bg-{{ $attendance->status_color }} fs-6" id="status-badge-{{ $attendance->id }}">
                                <i class="fas fa-{{ $attendance->status_icon }} me-1"></i>
                                {{ $attendance->status_text }}
                            </span>
                        </div>

                        <!-- نوع الرعاية -->
                        <div class="mb-3">
                            <span class="badge bg-secondary">
                                <i class="fas fa-tag me-1"></i>{{ $attendance->subscription->daycareType->name ?? '-' }}
                            </span>
                        </div>

                        <!-- أزرار الحالة -->
                        <div class="d-flex gap-2 mb-3" id="btn-group-{{ $attendance->id }}">
                            <button type="button"
                                    class="btn btn-{{ $attendance->status === 'present' ? 'success' : 'outline-success' }} flex-fill"
                                    onclick="setAttendance({{ $attendance->id }}, 'present')">
                                <i class="fas fa-check me-1"></i>حاضر
                            </button>
                            <button type="button"
                                    class="btn btn-{{ $attendance->status === 'absent' ? 'danger' : 'outline-danger' }} flex-fill"
                                    onclick="setAttendance({{ $attendance->id }}, 'absent')">
                                <i class="fas fa-times me-1"></i>غائب
                            </button>
                        </div>

                        <!-- حقل الملاحظات -->
                        <div class="mb-0">
                            <label class="form-label small text-muted mb-1">
                                <i class="fas fa-comment me-1"></i>ملاحظات
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="notes-{{ $attendance->id }}"
                                       value="{{ $attendance->notes }}" placeholder="أضف ملاحظة..."
                                       onkeypress="if(event.key === 'Enter') saveNotes({{ $attendance->id }})">
                                <button class="btn btn-primary" type="button" onclick="saveNotes({{ $attendance->id }})" title="حفظ">
                                    <i class="fas fa-save"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="fas fa-sun fa-4x mb-3 opacity-25"></i>
                <p class="mb-0">لا يوجد طلاب مسندين إليك لهذا اليوم</p>
                <small>
                    @if(\Carbon\Carbon::parse($date)->isFriday() || \Carbon\Carbon::parse($date)->isSaturday())
                        هذا اليوم عطلة نهاية الأسبوع
                    @else
                        جرب اختيار تاريخ آخر
                    @endif
                </small>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal قارئ الباركود -->
<div class="modal fade" id="barcodeScannerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode me-2"></i>مسح باركود الطالب
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div id="scanner-container" style="width: 100%; max-width: 400px; margin: 0 auto;">
                        <video id="barcode-video" style="width: 100%; border-radius: 8px;"></video>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        وجّه الكاميرا نحو باركود الطالب
                    </p>
                </div>
                <div class="text-center">
                    <span class="text-muted">أو أدخل الكود يدوياً:</span>
                    <div class="input-group mt-2">
                        <input type="text" id="manual-code-input" class="form-control" placeholder="كود الطالب" onkeypress="if(event.key === 'Enter') searchByCode()">
                        <button class="btn btn-primary" type="button" onclick="searchByCode()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تعديل الحضور -->
<div class="modal fade" id="attendanceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-check me-2"></i>
                    <span id="modal-student-name">تعديل الحضور</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal-attendance-id">

                <!-- كود الطالب -->
                <div class="text-center mb-3">
                    <span class="badge bg-secondary fs-6" id="modal-student-code"></span>
                </div>

                <!-- الحالة الحالية -->
                <div class="text-center mb-4">
                    <span class="badge fs-5 px-4 py-2" id="modal-current-status"></span>
                </div>

                <!-- أزرار تغيير الحالة -->
                <div class="row g-2 mb-4">
                    <div class="col-4">
                        <button type="button" class="btn btn-success w-100 py-3" onclick="setModalAttendance('present')">
                            <i class="fas fa-check fa-2x d-block mb-1"></i>
                            حاضر
                        </button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-warning w-100 py-3" onclick="setModalAttendance('pending')">
                            <i class="fas fa-clock fa-2x d-block mb-1"></i>
                            انتظار
                        </button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-danger w-100 py-3" onclick="setModalAttendance('absent')">
                            <i class="fas fa-times fa-2x d-block mb-1"></i>
                            غائب
                        </button>
                    </div>
                </div>

                <!-- حقل الملاحظات -->
                <div class="mb-0">
                    <label class="form-label">
                        <i class="fas fa-comment me-1"></i>ملاحظات
                    </label>
                    <textarea id="modal-notes" class="form-control" rows="3" placeholder="أضف ملاحظة..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" onclick="saveModalAttendance()">
                    <i class="fas fa-save me-1"></i>حفظ الملاحظات
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.student-card {
    transition: all 0.3s ease;
    border-right: 4px solid #dee2e6;
}
.student-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}
.student-card.status-present {
    border-right-color: #198754;
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.05) 0%, transparent 100%);
}
.student-card.status-absent {
    border-right-color: #dc3545;
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, transparent 100%);
}
.student-card.status-pending {
    border-right-color: #ffc107;
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.05) 0%, transparent 100%);
}
.student-card.highlight {
    animation: highlight-pulse 0.5s ease;
}
@keyframes highlight-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(6, 57, 115, 0.4); }
    50% { box-shadow: 0 0 0 10px rgba(6, 57, 115, 0); }
}

/* تحسينات للجوال */
@media (max-width: 576px) {
    .student-card .card-body {
        padding: 0.75rem !important;
    }
    .student-card h6 {
        font-size: 0.95rem;
    }
}
</style>
@endpush

@push('scripts')
<!-- مكتبة قراءة الباركود -->
<script src="https://unpkg.com/@zxing/library@latest"></script>
<script>
// متغيرات عامة
let codeReader = null;
let scannerModal = null;
let attendanceModal = null;
const attendanceData = {
    @foreach($attendances as $attendance)
    "{{ $attendance->subscription->student->code }}": {
        id: {{ $attendance->id }},
        status: "{{ $attendance->status }}",
        notes: @json($attendance->notes),
        student_name: @json($attendance->subscription->student->name),
        student_code: "{{ $attendance->subscription->student->code }}"
    },
    @endforeach
};

document.addEventListener('DOMContentLoaded', function() {
    scannerModal = new bootstrap.Modal(document.getElementById('barcodeScannerModal'));
    attendanceModal = new bootstrap.Modal(document.getElementById('attendanceModal'));

    // تحديث تصميم البطاقات حسب الحالة
    updateCardStyles();

    // إيقاف الكاميرا عند إغلاق المودل
    document.getElementById('barcodeScannerModal').addEventListener('hidden.bs.modal', function() {
        stopScanner();
    });
});

function updateCardStyles() {
    document.querySelectorAll('.student-card').forEach(card => {
        const id = card.id.replace('card-', '');
        const badge = document.getElementById(`status-badge-${id}`);

        card.classList.remove('status-present', 'status-absent', 'status-pending');

        if (badge.classList.contains('bg-success')) {
            card.classList.add('status-present');
        } else if (badge.classList.contains('bg-danger')) {
            card.classList.add('status-absent');
        } else if (badge.classList.contains('bg-warning')) {
            card.classList.add('status-pending');
        }
    });
}

function changeDate(days) {
    const dateInput = document.getElementById('dateInput');
    const currentDate = new Date(dateInput.value);
    currentDate.setDate(currentDate.getDate() + days);
    dateInput.value = currentDate.toISOString().split('T')[0];
    goToDate();
}

function goToDate() {
    const date = document.getElementById('dateInput').value;
    window.location.href = `{{ route('specialist.daycare.index') }}?date=${date}`;
}

function goToToday() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('dateInput').value = today;
    goToDate();
}

// وظائف قارئ الباركود
function openBarcodeScanner() {
    scannerModal.show();
    startScanner();
}

function startScanner() {
    codeReader = new ZXing.BrowserMultiFormatReader();

    codeReader.decodeFromVideoDevice(null, 'barcode-video', (result, err) => {
        if (result) {
            const code = result.text;
            stopScanner();
            scannerModal.hide();
            findStudentByCode(code);
        }
    });
}

function stopScanner() {
    if (codeReader) {
        codeReader.reset();
        codeReader = null;
    }
}

function searchByCode() {
    const code = document.getElementById('manual-code-input').value.trim();
    if (code) {
        scannerModal.hide();
        findStudentByCode(code);
    }
}

function findStudentByCode(code) {
    // البحث في البيانات المحلية
    const attendance = attendanceData[code];

    if (attendance) {
        openAttendanceModal(attendance);

        // تمييز البطاقة
        const card = document.querySelector(`[data-code="${code}"]`);
        if (card) {
            card.classList.add('highlight');
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => card.classList.remove('highlight'), 1000);
        }
    } else {
        alert('لم يتم العثور على طالب بهذا الكود في قائمة اليوم');
    }
}

function openAttendanceModal(attendance) {
    document.getElementById('modal-attendance-id').value = attendance.id;
    document.getElementById('modal-student-name').textContent = attendance.student_name;
    document.getElementById('modal-student-code').textContent = attendance.student_code;
    document.getElementById('modal-notes').value = attendance.notes || '';

    updateModalStatus(attendance.status);
    attendanceModal.show();
}

function updateModalStatus(status) {
    const statusBadge = document.getElementById('modal-current-status');
    const statusConfig = {
        'present': { color: 'success', icon: 'check-circle', text: 'حاضر' },
        'absent': { color: 'danger', icon: 'times-circle', text: 'غائب' },
        'pending': { color: 'warning', icon: 'clock', text: 'قيد الانتظار' }
    };

    const config = statusConfig[status];
    statusBadge.className = `badge bg-${config.color} fs-5 px-4 py-2`;
    statusBadge.innerHTML = `<i class="fas fa-${config.icon} me-2"></i>${config.text}`;
}

function setModalAttendance(status) {
    const attendanceId = document.getElementById('modal-attendance-id').value;
    setAttendance(attendanceId, status, true);
}

function saveModalAttendance() {
    const attendanceId = document.getElementById('modal-attendance-id').value;
    const notes = document.getElementById('modal-notes').value;

    // تحديث حقل الملاحظات في البطاقة
    const notesInput = document.getElementById(`notes-${attendanceId}`);
    if (notesInput) {
        notesInput.value = notes;
    }

    // حفظ الملاحظات
    saveNotes(attendanceId, true);
}

function setAttendance(attendanceId, status, fromModal = false) {
    const badge = document.getElementById(`status-badge-${attendanceId}`);
    const btnGroup = document.getElementById(`btn-group-${attendanceId}`);

    // تعطيل الأزرار مؤقتاً
    if (btnGroup) {
        btnGroup.querySelectorAll('button').forEach(btn => btn.disabled = true);
    }

    fetch(`{{ url('specialist/daycare-attendance') }}/${attendanceId}/toggle`, {
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

            // تحديث الشارة في البطاقة
            if (badge) {
                badge.className = `badge bg-${config.color} fs-6`;
                badge.innerHTML = `<i class="fas fa-${config.icon} me-1"></i>${config.text}`;
            }

            // تحديث الأزرار في البطاقة
            if (btnGroup) {
                btnGroup.innerHTML = `
                    <button type="button" class="btn btn-${status === 'present' ? 'success' : 'outline-success'} flex-fill" onclick="setAttendance(${attendanceId}, 'present')">
                        <i class="fas fa-check me-1"></i>حاضر
                    </button>
                    <button type="button" class="btn btn-${status === 'absent' ? 'danger' : 'outline-danger'} flex-fill" onclick="setAttendance(${attendanceId}, 'absent')">
                        <i class="fas fa-times me-1"></i>غائب
                    </button>
                `;
            }

            // تحديث المودل إذا كان مفتوح
            if (fromModal) {
                updateModalStatus(status);
            }

            // تحديث الإحصائيات وتصميم البطاقات
            updateStats();
            updateCardStyles();
        } else {
            alert(data.message || 'حدث خطأ');
            if (btnGroup) {
                btnGroup.querySelectorAll('button').forEach(btn => btn.disabled = false);
            }
        }
    })
    .catch(error => {
        alert('حدث خطأ في الاتصال');
        console.error(error);
        if (btnGroup) {
            btnGroup.querySelectorAll('button').forEach(btn => btn.disabled = false);
        }
    });
}

function saveNotes(attendanceId, closeModal = false) {
    const notesInput = document.getElementById(`notes-${attendanceId}`);
    const modalNotes = document.getElementById('modal-notes');
    const notes = closeModal ? modalNotes.value : (notesInput ? notesInput.value : '');

    // جلب الحالة الحالية من الشارة
    const badge = document.getElementById(`status-badge-${attendanceId}`);
    let currentStatus = 'pending';
    if (badge && badge.classList.contains('bg-success')) currentStatus = 'present';
    else if (badge && badge.classList.contains('bg-danger')) currentStatus = 'absent';

    fetch(`{{ url('specialist/daycare-attendance') }}/${attendanceId}`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            status: currentStatus,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // تحديث حقل الملاحظات في البطاقة
            if (notesInput) {
                notesInput.value = notes;
                notesInput.classList.add('border-success');
                setTimeout(() => notesInput.classList.remove('border-success'), 1000);
            }

            if (closeModal) {
                attendanceModal.hide();
            }
        } else {
            alert(data.message || 'حدث خطأ في حفظ الملاحظة');
        }
    })
    .catch(error => {
        alert('حدث خطأ في الاتصال');
        console.error(error);
    });
}

function updateStats() {
    let present = 0, absent = 0, pending = 0;

    document.querySelectorAll('[id^="status-badge-"]').forEach(badge => {
        if (badge.classList.contains('bg-success')) present++;
        else if (badge.classList.contains('bg-danger')) absent++;
        else if (badge.classList.contains('bg-warning')) pending++;
    });

    document.getElementById('stats-present').textContent = present;
    document.getElementById('stats-absent').textContent = absent;
    document.getElementById('stats-pending').textContent = pending;
}

// وظائف البحث
function filterStudents() {
    const searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();
    const clearBtn = document.getElementById('clearSearchBtn');
    const cards = document.querySelectorAll('.student-card');

    // إظهار/إخفاء زر المسح
    clearBtn.style.display = searchTerm ? 'block' : 'none';

    cards.forEach(card => {
        const studentName = card.querySelector('h6').textContent.toLowerCase();
        const studentCode = card.getAttribute('data-code').toLowerCase();
        const parentCol = card.closest('.col-12');

        if (searchTerm === '' || studentName.includes(searchTerm) || studentCode.includes(searchTerm)) {
            parentCol.style.display = '';
        } else {
            parentCol.style.display = 'none';
        }
    });
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('clearSearchBtn').style.display = 'none';
    filterStudents();
}
</script>
@endpush
