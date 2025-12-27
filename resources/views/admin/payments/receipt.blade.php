<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيصال {{ $payment->receipt_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A5;
            margin: 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Changa', sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #000;
            background: #f5f5f5;
            direction: rtl;
        }

        .receipt-container {
            max-width: 148mm;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        /* Header */
        .receipt-header {
            background: #063973;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .receipt-header img {
            height: 50px;
            margin-bottom: 10px;
        }

        .receipt-header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .receipt-header p {
            font-size: 11px;
            opacity: 0.9;
        }

        /* Receipt Title */
        .receipt-title {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-bottom: 2px dashed #dee2e6;
        }

        .receipt-title h2 {
            color: #063973;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .receipt-title .receipt-number {
            font-size: 14px;
            color: #666;
        }

        /* Receipt Body */
        .receipt-body {
            padding: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-size: 13px;
        }

        .info-value {
            font-weight: 600;
            color: #333;
        }

        /* Amount Box */
        .amount-box {
            background: #063973;
            color: white;
            text-align: center;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
        }

        .amount-box .label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .amount-box .amount {
            font-size: 28px;
            font-weight: bold;
        }

        /* Invoice Reference */
        .invoice-ref {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .invoice-ref h4 {
            font-size: 13px;
            color: #063973;
            margin-bottom: 10px;
        }

        /* Notes */
        .notes {
            background: #fff3cd;
            padding: 12px;
            border-radius: 6px;
            font-size: 12px;
            color: #856404;
        }

        /* Footer */
        .receipt-footer {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-top: 2px dashed #dee2e6;
        }

        .receipt-footer p {
            font-size: 11px;
            color: #666;
            margin: 3px 0;
        }

        .receipt-footer .thank-you {
            font-size: 13px;
            color: #063973;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .receipt-container {
                margin: 0;
                box-shadow: none;
            }

            .no-print {
                display: none;
            }
        }

        /* Print Button */
        .print-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background: #063973;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(6, 57, 115, 0.3);
            transition: all 0.3s;
        }

        .print-btn:hover {
            background: #052a54;
            transform: translateY(-2px);
        }

        /* Payment Method Icon */
        .payment-method {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #e9ecef;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        طباعة الإيصال
    </button>

    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <img src="{{ asset('assets/images/logo-white.png') }}" alt="Logo" onerror="this.style.display='none'">
        </div>

        <!-- Receipt Title -->
        <div class="receipt-title">
            <h2>إيصال استلام</h2>
            <div class="receipt-number">{{ $payment->receipt_number }}</div>
        </div>

        <!-- Receipt Body -->
        <div class="receipt-body">
            <!-- Amount Box -->
            <div class="amount-box">
                <div class="label">المبلغ المستلم</div>
                <div class="amount">{{ $payment->formatted_amount }}</div>
            </div>

            <!-- Payment Details -->
            <div class="info-row">
                <span class="info-label">التاريخ</span>
                <span class="info-value">{{ $payment->created_at->format('Y/m/d') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">الوقت</span>
                <span class="info-value">{{ $payment->created_at->format('h:i A') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">طريقة الدفع</span>
                <span class="info-value">
                    <span class="payment-method">{{ $payment->payment_method_text }}</span>
                </span>
            </div>
            @if($payment->creator)
            <div class="info-row">
                <span class="info-label">المستلم</span>
                <span class="info-value">{{ $payment->creator->name }}</span>
            </div>
            @endif

            <!-- Invoice Reference -->
            <div class="invoice-ref">
                <h4>مرجع الفاتورة</h4>
                <div class="info-row">
                    <span class="info-label">رقم الفاتورة</span>
                    <span class="info-value">{{ $payment->invoice->invoice_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">اسم الطالب</span>
                    <span class="info-value">{{ $payment->invoice->student->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">كود الطالب</span>
                    <span class="info-value">{{ $payment->invoice->student->code }}</span>
                </div>
            </div>

            @if($payment->notes)
            <div class="notes">
                <strong>ملاحظات:</strong> {{ $payment->notes }}
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <p class="thank-you">شكراً لكم</p>
            <p>ليبيا البيضاء - البكوش بجانب مخبز الرضوان</p>
            <p>0945851684 - 0931780511</p>
        </div>
    </div>
</body>
</html>
