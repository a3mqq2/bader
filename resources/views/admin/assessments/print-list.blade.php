<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة التقييمات - مؤسسة البدر للتعليم والتدريب</title>
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Changa', sans-serif;
            background: #fff;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
        }
        .container {
            max-width: 100%;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #063973;
        }
        .logo {
            max-width: 250px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #063973;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18px;
            color: #666;
            margin-top: 10px;
        }
        .print-date {
            font-size: 11px;
            color: #999;
            margin-top: 5px;
        }
        .filters-info {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .filters-info span {
            margin-left: 15px;
            display: inline-block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead th {
            background: #063973;
            color: #fff;
            padding: 12px 8px;
            text-align: right;
            font-weight: 600;
            border: 1px solid #063973;
            font-size: 11px;
        }
        tbody td {
            padding: 10px 8px;
            border: 1px solid #ddd;
            vertical-align: middle;
            font-size: 11px;
        }
        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        tbody tr:hover {
            background: #e9ecef;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 500;
        }
        .badge-success {
            background: #2ca87f;
            color: #fff;
        }
        .badge-warning {
            background: #dc8400;
            color: #fff;
        }
        .badge-info {
            background: #3ec9d6;
            color: #fff;
        }
        .badge-secondary {
            background: #6c757d;
            color: #fff;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #666;
        }
        .summary {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .summary-item {
            text-align: center;
            padding: 10px 20px;
            background: #f8f9fa;
            border-radius: 5px;
            border: 2px solid #063973;
        }
        .summary-item .number {
            font-size: 24px;
            font-weight: 700;
            color: #063973;
        }
        .summary-item .label {
            font-size: 12px;
            color: #666;
        }
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
            .container {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('logo-primary.png') }}" alt="Logo" class="logo">
            <div class="company-name">مؤسسة البدر للتعليم والتدريب</div>
            <div class="report-title">قائمة التقييمات</div>
            <div class="print-date">تاريخ الطباعة: {{ now()->format('Y/m/d H:i') }}</div>
        </div>

        <!-- Filters Info -->
        @if(request()->hasAny(['student_id', 'assessment_id', 'status', 'case_status', 'date_from', 'date_to']))
        <div class="filters-info">
            <strong>فلاتر البحث:</strong>
            @if(request('student_id'))
                <span>الطالب: {{ $students->find(request('student_id'))?->name ?? '-' }}</span>
            @endif
            @if(request('assessment_id'))
                <span>المقياس: {{ $allAssessments->find(request('assessment_id'))?->name ?? '-' }}</span>
            @endif
            @if(request('status'))
                <span>حالة التقييم: {{ request('status') == 'pending' ? 'في الانتظار' : 'مكتمل' }}</span>
            @endif
            @if(request('case_status'))
                @php
                    $caseStatusText = match(request('case_status')) {
                        'pending' => 'في الانتظار',
                        'in_progress' => 'جاري التقييم',
                        'completed' => 'مكتمل',
                        default => '-'
                    };
                @endphp
                <span>حالة دراسة الحالة: {{ $caseStatusText }}</span>
            @endif
            @if(request('date_from'))
                <span>من: {{ request('date_from') }}</span>
            @endif
            @if(request('date_to'))
                <span>إلى: {{ request('date_to') }}</span>
            @endif
        </div>
        @endif

        <!-- Summary -->
        <div class="summary">
            <div class="summary-item">
                <div class="number">{{ $assessmentItems->count() }}</div>
                <div class="label">إجمالي التقييمات</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $assessmentItems->where('assessment_status', 'completed')->count() }}</div>
                <div class="label">مكتملة</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $assessmentItems->where('assessment_status', 'pending')->count() }}</div>
                <div class="label">قيد الانتظار</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $assessmentItems->unique('invoice.student_id')->count() }}</div>
                <div class="label">عدد الطلاب</div>
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="15%">الطالب</th>
                    <th width="12%">الكود</th>
                    <th width="15%">المقياس</th>
                    <th width="10%">حالة التقييم</th>
                    <th width="12%">حالة دراسة الحالة</th>
                    <th width="12%">الأخصائي</th>
                    <th width="10%">التاريخ</th>
                    <th width="9%">الوقت</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assessmentItems as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->invoice->student->name ?? '-' }}</td>
                        <td><span class="badge badge-secondary">{{ $item->invoice->student->code ?? '' }}</span></td>
                        <td><span class="badge badge-info">{{ $item->assessment_name }}</span></td>
                        <td>
                            <span class="badge badge-{{ $item->assessment_status_color }}">
                                {{ $item->assessment_status_text }}
                            </span>
                        </td>
                        <td>
                            @if($item->invoice->studentCase)
                            <span class="badge badge-{{ $item->invoice->studentCase->status_color }}">
                                {{ $item->invoice->studentCase->status_text }}
                            </span>
                            @else
                            <span class="badge badge-secondary">-</span>
                            @endif
                        </td>
                        <td>{{ $item->assessor->name ?? '-' }}</td>
                        <td>{{ $item->created_at->format('Y/m/d') }}</td>
                        <td>{{ $item->created_at->format('h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 30px;">لا توجد تقييمات</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>مؤسسة البدر للتعليم والتدريب</p>
            <p>جميع الحقوق محفوظة &copy; {{ date('Y') }}</p>
        </div>

        <!-- Print Button -->
        <div class="no-print" style="text-align: center; margin-top: 20px;">
            <button onclick="window.print()" style="padding: 10px 30px; font-size: 14px; background: #063973; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-family: 'Changa', sans-serif;">
                طباعة
            </button>
            <button onclick="window.close()" style="padding: 10px 30px; font-size: 14px; background: #6c757d; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px; font-family: 'Changa', sans-serif;">
                إغلاق
            </button>
        </div>
    </div>

    <script>
        // Auto print when page loads
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
