@extends('admin.reports.print.layout')

@section('title', 'تقرير مؤشرات الخطر')

@section('content')
<div class="stats-row">
    <div class="stat-box">
        <h4>{{ $stats['total_at_risk'] }}</h4>
        <span>طلاب في خطر</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['without_case'] }}</h4>
        <span>بدون دراسة حالة</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['under_assessment'] }}</h4>
        <span>تحت التقييم</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['absent_3_days'] }}</h4>
        <span>غياب +3 أيام</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['absent_week'] }}</h4>
        <span>غياب +أسبوع</span>
    </div>
</div>

@if(request('risk_type'))
<p style="margin-bottom: 15px; font-size: 12px; color: #666;">
    <strong>نوع المؤشر:</strong>
    @switch(request('risk_type'))
        @case('at_risk') طلاب في خطر (غياب متتالي) @break
        @case('without_case') بدون دراسة حالة @break
        @case('under_assessment') تحت التقييم @break
        @case('absent_3_days') غياب +3 أيام @break
        @case('absent_week') غياب +أسبوع @break
        @default جميع المؤشرات
    @endswitch
</p>
@endif

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>الطالب</th>
            <th>الكود</th>
            <th>الحالة</th>
            <th>دراسة الحالة</th>
            <th>آخر حضور</th>
            <th>أيام الغياب</th>
            <th>مؤشر الخطر</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $index => $student)
        @php
            $lastAttendance = $student->lastAttendance?->date ?? null;
            $absentDays = $lastAttendance ? now()->diffInDays($lastAttendance) : null;

            // Determine risk level
            $riskLevel = 'low';
            $riskText = 'منخفض';
            if ($student->status === 'under_assessment') {
                $riskLevel = 'warning';
                $riskText = 'تحت التقييم';
            }
            if ($student->cases->isEmpty()) {
                $riskLevel = 'warning';
                $riskText = 'بدون دراسة حالة';
            }
            if ($absentDays && $absentDays >= 3) {
                $riskLevel = 'danger';
                $riskText = 'غياب ' . $absentDays . ' أيام';
            }
            if ($absentDays && $absentDays >= 7) {
                $riskLevel = 'danger';
                $riskText = 'غياب متتالي طويل';
            }
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $student->name }}</td>
            <td>{{ $student->code ?? '-' }}</td>
            <td>
                @if($student->status === 'active')
                    <span class="badge badge-success">نشط</span>
                @elseif($student->status === 'under_assessment')
                    <span class="badge badge-warning">تحت التقييم</span>
                @else
                    <span class="badge badge-secondary">{{ $student->status }}</span>
                @endif
            </td>
            <td>
                @if($student->cases->isNotEmpty())
                    <span class="badge badge-success">موجودة</span>
                @else
                    <span class="badge badge-danger">غير موجودة</span>
                @endif
            </td>
            <td>{{ $lastAttendance ? \Carbon\Carbon::parse($lastAttendance)->format('Y/m/d') : '-' }}</td>
            <td class="{{ $absentDays >= 3 ? 'text-danger fw-bold' : '' }}">
                {{ $absentDays ?? '-' }}
            </td>
            <td>
                <span class="badge badge-{{ $riskLevel }}">{{ $riskText }}</span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<p style="text-align: left; font-size: 11px; color: #666;">
    إجمالي السجلات: {{ $students->count() }}
</p>
@endsection
