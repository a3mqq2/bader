@extends('admin.reports.print.layout')

@section('title', 'تقرير الأخصائيين')

@section('content')
<div class="stats-row">
    <div class="stat-box">
        <h4>{{ $stats['total_specialists'] }}</h4>
        <span>عدد الأخصائيين</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['total_sessions'] }}</h4>
        <span>إجمالي الجلسات</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['completed_sessions'] }}</h4>
        <span>جلسات مكتملة</span>
    </div>
    <div class="stat-box">
        <h4>{{ number_format($stats['avg_completion_rate'], 1) }}%</h4>
        <span>متوسط نسبة الإنجاز</span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>الأخصائي</th>
            <th>عدد الطلاب</th>
            <th>إجمالي الجلسات</th>
            <th>مكتملة</th>
            <th>مجدولة</th>
            <th>غياب</th>
            <th>نسبة الإنجاز</th>
            <th>نسبة الحضور</th>
        </tr>
    </thead>
    <tbody>
        @foreach($specialists as $index => $specialist)
        @php
            $completionRate = $specialist->total_sessions > 0
                ? ($specialist->completed_sessions / $specialist->total_sessions) * 100
                : 0;
            $attendanceRate = ($specialist->completed_sessions + $specialist->absent_sessions) > 0
                ? ($specialist->completed_sessions / ($specialist->completed_sessions + $specialist->absent_sessions)) * 100
                : 0;
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $specialist->name }}</td>
            <td>{{ $specialist->students_count }}</td>
            <td>{{ $specialist->total_sessions }}</td>
            <td class="text-success">{{ $specialist->completed_sessions }}</td>
            <td class="text-info">{{ $specialist->scheduled_sessions }}</td>
            <td class="text-danger">{{ $specialist->absent_sessions }}</td>
            <td>{{ number_format($completionRate, 1) }}%</td>
            <td>{{ number_format($attendanceRate, 1) }}%</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="fw-bold" style="background-color: #e9ecef;">
            <td colspan="3">الإجمالي</td>
            <td>{{ $specialists->sum('students_count') }}</td>
            <td>{{ $specialists->sum('total_sessions') }}</td>
            <td class="text-success">{{ $specialists->sum('completed_sessions') }}</td>
            <td class="text-info">{{ $specialists->sum('scheduled_sessions') }}</td>
            <td class="text-danger">{{ $specialists->sum('absent_sessions') }}</td>
            <td colspan=""></td>
        </tr>
    </tfoot>
</table>

@if($topPerformers->count() > 0)
<div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd;">
    <h4 style="font-size: 14px; margin-bottom: 10px;">أفضل الأخصائيين أداءً</h4>
    <table>
        <thead>
            <tr>
                <th>الترتيب</th>
                <th>الأخصائي</th>
                <th>الجلسات المكتملة</th>
                <th>نسبة الإنجاز</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topPerformers->take(5) as $index => $performer)
            @php
                $performerRate = $performer->total_sessions > 0
                    ? ($performer->completed_sessions / $performer->total_sessions) * 100
                    : 0;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $performer->name }}</td>
                <td>{{ $performer->completed_sessions }}</td>
                <td>{{ number_format($performerRate, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<p style="text-align: left; font-size: 11px; color: #666;">
    إجمالي السجلات: {{ $specialists->count() }}
</p>
@endsection
