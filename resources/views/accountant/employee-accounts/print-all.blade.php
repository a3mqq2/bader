<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف حسابات الموظفين</title>
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

        .summary-item .value.positive {
            color: #198754;
        }

        .summary-item .value.negative {
            color: #dc3545;
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

        .text-success {
            color: #198754;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-secondary {
            color: #6c757d;
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
            <h1>كشف حسابات الموظفين</h1>
            <div class="date">تاريخ الطباعة: {{ now()->format('Y/m/d - H:i') }}</div>
        </div>

        @if(request()->hasAny(['search', 'balance_status']))
        <div class="filters-info">
            <strong>فلاتر البحث:</strong>
            @if(request('search'))
                <span>بحث: {{ request('search') }}</span>
            @endif
            @if(request('balance_status'))
                <span>حالة الرصيد:
                    @if(request('balance_status') == 'positive') دائن (موجب)
                    @elseif(request('balance_status') == 'negative') مدين (سالب)
                    @else متوازن (صفر)
                    @endif
                </span>
            @endif
        </div>
        @endif

        <div class="summary">
            <div class="summary-item">
                <div class="label">عدد الموظفين</div>
                <div class="value">{{ $stats['total_employees'] }}</div>
            </div>
            <div class="summary-item">
                <div class="label">إجمالي الأرصدة الدائنة</div>
                <div class="value positive">{{ number_format($stats['positive_balance'], 2) }} د.ل</div>
            </div>
            <div class="summary-item">
                <div class="label">إجمالي الأرصدة المدينة</div>
                <div class="value negative">{{ number_format(abs($stats['negative_balance']), 2) }} د.ل</div>
            </div>
            <div class="summary-item">
                <div class="label">صافي الأرصدة</div>
                <div class="value {{ ($stats['positive_balance'] + $stats['negative_balance']) >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($stats['positive_balance'] + $stats['negative_balance'], 2) }} د.ل
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 12%;">الكود</th>
                    <th style="width: 25%;">الموظف</th>
                    <th style="width: 13%;">الهاتف</th>
                    <th style="width: 10%;">الحركات</th>
                    <th style="width: 15%;">الرصيد</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $index => $employee)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $employee->code }}</td>
                        <td>{{ $employee->name }}</td>
                        <td class="text-center">{{ $employee->phone ?: '-' }}</td>
                        <td class="text-center">{{ $employee->account_transactions_count }}</td>
                        <td class="text-center fw-bold {{ $employee->account_balance > 0 ? 'text-success' : ($employee->account_balance < 0 ? 'text-danger' : 'text-secondary') }}">
                            {{ number_format($employee->account_balance, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">لا يوجد موظفين</td>
                    </tr>
                @endforelse
            </tbody>
            @if($employees->count() > 0)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="text-center">الإجمالي</td>
                        <td class="text-center">{{ $employees->sum('account_transactions_count') }}</td>
                        <td class="text-center">
                            <span class="text-success">+{{ number_format($stats['positive_balance'], 2) }}</span>
                            <br>
                            <span class="text-danger">{{ number_format($stats['negative_balance'], 2) }}</span>
                        </td>
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
