@extends('layouts.dashboard')

@section('title', 'إدارة النسخ الاحتياطي والصيانة')

@section('dashboard_content')
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-black text-foreground tracking-tight mb-1">
            مركز النسخ الاحتياطي وإدارة بيانات النظام
        </h2>
        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">
            جامعة نكسوس للعلوم والتكنولوجيا - التحكم الكامل بقواعد البيانات والنسخ الاحتياطية
        </p>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/40 text-emerald-800 dark:text-emerald-300 text-xs font-bold text-center shadow-xs">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/40 text-rose-800 dark:text-rose-300 text-xs font-bold text-center shadow-xs">
        {{ session('error') }}
    </div>
@endif

<!-- Main Backup Action Cards -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- 1. Export Backups Card -->
    <div class="bg-card border border-border rounded-3xl p-6 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-3 mb-4">
                <span class="p-3 bg-primary/10 text-primary rounded-2xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                </span>
                <div>
                    <h3 class="font-extrabold text-foreground text-base">تصدير وتحميل النسخ الاحتياطية</h3>
                    <p class="text-xs text-muted-foreground font-semibold">احصل على نسخ كاملة لقاعدة بيانات النظام متوافقة مع الاستضافات المحلية والسحابية.</p>
                </div>
            </div>

            <div class="space-y-3 my-6">
                <!-- MySQL Dump (.sql) for Hostinger -->
                <div class="p-4 border border-border bg-muted/20 rounded-2xl flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-foreground text-xs flex items-center gap-1.5">
                            <span>نسخة MySQL الشاملة (.sql)</span>
                            <span class="text-[9px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full font-extrabold">موصى بها للاستضافة Hostinger</span>
                        </h4>
                        <p class="text-[11px] text-muted-foreground font-medium mt-0.5">تتضمن كافة البيانات والـ Insert statements جاهزة لـ phpMyAdmin.</p>
                    </div>
                    <a href="{{ route('backup.sql') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm shrink-0">
                        تحميل .sql
                    </a>
                </div>

                <!-- Backup (.bak) -->
                <div class="p-4 border border-border bg-muted/20 rounded-2xl flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-foreground text-xs">نسخة احتياطية سريعة (.bak)</h4>
                        <p class="text-[11px] text-muted-foreground font-medium mt-0.5">نسخة مخصصة للصيانة والاستعادة السريعة.</p>
                    </div>
                    <a href="{{ route('backup.sqlite', ['ext' => 'bak']) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm shrink-0">
                        تحميل .bak
                    </a>
                </div>

                <!-- SQLite Backup (.sqlite) -->
                <div class="p-4 border border-border bg-muted/20 rounded-2xl flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-foreground text-xs">ملف قاعدة البيانات المباشر (.sqlite)</h4>
                        <p class="text-[11px] text-muted-foreground font-medium mt-0.5">نسخة خام من قاعدة بيانات المحرك الحالي.</p>
                    </div>
                    <a href="{{ route('backup.sqlite') }}" class="px-4 py-2 border border-border bg-card hover:bg-muted text-foreground text-xs font-bold rounded-xl transition-all shrink-0">
                        تحميل .sqlite
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Restore Backup Card -->
    <div class="bg-card border border-border rounded-3xl p-6 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-3 mb-4">
                <span class="p-3 bg-amber-50 dark:bg-amber-950/30 text-amber-600 rounded-2xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                </span>
                <div>
                    <h3 class="font-extrabold text-foreground text-base">استعادة نسخة احتياطية (Restore Database)</h3>
                    <p class="text-xs text-muted-foreground font-semibold">استرجع بيانات النظام من ملف نسخة احتياطية سبق حفظها (.sql / .sqlite / .bak).</p>
                </div>
            </div>

            <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data" class="space-y-4 my-4" onsubmit="return confirm('تنبيه هام جداً: استعادة النسخة الاحتياطية ستستبدل البيانات الحالية بالبيانات الموجودة بالملف المرفوع. هل تريد الاستمرار؟')">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-foreground mb-2">حدد ملف النسخة الاحتياطية (.sql, .sqlite, .bak)</label>
                    <input type="file" name="file" accept=".sql, .sqlite, .bak, .txt" required class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>

                <div class="p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/30 rounded-2xl text-xs space-y-1.5">
                    <p class="font-bold text-amber-800 dark:text-amber-300">إرشادات الاستعادة والأمان:</p>
                    <ul class="list-disc list-inside text-amber-700 dark:text-amber-400 text-[11px] space-y-1">
                        <li>تأكد من اختيار ملف صادر رسمياً من هذا النظام أو استضافة Hostinger.</li>
                        <li>سيتم تنظيف الكاش وإعادة بناء الفهارس فور تفعيل الملف.</li>
                    </ul>
                </div>

                <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition-all shadow-md cursor-pointer">
                    تأكيد وتفعيل استعادة البيانات
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Excel Export & Import Center -->
<div class="bg-card border border-border rounded-3xl p-6 shadow-sm mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="font-extrabold text-foreground text-base mb-1">مركز تصدير كشوفات الإكسل (Excel Modules Export)</h3>
            <p class="text-xs text-muted-foreground font-semibold">تصدير واستخراج بيانات الأقسام بدقة تامة وبمحاذاة كاملة من اليمين إلى اليسار.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Alumni Certificate Tracking Excel -->
        <div class="p-4 border border-border bg-muted/10 rounded-2xl flex items-center justify-between">
            <div>
                <h4 class="font-bold text-foreground text-xs mb-1">كشف متابعة الشهادات والتعميد</h4>
                <span class="text-[10px] text-muted-foreground font-medium">شيت متابعة رفع واستلام الوزارة</span>
            </div>
            <a href="{{ route('reports.excel', ['report_type' => 'tracking']) }}" class="px-3.5 py-2 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-all shadow-xs">
                تصدير Excel
            </a>
        </div>

        <!-- Alumni Directory Excel -->
        <div class="p-4 border border-border bg-muted/10 rounded-2xl flex items-center justify-between">
            <div>
                <h4 class="font-bold text-foreground text-xs mb-1">دليل الخريجين الشامل</h4>
                <span class="text-[10px] text-muted-foreground font-medium">بيانات الخريجين، المؤهلات والتوظيف</span>
            </div>
            <a href="{{ route('reports.excel', ['report_type' => 'alumni']) }}" class="px-3.5 py-2 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-all shadow-xs">
                تصدير Excel
            </a>
        </div>

        <!-- Graduation Projects Excel -->
        <div class="p-4 border border-border bg-muted/10 rounded-2xl flex items-center justify-between">
            <div>
                <h4 class="font-bold text-foreground text-xs mb-1">كشف أبحاث ومشاريع التخرج</h4>
                <span class="text-[10px] text-muted-foreground font-medium">الأبحاث، المشرفين، ونسب الإنجاز</span>
            </div>
            <a href="{{ route('reports.excel', ['report_type' => 'projects']) }}" class="px-3.5 py-2 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-all shadow-xs">
                تصدير Excel
            </a>
        </div>

        <!-- Users Excel -->
        <div class="p-4 border border-border bg-muted/10 rounded-2xl flex items-center justify-between">
            <div>
                <h4 class="font-bold text-foreground text-xs mb-1">دليل المستخدمين والحسابات</h4>
                <span class="text-[10px] text-muted-foreground font-medium">كشف المستخدمين والصلاحيات والجلسات</span>
            </div>
            <a href="{{ route('reports.excel', ['report_type' => 'users']) }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-xs">
                تصدير Excel
            </a>
        </div>
    </div>
</div>
@endsection
