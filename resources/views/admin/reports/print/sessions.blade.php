@extends('admin.reports.print.layout')

@section('title', 'تقرير الجلسات')

@section('content')
<div class="stats-row">
    <div class="stat-box">
        <h4>{{ $stats['total'] }}</h4>
        <span>إجمالي الجلسات</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['completed'] }}</h4>
        <span>مكتملة</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['scheduled'] }}</h4>
        <span>مجدولة</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['absent'] }}</h4>
        <span>غياب</span>
    </div>
    <div class="stat-box">
        <h4>{{ number_format($stats['completion_rate'], 1) }}%</h4>
        <span>نسبة الإكمال</span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>الطالب</th>
            <th>نوع الجلسة</th>
            <th>الأخصائي</th>
            <th>التاريخ</th>
            <th>الوقت</th>
            <th>الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sessions as $index => $session)
        @php
            $statusTexts = [
                'scheduled' => 'مجدولة',
                'completed' => 'مكتملة',
                'cancelled' => 'ملغية',
                'absent' => 'غياب'
            ];
            $statusColors = [
                'scheduled' => 'info',
                'completed' => 'success',
                'cancelled' => 'secondary',
                'absent' => 'danger'
            ];
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $session->package?->student?->name ?? '-' }}</td>
            <td>{{ $session->package?->therapySession?->name ?? '-' }}</td>
            <td>{{ $session->package?->specialist?->name ?? '-' }}</td>
            <td>{{ $session->session_date?->format('Y/m/d') ?? '-' }}</td>
            <td>{{ $session->session_time ?? '-' }}</td>
            <td>
                <span class="badge badge-{{ $statusColors[$session->status] ?? 'secondary' }}">
                    {{ $statusTexts[$session->status] ?? $session->status }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<p style="text-align: left; font-size: 11px; color: #666;">
    إجمالي السجلات: {{ $sessions->count() }}
</p>
@endsection
