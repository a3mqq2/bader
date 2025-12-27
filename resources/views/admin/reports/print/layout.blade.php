<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - طباعة</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #151f42;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Tajawal', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #fff;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary-color);
        }
        .header .logo {
            max-height: 70px;
            margin-bottom: 10px;
        }
        .header .org-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        .header h1 {
            font-size: 16px;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 11px;
            color: #666;
        }
        .stats-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .stat-box {
            flex: 1;
            min-width: 100px;
            padding: 10px;
            border: 1px solid var(--primary-color);
            border-radius: 5px;
            text-align: center;
        }
        .stat-box h4 {
            font-size: 18px;
            margin-bottom: 3px;
            color: var(--primary-color);
        }
        .stat-box span {
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: right;
            font-size: 11px;
        }
        th {
            background-color: var(--primary-color);
            color: #fff;
            font-weight: 700;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 500;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .badge-secondary { background: #e2e3e5; color: #383d41; }
        .badge-primary { background: var(--primary-color); color: #fff; }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid var(--primary-color);
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            @page { margin: 1cm; }
        }
        .print-btn {
            position: fixed;
            top: 10px;
            left: 10px;
            padding: 10px 20px;
            background: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Tajawal', sans-serif;
        }
        .print-btn:hover { background: #0d1530; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-warning { color: #ffc107; }
        .text-info { color: #17a2b8; }
        .text-primary { color: var(--primary-color); }
        .fw-bold { font-weight: 700; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        طباعة
    </button>

    <div class="header">
        <img src="{{ asset('logo-primary.png') }}" alt="Logo" class="logo">
        <div class="org-name">مؤسسة البدر للتخاطب وتنمية المهارات</div>
        <h1>@yield('title')</h1>
    </div>

    <div class="meta">
        <span>تاريخ الطباعة: {{ now()->format('Y/m/d H:i') }}</span>
        @if(request('date_from') || request('date_to'))
            <span>
                الفترة:
                {{ request('date_from') ?? 'البداية' }}
                إلى
                {{ request('date_to') ?? 'الآن' }}
            </span>
        @endif
    </div>

    @yield('content')

    <div class="footer">
        <p>مؤسسة البدر للتخاطب وتنمية المهارات</p>
    </div>
</body>
</html>
