<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ar_fix('تقرير النظام - جامعة نكسوس') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #111111;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1a3a6b;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            color: #1a3a6b;
            font-size: 15px;
            margin: 0 0 4px 0;
            font-weight: bold;
        }
        .header p {
            color: #444444;
            margin: 0;
            font-size: 11px;
            font-weight: bold;
        }
        .date {
            text-align: left;
            font-size: 8px;
            color: #666666;
            margin-bottom: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th, td {
            border: 1px solid #333333;
            padding: 5px;
            text-align: right;
            font-size: 8.5px;
        }
        th {
            background-color: #1a3a6b;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
        }
        .completed { background-color: #e6f7ed; color: #10b981; }
        .processing { background-color: #fffbeb; color: #d97706; }
        .pending { background-color: #eff6ff; color: #3b82f6; }
        .rejected { background-color: #fef2f2; color: #ef4444; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ar_fix('جامعة نكسوس للعلوم والتكنولوجيا') }}</h1>
        @if($reportType === 'projects')
            <p>{{ ar_fix('تقرير أبحاث التخرج ومشاريع الطلاب') }}</p>
        @elseif($reportType === 'requests')
            <p>{{ ar_fix('تقرير طلبات المعاملات والشهادات الأكاديمية') }}</p>
        @elseif($reportType === 'alumni')
            <p>{{ ar_fix('تقرير دليل الخريجين وحالة التوظيف') }}</p>
        @endif
    </div>
    
    <div class="date">
        {{ ar_fix('تاريخ استخراج التقرير:') }} {{ date('Y-m-d H:i:s') }}
    </div>

    @if($reportType === 'projects')
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">{{ ar_fix('رقم البحث') }}</th>
                    <th style="width: 35%;">{{ ar_fix('عنوان البحث') }}</th>
                    <th style="width: 20%;">{{ ar_fix('القسم') }}</th>
                    <th style="width: 15%;">{{ ar_fix('المشرف') }}</th>
                    <th style="width: 15%;">{{ ar_fix('الحالة') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $g)
                    <tr>
                        <td style="text-align: center;">GP-{{ 1000 + $g->id }}</td>
                        <td>{{ ar_fix($lang == 'ar' ? $g->title_ar : $g->title_en) }}</td>
                        <td>{{ ar_fix($g->department) }}</td>
                        <td>{{ ar_fix($g->supervisor) }}</td>
                        <td style="text-align: center;">
                            <span class="status-badge {{ $g->status }}">
                                {{ ar_fix($g->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($reportType === 'requests')
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">{{ ar_fix('رقم المعاملة') }}</th>
                    <th style="width: 25%;">{{ ar_fix('الخريج') }}</th>
                    <th style="width: 25%;">{{ ar_fix('نوع الوثيقة') }}</th>
                    <th style="width: 20%;">{{ ar_fix('القسم') }}</th>
                    <th style="width: 15%;">{{ ar_fix('الحالة') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requestsData as $r)
                    <tr>
                        <td style="text-align: center;">REQ-{{ 1000 + $r->id }}</td>
                        <td>{{ ar_fix($r->alumnus->name_ar ?? 'غير معروف') }}</td>
                        <td>{{ ar_fix($lang == 'ar' ? $r->doc_type_ar : $r->doc_type_en) }}</td>
                        <td>{{ ar_fix($r->alumnus->department ?? 'غير معروف') }}</td>
                        <td style="text-align: center;">
                            <span class="status-badge {{ $r->status }}">
                                {{ ar_fix($r->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($reportType === 'alumni')
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">{{ ar_fix('اسم الخريج') }}</th>
                    <th style="width: 20%;">{{ ar_fix('القسم') }}</th>
                    <th style="width: 15%;">{{ ar_fix('سنة التخرج') }}</th>
                    <th style="width: 20%;">{{ ar_fix('حالة العمل') }}</th>
                    <th style="width: 20%;">{{ ar_fix('الهاتف') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumniData as $al)
                    <tr>
                        <td>{{ ar_fix($al->name_ar) }}</td>
                        <td>{{ ar_fix($al->department ?? 'غير محدد') }}</td>
                        <td style="text-align: center;">{{ $al->alumniProfile->graduation_year ?? 'غير محدد' }}</td>
                        <td style="text-align: center;">{{ ar_fix(($al->alumniProfile && $al->alumniProfile->employed) ? 'موظف' : 'باحث عن عمل') }}</td>
                        <td style="text-align: center;">{{ $al->phone ?? 'غير متوفر' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
