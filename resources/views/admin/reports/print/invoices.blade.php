@extends('admin.reports.print.layout')

@section('title', 'تقرير الفواتير')

@section('content')
<div class="stats-row">
    <div class="stat-box">
        <h4>{{ $stats['total_invoices'] }}</h4>
        <span>إجمالي الفواتير</span>
    </div>
    <div class="stat-box">
        <h4>{{ number_format($stats['total_amount'], 2) }}</h4>
        <span>إجمالي المبلغ (د.ل)</span>
    </div>
    <div class="stat-box">
        <h4>{{ number_format($stats['paid_amount'], 2) }}</h4>
        <span>المدفوع (د.ل)</span>
    </div>
    <div class="stat-box">
        <h4>{{ number_format($stats['remaining_amount'], 2) }}</h4>
        <span>المتبقي (د.ل)</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['paid_count'] }}</h4>
        <span>فواتير مدفوعة</span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>رقم الفاتورة</th>
            <th>الطالب</th>
            <th>النوع</th>
            <th>المبلغ</th>
            <th>المدفوع</th>
            <th>المتبقي</th>
            <th>التاريخ</th>
            <th>الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $index => $invoice)
        @php
            $remaining = $invoice->total_amount - $invoice->paid_amount;
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $invoice->invoice_number ?? 'INV-'.$invoice->id }}</td>
            <td>{{ $invoice->student?->name ?? '-' }}</td>
            <td>{{ $invoice->invoiceType?->name ?? $invoice->description ?? '-' }}</td>
            <td>{{ number_format($invoice->total_amount, 2) }}</td>
            <td class="text-success">{{ number_format($invoice->paid_amount, 2) }}</td>
            <td class="text-danger">{{ number_format($remaining, 2) }}</td>
            <td>{{ $invoice->created_at?->format('Y/m/d') ?? '-' }}</td>
            <td>
                @if($invoice->status === 'paid')
                    <span class="badge badge-success">مدفوعة</span>
                @elseif($invoice->status === 'partial')
                    <span class="badge badge-warning">جزئية</span>
                @else
                    <span class="badge badge-danger">غير مدفوعة</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="fw-bold" style="background-color: #e9ecef;">
            <td colspan="4">الإجمالي</td>
            <td>{{ number_format($stats['total_amount'], 2) }}</td>
            <td class="text-success">{{ number_format($stats['paid_amount'], 2) }}</td>
            <td class="text-danger">{{ number_format($stats['remaining_amount'], 2) }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

<p style="text-align: left; font-size: 11px; color: #666;">
    إجمالي السجلات: {{ $invoices->count() }}
</p>
@endsection
