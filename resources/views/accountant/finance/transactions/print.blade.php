<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيصال مالي #{{ $transaction->id }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 210mm 148mm;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 210mm;
            height: 148mm;
            font-family: 'Changa', 'Segoe UI', Tahoma, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            direction: rtl;
            background: #fff;
            color: #000;
        }

        .receipt {
            width: 210mm;
            height: 148mm;
            padding: 8mm;
            border: 2px solid #000;
            position: relative;
            overflow: hidden;
        }

        /* Header */
        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 8px;
            border-bottom: 2px solid #000;
            margin-bottom: 12px;
        }

        .company-logo {
            height: 145px;
            width: auto;
        }

        .receipt-type {
            font-size: 22px;
            font-weight: bold;
            padding: 5px 20px;
            border: 2px solid #000;
        }

        .receipt-info {
            text-align: left;
        }

        .receipt-number {
            font-size: 14px;
            font-weight: bold;
        }

        .receipt-date {
            font-size: 10px;
            margin-top: 3px;
        }

        /* Body */
        .receipt-body {
            padding: 5px 0;
        }

        .receipt-line {
            font-size: 13px;
            padding: 8px 0;
            display: flex;
            align-items: baseline;
        }

        .receipt-line .label {
            font-weight: bold;
            min-width: 130px;
        }

        .receipt-line .value {
            flex: 1;
            border-bottom: 1px dotted #000;
            padding: 0 10px;
            min-height: 20px;
        }

        .receipt-line .extra-label {
            font-weight: bold;
            margin-right: 20px;
            min-width: 100px;
        }

        .receipt-line .extra-value {
            flex: 1;
            border-bottom: 1px dotted #000;
            padding: 0 10px;
        }

        /* Amount Section */
        .amount-section {
            border: 2px solid #000;
            padding: 12px;
            text-align: center;
            margin: 12px 0;
        }

        .amount-label {
            font-size: 13px;
            margin-bottom: 5px;
        }

        .amount-value {
            font-size: 28px;
            font-weight: bold;
        }

        /* Description */
        .description-section {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 10px;
            min-height: 35px;
        }

        .description-section .label {
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 12px;
        }

        .description-section .content {
            font-size: 12px;
        }

        /* Footer */
        .receipt-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 8px;
            border-top: 1px solid #000;
            position: absolute;
            bottom: 8mm;
            left: 8mm;
            right: 8mm;
        }

        .signature-box {
            text-align: center;
            width: 120px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 28px;
            padding-top: 4px;
            font-size: 10px;
        }

        .footer-center {
            text-align: center;
        }

        .footer-center .company-logo-footer {
            height: 25px;
            width: auto;
        }

        .footer-center .date {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
        }

        /* Print Actions */
        .print-actions {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #333;
            padding: 12px;
            text-align: center;
            z-index: 1000;
        }

        .print-actions button {
            padding: 8px 20px;
            font-size: 13px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            margin: 0 6px;
            font-family: 'Changa', sans-serif;
        }

        .btn-print {
            background: #fff;
            color: #333;
        }

        .btn-print:hover {
            background: #eee;
        }

        .btn-close-page {
            background: #666;
            color: #fff;
        }

        .btn-close-page:hover {
            background: #555;
        }

        @media print {
            @page {
                size: 210mm 148mm;
                margin: 0;
            }

            html, body {
                width: 210mm;
                height: 148mm;
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .print-actions {
                display: none !important;
            }

            .receipt {
                border: 2px solid #000 !important;
                page-break-inside: avoid;
                page-break-after: avoid;
                page-break-before: avoid;
            }
        }

        @media screen {
            body {
                background: #e0e0e0;
                padding: 60px 20px 20px 20px;
                width: auto;
                height: auto;
            }

            .receipt {
                margin: 0 auto;
                background: #fff;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            }
        }
    </style>
</head>
<body>
    <div class="print-actions">
        <button class="btn-print" onclick="window.print()">
            طباعة الإيصال
        </button>
        <button class="btn-close-page" onclick="window.close()">
            إغلاق
        </button>
    </div>

    <div class="receipt">
        <div class="receipt-header">
            <img src="{{ asset('logo-primary.png') }}" alt="Logo" class="company-logo">

            <div class="receipt-type">
                {{ $transaction->type === 'income' ? 'سند قبض' : 'سند صرف' }}
            </div>

            <div class="receipt-info">
                <div class="receipt-number">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="receipt-date">{{ $transaction->created_at->format('Y/m/d - H:i') }}</div>
            </div>
        </div>

        <div class="receipt-body">
            <div class="receipt-line">
                <span class="label">{{ $transaction->type === 'income' ? 'استلمنا من السيد/ة:' : 'صرفنا إلى السيد/ة:' }}</span>
                <span class="value">{{ $transaction->recipient_name ?: '' }}</span>
            </div>

            <div class="receipt-line">
                <span class="label">مبلغ وقدره:</span>
                <span class="value">{{ number_format($transaction->amount, 2) }} د.ل</span>
            </div>

            <div class="receipt-line">
                <span class="label">طريقة الدفع:</span>
                <span class="value">{{ $transaction->payment_method_text }}</span>
                @if($transaction->payment_method === 'bank_transfer' && $transaction->account_number)
                <span class="extra-label">رقم الحساب:</span>
                <span class="extra-value">{{ $transaction->account_number }}</span>
                @endif
            </div>

            @if($transaction->document_number)
            <div class="receipt-line">
                <span class="label">رقم المستند:</span>
                <span class="value">{{ $transaction->document_number }}</span>
            </div>
            @endif
        </div>

        <div class="amount-section">
            <div class="amount-label">المبلغ</div>
            <div class="amount-value">{{ number_format($transaction->amount, 2) }} د.ل</div>
        </div>

        <div class="description-section">
            <div class="label">وذلك عن:</div>
            <div class="content">{{ $transaction->description ?: '' }}</div>
        </div>

        <div class="receipt-footer">
            <div class="signature-box">
                <div class="signature-line">توقيع المستلم</div>
            </div>

            <div class="footer-center">
                <div class="date">{{ now()->format('Y/m/d H:i:s') }}</div>
            </div>

            <div class="signature-box">
                <div class="signature-line">توقيع المسؤول</div>
            </div>
        </div>
    </div>

    <script>
        @if(request()->has('auto_print'))
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
        @endif
    </script>
</body>
</html>
