@extends('admin.reports.print.layout')

@section('title', 'تقرير دراسات الحالة')

@section('content')
<div class="stats-row">
    <div class="stat-box">
        <h4>{{ $stats['total'] }}</h4>
        <span>إجمالي الحالات</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['pending'] }}</h4>
        <span>قيد الانتظار</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['in_progress'] }}</h4>
        <span>جاري</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['completed'] }}</h4>
        <span>مكتمل</span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>الطالب</th>
            <th>الكود</th>
            <th>حالة الدراسة</th>
            <th>عدد التقييمات</th>
            <th>المبلغ الإجمالي</th>
            <th>المدفوع</th>
            <th>المتبقي</th>
            <th>تاريخ البدء</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cases as $index => $case)
        @php
            $total = $case->invoice?->total ?? 0;
            $paid = $case->invoice?->paid ?? 0;
            $remaining = $total - $paid;
            $statusTexts = ['pending' => 'قيد الانتظار', 'in_progress' => 'جاري', 'completed' => 'مكتمل'];
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $case->student->name ?? '-' }}</td>
            <td>{{ $case->student->code ?? '-' }}</td>
            <td>
                <span class="badge badge-{{ $case->status === 'completed' ? 'success' : ($case->status === 'in_progress' ? 'info' : 'warning') }}">
                    {{ $statusTexts[$case->status] ?? $case->status }}
                </span>
            </td>
            <td>{{ $case->invoice?->items?->count() ?? 0 }}</td>
            <td>{{ number_format($total, 2) }} د.ل</td>
            <td class="text-success">{{ number_format($paid, 2) }} د.ل</td>
            <td class="text-danger">{{ number_format($remaining, 2) }} د.ل</td>
            <td>{{ $case->created_at->format('Y/m/d') }}</td>
        </tr>
        @endforeach
    </tbody>
    @if($cases->count() > 0)
    <tfoot>
        <tr class="fw-bold">
            <td colspan="5">الإجمالي</td>
            <td>{{ number_format($cases->sum(fn($c) => $c->invoice?->total ?? 0), 2) }} د.ل</td>
            <td class="text-success">{{ number_format($cases->sum(fn($c) => $c->invoice?->paid ?? 0), 2) }} د.ل</td>
            <td class="text-danger">{{ number_format($cases->sum(fn($c) => ($c->invoice?->total ?? 0) - ($c->invoice?->paid ?? 0)), 2) }} د.ل</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

<p style="text-align: left; font-size: 11px; color: #666;">
    إجمالي السجلات: {{ $cases->count() }}
</p>
@endsection
