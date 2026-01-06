@extends('layouts.app')

@section('title', 'مستحقات الطالب - ' . $student->name)

@section('content')
<div class="row">
    <!-- بيانات الطالب -->
    <div class="col-md-8 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="avtar avtar-xl bg-light-primary">
                        <i class="ti ti-user fs-2 text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="mb-0">{{ $student->name }}</h5>
                            <span class="badge bg-{{ $student->status_color }}">{{ $student->status_text }}</span>
                        </div>
                        <div class="d-flex flex-wrap gap-3 text-muted small">
                            <span><i class="ti ti-id-badge me-1"></i>{{ $student->code }}</span>
                            <span><i class="ti ti-phone me-1"></i>{{ $student->phone ?: '-' }}</span>
                            @if($student->guardian_name)
                            <span><i class="ti ti-user-check me-1"></i>{{ $student->guardian_name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إجمالي المستحقات -->
    <div class="col-md-4 mb-3">
        <div class="card h-100 border-0 bg-light-danger">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-center">
                    <p class="mb-1 text-muted">إجمالي المستحقات</p>
                    <h2 class="mb-0 text-danger">{{ number_format($totalDues, 2) }} <small class="fs-5">د.ل</small></h2>
                    <small class="text-muted">{{ $student->invoices->whereIn('status', ['pending', 'partial'])->count() }} فاتورة مستحقة</small>
                </div>
            </div>
        </div>
    </div>

    <!-- الفواتير -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="ti ti-file-invoice me-2"></i>
                    جميع الفواتير
                </h6>
                <a href="{{ route('accountant.dues.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-right me-1"></i> رجوع
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th width="18%">الفاتورة</th>
                                <th width="12%">التاريخ</th>
                                <th width="12%" class="text-center">الإجمالي</th>
                                <th width="12%" class="text-center">المدفوع</th>
                                <th width="12%" class="text-center">المتبقي</th>
                                <th width="10%" class="text-center">الحالة</th>
                                <th width="24%" class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->invoices as $invoice)
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $invoice->invoice_number }}</strong>
                                        @if($invoice->description)
                                            <div class="small text-muted text-truncate" style="max-width: 200px;">{{ $invoice->description }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $invoice->created_at->format('Y/m/d') }}</td>
                                    <td class="text-center">{{ number_format($invoice->total_amount, 2) }}</td>
                                    <td class="text-center text-success">{{ number_format($invoice->paid_amount, 2) }}</td>
                                    <td class="text-center">
                                        <span class="fw-bold text-danger fs-6">{{ number_format($invoice->balance, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $invoice->status_color }}">{{ $invoice->status_text }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($invoice->status !== 'paid')
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="openPaymentModal({{ $invoice->id }}, '{{ $invoice->invoice_number }}', {{ $invoice->balance }})">
                                                <i class="ti ti-cash me-1"></i> تحصيل
                                            </button>
                                        @else
                                            <span class="badge bg-success"><i class="ti ti-check me-1"></i>مسددة</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($invoice->payments->count() > 0)
                                    <tr class="bg-light">
                                        <td colspan="7" class="py-3 px-4">
                                            <div class="mb-2">
                                                <i class="ti ti-history me-1 text-muted"></i>
                                                <span class="text-muted fw-bold">الدفعات السابقة ({{ $invoice->payments->count() }})</span>
                                            </div>
                                            <div class="d-flex flex-wrap gap-3">
                                                @foreach($invoice->payments as $payment)
                                                    <div class="border rounded bg-white p-2 d-flex align-items-center gap-3" style="min-width: 220px;">
                                                        <div class="flex-grow-1">
                                                            <div class="fw-bold text-success fs-6">{{ number_format($payment->amount, 2) }} د.ل</div>
                                                            <div class="text-muted small">
                                                                <i class="ti ti-calendar me-1"></i>{{ $payment->created_at->format('Y/m/d') }}
                                                                <span class="mx-1">-</span>
                                                                {{ $payment->payment_method == 'cash' ? 'نقداً' : 'تحويل' }}
                                                            </div>
                                                        </div>
                                                        <a href="{{ route('accountant.dues.payment.print', $payment) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                            <i class="ti ti-printer"></i>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="ti ti-file-off fs-1 d-block mb-2"></i>
                                        لا توجد فواتير
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

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h6 class="modal-title"><i class="ti ti-cash me-2"></i>تحصيل دفعة</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- معلومات الفاتورة -->
                    <div class="bg-light rounded p-3 mb-4">
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted d-block">رقم الفاتورة</small>
                                <strong id="invoiceNumber" class="text-primary"></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">المبلغ المتبقي</small>
                                <strong class="text-danger"><span id="invoiceBalance"></span> د.ل</strong>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="amount" id="amountInput" class="form-control" step="0.01" min="0.01" required>
                                <span class="input-group-text">د.ل</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الخزينة <span class="text-danger">*</span></label>
                            <select name="treasury_id" class="form-select" required>
                                <option value="">اختر الخزينة...</option>
                                @foreach($treasuries as $treasury)
                                    <option value="{{ $treasury->id }}">{{ $treasury->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">طريقة الدفع</label>
                            <select name="payment_method" id="paymentMethod" class="form-select" onchange="toggleBankFields()">
                                <option value="cash">نقداً</option>
                                <option value="bank_transfer">تحويل بنكي</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="bankFields" style="display: none;">
                            <label class="form-label">اسم المصرف</label>
                            <input type="text" name="bank_name" id="bankName" class="form-control" placeholder="اسم المصرف">
                        </div>
                        <div class="col-12">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="ملاحظات إضافية (اختياري)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-check me-1"></i> تأكيد التحصيل
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openPaymentModal(invoiceId, invoiceNumber, balance) {
        document.getElementById('invoiceNumber').textContent = invoiceNumber;
        document.getElementById('invoiceBalance').textContent = balance.toFixed(2);
        document.getElementById('amountInput').value = balance.toFixed(2);
        document.getElementById('amountInput').max = balance;
        document.getElementById('paymentForm').action = '{{ url("accountant/dues/invoice") }}/' + invoiceId + '/payment';

        // Reset
        document.getElementById('paymentMethod').value = 'cash';
        document.getElementById('bankName').value = '';
        toggleBankFields();

        new bootstrap.Modal(document.getElementById('paymentModal')).show();
    }

    function toggleBankFields() {
        const show = document.getElementById('paymentMethod').value === 'bank_transfer';
        document.getElementById('bankFields').style.display = show ? 'block' : 'none';
        document.getElementById('bankName').required = show;
    }

    @if(session('print_payment_id'))
        window.open('{{ route("accountant.dues.payment.print", session("print_payment_id")) }}', '_blank', 'width=800,height=600');
    @endif
</script>
@endpush
