<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف الطلاب</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Changa', sans-serif;
            background: #fff;
            color: #000;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header img {
            height: 50px;
            margin-bottom: 5px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .header .date {
            font-size: 11px;
            color: #666;
        }

        .filters-info {
            background: #f9f9f9;
            padding: 8px 12px;
            border: 1px solid #000;
            margin-bottom: 10px;
            font-size: 11px;
        }

        .filters-info span {
            margin-left: 15px;
        }

        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #000;
            background: #f9f9f9;
        }

        .summary-item {
            text-align: center;
        }

        .summary-item .label {
            font-size: 11px;
            color: #666;
        }

        .summary-item .value {
            font-size: 16px;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: right;
        }

        th {
            background: #f0f0f0;
            font-weight: 600;
            font-size: 11px;
        }

        td {
            font-size: 11px;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: 600;
        }

        .total-row {
            background: #f0f0f0;
            font-weight: 700;
        }

        .footer {
            border-top: 2px solid #000;
            padding-top: 10px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            border: 1px solid #000;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
        }

        @media screen {
            body {
                background: #f0f0f0;
                padding: 20px;
            }

            .container {
                max-width: 210mm;
                margin: 0 auto;
                background: #fff;
                padding: 15mm;
                box-shadow: 0 0 10px rgba(0,0,0,0.2);
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="header">
            <img src="{{ asset('logo-primary.png') }}" alt="Logo">
            <h1>كشف الطلاب</h1>
            <div class="date">تاريخ الطباعة: {{ now()->format('Y/m/d - H:i') }}</div>
        </div>

        @if(request()->hasAny(['search', 'status', 'gender', 'date_from', 'date_to']))
        <div class="filters-info">
            <strong>فلاتر البحث:</strong>
            @if(request('search'))
                <span>بحث: {{ request('search') }}</span>
            @endif
            @if(request('status'))
                <span>الحالة: {{ request('status') == 'new' ? 'جديد' : (request('status') == 'active' ? 'نشط' : request('status')) }}</span>
            @endif
            @if(request('gender'))
                <span>الجنس: {{ request('gender') == 'male' ? 'ذكر' : 'أنثى' }}</span>
            @endif
            @if(request('date_from'))
                <span>من: {{ request('date_from') }}</span>
            @endif
            @if(request('date_to'))
                <span>إلى: {{ request('date_to') }}</span>
            @endif
        </div>
        @endif

        <div class="summary">
            <div class="summary-item">
                <div class="label">إجمالي الطلاب</div>
                <div class="value">{{ $students->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">نشط</div>
                <div class="value">{{ $students->where('status', 'active')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">جديد</div>
                <div class="value">{{ $students->where('status', 'new')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">ذكور</div>
                <div class="value">{{ $students->where('gender', 'male')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">إناث</div>
                <div class="value">{{ $students->where('gender', 'female')->count() }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 10%;">الكود</th>
                    <th style="width: 20%;">اسم الطالب</th>
                    <th style="width: 8%;">العمر</th>
                    <th style="width: 7%;">الجنس</th>
                    <th style="width: 18%;">ولي الأمر</th>
                    <th style="width: 15%;">الهاتف</th>
                    <th style="width: 8%;">الحالة</th>
                    <th style="width: 10%;">التسجيل</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $index => $student)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $student->code }}</td>
                        <td class="fw-bold">{{ $student->name }}</td>
                        <td class="text-center">{{ $student->age ?? '-' }}</td>
                        <td class="text-center">{{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}</td>
                        <td>{{ $student->guardian_name ?: '-' }}</td>
                        <td>{{ $student->phone ?: '-' }}</td>
                        <td class="text-center">
                            <span class="badge">{{ $student->status_text ?? ($student->status == 'active' ? 'نشط' : ($student->status == 'new' ? 'جديد' : $student->status)) }}</span>
                        </td>
                        <td class="text-center">{{ $student->created_at->format('Y/m/d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">لا يوجد طلاب</td>
                    </tr>
                @endforelse
            </tbody>
            @if($students->count() > 0)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="9" class="text-center">الإجمالي: {{ $students->count() }} طالب</td>
                    </tr>
                </tfoot>
            @endif
        </table>

        <div class="footer">
            <div>طُبع بواسطة: {{ auth()->user()->name }}</div>
            <div>مؤسسة البدر</div>
        </div>
    </div>
</body>
</html>
