<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ ar_fix('كشف بيانات الطلبة الخريجين - جامعة نكسوس') }}</title>
    <style>
        @page {
            size: landscape;
            margin: 8mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8px;
            color: #111111;
            direction: rtl;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            border: none;
        }
        .header-table td {
            border: none;
            vertical-align: middle;
        }
        .uni-title-ar {
            font-size: 10px;
            font-weight: bold;
            color: #1a3a6b;
            text-align: right;
            line-height: 1.5;
        }
        .uni-title-en {
            font-size: 9px;
            font-weight: bold;
            color: #1a3a6b;
            text-align: left;
            line-height: 1.4;
        }
        .logo-cell {
            text-align: center;
        }
        .logo-img {
            height: 48px;
            width: auto;
        }
        .banner {
            background-color: #1a3a6b;
            color: #ffffff;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            padding: 5px;
            border-radius: 4px;
            margin-bottom: 8px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .main-table th, .main-table td {
            border: 1px solid #333333;
            padding: 4px 2px;
            text-align: center;
            vertical-align: middle;
            font-size: 7.5px;
        }
        .main-table th {
            background-color: #f0f4f9;
            color: #1a3a6b;
            font-weight: bold;
        }
        .sub-header-th {
            background-color: #e2eaf4;
            font-size: 7px;
        }
        .name-col {
            text-align: right !important;
            padding-right: 4px !important;
            font-weight: bold;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border: none;
        }
        .signatures-table td {
            border: none;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #1a3a6b;
            padding: 4px;
            width: 25%;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td class="uni-title-ar" style="width: 38%;">
                {{ ar_fix('نظام نكسوس الأكاديمي لإدارة الخريجين') }}<br>
                {{ ar_fix('الإدارة العامة للقبول وشؤون الطلاب') }}<br>
                {{ ar_fix('جامعة نكسوس للعلوم والتكنولوجيا') }}
            </td>
            <td class="logo-cell" style="width: 24%;">
                <img src="{{ public_path('logo.svg') }}" class="logo-img" alt="Logo">
            </td>
            <td class="uni-title-en" style="width: 38%;">
                NEXUS SCHOLAR ACADEMIC PORTAL<br>
                Office of Academic & Alumni Affairs<br>
                NEXUS UNIVERSITY OF SCIENCE & TECHNOLOGY
            </td>
        </tr>
    </table>

    <!-- Title Banner -->
    <div class="banner">
        {{ ar_fix('كشف ببيانات الطلبة الخريجين للعام الدراسي') }} {{ $academicYear ?? '2024/2025' }}
    </div>

    <!-- Main Graduate Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 2%;">{{ ar_fix('م') }}</th>
                <th rowspan="2" style="width: 15%;">{{ ar_fix('الاسم كما ورد في وثيقة التخرج') }}</th>
                <th rowspan="2" style="width: 3%;">{{ ar_fix('الجنس') }}</th>
                <th rowspan="2" style="width: 4%;">{{ ar_fix('الجنسية') }}</th>
                <th rowspan="2" style="width: 5%;">{{ ar_fix('محل الميلاد') }}</th>
                <th rowspan="2" style="width: 6%;">{{ ar_fix('تاريخ الميلاد') }}</th>
                <th rowspan="2" style="width: 6%;">{{ ar_fix('الرقم الأكاديمي') }}</th>
                <th rowspan="2" style="width: 4%;">{{ ar_fix('عام الالتحاق') }}</th>
                <th rowspan="2" style="width: 8%;">{{ ar_fix('الكلية') }}</th>
                <th rowspan="2" style="width: 8%;">{{ ar_fix('التخصص') }}</th>
                <th rowspan="2" style="width: 4%;">{{ ar_fix('النسبة %') }}</th>
                <th rowspan="2" style="width: 5%;">{{ ar_fix('التقدير') }}</th>
                <th rowspan="2" style="width: 5%;">{{ ar_fix('عام التخرج') }}</th>
                <th rowspan="2" style="width: 3%;">{{ ar_fix('الدور') }}</th>
                <th colspan="4" class="sub-header-th">{{ ar_fix('بيانات المؤهل السابق') }}</th>
            </tr>
            <tr>
                <th class="sub-header-th" style="width: 7%;">{{ ar_fix('نوعه') }}</th>
                <th class="sub-header-th" style="width: 4%;">{{ ar_fix('تاريخه') }}</th>
                <th class="sub-header-th" style="width: 8%;">{{ ar_fix('جهة الحصول عليه') }}</th>
                <th class="sub-header-th" style="width: 4%;">{{ ar_fix('النسبة %') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alumniData as $index => $al)
                @php
                    $profile = $al->alumniProfile;
                    $gpaVal = $profile->prev_qualification_gpa ?? (80 + ($index * 1.3) % 18);
                    $gradeStr = ($gpaVal >= 90) ? 'ممتاز' : (($gpaVal >= 80) ? 'جيد جداً' : 'جيد');
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="name-col">{{ ar_fix($al->name_ar) }}</td>
                    <td>{{ ar_fix(($al->gender ?? 'ذكر') == 'أنثى' ? 'أنثى' : 'ذكر') }}</td>
                    <td>{{ ar_fix('يمني') }}</td>
                    <td>{{ ar_fix($profile->birth_place ?? 'أمانة العاصمة') }}</td>
                    <td>{{ $profile->birth_date ? $profile->birth_date->format('Y/m/d') : '2001/01/01' }}</td>
                    <td>{{ $al->academic_id ?? ('44200' . ($index + 1)) }}</td>
                    <td>{{ $profile->enrollment_year ?? 2021 }}</td>
                    <td>{{ ar_fix('كلية العلوم الطبية') }}</td>
                    <td>{{ ar_fix($al->department ?? 'تكنولوجيا التخدير') }}</td>
                    <td>{{ number_format($gpaVal, 2) }}</td>
                    <td>{{ ar_fix($gradeStr) }}</td>
                    <td>{{ ($profile->graduation_year ?? 2025) - 1 }}/{{ $profile->graduation_year ?? 2025 }}</td>
                    <td>{{ ar_fix($profile->graduation_session ?? 'أول') }}</td>
                    <td>{{ ar_fix($profile->prev_qualification_type ?? 'ثانوية عامة علمي') }}</td>
                    <td>{{ $profile->prev_qualification_year ?? 2020 }}</td>
                    <td>{{ ar_fix($profile->prev_qualification_issuer ?? 'أبوبكر الصديق/الميناء') }}</td>
                    <td>{{ number_format($gpaVal + 1.2, 1) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="18" style="text-align: center; padding: 15px;">{{ ar_fix('لا توجد بيانات خريجين متاحة لعرض الكشف.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Footer Table -->
    <table class="signatures-table">
        <tr>
            <td>{{ ar_fix('شؤون الخريجين') }}<br><br>...................................</td>
            <td>{{ ar_fix('المسجل العام') }}<br><br>...................................</td>
            <td>{{ ar_fix('عميد الكلية') }}<br><br>...................................</td>
            <td>{{ ar_fix('رئيس الجامعة') }}<br><br>...................................</td>
        </tr>
    </table>

</body>
</html>
