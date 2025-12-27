@extends('layouts.app')

@section('title', 'الحركات المالية')

@section('content')
<div class="row">
    <!-- الفلترة -->
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header py-2">
                <a class="text-dark text-decoration-none d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#filterCollapse">
                    <span><i class="ti ti-filter me-2"></i>البحث والفلترة</span>
                    <i class="ti ti-chevron-down"></i>
                </a>
            </div>
            <div class="collapse {{ request()->hasAny(['treasury_id', 'category_id', 'type', 'payment_method', 'date_from', 'date_to', 'search']) ? 'show' : '' }}" id="filterCollapse">
                <div class="card-body py-3">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small">النوع</label>
                            <select name="type" id="typeFilter" class="form-select">
                                <option value="">الكل</option>
                                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>إيراد</option>
                                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>مصروف</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">التصنيف</label>
                            <select name="category_id" id="categoryFilter" class="form-select">
                                <option value="">الكل</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" data-type="{{ $category->type }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">الخزينة</label>
                            <select name="treasury_id" class="form-select">
                                <option value="">الكل</option>
                                @foreach($treasuries as $treasury)
                                    <option value="{{ $treasury->id }}" {{ request('treasury_id') == $treasury->id ? 'selected' : '' }}>{{ $treasury->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">طريقة الدفع</label>
                            <select name="payment_method" class="form-select">
                                <option value="">الكل</option>
                                <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>نقدي</option>
                                <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">من تاريخ</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">إلى تاريخ</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">بحث</label>
                            <input type="text" name="search" class="form-control" placeholder="وصف، ملاحظات..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search me-1"></i> بحث
                            </button>
                            @if(request()->hasAny(['treasury_id', 'category_id', 'type', 'payment_method', 'date_from', 'date_to', 'search']))
                                <a href="{{ route('accountant.finance.transactions.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-x me-1"></i> إلغاء
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
                    <i class="ti ti-arrows-exchange me-2"></i>
                    الحركات المالية
                    <span class="badge bg-secondary ms-2">{{ $transactions->total() }}</span>
                </h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('accountant.finance.transactions.print-report', request()->query()) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-printer me-1"></i> طباعة الكشف
                    </a>
                    <a href="{{ route('accountant.finance.transactions.create') }}" class="btn btn-primary btn-sm">
                        <i class="ti ti-plus me-1"></i> إضافة حركة
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th width="10%">التاريخ</th>
                                <th width="12%">الخزينة</th>
                                <th width="15%">التصنيف</th>
                                <th>الوصف</th>
                                <th width="8%" class="text-center">النوع</th>
                                <th width="12%" class="text-center">المبلغ</th>
                                <th width="10%" class="text-center">الرصيد بعد</th>
                                <th width="6%" class="text-center">طباعة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>
                                        <div>{{ $transaction->created_at->format('Y/m/d') }}</div>
                                        <small class="text-muted">{{ $transaction->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>{{ $transaction->treasury->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->type === 'income' ? 'light-success' : 'light-danger' }} text-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">
                                            {{ $transaction->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction->description ?: '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">
                                            {{ $transaction->type === 'income' ? 'إيراد' : 'مصروف' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">
                                            {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold">{{ number_format($transaction->balance_after, 2) }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="printReceipt({{ $transaction->id }})" title="طباعة">
                                            <i class="ti ti-printer"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="ti ti-arrows-exchange fs-1 d-block mb-2"></i>
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

@push('scripts')
<script>
function filterCategories() {
    const type = document.getElementById('typeFilter').value;
    const categorySelect = document.getElementById('categoryFilter');
    const options = categorySelect.querySelectorAll('option[data-type]');

    options.forEach(option => {
        if (!type || option.dataset.type === type) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
            // If currently selected option is hidden, reset
            if (option.selected) {
                categorySelect.value = '';
            }
        }
    });
}

// Event listener
document.getElementById('typeFilter').addEventListener('change', filterCategories);

// Initialize on page load
document.addEventListener('DOMContentLoaded', filterCategories);

function printReceipt(transactionId) {
    window.open(`{{ url('accountant/finance/transactions') }}/${transactionId}/print`, '_blank', 'width=800,height=600');
}

@if(session('print_transaction_id'))
document.addEventListener('DOMContentLoaded', function() {
    printReceipt({{ session('print_transaction_id') }});
});
@endif
</script>
@endpush
@endsection
