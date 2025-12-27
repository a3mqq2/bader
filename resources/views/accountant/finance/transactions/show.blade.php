@extends('layouts.app')

@section('title', 'تفاصيل الحركة المالية')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>
                        تفاصيل الحركة المالية #{{ $transaction->id }}
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.open('{{ route('accountant.finance.transactions.print', $transaction) }}', '_blank', 'width=800,height=600')">
                            <i class="fas fa-print me-1"></i> طباعة
                        </button>
                        <a href="{{ route('accountant.finance.transactions.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i> رجوع
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    معلومات الحركة
                                </h6>
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted" style="width: 40%;">رقم الحركة:</td>
                                        <td class="fw-bold">#{{ $transaction->id }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">التاريخ:</td>
                                        <td>{{ $transaction->created_at->format('Y/m/d H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">النوع:</td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->type_color }}">
                                                {{ $transaction->type_text }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">الخزينة:</td>
                                        <td>
                                            <a href="{{ route('accountant.finance.transactions.index', ['treasury_id' => $transaction->treasury_id]) }}">
                                                {{ $transaction->treasury->name }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">التصنيف:</td>
                                        <td>
                                            <a href="{{ route('accountant.finance.transactions.index', ['category_id' => $transaction->category_id]) }}">
                                                {{ $transaction->category->name }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-{{ $transaction->type_color }} text-white h-100">
                            <div class="card-body d-flex flex-column justify-content-center text-center">
                                <h6 class="mb-2">المبلغ</h6>
                                <h2 class="mb-2">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} د.ل
                                </h2>
                                <small>الرصيد بعد الحركة: {{ number_format($transaction->balance_after, 2) }} د.ل</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-credit-card me-1"></i>
                            تفاصيل الدفع
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">طريقة الدفع:</small>
                                <div>
                                    @if($transaction->payment_method === 'cash')
                                        <span class="badge bg-success"><i class="fas fa-money-bill me-1"></i>{{ $transaction->payment_method_text }}</span>
                                    @else
                                        <span class="badge bg-info"><i class="fas fa-university me-1"></i>{{ $transaction->payment_method_text }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($transaction->document_number)
                                <div class="col-md-4">
                                    <small class="text-muted">رقم المستند:</small>
                                    <div>{{ $transaction->document_number }}</div>
                                </div>
                            @endif
                            @if($transaction->recipient_name)
                                <div class="col-md-4">
                                    <small class="text-muted">اسم المستلم:</small>
                                    <div>{{ $transaction->recipient_name }}</div>
                                </div>
                            @endif
                        </div>

                        @if($transaction->payment_method === 'bank_transfer')
                            <hr>
                            <div class="row">
                                @if($transaction->bank_name)
                                    <div class="col-md-6">
                                        <small class="text-muted">اسم المصرف:</small>
                                        <div>{{ $transaction->bank_name }}</div>
                                    </div>
                                @endif
                                @if($transaction->account_number)
                                    <div class="col-md-6">
                                        <small class="text-muted">رقم الحساب:</small>
                                        <div>{{ $transaction->account_number }}</div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                @if($transaction->description)
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-align-right me-1"></i>
                                الوصف
                            </h6>
                            <p class="mb-0">{{ $transaction->description }}</p>
                        </div>
                    </div>
                @endif

                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-user-edit me-1"></i>
                            معلومات إضافية
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">تم الإنشاء بواسطة:</small>
                                <div>{{ $transaction->creator->name ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">تاريخ الإنشاء:</small>
                                <div>{{ $transaction->created_at->format('Y/m/d H:i:s') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
