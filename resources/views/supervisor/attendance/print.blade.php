<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير الحضور</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Tajawal', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background: #fff;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #151f42;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header .logo {
            height: 50px;
        }
        .header .title {
            text-align: center;
            flex: 1;
        }
        .header .title h1 {
            font-size: 18px;
            color: #151f42;
            margin-bottom: 5px;
        }
        .header .title p {
            font-size: 14px;
            color: #666;
        }
        .header .date {
            text-align: left;
            font-size: 11px;
            color: #666;
        }
        .stats-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-box {
            flex: 1;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
        }
        .stat-box h4 {
            font-size: 20px;
            color: #151f42;
            margin-bottom: 5px;
        }
        .stat-box span {
            font-size: 11px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: right;
        }
        th {
            background: #151f42;
            color: #fff;
            font-weight: 500;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #666;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            @page { margin: 10mm; }
        }
        .print-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background: #151f42;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">طباعة</button>

    <div class="header">
        <img src="{{ asset('logo-primary.png') }}" alt="Logo" class="logo">
        <div class="title">
            <h1>مؤسسة البدر للتخاطب وتنمية المهارات</h1>
            <p>تقرير حضور الموظفين</p>
        </div>
        <div class="date">
            <p>تاريخ الطباعة: {{ now()->format('Y/m/d') }}</p>
            <p>الوقت: {{ now()->format('h:i A') }}</p>
        </div>
    </div>

    <div class="stats-row">
        <div class="stat-box">
            <h4>{{ $stats['total'] }}</h4>
            <span>إجمالي السجلات</span>
        </div>
        <div class="stat-box">
            <h4>{{ $stats['present'] }}</h4>
            <span>حاضرين</span>
        </div>
        <div class="stat-box">
            <h4>{{ $stats['late'] }}</h4>
            <span>متأخرين</span>
        </div>
        <div class="stat-box">
            <h4>{{ $stats['absent'] }}</h4>
            <span>غائبين</span>
        </div>
    </div>

    @if(request('date'))
    <p style="margin-bottom: 10px; font-size: 12px;"><strong>التاريخ:</strong> {{ request('date') }}</p>
    @elseif(request('date_from') && request('date_to'))
    <p style="margin-bottom: 10px; font-size: 12px;"><strong>الفترة:</strong> من {{ request('date_from') }} إلى {{ request('date_to') }}</p>
    @else
    <p style="margin-bottom: 10px; font-size: 12px;"><strong>التاريخ:</strong> {{ now()->format('Y/m/d') }} (اليوم)</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>التاريخ</th>
                <th>الموظف</th>
                <th>الكود</th>
                <th>الدور</th>
                <th>وقت الدخول</th>
                <th>وقت الخروج</th>
                <th>ساعات العمل</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $attendance)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $attendance->date->format('Y/m/d') }}</td>
                <td>{{ $attendance->user->name }}</td>
                <td>{{ $attendance->user->code }}</td>
                <td>{{ $attendance->user->role_text }}</td>
                <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '-' }}</td>
                <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '-' }}</td>
                <td>{{ $attendance->formatted_work_hours }}</td>
                <td>
                    <span class="badge badge-{{ $attendance->status_color }}">{{ $attendance->status_text }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <span>إجمالي السجلات: {{ $attendances->count() }}</span>
        <span>مؤسسة البدر للتخاطب وتنمية المهارات</span>
    </div>
</body>
</html>
