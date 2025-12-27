<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول الجلسات - {{ $package->student->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Changa', sans-serif;
            direction: rtl;
            padding: 15px;
            background: white;
            font-size: 12px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #063973;
        }
        .main-table th,
        .main-table td {
            border: 1px solid #063973;
            padding: 8px 10px;
        }
        .header-row {
            background: #063973;
            color: white;
        }
        .header-row td {
            border-color: #063973;
        }
        .logo-cell {
            width: 150px;
            text-align: center;
            padding: 10px;
        }
        .logo-cell img {
            max-width: 120px;
        }
        .title-cell {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        .info-row td {
            background: #f8f9fa;
            font-size: 11px;
        }
        .info-label {
            font-weight: bold;
            color: #063973;
            width: 80px;
        }
        .sessions-header {
            background: #063973;
            color: white;
            text-align: center;
            font-weight: bold;
        }
        .session-row td {
            text-align: center;
            height: 28px;
        }
        .session-row:nth-child(even) {
            background: #f8f9fa;
        }
        .signature-row td {
            height: 60px;
            text-align: center;
            vertical-align: bottom;
            padding-bottom: 5px;
        }
        .footer-row td {
            background: #f8f9fa;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .no-print {
            text-align: center;
            margin-bottom: 15px;
        }
        .no-print button {
            padding: 8px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
        }
        .btn-print {
            background: #063973;
            color: white;
        }
        .btn-close {
            background: #6c757d;
            color: white;
        }
        @media print {
            body {
                padding: 5px;
            }
            .no-print {
                display: none;
            }
            .main-table {
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">طباعة</button>
        <button class="btn-close" onclick="window.close()">إغلاق</button>
    </div>

    <table class="main-table">
        <!-- Header -->
        <tr class="header-row">
            <td colspan="5" class="title-cell">جدول جلسات العلاج</td>
            <td class="logo-cell" rowspan="2">
                <img src="{{ asset('/assets/images/logo-white.png') }}" alt="Logo">
            </td>
        </tr>
        <tr class="header-row">
            <td colspan="5" style="text-align: center; font-size: 12px;">{{ $package->therapySession->name ?? 'جلسة علاجية' }}</td>
        </tr>

        <!-- Student Info -->
        <tr class="info-row">
            <td class="info-label">الطالب:</td>
            <td colspan="2">{{ $package->student->name }}</td>
            <td class="info-label">الكود:</td>
            <td>{{ $package->student->code }}</td>
            <td class="info-label" style="text-align: center;">الهاتف: {{ $package->student->phone }}</td>
        </tr>
        <tr class="info-row">
            <td class="info-label">الأخصائي:</td>
            <td>{{ $package->specialist->name ?? '-' }}</td>
            <td class="info-label">من:</td>
            <td>{{ $package->start_date->format('Y/m/d') }}</td>
            <td class="info-label">إلى:</td>
            <td style="text-align: center;">{{ $package->end_date->format('Y/m/d') }}</td>
        </tr>
        <tr class="info-row">
            <td class="info-label">الوقت:</td>
            <td>{{ date('h:i A', strtotime($package->session_time)) }}</td>
            <td class="info-label">المدة:</td>
            <td>{{ $package->session_duration }} دقيقة</td>
            <td class="info-label">الأيام:</td>
            <td style="text-align: center;">{{ $package->days_text }}</td>
        </tr>

        <!-- Sessions Header -->
        <tr class="sessions-header">
            <td width="8%">#</td>
            <td width="18%">اليوم</td>
            <td width="22%">التاريخ</td>
            <td width="15%">الوقت</td>
            <td width="18%">الحالة</td>
            <td width="19%">التوقيع</td>
        </tr>

        <!-- Sessions -->
        @foreach($package->sessions->sortBy('session_date') as $index => $session)
        <tr class="session-row">
            <td>{{ $index + 1 }}</td>
            <td>{{ $session->day_name }}</td>
            <td>{{ $session->session_date->format('Y/m/d') }}</td>
            <td>{{ $session->formatted_time }}</td>
            <td></td>
            <td></td>
        </tr>
        @endforeach

        <!-- Empty rows if less than 10 sessions -->
        @for($i = $package->sessions->count(); $i < 10; $i++)
        <tr class="session-row">
            <td>{{ $i + 1 }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @endfor

        <!-- Signatures -->
        <tr class="signature-row">
            <td colspan="3">توقيع ولي الأمر: ...........................</td>
            <td colspan="3">توقيع الأخصائي: ...........................</td>
        </tr>

        <!-- Footer -->
        <tr class="footer-row">
            <td colspan="6">
                مؤسسة البدر للتخاطب وتنمية المهارات | تاريخ الطباعة: {{ now()->format('Y/m/d') }}
            </td>
        </tr>
    </table>
</body>
</html>
