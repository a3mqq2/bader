<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بطاقة هوية - {{ $user->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Tajawal', sans-serif;
            background: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .print-btn {
            margin-bottom: 20px;
            padding: 12px 30px;
            background: #151f42;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
        }
        .print-btn:hover {
            background: #0d1530;
        }
        .cards-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        /* CR80 ID Card Standard Size: 85.6mm x 53.98mm */
        .id-card {
            width: 85.6mm;
            height: 53.98mm;
            background: #fff;
            border-radius: 2mm;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            position: relative;
        }
        /* Front Side */
        .card-front {
            padding: 3mm;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 0.4mm solid #151f42;
            padding-bottom: 1.5mm;
            margin-bottom: 1.5mm;
        }
        .card-header .logo {
            height: 8mm;
            width: auto;
        }
        .card-header .org-info {
            text-align: left;
            flex: 1;
            margin-right: 2mm;
        }
        .card-header .org-name {
            font-size: 6pt;
            font-weight: 700;
            color: #151f42;
            line-height: 1.2;
        }
        .card-header .card-type {
            font-size: 5pt;
            color: #666;
        }
        .card-body {
            display: flex;
            gap: 2mm;
            flex: 1;
            overflow: hidden;
        }
        .photo-section {
            width: 16mm;
            flex-shrink: 0;
        }
        .photo-placeholder {
            width: 16mm;
            height: 20mm;
            border: 0.3mm solid #ddd;
            background: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5pt;
            color: #999;
            text-align: center;
            line-height: 1.2;
        }
        .info-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }
        .info-row {
            margin-bottom: 1mm;
        }
        .info-label {
            font-size: 5pt;
            color: #888;
            display: block;
        }
        .info-value {
            font-size: 7pt;
            font-weight: 700;
            color: #151f42;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .info-value.name {
            font-size: 8pt;
        }
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 1.5mm;
            border-top: 0.2mm solid #eee;
        }
        .code-section {
            text-align: center;
        }
        .code-label {
            font-size: 4pt;
            color: #888;
        }
        .code-value {
            font-size: 9pt;
            font-weight: 700;
            color: #151f42;
            letter-spacing: 0.5px;
        }
        .barcode-section {
            text-align: center;
        }
        .barcode {
            height: 6mm;
            width: auto;
            max-width: 30mm;
        }
        .barcode-text {
            font-size: 4pt;
            color: #666;
            font-family: monospace;
        }

        /* Back Side */
        .card-back {
            padding: 3mm;
            display: flex;
            flex-direction: column;
            height: 100%;
            background: #fafafa;
            overflow: hidden;
        }
        .back-header {
            text-align: center;
            border-bottom: 0.3mm solid #ddd;
            padding-bottom: 1.5mm;
            margin-bottom: 1.5mm;
        }
        .back-title {
            font-size: 6pt;
            font-weight: 700;
            color: #151f42;
        }
        .terms {
            font-size: 5pt;
            color: #666;
            line-height: 1.4;
            flex: 1;
            overflow: hidden;
        }
        .terms p {
            margin-bottom: 0.8mm;
        }
        .back-footer {
            text-align: center;
            margin-top: auto;
            padding-top: 1.5mm;
            border-top: 0.3mm solid #ddd;
        }
        .contact-info {
            font-size: 5pt;
            color: #888;
        }
        .signature-line {
            margin-top: 2mm;
            border-top: 0.3mm solid #151f42;
            width: 20mm;
            margin-left: auto;
            margin-right: auto;
        }
        .signature-label {
            font-size: 4pt;
            color: #888;
            text-align: center;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            html, body {
                width: 85.6mm;
                height: 53.98mm;
            }
            body {
                background: #fff;
                padding: 0;
                margin: 0;
                display: block;
                min-height: auto;
            }
            .print-btn {
                display: none !important;
            }
            .cards-container {
                gap: 0;
                display: block;
            }
            .id-card {
                box-shadow: none;
                border: none;
                border-radius: 0;
                page-break-after: always;
                page-break-inside: avoid;
                margin: 0;
                width: 85.6mm;
                height: 53.98mm;
                overflow: hidden;
            }
            .id-card:last-child {
                page-break-after: auto;
            }
            .card-front, .card-back {
                overflow: hidden;
            }
            @page {
                size: 85.6mm 53.98mm;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">طباعة البطاقة</button>

    <div class="cards-container">
        <!-- Front Side -->
        <div class="id-card">
            <div class="card-front">
                <div class="card-header">
                    <img src="{{ asset('logo-primary.png') }}" alt="Logo" class="logo">
                    <div class="org-info">
                        <div class="org-name">مؤسسة البدر للتخاطب وتنمية المهارات</div>
                        <div class="card-type">بطاقة هوية موظف</div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="photo-section">
                        <div class="photo-placeholder">
                            الصورة<br>الشخصية
                        </div>
                    </div>
                    <div class="info-section">
                        <div class="info-row">
                            <span class="info-label">الاسم</span>
                            <span class="info-value name">{{ $user->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">المسمى الوظيفي</span>
                            <span class="info-value">{{ $user->role_text }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">تاريخ الإصدار</span>
                            <span class="info-value">{{ now()->format('Y/m/d') }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="code-section">
                        <div class="code-label">رقم الموظف</div>
                        <div class="code-value">{{ $user->code }}</div>
                    </div>
                    <div class="barcode-section">
                        <svg class="barcode" id="barcode"></svg>
                        <div class="barcode-text">{{ $user->code }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Side -->
        <div class="id-card">
            <div class="card-back">
                <div class="back-header">
                    <div class="back-title">شروط وأحكام</div>
                </div>

                <div class="terms">
                    <p>- هذه البطاقة ملك لمؤسسة البدر للتخاطب وتنمية المهارات.</p>
                    <p>- يجب إبراز البطاقة عند الطلب.</p>
                    <p>- في حالة فقدان البطاقة يرجى إبلاغ الإدارة فوراً.</p>
                    <p>- البطاقة غير قابلة للتحويل.</p>
                    <p>- يرجى إعادة البطاقة عند انتهاء الخدمة.</p>
                </div>

                <div class="signature-line"></div>
                <div class="signature-label">توقيع المدير</div>

                <div class="back-footer">
                    <div class="contact-info">
                        مؤسسة البدر للتخاطب وتنمية المهارات
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        JsBarcode("#barcode", "{{ $user->code }}", {
            format: "CODE128",
            width: 1,
            height: 20,
            displayValue: false,
            margin: 0
        });
    </script>
</body>
</html>
