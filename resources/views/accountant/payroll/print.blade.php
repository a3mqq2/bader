<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف مرتبات {{ $payroll->period_text }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
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
            font-size: 11px;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header img {
            height: 40px;
            margin-bottom: 5px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .header .period {
            font-size: 14px;
            color: #333;
        }

        .summary {
            display: flex;
            justify-content: space-around;
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #000;
            background: #f9f9f9;
        }

        .summary-item {
            text-align: center;
        }

        .summary-item .label {
            font-size: 10px;
            color: #666;
        }

        .summary-item .value {
            font-size: 14px;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: right;
        }

        th {
            background: #f0f0f0;
            font-weight: 600;
            font-size: 10px;
        }

        td {
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: 600;
        }

        .total-row {
            background: #e0e0e0;
            font-weight: 700;
        }

        .footer {
            border-top: 2px solid #000;
            padding-top: 8px;
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }

        .signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .signature-box {
            text-align: center;
            width: 25%;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 30px;
            padding-top: 5px;
            font-size: 10px;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @media screen {
            body {
                background: #f0f0f0;
                padding: 20px;
            }

            .container {
                max-width: 297mm;
                margin: 0 auto;
                background: #fff;
                padding: 10mm;
                box-shadow: 0 0 10px rgba(0,0,0,0.2);
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="header">
            <img src="{{ asset('logo-primary.png') }}" alt="Logo">
            <h1>كشف مرتبات الموظفين</h1>
            <div class="period">{{ $payroll->period_text }}</div>
        </div>

        <div class="summary">
            <div class="summary-item">
                <div class="label">عدد الموظفين</div>
                <div class="value text-white">{{ $payroll->items->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label text-white">إجمالي الرواتب</div>
                <div class="value text-white">{{ number_format($payroll->total_salaries, 2) }} د.ل</div>
            </div>
            <div class="summary-item">
                <div class="label">إجمالي المكافآت</div>
                <div class="value" style="color: green;">{{ number_format($payroll->total_bonuses, 2) }} د.ل</div>
            </div>
            <div class="summary-item">
                <div class="label">إجمالي الخصومات</div>
                <div class="value" style="color: red;">{{ number_format($payroll->total_deductions, 2) }} د.ل</div>
            </div>
            <div class="summary-item">
                <div class="label">صافي الصرف</div>
                <div class="value" style="color: blue;">{{ number_format($payroll->total_net, 2) }} د.ل</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">#</th>
                    <th style="width: 8%;">الكود</th>
                    <th style="width: 18%;">اسم الموظف</th>
                    <th style="width: 8%;">أيام الحضور</th>
                    <th style="width: 8%;">ساعات العمل</th>
                    <th style="width: 12%;">الراتب الأساسي</th>
                    <th style="width: 10%;">المكافأة</th>
                    <th style="width: 10%;">الخصم</th>
                    <th style="width: 12%;">الصافي</th>
                    <th style="width: 10%;">التوقيع</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payroll->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->user->code }}</td>
                        <td class="fw-bold">{{ $item->user->name }}</td>
                        <td class="text-center">{{ $item->work_days }}</td>
                        <td class="text-center">{{ number_format($item->work_hours, 1) }}</td>
                        <td>{{ number_format($item->base_salary, 2) }} د.ل</td>
                        <td style="color: green;">{{ number_format($item->bonus, 2) }} د.ل</td>
                        <td style="color: red;">{{ number_format($item->deduction, 2) }} د.ل</td>
                        <td class="fw-bold">{{ number_format($item->net_salary, 2) }} د.ل</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-center">الإجمالي</td>
                    <td>{{ number_format($payroll->total_salaries, 2) }} د.ل</td>
                    <td>{{ number_format($payroll->total_bonuses, 2) }} د.ل</td>
                    <td>{{ number_format($payroll->total_deductions, 2) }} د.ل</td>
                    <td class="fw-bold">{{ number_format($payroll->total_net, 2) }} د.ل</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">إعداد: {{ $payroll->creator->name ?? '-' }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">مراجعة</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">اعتماد</div>
            </div>
        </div>

        <div class="footer">
            <div>تاريخ الطباعة: {{ now()->format('Y/m/d H:i') }}</div>
            <div>الخزينة: {{ $payroll->treasury->name ?? '-' }}</div>
            <div>مؤسسة البدر</div>
        </div>
    </div>
</body>
</html>
