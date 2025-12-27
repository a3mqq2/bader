@extends('layouts.app')

@section('title', 'لوحة تحكم المحاسب')

@section('content')
<div class="row">
    <!-- إحصائيات -->
    <div class="col-md-3 col-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 text-white">إجمالي الأرصدة</h6>
                        <h4 class="mb-0 text-white">{{ number_format($stats['total_balance'], 2) }}</h4>
                        <small>د.ل</small>
                    </div>
                    <i class="fas fa-wallet fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">عدد الخزائن</h6>
                        <h4 class="mb-0">{{ $stats['treasuries_count'] }}</h4>
                        <small>خزينة نشطة</small>
                    </div>
                    <i class="fas fa-vault fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">إيرادات اليوم</h6>
                        <h4 class="mb-0">{{ number_format($stats['today_income'], 2) }}</h4>
                        <small>د.ل</small>
                    </div>
                    <i class="fas fa-arrow-up fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">مصروفات اليوم</h6>
                        <h4 class="mb-0">{{ number_format($stats['today_expense'], 2) }}</h4>
                        <small>د.ل</small>
                    </div>
                    <i class="fas fa-arrow-down fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- إحصائيات الشهر -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    ملخص الشهر الحالي
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h6 class="text-muted">الإيرادات</h6>
                        <h4 class="text-success">{{ number_format($stats['month_income'], 2) }}</h4>
                    </div>
                    <div class="col-4">
                        <h6 class="text-muted">المصروفات</h6>
                        <h4 class="text-danger">{{ number_format($stats['month_expense'], 2) }}</h4>
                    </div>
                    <div class="col-4">
                        <h6 class="text-muted">الصافي</h6>
                        <h4 class="text-primary">{{ number_format($stats['month_income'] - $stats['month_expense'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الخزائن -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-vault me-2"></i>
                    الخزائن المالية
                </h5>
                <a href="{{ route('accountant.finance.treasuries.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>الخزينة</th>
                                <th>الرصيد الحالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($treasuries as $treasury)
                                <tr>
                                    <td>
                                        <a href="{{ route('accountant.finance.transactions.index', ['treasury_id' => $treasury->id]) }}">
                                            {{ $treasury->name }}
                                        </a>
                                    </td>
                                    <td class="fw-bold {{ $treasury->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($treasury->current_balance, 2) }} د.ل
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3">لا توجد خزائن</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- آخر الحركات -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    آخر الحركات المالية
                </h5>
                <a href="{{ route('accountant.finance.transactions.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>التاريخ</th>
                                <th>الخزينة</th>
                                <th>التصنيف</th>
                                <th>النوع</th>
                                <th>المبلغ</th>
                                <th>بواسطة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('Y/m/d H:i') }}</td>
                                    <td>{{ $transaction->treasury->name }}</td>
                                    <td>{{ $transaction->category->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->type_color }}">
                                            {{ $transaction->type_text }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-{{ $transaction->type_color }}">
                                        {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} د.ل
                                    </td>
                                    <td>{{ $transaction->creator->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">لا توجد حركات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
