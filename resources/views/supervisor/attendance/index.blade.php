@extends('layouts.app')

@section('title', 'تسجيل حضور الموظفين')

@section('content')
<div class="row">
    <!-- قسم الماسح -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-scan me-2"></i>
                    مسح الباركود
                </h5>
            </div>
            <div class="card-body text-center">
                <div class="scanner-area mb-4">
                    <div class="barcode-icon mb-3">
                        <i class="ti ti-barcode" style="font-size: 80px; color: #151f42;"></i>
                    </div>
                    <input type="text"
                           id="barcodeInput"
                           class="form-control form-control-lg text-center"
                           placeholder="امسح الباركود أو أدخل الكود"
                           maxlength="6"
                           autofocus
                           autocomplete="off"
                           style="font-size: 24px; letter-spacing: 8px; font-weight: bold;">
                    <small class="text-muted d-block mt-2">اضغط Enter بعد المسح</small>
                </div>

                <!-- نتيجة المسح -->
                <div id="scanResult" class="d-none">
                    <hr>
                    <div id="resultContent"></div>
                </div>
            </div>
        </div>

        <!-- الوقت الحالي -->
        <div class="card mt-3">
            <div class="card-body text-center">
                <h2 id="currentTime" class="mb-0" style="font-size: 48px; color: #151f42;"></h2>
                <p id="currentDate" class="text-muted mb-0"></p>
            </div>
        </div>
    </div>

    <!-- قسم حضور اليوم -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-users me-2"></i>
                    حضور اليوم
                    <span class="badge bg-primary ms-2" id="todayCount">{{ $todayAttendances->count() }}</span>
                </h5>
                <a href="{{ route('supervisor.attendance.log') }}" class="btn btn-outline-primary btn-sm">
                    <i class="ti ti-list me-1"></i>
                    عرض السجل الكامل
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover mb-0" id="todayTable">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>الموظف</th>
                                <th>الدور</th>
                                <th>الدخول</th>
                                <th>الخروج</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody id="todayTableBody">
                            @forelse($todayAttendances as $attendance)
                            <tr id="attendance-{{ $attendance->id }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:35px;height:35px;font-size:12px;">
                                            {{ mb_substr($attendance->user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <strong>{{ $attendance->user->name }}</strong>
                                            <br><small class="text-muted">{{ $attendance->user->code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-info">{{ $attendance->user->role_text }}</span></td>
                                <td>
                                    @if($attendance->check_in)
                                        <span class="text-success">
                                            <i class="ti ti-login me-1"></i>
                                            {{ \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->check_out)
                                        <span class="text-danger">
                                            <i class="ti ti-logout me-1"></i>
                                            {{ \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning">لم يسجل خروج</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $attendance->status_color }}">
                                        {{ $attendance->status_text }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow">
                                <td colspan="5" class="text-center py-5">
                                    <i class="ti ti-users-minus" style="font-size: 48px; color: #ccc;"></i>
                                    <p class="text-muted mt-2 mb-0">لا يوجد حضور مسجل اليوم</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal للنتيجة -->
<div class="modal fade" id="resultModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5" id="modalBody">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    #barcodeInput:focus {
        border-color: #151f42;
        box-shadow: 0 0 0 0.2rem rgba(21, 31, 66, 0.25);
    }
    .scanner-area {
        padding: 30px;
        background: #f8f9fa;
        border-radius: 10px;
        border: 2px dashed #dee2e6;
    }
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 1;
    }
    .result-success {
        animation: pulse-green 0.5s ease;
    }
    .result-error {
        animation: pulse-red 0.5s ease;
    }
    @keyframes pulse-green {
        0% { background-color: #d4edda; }
        100% { background-color: transparent; }
    }
    @keyframes pulse-red {
        0% { background-color: #f8d7da; }
        100% { background-color: transparent; }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const barcodeInput = document.getElementById('barcodeInput');
    const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
    const modalBody = document.getElementById('modalBody');

    // تحديث الوقت
    function updateTime() {
        const now = new Date();
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };

        document.getElementById('currentTime').textContent = now.toLocaleTimeString('ar-SA', timeOptions);
        document.getElementById('currentDate').textContent = now.toLocaleDateString('ar-SA', dateOptions);
    }
    updateTime();
    setInterval(updateTime, 1000);

    // معالجة المسح
    barcodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const code = this.value.trim();

            if (code.length !== 6) {
                showResult(false, 'الكود يجب أن يكون 6 أرقام');
                return;
            }

            // إرسال الطلب
            fetch('{{ route("supervisor.attendance.scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code: code })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResult(true, data.message, data);
                    updateTable(data.attendance);
                } else {
                    showResult(false, data.message);
                }
            })
            .catch(error => {
                showResult(false, 'حدث خطأ في الاتصال');
            });

            this.value = '';
        }
    });

    // عرض النتيجة
    function showResult(success, message, data = null) {
        let icon = success ?
            '<i class="ti ti-circle-check" style="font-size: 80px; color: #28a745;"></i>' :
            '<i class="ti ti-circle-x" style="font-size: 80px; color: #dc3545;"></i>';

        let content = `
            <div class="${success ? 'result-success' : 'result-error'}">
                ${icon}
                <h4 class="mt-3 mb-2">${message}</h4>
        `;

        if (data && data.user) {
            content += `
                <div class="mt-3">
                    <h5 class="mb-1">${data.user.name}</h5>
                    <p class="text-muted mb-1">${data.user.role}</p>
                    <p class="mb-0">
                        <span class="badge bg-${data.type === 'check_in' ? 'success' : 'danger'} fs-6">
                            ${data.type === 'check_in' ? 'دخول' : 'خروج'}: ${data.time}
                        </span>
                    </p>
                    ${data.work_hours ? `<p class="mt-2 mb-0"><strong>ساعات العمل:</strong> ${data.work_hours}</p>` : ''}
                </div>
            `;
        }

        content += '</div>';
        modalBody.innerHTML = content;
        resultModal.show();

        // إغلاق تلقائي
        setTimeout(() => {
            resultModal.hide();
            barcodeInput.focus();
        }, 3000);
    }

    // تحديث الجدول
    function updateTable(attendance) {
        const tbody = document.getElementById('todayTableBody');
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();

        let existingRow = document.getElementById('attendance-' + attendance.id);

        const rowHtml = `
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:35px;height:35px;font-size:12px;">
                        ${attendance.user_name.substring(0, 2)}
                    </div>
                    <div>
                        <strong>${attendance.user_name}</strong>
                        <br><small class="text-muted">${attendance.user_code}</small>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-info">${attendance.user_role}</span></td>
            <td>
                ${attendance.check_in ? `<span class="text-success"><i class="ti ti-login me-1"></i>${attendance.check_in}</span>` : '-'}
            </td>
            <td>
                ${attendance.check_out ?
                    `<span class="text-danger"><i class="ti ti-logout me-1"></i>${attendance.check_out}</span>` :
                    '<span class="badge bg-warning">لم يسجل خروج</span>'}
            </td>
            <td>
                <span class="badge bg-${attendance.status_color}">${attendance.status_text}</span>
            </td>
        `;

        if (existingRow) {
            existingRow.innerHTML = rowHtml;
            existingRow.classList.add('result-success');
            setTimeout(() => existingRow.classList.remove('result-success'), 1000);
        } else {
            const newRow = document.createElement('tr');
            newRow.id = 'attendance-' + attendance.id;
            newRow.innerHTML = rowHtml;
            newRow.classList.add('result-success');
            tbody.insertBefore(newRow, tbody.firstChild);

            // تحديث العداد
            const count = document.getElementById('todayCount');
            count.textContent = parseInt(count.textContent) + 1;
        }
    }

    // إعادة التركيز على حقل الإدخال
    document.addEventListener('click', function() {
        barcodeInput.focus();
    });
});
</script>
@endpush
