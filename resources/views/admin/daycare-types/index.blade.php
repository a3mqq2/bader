@extends('layouts.app')

@section('title', 'أنواع الرعاية النهارية')

@section('content')
<div class="row">
    <!-- فورم الإضافة/التعديل - اليسار -->
    <div class="col-md-4">
        <div class="card sticky-top" style="top: 100px;">
            <div class="card-header text-white" style="background: #063973;">
                <h6 class="mb-0 text-white" id="formTitle">
                    <i class="fas fa-plus-circle me-1"></i> إضافة نوع رعاية جديد
                </h6>
            </div>
            <div class="card-body">
                <form id="daycareForm" method="POST" action="{{ route('admin.daycare-types.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-tag me-1"></i> اسم نوع الرعاية <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="typeName" class="form-control" placeholder="مثال: رعاية نهارية – نصف يوم" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-money-bill me-1"></i> السعر الشهري (د.ل) <span class="text-danger">*</span></label>
                        <input type="number" name="price" id="typePrice" class="form-control" placeholder="0.00" step="0.01" min="0" required>
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

    <!-- جدول الأنواع - اليمين -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-child me-1"></i>
                        قائمة أنواع الرعاية النهارية
                        <span class="badge bg-secondary ms-1">{{ $types->count() }}</span>
                    </h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>نوع الرعاية</th>
                                <th width="20%">السعر الشهري</th>
                                <th width="15%">الحالة</th>
                                <th width="20%" class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($types as $type)
                                <tr id="row-{{ $type->id }}">
                                    <td>
                                        <div class="fw-semibold">{{ $type->name }}</div>
                                    </td>
                                    <td>
                                        <span class="fw-bold" style="color: #063973;">{{ $type->formatted_price }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $type->status_color }}">{{ $type->status_text }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                onclick="editType({{ $type->id }}, '{{ addslashes($type->name) }}', {{ $type->price }})"
                                                title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.daycare-types.toggle', $type) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $type->is_active ? 'secondary' : 'success' }}" title="{{ $type->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}">
                                                <i class="fas fa-{{ $type->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteType({{ $type->id }}, '{{ addslashes($type->name) }}')" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-child fa-2x text-muted mb-2 d-block"></i>
                                        <p class="text-muted mb-0">لا توجد أنواع رعاية نهارية</p>
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
                <strong id="deleteTypeName"></strong>
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

function editType(id, name, price) {
    editingId = id;

    document.getElementById('typeName').value = name;
    document.getElementById('typePrice').value = price;

    document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit me-1"></i> تعديل: ' + name;
    document.getElementById('daycareForm').action = '{{ url("admin/daycare-types") }}/' + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-save me-1"></i> تحديث';
    document.getElementById('btnCancel').classList.remove('d-none');

    // تمييز الصف المحدد
    document.querySelectorAll('tbody tr').forEach(tr => tr.classList.remove('table-warning'));
    document.getElementById('row-' + id)?.classList.add('table-warning');

    // التمرير للفورم
    document.getElementById('daycareForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function resetForm() {
    editingId = null;

    document.getElementById('daycareForm').reset();
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle me-1"></i> إضافة نوع رعاية جديد';
    document.getElementById('daycareForm').action = '{{ route("admin.daycare-types.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-save me-1"></i> حفظ';
    document.getElementById('btnCancel').classList.add('d-none');

    document.querySelectorAll('tbody tr').forEach(tr => tr.classList.remove('table-warning'));
}

function deleteType(id, name) {
    document.getElementById('deleteTypeName').textContent = name;
    document.getElementById('deleteForm').action = '{{ url("admin/daycare-types") }}/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
