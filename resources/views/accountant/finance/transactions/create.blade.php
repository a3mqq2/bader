@extends('layouts.app')

@section('title', 'إضافة حركة مالية')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        إضافة حركة مالية جديدة
                    </h5>
                    <a href="{{ route('accountant.finance.transactions.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i> رجوع
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('accountant.finance.transactions.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="treasury_id" class="form-label">
                                <i class="fas fa-vault me-1 text-muted"></i>
                                الخزينة <span class="text-danger">*</span>
                            </label>
                            <select name="treasury_id" id="treasury_id" class="form-select @error('treasury_id') is-invalid @enderror" required>
                                <option value="">اختر الخزينة</option>
                                @foreach($treasuries as $treasury)
                                    <option value="{{ $treasury->id }}" {{ old('treasury_id', $selectedTreasury) == $treasury->id ? 'selected' : '' }}>
                                        {{ $treasury->name }} ({{ number_format($treasury->current_balance, 2) }} د.ل)
                                    </option>
                                @endforeach
                            </select>
                            @error('treasury_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">
                                <i class="fas fa-exchange-alt me-1 text-muted"></i>
                                نوع الحركة <span class="text-danger">*</span>
                            </label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">اختر النوع</option>
                                <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>إيراد</option>
                                <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>مصروف</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">
                                <i class="fas fa-tag me-1 text-muted"></i>
                                التصنيف <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required disabled>
                                    <option value="">اختر نوع الحركة أولاً</option>
                                </select>
                                <button type="button" class="btn btn-outline-primary" id="addCategoryBtn" data-bs-toggle="modal" data-bs-target="#addCategoryModal" disabled>
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">
                                <i class="fas fa-coins me-1 text-muted"></i>
                                المبلغ <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" step="0.01" min="0.01" required>
                                <span class="input-group-text">د.ل</span>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label">
                                <i class="fas fa-credit-card me-1 text-muted"></i>
                                طريقة الدفع <span class="text-danger">*</span>
                            </label>
                            <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                <option value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'selected' : '' }}>نقدي</option>
                                <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="document_number" class="form-label">
                                <i class="fas fa-file-alt me-1 text-muted"></i>
                                رقم المستند
                            </label>
                            <input type="text" name="document_number" id="document_number" class="form-control @error('document_number') is-invalid @enderror" value="{{ old('document_number') }}">
                            @error('document_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="bank_fields" style="display: none;" class="col-12">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="bank_name" class="form-label">
                                        <i class="fas fa-university me-1 text-muted"></i>
                                        اسم المصرف
                                    </label>
                                    <input type="text" name="bank_name" id="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name') }}">
                                    @error('bank_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="account_number" class="form-label">
                                        <i class="fas fa-hashtag me-1 text-muted"></i>
                                        رقم الحساب
                                    </label>
                                    <input type="text" name="account_number" id="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number') }}">
                                    @error('account_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="recipient_name" class="form-label">
                                <i class="fas fa-user me-1 text-muted"></i>
                                اسم المستلم
                            </label>
                            <input type="text" name="recipient_name" id="recipient_name" class="form-control @error('recipient_name') is-invalid @enderror" value="{{ old('recipient_name') }}">
                            @error('recipient_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-right me-1 text-muted"></i>
                                الوصف
                            </label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('accountant.finance.transactions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> حفظ الحركة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal إضافة تصنيف جديد -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>
                    إضافة تصنيف جديد
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="new_category_name" class="form-label">
                        اسم التصنيف <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="new_category_name" required>
                    <div class="invalid-feedback" id="category_name_error"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">نوع التصنيف</label>
                    <input type="text" class="form-control bg-light" id="new_category_type_display" readonly>
                    <input type="hidden" id="new_category_type">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">
                    <i class="fas fa-save me-1"></i> حفظ التصنيف
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const categorySelect = document.getElementById('category_id');
    const addCategoryBtn = document.getElementById('addCategoryBtn');
    const paymentMethod = document.getElementById('payment_method');
    const bankFields = document.getElementById('bank_fields');

    // حفظ القيمة القديمة للتصنيف
    const oldCategoryId = "{{ old('category_id') }}";

    // دالة جلب التصنيفات حسب النوع
    function loadCategories(type, selectedId = null) {
        if (!type) {
            categorySelect.innerHTML = '<option value="">اختر نوع الحركة أولاً</option>';
            categorySelect.disabled = true;
            addCategoryBtn.disabled = true;
            return;
        }

        categorySelect.disabled = true;
        categorySelect.innerHTML = '<option value="">جاري التحميل...</option>';

        fetch(`{{ route('accountant.finance.categories.by-type') }}?type=${type}`)
            .then(response => response.json())
            .then(categories => {
                categorySelect.innerHTML = '<option value="">اختر التصنيف</option>';

                categories.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.name;
                    if (selectedId && cat.id == selectedId) {
                        option.selected = true;
                    }
                    categorySelect.appendChild(option);
                });

                categorySelect.disabled = false;
                addCategoryBtn.disabled = false;

                // تحديث نوع التصنيف في الـ Modal
                document.getElementById('new_category_type').value = type;
                document.getElementById('new_category_type_display').value = type === 'income' ? 'إيراد' : 'مصروف';
            })
            .catch(error => {
                console.error('Error:', error);
                categorySelect.innerHTML = '<option value="">حدث خطأ في التحميل</option>';
            });
    }

    // عند تغيير نوع الحركة
    typeSelect.addEventListener('change', function() {
        loadCategories(this.value, oldCategoryId);
    });

    // تحميل التصنيفات عند تحميل الصفحة إذا كان هناك نوع محدد
    if (typeSelect.value) {
        loadCategories(typeSelect.value, oldCategoryId);
    }

    // إظهار/إخفاء حقول البنك
    function toggleBankFields() {
        if (paymentMethod.value === 'bank_transfer') {
            bankFields.style.display = 'block';
        } else {
            bankFields.style.display = 'none';
        }
    }

    paymentMethod.addEventListener('change', toggleBankFields);
    toggleBankFields();

    // حفظ تصنيف جديد
    document.getElementById('saveCategoryBtn').addEventListener('click', function() {
        const name = document.getElementById('new_category_name').value.trim();
        const type = document.getElementById('new_category_type').value;

        if (!name) {
            document.getElementById('new_category_name').classList.add('is-invalid');
            document.getElementById('category_name_error').textContent = 'يرجى إدخال اسم التصنيف';
            return;
        }

        document.getElementById('new_category_name').classList.remove('is-invalid');

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...';

        fetch('{{ route('accountant.finance.categories.store-ajax') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name, type })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // إضافة التصنيف الجديد للقائمة واختياره
                const option = document.createElement('option');
                option.value = data.category.id;
                option.textContent = data.category.name;
                option.selected = true;
                categorySelect.appendChild(option);

                // إغلاق المودال وتنظيف الحقول
                bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide();
                document.getElementById('new_category_name').value = '';

                // إظهار رسالة نجاح
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: data.message || 'حدث خطأ أثناء الحفظ'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ في الاتصال'
            });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ التصنيف';
        });
    });

    // تنظيف المودال عند إغلاقه
    document.getElementById('addCategoryModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('new_category_name').value = '';
        document.getElementById('new_category_name').classList.remove('is-invalid');
    });
});
</script>
@endpush
@endsection
