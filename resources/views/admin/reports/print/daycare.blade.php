@extends('admin.reports.print.layout')

@section('title', 'تقرير الرعاية النهارية')

@section('content')
<div class="stats-row">
    <div class="stat-box">
        <h4>{{ $stats['total_subscriptions'] }}</h4>
        <span>إجمالي الاشتراكات</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['active'] }}</h4>
        <span>اشتراكات نشطة</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['total_present'] }}</h4>
        <span>أيام الحضور</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['total_absent'] }}</h4>
        <span>أيام الغياب</span>
    </div>
    <div class="stat-box">
        <h4>{{ number_format($stats['attendance_rate'], 1) }}%</h4>
        <span>نسبة الحضور</span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>الطالب</th>
            <th>الكود</th>
            <th>نوع الرعاية</th>
            <th>تاريخ البدء</th>
            <th>تاريخ الانتهاء</th>
            <th>أيام الحضور</th>
            <th>أيام الغياب</th>
            <th>الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($subscriptions as $index => $sub)
        @php
            $presentDays = $sub->attendances->where('status', 'present')->count();
            $absentDays = $sub->attendances->where('status', 'absent')->count();
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $sub->student?->name ?? '-' }}</td>
            <td>{{ $sub->student?->code ?? '-' }}</td>
            <td>{{ $sub->daycareType?->name ?? '-' }}</td>
            <td>{{ $sub->start_date?->format('Y/m/d') ?? '-' }}</td>
            <td>{{ $sub->end_date?->format('Y/m/d') ?? '-' }}</td>
            <td class="text-success">{{ $presentDays }}</td>
            <td class="text-danger">{{ $absentDays }}</td>
            <td>
                <span class="badge badge-{{ $sub->status === 'active' ? 'success' : 'secondary' }}">
                    {{ $sub->status === 'active' ? 'نشط' : ($sub->status === 'cancelled' ? 'ملغي' : 'منتهي') }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<p style="text-align: left; font-size: 11px; color: #666;">
    إجمالي السجلات: {{ $subscriptions->count() }}
</p>
@endsection
