@extends('layouts.app')

@section('title', 'تقرير الفواتير')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- فلتر التاريخ -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="ti ti-filter me-2"></i>
                    فلترة التقرير
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">حالة الفاتورة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>مدفوعة جزئياً</option>
                            <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>غير مدفوعة</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-search me-1"></i> بحث
                        </button>
                        <a href="{{ route('admin.reports.invoices') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-refresh"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- التقرير -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="ti ti-file-invoice me-2"></i>
                    تقرير الفواتير
                    <span class="badge bg-danger ms-2">{{ $invoices->count() }}</span>
                </h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reports.invoices', array_merge(request()->query(), ['print' => 1])) }}"
                       class="btn btn-outline-info btn-sm" target="_blank">
                        <i class="ti ti-printer me-1"></i> طباعة
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-right me-1"></i> رجوع
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- ملخص مالي -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center p-3">
                                <h4 class="mb-1">{{ number_format($stats['total_amount'], 2) }}</h4>
                                <small>إجمالي الفواتير (د.ل)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center p-3">
                                <h4 class="mb-1">{{ number_format($stats['total_paid'], 2) }}</h4>
                                <small>إجمالي المدفوع (د.ل)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center p-3">
                                <h4 class="mb-1">{{ number_format($stats['total_remaining'], 2) }}</h4>
                                <small>إجمالي المتبقي (د.ل)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center p-3">
                                <h4 class="mb-1">{{ number_format($stats['collection_rate'], 1) }}%</h4>
                                <small>نسبة التحصيل</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إحصائيات حسب الحالة -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="bg-success bg-opacity-10 rounded p-3 text-center">
                            <h5 class="mb-1 text-success">{{ $stats['paid_count'] }}</h5>
                            <small class="text-muted">فواتير مدفوعة</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-warning bg-opacity-10 rounded p-3 text-center">
                            <h5 class="mb-1 text-warning">{{ $stats['partial_count'] }}</h5>
                            <small class="text-muted">فواتير مدفوعة جزئياً</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-danger bg-opacity-10 rounded p-3 text-center">
                            <h5 class="mb-1 text-danger">{{ $stats['unpaid_count'] }}</h5>
                            <small class="text-muted">فواتير غير مدفوعة</small>
                        </div>
                    </div>
                </div>

                <!-- جدول الفواتير -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-danger">
                            <tr>
                                <th>#</th>
                                <th>رقم الفاتورة</th>
                                <th>الطالب</th>
                                <th>النوع</th>
                                <th>المبلغ الإجمالي</th>
                                <th>المدفوع</th>
                                <th>المتبقي</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th>الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $index => $invoice)
                            @php
                                $remaining = $invoice->total - $invoice->paid;
                                if ($remaining <= 0) {
                                    $statusText = 'مدفوعة';
                                    $statusColor = 'success';
                                } elseif ($invoice->paid > 0) {
                                    $statusText = 'جزئية';
                                    $statusColor = 'warning';
                                } else {
                                    $statusText = 'غير مدفوعة';
                                    $statusColor = 'danger';
                                }
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge bg-secondary">{{ $invoice->invoice_number ?? $invoice->id }}</span></td>
                                <td>
                                    <span class="fw-bold">{{ $invoice->student?->name ?? '-' }}</span>
                                    <br><small class="text-muted">{{ $invoice->student?->code ?? '' }}</small>
                                </td>
                                <td>{{ $invoice->type?->name ?? $invoice->description ?? '-' }}</td>
                                <td class="fw-bold">{{ number_format($invoice->total, 2) }} د.ل</td>
                                <td class="text-success">{{ number_format($invoice->paid, 2) }} د.ل</td>
                                <td class="text-danger">{{ number_format($remaining, 2) }} د.ل</td>
                                <td>
                                    <span class="badge bg-{{ $statusColor }}">{{ $statusText }}</span>
                                </td>
                                <td>{{ $invoice->created_at->format('Y/m/d') }}</td>
                                <td>
                                    <a href="{{ route('admin.invoices.print', $invoice) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                        <i class="ti ti-printer"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="ti ti-file-invoice fa-2x mb-2 d-block"></i>
                                    لا توجد فواتير
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($invoices->count() > 0)
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="4" class="text-start">الإجمالي</td>
                                <td>{{ number_format($stats['total_amount'], 2) }} د.ل</td>
                                <td class="text-success">{{ number_format($stats['total_paid'], 2) }} د.ل</td>
                                <td class="text-danger">{{ number_format($stats['total_remaining'], 2) }} د.ل</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

                <!-- إحصائيات حسب النوع -->
                @if($invoicesByType->count() > 0)
                <div class="mt-4 pt-4 border-top">
                    <h6 class="mb-3">
                        <i class="ti ti-chart-pie text-primary me-2"></i>
                        توزيع الفواتير حسب النوع
                    </h6>
                    <div class="row">
                        @foreach($invoicesByType as $type)
                        <div class="col-md-3 mb-3">
                            <div class="card border h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-primary mb-2">{{ $type->type?->name ?? $type->description ?? 'غير محدد' }}</h6>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">عدد الفواتير:</span>
                                        <span class="fw-bold">{{ $type->count }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">الإجمالي:</span>
                                        <span class="fw-bold">{{ number_format($type->total_amount, 2) }} د.ل</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">المحصّل:</span>
                                        <span class="fw-bold text-success">{{ number_format($type->total_paid, 2) }} د.ل</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
