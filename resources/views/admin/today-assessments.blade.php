@extends('layouts.app')

@section('title', 'التقييمات')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ti ti-clipboard-text me-2"></i>
                        إدارة التقييمات
                    </h5>
                    <a href="{{ route('admin.assessments.print', request()->all()) }}" target="_blank" class="btn btn-primary btn-sm">
                        <i class="ti ti-printer me-1"></i>
                        طباعة
                    </a>
                </div>
            </div>

            <div class="card-body">
                @include('layouts.messages')

                <!-- الفلاتر -->
                <form method="GET" action="{{ route('admin.today-assessments') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">المقياس</label>
                            <select name="assessment_id" class="form-select">
                                <option value="">-- جميع المقاييس --</option>
                                @foreach($allAssessments as $assessment)
                                <option value="{{ $assessment->id }}" {{ request('assessment_id') == $assessment->id ? 'selected' : '' }}>
                                    {{ $assessment->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">حالة التقييم</label>
                            <select name="status" class="form-select">
                                <option value="">-- الكل --</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">حالة الحالة</label>
                            <select name="case_status" class="form-select">
                                <option value="">-- الكل --</option>
                                <option value="pending" {{ request('case_status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                <option value="in_progress" {{ request('case_status') == 'in_progress' ? 'selected' : '' }}>جاري التقييم</option>
                                <option value="completed" {{ request('case_status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" name="date_from" class="form-select" value="{{ request('date_from') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" name="date_to" class="form-select" value="{{ request('date_to') }}">
                        </div>

                        <div class="col-md-10">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search me-1"></i> بحث
                            </button>
                            <a href="{{ route('admin.today-assessments') }}" class="btn btn-secondary">
                                <i class="ti ti-refresh me-1"></i> إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>

                <!-- الجدول -->
                @if($assessmentItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>الطالب</th>
                                <th>المقياس</th>
                                <th>حالة التقييم</th>
                                <th>حالة دراسة الحالة</th>
                                <th>الأخصائي</th>
                                <th>التاريخ</th>
                                <th width="18%" class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assessmentItems as $index => $item)
                            <tr>
                                <td>{{ ($assessmentItems->currentPage() - 1) * $assessmentItems->perPage() + $index + 1 }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $item->invoice->student->name ?? '-' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $item->invoice->student->code ?? '' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $item->assessment_name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->assessment_status_color }}">
                                        {{ $item->assessment_status_text }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->invoice->studentCase)
                                    <span class="badge bg-{{ $item->invoice->studentCase->status_color }}">
                                        {{ $item->invoice->studentCase->status_text }}
                                    </span>
                                    @else
                                    <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $item->assessor->name ?? '-' }}
                                </td>
                                <td>
                                    <small>{{ $item->created_at->format('Y/m/d') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button"
                                                class="btn btn-outline-info"
                                                onclick="viewNotes({{ $item->id }})">
                                            <i class="ti ti-notes me-1"></i>
                                            الملاحظات
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $assessmentItems->links() }}
                </div>
                @else
                <div class="alert alert-info text-center mb-0">
                    <i class="ti ti-info-circle me-2"></i>
                    لا توجد تقييمات
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal عرض دراسة الحالة -->
<div class="modal fade" id="caseModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: #063973;">
                <h5 class="modal-title text-white">
                    <i class="ti ti-file-text me-2"></i>
                    تفاصيل دراسة الحالة
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="caseContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal الملاحظات -->
<div class="modal fade" id="notesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: #063973;">
                <h5 class="modal-title text-white">
                    <i class="ti ti-notes me-2"></i>
                    ملاحظات التقييم
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="notesContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const caseModal = new bootstrap.Modal(document.getElementById('caseModal'));
const notesModal = new bootstrap.Modal(document.getElementById('notesModal'));

function viewCase(caseId) {
    document.getElementById('caseContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">جاري التحميل...</span>
            </div>
        </div>
    `;

    caseModal.show();

    fetch(`/admin/cases/${caseId}/details`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const c = data.case;
            const student = data.student;

            let html = `
                <div class="mb-3">
                    <h6 class="text-muted mb-2">الطالب</h6>
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px;font-size:1.2rem;">
                            ${student.name.substring(0, 2).toUpperCase()}
                        </div>
                        <div>
                            <strong>${student.name}</strong>
                            <br><small class="text-muted">${student.code}</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-2">حالة دراسة الحالة</h6>
                    <span class="badge bg-${c.status_color}">${c.status_text}</span>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-2">الملاحظات</h6>
                    <div class="p-3 bg-light rounded">
                        ${c.notes || '<small class="text-muted">لا توجد ملاحظات</small>'}
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-2">تاريخ الإنشاء</h6>
                    <small>${c.created_at}</small>
                </div>

                <div class="mb-0">
                    <h6 class="text-muted mb-2">تم الإنشاء بواسطة</h6>
                    <small>${c.creator_name || '-'}</small>
                </div>
            `;

            document.getElementById('caseContent').innerHTML = html;
        } else {
            document.getElementById('caseContent').innerHTML = `
                <div class="alert alert-danger mb-0">
                    <i class="ti ti-alert-circle me-2"></i>
                    ${data.message || 'حدث خطأ'}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error(error);
        document.getElementById('caseContent').innerHTML = `
            <div class="alert alert-danger mb-0">
                <i class="ti ti-alert-circle me-2"></i>
                حدث خطأ في الاتصال
            </div>
        `;
    });
}

function viewNotes(itemId) {
    document.getElementById('notesContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">جاري التحميل...</span>
            </div>
        </div>
    `;

    notesModal.show();

    fetch(`/admin/assessment-items/${itemId}/notes`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = data.item;

            let html = `
                <div class="mb-3">
                    <h6 class="text-muted mb-2">المقياس</h6>
                    <span class="badge bg-info">${item.assessment_name}</span>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-2">النتيجة</h6>
                    <div class="p-3 bg-light rounded">
                        ${item.assessment_result || '<small class="text-muted">لم يتم إدخال النتيجة بعد</small>'}
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-2">الملاحظات</h6>
                    <div class="p-3 bg-light rounded">
                        ${item.assessment_notes || '<small class="text-muted">لا توجد ملاحظات</small>'}
                    </div>
                </div>

                <div class="mb-0">
                    <h6 class="text-muted mb-2">تم التقييم بواسطة</h6>
                    <small>${item.assessor_name || '-'}</small>
                    ${item.assessed_at ? `<br><small class="text-muted">${item.assessed_at}</small>` : ''}
                </div>
            `;

            document.getElementById('notesContent').innerHTML = html;
        } else {
            document.getElementById('notesContent').innerHTML = `
                <div class="alert alert-danger mb-0">
                    <i class="ti ti-alert-circle me-2"></i>
                    ${data.message || 'حدث خطأ'}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error(error);
        document.getElementById('notesContent').innerHTML = `
            <div class="alert alert-danger mb-0">
                <i class="ti ti-alert-circle me-2"></i>
                حدث خطأ في الاتصال
            </div>
        `;
    });
}
</script>
@endpush
