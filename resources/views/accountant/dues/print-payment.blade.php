<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيصال استلام - {{ $payment->id }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 210mm 148mm landscape;
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
            font-family: 'Changa', sans-serif;
            background: #fff;
            color: #000;
            font-size: 14px;
        }

        .receipt {
            width: 210mm;
            height: 148mm;
            padding: 10mm;
            display: flex;
            flex-direction: column;
            page-break-inside: avoid;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8mm;
            margin-bottom: 8mm;
        }

        .header img {
            height: 50px;
            margin-bottom: 5mm;
        }

        .header h1 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 2mm;
        }

        .receipt-number {
            font-size: 12px;
            color: #666;
        }

        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .info-row {
            display: flex;
            margin-bottom: 6mm;
            align-items: baseline;
        }

        .info-label {
            font-weight: 600;
            min-width: 120px;
        }

        .info-value {
            flex: 1;
            border-bottom: 1px dotted #000;
            padding-bottom: 2px;
            padding-right: 5mm;
        }

        .amount-box {
            border: 2px solid #000;
            padding: 5mm;
            text-align: center;
            margin: 6mm 0;
        }

        .amount-box .label {
            font-size: 14px;
            margin-bottom: 2mm;
        }

        .amount-box .value {
            font-size: 26px;
            font-weight: 700;
        }

        .footer {
            border-top: 2px solid #000;
            padding-top: 5mm;
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer .date {
            font-size: 12px;
        }

        .footer .org {
            font-size: 14px;
            font-weight: 600;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 8mm;
        }

        .signature-box {
            text-align: center;
            width: 40%;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 15mm;
            padding-top: 2mm;
            font-size: 12px;
        }

        @media print {
            html, body {
                width: 210mm;
                height: 148mm;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .receipt {
                width: 210mm;
                height: 148mm;
            }
        }

        @media screen {
            body {
                background: #f0f0f0;
                padding: 20px;
                height: auto;
            }

            .receipt {
                background: #fff;
                margin: 0 auto;
                box-shadow: 0 0 10px rgba(0,0,0,0.2);
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="header">
            <img src="{{ asset('logo-primary.png') }}" alt="Logo">
            <h1>إيصال استلام</h1>
            <div class="receipt-number">رقم الإيصال: {{ $payment->id }}</div>
        </div>

        <div class="content">
            <div class="info-row">
                <span class="info-label">استلمنا من السيد/ة:</span>
                <span class="info-value">{{ $payment->invoice->student->guardian_name ?? $payment->invoice->student->name }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">وذلك عن:</span>
                <span class="info-value">{{ $payment->invoice->description ?: ('فاتورة رقم ' . $payment->invoice->invoice_number) }}</span>
            </div>

            <div class="amount-box">
                <div class="label">المبلغ المستلم</div>
                <div class="value">{{ number_format($payment->amount, 2) }} د.ل</div>
            </div>

            <div class="info-row">
                <span class="info-label">طريقة الدفع:</span>
                <span class="info-value">
                    @if($payment->payment_method === 'cash')
                        نقداً
                    @else
                        حوالة مصرفية
                        @if($payment->bank_name)
                            - {{ $payment->bank_name }}
                        @endif
                        @if($payment->account_number)
                            ({{ $payment->account_number }})
                        @endif
                    @endif
                </span>
            </div>

            @if($payment->notes)
            <div class="info-row">
                <span class="info-label">ملاحظات:</span>
                <span class="info-value">{{ $payment->notes }}</span>
            </div>
            @endif

            <div class="signatures">
                <div class="signature-box">
                    <div class="signature-line">توقيع المستلم</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">توقيع المحاسب</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="date">{{ $payment->created_at->format('Y/m/d - H:i') }}</div>
            <div class="org">مؤسسة البدر</div>
        </div>
    </div>
</body>
</html>
