<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة {{ $invoice->invoice_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Changa', sans-serif;
            font-size: 14px;
            line-height: 1.8;
            color: #000;
            background: #fff;
            direction: rtl;
        }

        .invoice-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 30px;
        }

        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 25px;
            border-bottom: 2px solid #063973;
            margin-bottom: 30px;
        }

        .logo-section img {
            height: 120px;
            width: auto;
        }

        .invoice-title {
            text-align: left;
        }

        .invoice-title h2 {
            font-size: 32px;
            color: #063973;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .invoice-number {
            font-size: 18px;
            color: #000;
            font-weight: 600;
        }

        .invoice-date {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        /* Info Section */
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 35px;
            gap: 30px;
        }

        .info-box {
            flex: 1;
        }

        .info-box h3 {
            font-size: 14px;
            color: #063973;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #ddd;
            font-weight: 600;
        }

        .info-row {
            display: flex;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .info-label {
            width: 90px;
            color: #666;
        }

        .info-value {
            color: #000;
            font-weight: 500;
        }

        /* Items Table */
        .items-section {
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: #063973;
            color: white;
            padding: 12px 15px;
            text-align: right;
            font-weight: 600;
            font-size: 13px;
        }

        table th:first-child {
            width: 8%;
            text-align: center;
        }

        table th:nth-child(3),
        table th:nth-child(4),
        table th:last-child {
            text-align: center;
            width: 15%;
        }

        table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        table td:first-child {
            text-align: center;
            color: #666;
        }

        table td:nth-child(3),
        table td:nth-child(4),
        table td:last-child {
            text-align: center;
        }

        table td:last-child {
            font-weight: 600;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        /* Summary */
        .summary-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }

        .summary-box {
            width: 280px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-row.total {
            background: #063973;
            color: white;
            padding: 12px 15px;
            margin: -1px;
            font-weight: 600;
        }

        .summary-row.balance {
            font-weight: 700;
            font-size: 16px;
            color: #000;
            padding-top: 15px;
        }

        /* Notes */
        .notes-section {
            margin-bottom: 30px;
            padding: 15px;
            background: #f5f5f5;
            border-right: 3px solid #063973;
        }

        .notes-section strong {
            color: #063973;
        }

        /* Footer */
        .invoice-footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
        }

        .footer-note {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
        }

        .footer-contact {
            display: flex;
            justify-content: center;
            gap: 40px;
            font-size: 12px;
            color: #666;
        }

        /* Print Styles */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .invoice-container {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Print Button */
        .print-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 12px 30px;
            background: #063973;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Changa', sans-serif;
            transition: all 0.3s;
        }

        .print-btn:hover {
            background: #052a54;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        طباعة الفاتورة
    </button>

    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="logo-section">
                <img src="{{ asset('logo-primary.png') }}" alt="Logo">
            </div>
            <div class="invoice-title">
                <h2>فاتورة</h2>
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                <div class="invoice-date">{{ $invoice->created_at->format('Y/m/d') }}</div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-box">
                <h3>بيانات الطالب</h3>
                <div class="info-row">
                    <span class="info-label">الاسم:</span>
                    <span class="info-value">{{ $invoice->student->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">الكود:</span>
                    <span class="info-value">{{ $invoice->student->code }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">ولي الأمر:</span>
                    <span class="info-value">{{ $invoice->student->guardian_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">الهاتف:</span>
                    <span class="info-value">{{ $invoice->student->phone }}</span>
                </div>
            </div>
            <div class="info-box">
                <h3>بيانات الفاتورة</h3>
                <div class="info-row">
                    <span class="info-label">التاريخ:</span>
                    <span class="info-value">{{ $invoice->created_at->format('Y/m/d') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">الوقت:</span>
                    <span class="info-value">{{ $invoice->created_at->format('h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">النوع:</span>
                    <span class="info-value">{{ $invoice->type_text }}</span>
                </div>
                @if($invoice->description)
                <div class="info-row">
                    <span class="info-label">الوصف:</span>
                    <span class="info-value">{{ $invoice->description }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Items Section -->
        <div class="items-section">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>البند</th>
                        <th>السعر</th>
                        <th>الكمية</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->assessment_name }}</td>
                        <td>{{ number_format($item->price, 2) }} د.ل</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->total, 2) }} د.ل</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-box">
                <div class="summary-row total">
                    <span>إجمالي الفاتورة</span>
                    <span>{{ $invoice->formatted_total }}</span>
                </div>
                @if($invoice->discount > 0)
                <div class="summary-row">
                    <span>الخصم</span>
                    <span>- {{ number_format($invoice->discount, 2) }} د.ل</span>
                </div>
                @endif
                <div class="summary-row">
                    <span>المبلغ المدفوع</span>
                    <span>{{ $invoice->formatted_paid }}</span>
                </div>
                <div class="summary-row balance">
                    <span>المبلغ المتبقي</span>
                    <span>{{ $invoice->formatted_balance }}</span>
                </div>
            </div>
        </div>

        @if($invoice->notes)
        <div class="notes-section">
            <strong>ملاحظات:</strong>
            <p style="margin: 5px 0 0;">{{ $invoice->notes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="invoice-footer">
            <p class="footer-note">شكراً لثقتكم بنا</p>
            <div class="footer-contact">
                <span>ليبيا البيضاء - البكوش بجانب مخبز الرضوان</span>
                <span>0945851684 - 0931780511</span>
            </div>
        </div>
    </div>
</body>
</html>
