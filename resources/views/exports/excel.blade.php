<html lang="ar" dir="rtl" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
    <style>
        body { direction: rtl; text-align: right; font-family: 'Segoe UI', Tahoma, sans-serif; }
        table { border-collapse: collapse; direction: rtl; width: 100%; text-align: right; }
        th { background-color: #1a3a6b; color: #ffffff; font-weight: bold; text-align: right; }
        th, td { border: 1px solid #dcdcdc; padding: 6px; text-align: right; font-family: 'Segoe UI', Tahoma, sans-serif; font-size: 11px; }
    </style>
</head>
<body dir="rtl">
    @if($reportType === 'projects')
        <table dir="rtl">
            <thead>
                <tr>
                    <th style="background-color: #1a3a6b; color: #ffffff;">رقم البحث</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">عنوان البحث</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">القسم</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">المشرف</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">المرحلة</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">الحالة</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">نسبة الإنجاز</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">تاريخ الإنشاء</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $g)
                    <tr>
                        <td style="text-align: center;">GP-{{ 1000 + $g->id }}</td>
                        <td>{{ $lang == 'ar' ? $g->title_ar : $g->title_en }}</td>
                        <td>{{ $g->department }}</td>
                        <td>{{ $g->supervisor }}</td>
                        <td>{{ $g->stage }}</td>
                        <td>{{ $g->status }}</td>
                        <td style="text-align: center;">{{ $g->progress }}%</td>
                        <td style="text-align: center;">{{ $g->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($reportType === 'requests' || $reportType === 'tracking')
        <!-- Header Title Banner -->
        <table dir="rtl">
            <tr>
                <th colspan="11" style="background-color: #1a3a6b; color: #ffffff; font-size: 14px; font-weight: bold; text-align: center; height: 35px;">
                    نظام شؤون الخريجين - التقرير الشامل ومتابعة الدفعات والشهادات الجامعية
                </th>
            </tr>
        </table>
        <br>
        <table dir="rtl">
            <thead>
                <tr>
                    <th style="background-color: #1a3a6b; color: #ffffff;">م</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">اسم الطالب رباعياً</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">الرقم الأكاديمي</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">التخصص الأكاديمي</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">نوع الوثيقة</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">العام الدراسي</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">تاريخ الرفع للوزارة</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">حالة الشهادة والتعميد</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">سبب/نوع التعديل</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">تفاصيل التعديل الأخرى</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">تاريخ التسليم للطالب</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requestsData as $idx => $r)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td>{{ $r->alumnus->name_ar ?? 'غير معروف' }}</td>
                        <td style="text-align: center;">{{ $r->alumnus->academic_id ?? ('44200' . ($idx + 1)) }}</td>
                        <td>{{ $r->alumnus->department ?? 'غير محدد' }}</td>
                        <td>{{ $lang == 'ar' ? $r->doc_type_ar : $r->doc_type_en }}</td>
                        <td style="text-align: center;">{{ $r->alumnus->alumniProfile->graduation_year ?? '2025' }}</td>
                        <td style="text-align: center;">{{ $r->submitted_to_ministry_at ? $r->submitted_to_ministry_at->format('Y-m-d') : '-' }}</td>
                        <td style="text-align: center; font-weight: bold;">
                            {{ __('messages.ministry_status_' . ($r->ministry_status ?? 'in_ministry')) }}
                        </td>
                        <td>{{ __('messages.' . ($r->modification_reason_type ?? 'reason_none')) }}</td>
                        <td>{{ $r->modification_details ?? '-' }}</td>
                        <td style="text-align: center;">{{ $r->delivered_to_student_at ? $r->delivered_to_student_at->format('Y-m-d') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($reportType === 'alumni')
        <table dir="rtl">
            <thead>
                <tr>
                    <th style="background-color: #1a3a6b; color: #ffffff;">م</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">اسم الخريج</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">الرقم الأكاديمي</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">القسم/التخصص</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">سنة التخرج</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">محل الميلاد</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">تاريخ الميلاد</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">المؤهل السابق</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">النسبة المئوية %</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">حالة التوظيف</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">مكان العمل الحالي</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">الهاتف</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumniData as $idx => $al)
                    @php $profile = $al->alumniProfile; @endphp
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td>{{ $al->name_ar }}</td>
                        <td style="text-align: center;">{{ $al->academic_id ?? ('44200' . ($idx + 1)) }}</td>
                        <td>{{ $al->department ?? 'غير محدد' }}</td>
                        <td style="text-align: center;">{{ $profile->graduation_year ?? 'غير محدد' }}</td>
                        <td>{{ $profile->birth_place ?? 'أمانة العاصمة' }}</td>
                        <td style="text-align: center;">{{ $profile->birth_date ? $profile->birth_date->format('Y-m-d') : '2001-01-01' }}</td>
                        <td>{{ $profile->prev_qualification_type ?? 'ثانوية عامة علمي' }}</td>
                        <td style="text-align: center;">{{ number_format($profile->prev_qualification_gpa ?? 85, 2) }}%</td>
                        <td>{{ ($profile && $profile->employed) ? 'موظف' : 'باحث عن عمل' }}</td>
                        <td>{{ $profile->workplace ?? 'لا يوجد' }}</td>
                        <td>{{ $al->phone ?? 'غير متوفر' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($reportType === 'users')
        <!-- Users Excel Export in Arabic -->
        <table dir="rtl">
            <thead>
                <tr>
                    <th style="background-color: #1a3a6b; color: #ffffff;">الاسم بالعربي</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">الاسم بالإنجليزي</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">البريد الإلكتروني</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">الرقم الأكاديمي</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">رقم الهاتف</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">القسم / التخصص</th>
                    <th style="background-color: #1a3a6b; color: #ffffff;">نوع الحساب / الدور</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usersData as $u)
                    <tr>
                        <td>{{ $u->name_ar }}</td>
                        <td>{{ $u->name_en }}</td>
                        <td>{{ $u->email }}</td>
                        <td style="text-align: center;">{{ $u->academic_id ?? '-' }}</td>
                        <td style="text-align: center;">{{ $u->phone ?? '-' }}</td>
                        <td>{{ $u->department ?? 'غير محدد' }}</td>
                        <td style="text-align: center;">{{ __('messages.role_' . $u->role) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
