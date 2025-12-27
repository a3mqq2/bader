@extends('layouts.app')

@section('title', 'الجلسات الفردية')

@section('content')
<div class="row">
    <!-- فورم الإضافة/التعديل - اليسار -->
    <div class="col-md-4">
        <div class="card sticky-top" style="top: 100px;">
            <div class="card-header text-white" style="background: #063973;">
                <h6 class="mb-0 text-white" id="formTitle">
                    <i class="fas fa-plus-circle me-1"></i> إضافة جلسة جديدة
                </h6>
            </div>
            <div class="card-body">
                <form id="sessionForm" method="POST" action="{{ route('admin.therapy-sessions.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-tag me-1"></i> اسم الجلسة <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="sessionName" class="form-control" placeholder="مثال: جلسة تخاطب" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-money-bill me-1"></i> السعر (د.ل) <span class="text-danger">*</span></label>
                        <input type="number" name="price" id="sessionPrice" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn text-white" style="background: #063973;" id="btnSubmit">
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

    <!-- جدول الجلسات - اليمين -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar-check me-1"></i>
                        قائمة الجلسات الفردية
                        <span class="badge bg-secondary ms-1">{{ $sessions->count() }}</span>
                    </h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>الجلسة</th>
                                <th width="20%">السعر</th>
                                <th width="15%">الحالة</th>
                                <th width="20%" class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                                <tr id="row-{{ $session->id }}">
                                    <td>
                                        <div class="fw-semibold">{{ $session->name }}</div>
                                    </td>
                                    <td>
                                        <span class="fw-bold" style="color: #063973;">{{ $session->formatted_price }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $session->status_color }}">{{ $session->status_text }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                onclick="editSession({{ $session->id }}, '{{ addslashes($session->name) }}', {{ $session->price }})"
                                                title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.therapy-sessions.toggle', $session) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $session->is_active ? 'secondary' : 'success' }}" title="{{ $session->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}">
                                                <i class="fas fa-{{ $session->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSession({{ $session->id }}, '{{ addslashes($session->name) }}')" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-calendar-check fa-2x text-muted mb-2 d-block"></i>
                                        <p class="text-muted mb-0">لا توجد جلسات</p>
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
                <strong id="deleteSessionName"></strong>
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

function editSession(id, name, price) {
    editingId = id;

    document.getElementById('sessionName').value = name;
    document.getElementById('sessionPrice').value = price;

    document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit me-1"></i> تعديل: ' + name;
    document.getElementById('sessionForm').action = '{{ url("admin/therapy-sessions") }}/' + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-save me-1"></i> تحديث';
    document.getElementById('btnCancel').classList.remove('d-none');

    // تمييز الصف المحدد
    document.querySelectorAll('tbody tr').forEach(tr => tr.classList.remove('table-warning'));
    document.getElementById('row-' + id)?.classList.add('table-warning');

    // التمرير للفورم
    document.getElementById('sessionForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function resetForm() {
    editingId = null;

    document.getElementById('sessionForm').reset();
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle me-1"></i> إضافة جلسة جديدة';
    document.getElementById('sessionForm').action = '{{ route("admin.therapy-sessions.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-save me-1"></i> حفظ';
    document.getElementById('btnCancel').classList.add('d-none');

    document.querySelectorAll('tbody tr').forEach(tr => tr.classList.remove('table-warning'));
}

function deleteSession(id, name) {
    document.getElementById('deleteSessionName').textContent = name;
    document.getElementById('deleteForm').action = '{{ url("admin/therapy-sessions") }}/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
