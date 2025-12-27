<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف حساب - {{ $user->name }}</title>
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

        .employee-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #000;
            background: #f9f9f9;
        }

        .employee-info .info-item {
            text-align: center;
        }

        .employee-info .label {
            font-size: 11px;
            color: #666;
        }

        .employee-info .value {
            font-size: 14px;
            font-weight: 600;
        }

        .summary {
            display: flex;
            justify-content: space-around;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #000;
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

        .summary-item.credit .value {
            color: #198754;
        }

        .summary-item.debit .value {
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

        .text-success {
            color: #198754;
        }

        .text-danger {
            color: #dc3545;
        }

        .footer {
            border-top: 2px solid #000;
            padding-top: 10px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
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
            <h1>كشف حساب موظف</h1>
            <div class="date">تاريخ الطباعة: {{ now()->format('Y/m/d - H:i') }}</div>
        </div>

        <div class="employee-info">
            <div class="info-item">
                <div class="label">الكود</div>
                <div class="value">{{ $user->code }}</div>
            </div>
            <div class="info-item">
                <div class="label">اسم الموظف</div>
                <div class="value">{{ $user->name }}</div>
            </div>
           
            <div class="info-item">
                <div class="label">الرصيد الحالي</div>
                <div class="value" style="color: {{ $user->account_balance >= 0 ? '#198754' : '#dc3545' }}">
                    {{ number_format($user->account_balance, 2) }} د.ل
                </div>
            </div>
        </div>

        <div class="summary">
            <div class="summary-item credit">
                <div class="label">إجمالي الصرف</div>
                <div class="value">{{ number_format($totals['credits'], 2) }} د.ل</div>
            </div>
            <div class="summary-item debit">
                <div class="label">إجمالي الخصم</div>
                <div class="value">{{ number_format($totals['debits'], 2) }} د.ل</div>
            </div>
            <div class="summary-item">
                <div class="label">الصافي</div>
                <div class="value">{{ number_format($totals['credits'] - $totals['debits'], 2) }} د.ل</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 15%;">التاريخ</th>
                    <th style="width: 10%;">النوع</th>
                    <th style="width: 15%;">المبلغ</th>
                    <th style="width: 15%;">الرصيد بعد</th>
                    <th style="width: 15%;">الخزينة</th>
                    <th style="width: 25%;">الوصف</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $index => $transaction)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $transaction->created_at->format('Y/m/d H:i') }}</td>
                        <td class="text-center">{{ $transaction->type_text }}</td>
                        <td class="fw-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                            {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} د.ل
                        </td>
                        <td>{{ number_format($transaction->balance_after, 2) }} د.ل</td>
                        <td>{{ $transaction->treasury->name ?? '-' }}</td>
                        <td>{{ $transaction->description ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">لا توجد حركات مالية</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <div>طُبع بواسطة: {{ auth()->user()->name }}</div>
            <div>مؤسسة البدر</div>
        </div>
    </div>
</body>
</html>
