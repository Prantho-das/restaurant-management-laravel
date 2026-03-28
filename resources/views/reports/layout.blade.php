<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4a5568;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #2d3748;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #718096;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f7fafc;
            color: #4a5568;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mt-4 { margin-top: 1rem; }
        .bg-gray-100 { background-color: #f7fafc; }
        .text-success { color: #38a169; }
        .text-danger { color: #e53e3e; }
        .summary-box {
            background-color: #edf2f7;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .summary-box h3 {
            margin-top: 0;
            font-size: 16px;
            border-bottom: 1px solid #cbd5e0;
            padding-bottom: 5px;
        }
        .grid {
            display: table;
            width: 100%;
        }
        .col {
            display: table-cell;
            width: 50%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Royal Heritage Restaurant</h1>
        <p>@yield('report_name')</p>
        <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <div class="footer">
        Generated on {{ now()->format('M d, Y H:i:s') }} | Page <span class="pagenum"></span>
    </div>
</body>
</html>
