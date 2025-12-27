@extends('admin.reports.print.layout')

@section('title', 'تقرير الطلاب')

@section('content')
<div class="stats-row">
    <div class="stat-box">
        <h4>{{ $stats['total'] }}</h4>
        <span>إجمالي الطلاب</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['active'] }}</h4>
        <span>نشط</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['new'] }}</h4>
        <span>جديد</span>
    </div>
    <div class="stat-box">
        <h4>{{ $stats['under_assessment'] }}</h4>
        <span>تحت التقييم</span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>الكود</th>
            <th>اسم الطالب</th>
            <th>العمر</th>
            <th>الجنس</th>
            <th>ولي الأمر</th>
            <th>الهاتف</th>
            <th>الحالة</th>
            <th>تاريخ التسجيل</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $index => $student)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $student->code }}</td>
            <td>{{ $student->name }}</td>
            <td>{{ $student->age }}</td>
            <td>{{ $student->gender_text }}</td>
            <td>{{ $student->guardian_name }}</td>
            <td>{{ $student->phone }}</td>
            <td>
                <span class="badge badge-{{ $student->status_color }}">{{ $student->status_text }}</span>
            </td>
            <td>{{ $student->created_at->format('Y/m/d') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p style="text-align: left; font-size: 11px; color: #666;">
    إجمالي السجلات: {{ $students->count() }}
</p>
@endsection
