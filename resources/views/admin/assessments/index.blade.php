@extends('layouts.app')

@section('title', 'المقاييس والتقييمات')

@section('content')
<div class="row">
    <!-- فورم الإضافة/التعديل - اليسار -->
    <div class="col-md-4">
        <div class="card sticky-top" style="top: 100px;">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0 text-white" id="formTitle">
                    <i class="fas fa-plus-circle me-1"></i> إضافة مقياس جديد
                </h6>
            </div>
            <div class="card-body">
                <form id="assessmentForm" method="POST" action="{{ route('admin.assessments.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-tag me-1"></i> اسم المقياس <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="assessmentName" class="form-control" placeholder="مثال: مقياس ستانفورد بينيه" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-money-bill me-1"></i> السعر (د.ل) <span class="text-danger">*</span></label>
                        <input type="number" name="price" id="assessmentPrice" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-align-left me-1"></i> الوصف</label>
                        <textarea name="description" id="assessmentDescription" class="form-control" rows="2" placeholder="وصف مختصر (اختياري)"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="assessmentActive" value="1" checked>
                            <label class="form-check-label" for="assessmentActive">مفعل</label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="fas fa-save me-1"></i> حفظ
                        </button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="btnCancel" onclick="resetForm()">
                            <i class="fas fa-times me-1"></i> إلغاء التعديل
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- جدول المقاييس - اليمين -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-clipboard-list me-1"></i>
                        قائمة المقاييس
                        <span class="badge bg-secondary ms-1">{{ $assessments->total() }}</span>
                    </h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>المقياس</th>
                                <th width="15%">السعر</th>
                                <th width="12%">الحالة</th>
                                <th width="15%" class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assessments as $assessment)
                                <tr id="row-{{ $assessment->id }}">
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $assessment->name }}
                                            @if($assessment->id === 1)
                                                <i class="fas fa-lock text-muted ms-1" title="أساسي"></i>
                                            @endif
                                        </div>
                                        @if($assessment->description)
                                            <small class="text-muted">{{ Str::limit($assessment->description, 40) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">{{ $assessment->formatted_price }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $assessment->status_color }}">{{ $assessment->status_text }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                onclick="editAssessment({{ $assessment->id }}, '{{ addslashes($assessment->name) }}', {{ $assessment->price }}, '{{ addslashes($assessment->description ?? '') }}', {{ $assessment->is_active ? 'true' : 'false' }})"
                                                title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.assessments.toggle', $assessment) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $assessment->is_active ? 'secondary' : 'success' }}" title="{{ $assessment->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}">
                                                <i class="fas fa-{{ $assessment->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </form>
                                        @if($assessment->id !== 1)
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteAssessment({{ $assessment->id }}, '{{ addslashes($assessment->name) }}')" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-clipboard-list fa-2x text-muted mb-2 d-block"></i>
                                        <p class="text-muted mb-0">لا يوجد مقاييس</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($assessments->hasPages())
                <div class="card-footer">
                    {{ $assessments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title"><i class="fas fa-exclamation-triangle me-1"></i> تأكيد الحذف</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-3">
                <p class="mb-1">هل أنت متأكد من حذف:</p>
                <strong id="deleteAssessmentName"></strong>
            </div>
            <div class="modal-footer justify-content-center py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash me-1"></i> حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let editingId = null;

function editAssessment(id, name, price, description, isActive) {
    editingId = id;

    document.getElementById('assessmentName').value = name;
    document.getElementById('assessmentPrice').value = price;
    document.getElementById('assessmentDescription').value = description;
    document.getElementById('assessmentActive').checked = isActive;

    document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit me-1"></i> تعديل: ' + name;
    document.getElementById('assessmentForm').action = '{{ url("admin/assessments") }}/' + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-save me-1"></i> تحديث';
    document.getElementById('btnCancel').classList.remove('d-none');

    // تمييز الصف المحدد
    document.querySelectorAll('tbody tr').forEach(tr => tr.classList.remove('table-warning'));
    document.getElementById('row-' + id)?.classList.add('table-warning');

    // التمرير للفورم
    document.getElementById('assessmentForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function resetForm() {
    editingId = null;

    document.getElementById('assessmentForm').reset();
    document.getElementById('assessmentActive').checked = true;
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle me-1"></i> إضافة مقياس جديد';
    document.getElementById('assessmentForm').action = '{{ route("admin.assessments.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-save me-1"></i> حفظ';
    document.getElementById('btnCancel').classList.add('d-none');

    document.querySelectorAll('tbody tr').forEach(tr => tr.classList.remove('table-warning'));
}

function deleteAssessment(id, name) {
    document.getElementById('deleteAssessmentName').textContent = name;
    document.getElementById('deleteForm').action = '{{ url("admin/assessments") }}/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
