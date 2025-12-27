<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بطاقة الطالب - {{ $student->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 210mm 148mm;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        html, body {
            width: 210mm;
            height: 148mm;
        }
        body {
            font-family: 'Changa', sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .card-container {
            width: 210mm;
            height: 148mm;
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            position: relative;
        }
        .card-header {
            background: linear-gradient(135deg, #0d397c 0%, #1a5dc8 100%);
            color: #fff;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo-section img {
            width: 180px;
            height: auto;
        }
        .company-info h1 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .company-info p {
            font-size: 12px;
            opacity: 0.9;
        }
        .card-type {
            background: rgba(255,255,255,0.2);
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .card-body {
            padding: 25px 30px;
            display: flex;
            gap: 30px;
        }
        .student-photo {
            width: 120px;
            height: 140px;
            background: linear-gradient(135deg, #e8f4fd 0%, #d0e8f9 100%);
            border: 3px solid #0d397c;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #0d397c;
        }
        .student-photo i {
            font-size: 50px;
            margin-bottom: 5px;
        }
        .student-photo span {
            font-size: 10px;
        }
        .student-info {
            flex: 1;
        }
        .student-name {
            font-size: 24px;
            font-weight: 700;
            color: #0d397c;
            margin-bottom: 5px;
        }
        .student-code {
            display: inline-block;
            background: #0d397c;
            color: #fff;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-item .label {
            color: #666;
            font-size: 12px;
            min-width: 70px;
        }
        .info-item .value {
            color: #333;
            font-weight: 600;
            font-size: 13px;
        }
        .card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: #f8f9fa;
            padding: 15px 30px;
            border-top: 2px solid #0d397c;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer-info {
            display: flex;
            gap: 30px;
            font-size: 11px;
            color: #666;
        }
        .footer-info span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .footer-info i {
            color: #0d397c;
        }
        .barcode-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #fff;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .barcode-container svg {
            height: 50px;
            width: auto;
        }
        .status-badge {
            position: absolute;
            top: 80px;
            left: 30px;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
        }
        .status-new {
            background: #fff3cd;
            color: #856404;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .decorative-line {
            position: absolute;
            top: 68px;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0d397c 0%, #1a5dc8 50%, #0d397c 100%);
        }

        /* Print styles */
        @media print {
            html, body {
                width: 210mm;
                height: 148mm;
                margin: 0;
                padding: 0;
                background: #fff;
                min-height: 148mm;
                max-height: 148mm;
                overflow: hidden;
            }
            body {
                display: block;
            }
            .card-container {
                box-shadow: none;
                border-radius: 0;
                width: 210mm;
                height: 148mm;
                margin: 0;
                position: absolute;
                top: 0;
                left: 0;
            }
            .no-print {
                display: none !important;
            }
        }

        /* Preview controls */
        .print-controls {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        .print-controls button {
            padding: 12px 30px;
            font-size: 14px;
            font-family: 'Changa', sans-serif;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-print {
            background: #0d397c;
            color: #fff;
        }
        .btn-print:hover {
            background: #0a2d63;
        }
        .btn-close {
            background: #6c757d;
            color: #fff;
        }
        .btn-close:hover {
            background: #545b62;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="card-container">
        <!-- Header -->
        <div class="card-header">
            <div class="logo-section">
                <img src="{{ asset('/assets/images/logo-white.png') }}" alt="Logo">
              
            </div>
            <div class="card-type">
                <i class="fas fa-id-card me-1"></i>
                بطاقة طالب
            </div>
        </div>
        <!-- Body -->
        <div class="card-body">
            <div class="student-photo">
                <i class="fas fa-{{ $student->gender === 'male' ? 'mars' : 'venus' }}"></i>
                <span>صورة الطالب</span>
            </div>
            <div class="student-info">
                <div class="student-name">{{ $student->name }}</div>
                <div class="student-code">{{ $student->code }}</div>

                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">تاريخ الميلاد:</span>
                        <span class="value">{{ $student->birth_date->format('Y/m/d') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">العمر:</span>
                        <span class="value">{{ $student->age }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">الجنس:</span>
                        <span class="value">{{ $student->gender_text }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">تاريخ التسجيل:</span>
                        <span class="value">{{ $student->created_at->format('Y/m/d') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">ولي الأمر:</span>
                        <span class="value">{{ $student->guardian_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">الهاتف:</span>
                        <span class="value">{{ $student->phone }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="card-footer">
            <div class="footer-info">
                <span><i class="fas fa-map-marker-alt"></i> البيضاء - شارع البكوش - بجوار مخبز الرضوان</span>
                <span><i class="fas fa-phone"></i> 0945851684 - 0931780511</span>
            </div>
            <div class="barcode-container">
                <svg id="barcode"></svg>
            </div>
        </div>
    </div>

    <!-- JsBarcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <script>
        JsBarcode("#barcode", "{{ $student->code }}", {
            format: "CODE128",
            width: 2,
            height: 45,
            displayValue: true,
            fontSize: 14,
            fontOptions: "bold",
            textMargin: 5,
            margin: 0,
            background: "#ffffff"
        });
    </script>

    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i>
            طباعة البطاقة
        </button>
        <button class="btn-close" onclick="window.close()">
            <i class="fas fa-times"></i>
            إغلاق
        </button>
    </div>
</body>
</html>
