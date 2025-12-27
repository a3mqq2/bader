@extends('layouts.app')

@section('title', 'حساب الموظف - ' . $user->name)

@section('content')
<div class="row">
    <!-- بيانات الموظف -->
    <div class="col-md-5 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="avtar avtar-xl bg-light-primary">
                        <i class="ti ti-user fs-2 text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="mb-0">{{ $user->name }}</h5>
                            <span class="badge bg-secondary">{{ $user->code }}</span>
                        </div>
                        <div class="d-flex flex-wrap gap-3 text-muted small">
                            @if($user->phone)
                            <span><i class="ti ti-phone me-1"></i>{{ $user->phone }}</span>
                            @endif
                            <span><i class="ti ti-shield me-1"></i>{{ $user->role_text }}</span>
                        </div>
                        @if($user->has_bank_account)
                        <div class="mt-2 small">
                            <span class="badge bg-light-info text-info">
                                <i class="ti ti-building-bank me-1"></i>
                                {{ $user->bank_name }} - {{ $user->bank_account_number }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الرصيد الحالي -->
    <div class="col-md-3 mb-3">
        <div class="card h-100 border-0 bg-light-{{ $user->balance_color }}">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-center">
                    <p class="mb-1 text-muted">الرصيد الحالي</p>
                    <h2 class="mb-0 text-{{ $user->balance_color }}">{{ number_format($user->account_balance, 2) }} <small class="fs-5">د.ل</small></h2>
                    <span class="badge bg-{{ $user->balance_color }} mt-2">{{ $user->balance_status }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- إجراءات سريعة -->
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="text-muted mb-3">
                    <i class="ti ti-bolt me-1"></i>
                    إجراءات سريعة
                </h6>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-warning flex-fill" onclick="openAdvanceModal()">
                        <i class="ti ti-cash me-1"></i> سلفة
                    </button>
                    <button type="button" class="btn btn-success flex-fill" onclick="openTransactionModal('bonus')">
                        <i class="ti ti-gift me-1"></i> مكافأة
                    </button>
                    <button type="button" class="btn btn-danger flex-fill" onclick="openTransactionModal('deduction')">
                        <i class="ti ti-minus me-1"></i> خصم
                    </button>
                </div>
                <div class="d-flex gap-2 mt-2">
                    <a href="{{ route('accountant.employee-accounts.print', array_merge(['user' => $user->id], request()->all())) }}" target="_blank" class="btn btn-outline-secondary flex-fill">
                        <i class="ti ti-printer me-1"></i> طباعة الكشف
                    </a>
                    <a href="{{ route('accountant.employee-accounts.index') }}" class="btn btn-outline-primary flex-fill">
                        <i class="ti ti-arrow-right me-1"></i> رجوع
                    </a>
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
            <div class="collapse {{ request()->hasAny(['type', 'date_from', 'date_to']) ? 'show' : '' }}" id="filterCollapse">
                <div class="card-body py-3">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small">نوع الحركة</label>
                            <select name="type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="advance" {{ request('type') === 'advance' ? 'selected' : '' }}>سلفة</option>
                                <option value="bonus" {{ request('type') === 'bonus' ? 'selected' : '' }}>مكافأة</option>
                                <option value="deduction" {{ request('type') === 'deduction' ? 'selected' : '' }}>خصم</option>
                                <option value="salary" {{ request('type') === 'salary' ? 'selected' : '' }}>راتب</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">من تاريخ</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">إلى تاريخ</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search me-1"></i> بحث
                            </button>
                            @if(request()->hasAny(['type', 'date_from', 'date_to']))
                                <a href="{{ route('accountant.employee-accounts.show', $user) }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-x"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول الحركات -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="ti ti-history me-2"></i>
                    سجل الحركات
                    <span class="badge bg-secondary ms-2">{{ $transactions->total() }}</span>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="11%">التاريخ</th>
                                <th width="9%">النوع</th>
                                <th width="7%">الحركة</th>
                                <th width="10%">المبلغ</th>
                                <th width="10%">الرصيد بعد</th>
                                <th width="10%">طريقة الدفع</th>
                                <th width="20%">الوصف</th>
                                <th width="10%">بواسطة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('Y/m/d H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->transaction_type_color }}">
                                            <i class="ti {{ $transaction->type_icon }} me-1"></i>
                                            {{ $transaction->transaction_type_text }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $transaction->type_color }}">
                                            {{ $transaction->type_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-{{ $transaction->type_color }}">
                                            {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="fw-bold">{{ number_format($transaction->balance_after, 2) }}</td>
                                    <td>
                                        @if($transaction->payment_method)
                                            <span class="badge bg-light-{{ $transaction->payment_method === 'cash' ? 'success' : 'info' }} text-{{ $transaction->payment_method === 'cash' ? 'success' : 'info' }}">
                                                <i class="ti {{ $transaction->payment_method_icon }} me-1"></i>
                                                {{ $transaction->payment_method_text }}
                                            </span>
                                            @if($transaction->payment_method === 'bank_transfer' && $transaction->bank_name)
                                                <div class="small text-muted mt-1">{{ $transaction->bank_name }}</div>
                                            @endif
                                            @if($transaction->treasury)
                                                <div class="small text-muted mt-1">{{ $transaction->treasury->name }}</div>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->description ?: '-' }}</td>
                                    <td class="small text-muted">{{ $transaction->creator->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="ti ti-history fs-1 d-block mb-2"></i>
                                        لا توجد حركات مالية
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($transactions->hasPages())
            <div class="card-footer">
                {{ $transactions->links() }}
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
                    صرف سلفة - {{ $user->name }}
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('accountant.employee-accounts.transaction.store', $user) }}" method="POST">
                @csrf
                <input type="hidden" name="transaction_type" value="advance">
                <div class="modal-body">
                    <!-- معلومات الموظف -->
                    <div class="bg-light rounded p-3 mb-4">
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted d-block">الموظف</small>
                                <strong class="text-primary">{{ $user->name }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">الرصيد الحالي</small>
                                <strong class="text-{{ $user->balance_color }}">{{ number_format($user->account_balance, 2) }} د.ل</strong>
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
                                        @if($user->has_bank_account)
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="fillSavedBankAccount()">
                                            <i class="ti ti-download me-1"></i> استخدام المحفوظ
                                        </button>
                                        @endif
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
                                                    حفظ/تحديث بيانات الحساب البنكي للموظف
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
                    <span id="modalTitleText">حركة مالية</span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('accountant.employee-accounts.transaction.store', $user) }}" method="POST">
                @csrf
                <input type="hidden" name="transaction_type" id="transactionType">
                <div class="modal-body">
                    <!-- معلومات الموظف -->
                    <div class="bg-light rounded p-3 mb-4">
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted d-block">الموظف</small>
                                <strong class="text-primary">{{ $user->name }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">الرصيد الحالي</small>
                                <strong class="text-{{ $user->balance_color }}">{{ number_format($user->account_balance, 2) }} د.ل</strong>
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
    const savedBankData = {
        bank_name: '{{ $user->bank_name ?? '' }}',
        bank_account_number: '{{ $user->bank_account_number ?? '' }}',
        bank_account_name: '{{ $user->bank_account_name ?? '' }}'
    };

    function openAdvanceModal() {
        // Reset form
        document.getElementById('advancePaymentMethod').value = '';
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

    function openTransactionModal(type) {
        document.getElementById('transactionType').value = type;

        const modalHeader = document.getElementById('modalHeader');
        const alert = document.getElementById('transactionAlert');
        const alertText = document.getElementById('alertText');
        const modalTitleText = document.getElementById('modalTitleText');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');

        if (type === 'bonus') {
            modalHeader.className = 'modal-header bg-success text-white';
            modalTitleText.textContent = 'إضافة مكافأة';
            alert.className = 'alert alert-success';
            alertText.textContent = 'سيتم تسجيل المكافأة وإضافتها لرصيد الموظف (بدون تأثير على الخزينة)';
            submitBtn.className = 'btn btn-success';
            submitText.textContent = 'تسجيل المكافأة';
        } else {
            modalHeader.className = 'modal-header bg-danger text-white';
            modalTitleText.textContent = 'تسجيل خصم';
            alert.className = 'alert alert-danger';
            alertText.textContent = 'سيتم تسجيل الخصم وإنقاصه من رصيد الموظف (بدون تأثير على الخزينة)';
            submitBtn.className = 'btn btn-danger';
            submitText.textContent = 'تسجيل الخصم';
        }

        new bootstrap.Modal(document.getElementById('transactionModal')).show();
    }
</script>
@endpush
