@extends('layouts.app')

@section('title', 'حسابات الموظفين')

@section('content')
<div class="row">
    <!-- إحصائيات -->
    <div class="col-md-4 mb-3">
        <div class="card border-0 bg-light-secondary">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar bg-secondary text-white">
                            <i class="ti ti-users fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1 text-muted small">إجمالي الموظفين</p>
                        <h4 class="mb-0">{{ $stats['total_employees'] }} <small class="fs-6 text-muted">موظف</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-0 bg-light-success">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar bg-success text-white">
                            <i class="ti ti-trending-up fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1 text-muted small">أرصدة لهم (دائنة)</p>
                        <h4 class="mb-0 text-success">{{ number_format($stats['positive_balance'], 2) }} <small class="fs-6">د.ل</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-0 bg-light-danger">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar bg-danger text-white">
                            <i class="ti ti-trending-down fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1 text-muted small">أرصدة عليهم (مدينة)</p>
                        <h4 class="mb-0 text-danger">{{ number_format(abs($stats['negative_balance']), 2) }} <small class="fs-6">د.ل</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلترة -->
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header py-2">
                <a class="text-dark text-decoration-none d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#filterCollapse">
                    <span><i class="ti ti-filter me-2"></i>البحث والفلترة</span>
                    <i class="ti ti-chevron-down"></i>
                </a>
            </div>
            <div class="collapse {{ request()->hasAny(['search', 'balance_status']) ? 'show' : '' }}" id="filterCollapse">
                <div class="card-body py-3">
                    <form method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label small">بحث</label>
                            <input type="text" name="search" class="form-control" placeholder="الاسم، الكود، الهاتف..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">حالة الرصيد</label>
                            <select name="balance_status" class="form-select">
                                <option value="">الكل</option>
                                <option value="positive" {{ request('balance_status') === 'positive' ? 'selected' : '' }}>له (دائن)</option>
                                <option value="negative" {{ request('balance_status') === 'negative' ? 'selected' : '' }}>عليه (مدين)</option>
                                <option value="zero" {{ request('balance_status') === 'zero' ? 'selected' : '' }}>متوازن (صفر)</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search me-1"></i> بحث
                            </button>
                            @if(request()->hasAny(['search', 'balance_status']))
                                <a href="{{ route('accountant.employee-accounts.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-x"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول الموظفين -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="ti ti-wallet me-2"></i>
                    حسابات الموظفين
                    <span class="badge bg-secondary ms-2">{{ $employees->total() }}</span>
                </h6>
                <a href="{{ route('accountant.employee-accounts.print-all', request()->all()) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-printer me-1"></i> طباعة الكشف
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="8%">الكود</th>
                                <th width="22%">الموظف</th>
                                <th width="12%" class="text-center">الحركات</th>
                                <th width="18%" class="text-center">الرصيد</th>
                                <th width="40%" class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $employee->code }}</span></td>
                                    <td>
                                        <div class="fw-bold">{{ $employee->name }}</div>
                                        <small class="text-muted">{{ $employee->phone ?: '-' }}</small>
                                        @if($employee->has_bank_account)
                                            <div class="small text-info"><i class="ti ti-building-bank me-1"></i>{{ $employee->bank_name }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $employee->account_transactions_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold fs-6 text-{{ $employee->balance_color }}">
                                            {{ number_format($employee->account_balance, 2) }} د.ل
                                        </div>
                                        <small class="text-{{ $employee->balance_color }}">{{ $employee->balance_status }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                            <a href="{{ route('accountant.employee-accounts.show', $employee) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="ti ti-eye me-1"></i> الحساب
                                            </a>
                                            <button type="button" class="btn btn-warning btn-sm" onclick="openAdvanceModal({{ $employee->id }}, '{{ $employee->name }}', {{ $employee->account_balance }}, '{{ $employee->bank_name }}', '{{ $employee->bank_account_number }}', '{{ $employee->bank_account_name }}')">
                                                <i class="ti ti-cash me-1"></i> سلفة
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm" onclick="openTransactionModal({{ $employee->id }}, '{{ $employee->name }}', {{ $employee->account_balance }}, 'bonus')">
                                                <i class="ti ti-gift me-1"></i> مكافأة
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="openTransactionModal({{ $employee->id }}, '{{ $employee->name }}', {{ $employee->account_balance }}, 'deduction')">
                                                <i class="ti ti-minus me-1"></i> خصم
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="ti ti-users fs-1 d-block mb-2"></i>
                                        لا يوجد موظفين
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($employees->hasPages())
            <div class="card-footer">
                {{ $employees->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Advance Modal (سلفة) -->
<div class="modal fade" id="advanceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h6 class="modal-title">
                    <i class="ti ti-cash me-2"></i>
                    <span id="advanceModalTitle">صرف سلفة</span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="advanceForm" method="POST">
                @csrf
                <input type="hidden" name="transaction_type" value="advance">
                <div class="modal-body">
                    <!-- معلومات الموظف -->
                    <div class="bg-light rounded p-3 mb-4">
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted d-block">الموظف</small>
                                <strong id="advanceEmployeeName" class="text-primary"></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">الرصيد الحالي</small>
                                <strong id="advanceEmployeeBalance"></strong>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- المبلغ -->
                        <div class="col-md-6">
                            <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                                <span class="input-group-text">د.ل</span>
                            </div>
                        </div>

                        <!-- طريقة الدفع -->
                        <div class="col-md-6">
                            <label class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                            <select name="payment_method" id="advancePaymentMethod" class="form-select" required onchange="togglePaymentFields()">
                                <option value="">اختر...</option>
                                <option value="cash">نقدي (كاش)</option>
                                <option value="bank_transfer">تحويل بنكي</option>
                            </select>
                        </div>

                        <!-- حقول الدفع النقدي -->
                        <div class="col-12" id="cashFields" style="display: none;">
                            <label class="form-label">الخزينة <span class="text-danger">*</span></label>
                            <select name="treasury_id" id="advanceTreasurySelect" class="form-select">
                                <option value="">اختر الخزينة...</option>
                                @foreach($treasuries as $treasury)
                                    <option value="{{ $treasury->id }}">{{ $treasury->name }} ({{ number_format($treasury->current_balance, 2) }} د.ل)</option>
                                @endforeach
                            </select>
                            <small class="text-muted">سيتم خصم المبلغ من الخزينة المختارة</small>
                        </div>

                        <!-- حقول التحويل البنكي -->
                        <div class="col-12" id="bankFields" style="display: none;">
                            <div class="card border-info">
                                <div class="card-header bg-light-info py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><i class="ti ti-building-bank me-2"></i>بيانات الحساب البنكي</span>
                                        <button type="button" class="btn btn-sm btn-outline-info" id="useSavedAccount" style="display: none;" onclick="fillSavedBankAccount()">
                                            <i class="ti ti-download me-1"></i> استخدام المحفوظ
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">اسم المصرف <span class="text-danger">*</span></label>
                                            <input type="text" name="bank_name" id="advanceBankName" class="form-control" placeholder="مثال: مصرف الوحدة">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">رقم الحساب <span class="text-danger">*</span></label>
                                            <input type="text" name="bank_account_number" id="advanceBankAccountNumber" class="form-control" placeholder="رقم الحساب البنكي">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">اسم صاحب الحساب</label>
                                            <input type="text" name="bank_account_name" id="advanceBankAccountName" class="form-control" placeholder="اسم صاحب الحساب">
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input type="checkbox" name="save_bank_account" value="1" class="form-check-input" id="saveBankAccount">
                                                <label class="form-check-label" for="saveBankAccount">
                                                    حفظ بيانات الحساب البنكي للموظف
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الوصف -->
                        <div class="col-12">
                            <label class="form-label">الوصف / السبب</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="سبب السلفة..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="ti ti-check me-1"></i> صرف السلفة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transaction Modal (مكافأة/خصم) -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="modalHeader">
                <h6 class="modal-title">
                    <i class="ti ti-exchange me-2"></i>
                    <span id="modalTitle">حركة مالية</span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="transactionForm" method="POST">
                @csrf
                <input type="hidden" name="transaction_type" id="transactionType">
                <div class="modal-body">
                    <!-- معلومات الموظف -->
                    <div class="bg-light rounded p-3 mb-4">
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted d-block">الموظف</small>
                                <strong id="employeeName" class="text-primary"></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">الرصيد الحالي</small>
                                <strong id="employeeBalance"></strong>
                            </div>
                        </div>
                    </div>

                    <div class="alert" id="transactionAlert">
                        <span id="alertText"></span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                                <span class="input-group-text">د.ل</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">الوصف / السبب</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="سبب العملية..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn" id="submitBtn">
                        <i class="ti ti-check me-1"></i> <span id="submitText">تأكيد</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // بيانات الحساب البنكي المحفوظة
    let savedBankData = {
        bank_name: '',
        bank_account_number: '',
        bank_account_name: ''
    };

    function openAdvanceModal(employeeId, employeeName, balance, bankName, bankAccountNumber, bankAccountName) {
        document.getElementById('advanceEmployeeName').textContent = employeeName;
        document.getElementById('advanceModalTitle').textContent = 'صرف سلفة - ' + employeeName;

        const balanceEl = document.getElementById('advanceEmployeeBalance');
        balanceEl.textContent = balance.toFixed(2) + ' د.ل';
        balanceEl.className = balance > 0 ? 'text-success' : (balance < 0 ? 'text-danger' : 'text-secondary');

        document.getElementById('advanceForm').action = '/accountant/employee-accounts/' + employeeId + '/transaction';

        // حفظ بيانات الحساب البنكي
        savedBankData = {
            bank_name: bankName || '',
            bank_account_number: bankAccountNumber || '',
            bank_account_name: bankAccountName || ''
        };

        // إظهار زر استخدام المحفوظ إذا كان هناك حساب محفوظ
        const useSavedBtn = document.getElementById('useSavedAccount');
        if (bankName && bankAccountNumber) {
            useSavedBtn.style.display = 'inline-block';
        } else {
            useSavedBtn.style.display = 'none';
        }

        // Reset form
        document.getElementById('advanceForm').reset();
        document.getElementById('cashFields').style.display = 'none';
        document.getElementById('bankFields').style.display = 'none';

        new bootstrap.Modal(document.getElementById('advanceModal')).show();
    }

    function togglePaymentFields() {
        const method = document.getElementById('advancePaymentMethod').value;
        const cashFields = document.getElementById('cashFields');
        const bankFields = document.getElementById('bankFields');
        const treasurySelect = document.getElementById('advanceTreasurySelect');
        const bankName = document.getElementById('advanceBankName');
        const bankAccountNumber = document.getElementById('advanceBankAccountNumber');

        if (method === 'cash') {
            cashFields.style.display = 'block';
            bankFields.style.display = 'none';
            treasurySelect.required = true;
            bankName.required = false;
            bankAccountNumber.required = false;
        } else if (method === 'bank_transfer') {
            cashFields.style.display = 'none';
            bankFields.style.display = 'block';
            treasurySelect.required = false;
            bankName.required = true;
            bankAccountNumber.required = true;
        } else {
            cashFields.style.display = 'none';
            bankFields.style.display = 'none';
            treasurySelect.required = false;
            bankName.required = false;
            bankAccountNumber.required = false;
        }
    }

    function fillSavedBankAccount() {
        document.getElementById('advanceBankName').value = savedBankData.bank_name;
        document.getElementById('advanceBankAccountNumber').value = savedBankData.bank_account_number;
        document.getElementById('advanceBankAccountName').value = savedBankData.bank_account_name;
    }

    function openTransactionModal(employeeId, employeeName, balance, type) {
        document.getElementById('transactionType').value = type;
        document.getElementById('employeeName').textContent = employeeName;

        const balanceEl = document.getElementById('employeeBalance');
        balanceEl.textContent = balance.toFixed(2) + ' د.ل';
        balanceEl.className = balance > 0 ? 'text-success' : (balance < 0 ? 'text-danger' : 'text-secondary');

        document.getElementById('transactionForm').action = '/accountant/employee-accounts/' + employeeId + '/transaction';

        const modalHeader = document.getElementById('modalHeader');
        const alert = document.getElementById('transactionAlert');
        const alertText = document.getElementById('alertText');
        const modalTitle = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');

        if (type === 'bonus') {
            modalHeader.className = 'modal-header bg-success text-white';
            modalTitle.textContent = 'إضافة مكافأة - ' + employeeName;
            alert.className = 'alert alert-success';
            alertText.textContent = 'سيتم تسجيل المكافأة وإضافتها لرصيد الموظف (بدون تأثير على الخزينة)';
            submitBtn.className = 'btn btn-success';
            submitText.textContent = 'تسجيل المكافأة';
        } else {
            modalHeader.className = 'modal-header bg-danger text-white';
            modalTitle.textContent = 'تسجيل خصم - ' + employeeName;
            alert.className = 'alert alert-danger';
            alertText.textContent = 'سيتم تسجيل الخصم وإنقاصه من رصيد الموظف (بدون تأثير على الخزينة)';
            submitBtn.className = 'btn btn-danger';
            submitText.textContent = 'تسجيل الخصم';
        }

        // Reset form
        document.getElementById('transactionForm').reset();
        document.getElementById('transactionType').value = type;

        new bootstrap.Modal(document.getElementById('transactionModal')).show();
    }
</script>
@endpush
