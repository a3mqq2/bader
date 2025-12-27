<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل الحضور - {{ $subscription->student->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #fff;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #063973;
        }
        .header h1 {
            color: #063973;
            font-size: 20px;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 16px;
            color: #666;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }
        .info-box {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
        }
        .info-box h3 {
            background: #063973;
            color: white;
            padding: 5px 10px;
            margin: -10px -10px 10px -10px;
            border-radius: 5px 5px 0 0;
            font-size: 12px;
        }
        .info-box table {
            width: 100%;
        }
        .info-box td {
            padding: 3px 0;
        }
        .info-box td:first-child {
            color: #666;
            width: 40%;
        }
        .stats-section {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-box {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
        }
        .stat-box.total { background: #f8f9fa; }
        .stat-box.present { background: #d4edda; color: #155724; }
        .stat-box.absent { background: #f8d7da; color: #721c24; }
        .stat-box.pending { background: #fff3cd; color: #856404; }
        .stat-box .number {
            font-size: 24px;
            font-weight: bold;
        }
        .stat-box .label {
            font-size: 11px;
        }
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .attendance-table th,
        .attendance-table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: center;
        }
        .attendance-table th {
            background: #063973;
            color: white;
            font-weight: normal;
        }
        .attendance-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .status-present {
            background: #d4edda !important;
            color: #155724;
            font-weight: bold;
        }
        .status-absent {
            background: #f8d7da !important;
            color: #721c24;
            font-weight: bold;
        }
        .status-pending {
            background: #fff3cd !important;
            color: #856404;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 10px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .signature-box {
            width: 30%;
            text-align: center;
        }
        .signature-box .line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>مؤسسة البدر للتخاطب وتنمية المهارات</h1>
        <h2>سجل حضور الرعاية النهارية</h2>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h3>بيانات الطالب</h3>
            <table>
                <tr>
                    <td>الاسم:</td>
                    <td><strong>{{ $subscription->student->name }}</strong></td>
                </tr>
                <tr>
                    <td>الكود:</td>
                    <td>{{ $subscription->student->code }}</td>
                </tr>
                <tr>
                    <td>ولي الأمر:</td>
                    <td>{{ $subscription->student->guardian_name }}</td>
                </tr>
            </table>
        </div>
        <div class="info-box">
            <h3>بيانات الاشتراك</h3>
            <table>
                <tr>
                    <td>نوع الرعاية:</td>
                    <td><strong>{{ $subscription->daycareType->name ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td>المشرف:</td>
                    <td>{{ $subscription->supervisor->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td>الفترة:</td>
                    <td>{{ $subscription->start_date->format('Y/m/d') }} - {{ $subscription->end_date->format('Y/m/d') }}</td>
                </tr>
                <tr>
                    <td>السعر:</td>
                    <td>{{ $subscription->formatted_price }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="stats-section">
        <div class="stat-box total">
            <div class="number">{{ $subscription->attendances->count() }}</div>
            <div class="label">إجمالي الأيام</div>
        </div>
        <div class="stat-box present">
            <div class="number">{{ $subscription->present_count }}</div>
            <div class="label">أيام الحضور</div>
        </div>
        <div class="stat-box absent">
            <div class="number">{{ $subscription->absent_count }}</div>
            <div class="label">أيام الغياب</div>
        </div>
        <div class="stat-box pending">
            <div class="number">{{ $subscription->pending_count }}</div>
            <div class="label">قيد الانتظار</div>
        </div>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="25%">التاريخ</th>
                <th width="20%">اليوم</th>
                <th width="20%">الحالة</th>
                <th>ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subscription->attendances->sortBy('date') as $index => $attendance)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $attendance->date->format('Y/m/d') }}</td>
                <td>{{ $attendance->day_name }}</td>
                <td class="status-{{ $attendance->status }}">{{ $attendance->status_text }}</td>
                <td>{{ $attendance->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <div class="line">المشرف</div>
        </div>
        <div class="signature-box">
            <div class="line">ولي الأمر</div>
        </div>
        <div class="signature-box">
            <div class="line">الإدارة</div>
        </div>
    </div>

    <div class="footer">
        تم الطباعة بتاريخ: {{ now()->format('Y/m/d - h:i A') }}
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
