@extends('layouts.dashboard')

@section('title', __('messages.reports_title'))

@section('dashboard_content')
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-black text-foreground tracking-tight mb-1">
            {{ __('messages.reports_title') }}
        </h2>
        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">
            {{ __('messages.reports_desc') }}
        </p>
    </div>

    <!-- Export Buttons -->
    <div class="flex items-center gap-2 no-print shrink-0">
        <a href="{{ route('reports.pdf', request()->all()) }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-border rounded-xl text-xs font-bold text-foreground bg-card hover:bg-muted transition-colors gap-1.5 shadow-sm">
            <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>{{ __('messages.export_pdf') }}</span>
        </a>

        <a href="{{ route('reports.excel', request()->all()) }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-border rounded-xl text-xs font-bold text-foreground bg-card hover:bg-muted transition-colors gap-1.5 shadow-sm">
            <svg class="h-4 w-4 text-emerald-650" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>{{ __('messages.export_excel') }}</span>
        </a>

        <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2.5 border border-border rounded-xl text-xs font-bold text-foreground bg-card hover:bg-muted transition-colors gap-1.5 shadow-sm">
            <svg class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>{{ __('messages.print') }}</span>
        </button>
    </div>
</div>

<!-- Tab Bar Selector -->
<div class="flex gap-1 mb-6 border-b border-border pb-px no-print">
    @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'projects_officer']))
    <a href="{{ route('reports.index', array_merge(request()->except('report_type'), ['report_type' => 'projects'])) }}" 
       class="px-5 py-3 text-xs font-black transition-all border-b-2 {{ $reportType === 'projects' ? 'border-primary text-primary bg-primary/10' : 'border-transparent text-muted-foreground hover:text-foreground hover:bg-muted/50' }} rounded-t-xl">
       أبحاث التخرج
    </a>
    @endif
    @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer']))
    <a href="{{ route('reports.index', array_merge(request()->except('report_type'), ['report_type' => 'requests'])) }}" 
       class="px-5 py-3 text-xs font-black transition-all border-b-2 {{ $reportType === 'requests' ? 'border-primary text-primary bg-primary/10' : 'border-transparent text-muted-foreground hover:text-foreground hover:bg-muted/50' }} rounded-t-xl">
       طلبات الوثائق
    </a>
    <a href="{{ route('reports.index', array_merge(request()->except('report_type'), ['report_type' => 'alumni'])) }}" 
       class="px-5 py-3 text-xs font-black transition-all border-b-2 {{ $reportType === 'alumni' ? 'border-primary text-primary bg-primary/10' : 'border-transparent text-muted-foreground hover:text-foreground hover:bg-muted/50' }} rounded-t-xl">
       دليل الخريجين
    </a>
    @endif
</div>

<!-- Filters Bar -->
<div class="bg-card border border-border rounded-2xl p-4 shadow-sm mb-6 no-print">
    <form action="{{ route('reports.index') }}" method="GET" class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
        <input type="hidden" name="report_type" value="{{ $reportType }}">

        <!-- Department -->
        <div>
            <label class="block text-[10px] font-extrabold text-muted-foreground/80 uppercase mb-1.5">{{ __('messages.filter_dept') }}</label>
            <select name="dept" class="w-full text-xs font-semibold px-3.5 py-2 border border-border rounded-xl outline-none bg-card text-foreground">
                <option value="all">{{ __('messages.all') }}</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept }}" {{ request('dept') == $dept ? 'selected' : '' }}>
                        {{ app()->getLocale() == 'ar' ? ($deptsAr[$dept] ?? $dept) : $dept }}
                    </option>
                @endforeach
            </select>
        </div>

        @if($reportType === 'projects')
            <!-- Supervisor -->
            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground/80 uppercase mb-1.5">{{ __('messages.filter_supervisor') }}</label>
                <select name="supervisor" class="w-full text-xs font-semibold px-3.5 py-2 border border-border rounded-xl outline-none bg-card text-foreground">
                    <option value="all">{{ __('messages.all') }}</option>
                    @foreach($supervisors as $sup)
                        <option value="{{ $sup->name_ar }}" {{ request('supervisor') == $sup->name_ar ? 'selected' : '' }}>
                            {{ app()->getLocale() == 'ar' ? $sup->name_ar : $sup->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground/80 uppercase mb-1.5">{{ __('messages.filter_status') }}</label>
                <select name="status" class="w-full text-xs font-semibold px-3.5 py-2 border border-border rounded-xl outline-none bg-card text-foreground">
                    <option value="all">{{ __('messages.all') }}</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('messages.status_completed') }}</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>{{ __('messages.status_processing') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('messages.status_pending') }}</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>{{ __('messages.status_overdue') }}</option>
                </select>
            </div>
        @elseif($reportType === 'requests')
            <!-- Status -->
            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground/80 uppercase mb-1.5">{{ __('messages.filter_status') }}</label>
                <select name="status" class="w-full text-xs font-semibold px-3.5 py-2 border border-border rounded-xl outline-none bg-card text-foreground">
                    <option value="all">{{ __('messages.all') }}</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>جاهز للتسليم</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>طلب جديد</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                </select>
            </div>
        @elseif($reportType === 'alumni')
            <!-- Graduation Year -->
            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground/80 uppercase mb-1.5">سنة التخرج</label>
                <select name="grad_year" class="w-full text-xs font-semibold px-3.5 py-2 border border-border rounded-xl outline-none bg-card text-foreground">
                    <option value="all">{{ __('messages.all') }}</option>
                    @foreach($graduationYears as $year)
                        <option value="{{ $year }}" {{ request('grad_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Employed Status -->
            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground/80 uppercase mb-1.5">حالة التوظيف</label>
                <select name="status" class="w-full text-xs font-semibold px-3.5 py-2 border border-border rounded-xl outline-none bg-card text-foreground">
                    <option value="all">{{ __('messages.all') }}</option>
                    <option value="employed" {{ request('status') == 'employed' ? 'selected' : '' }}>موظف</option>
                    <option value="seeking" {{ request('status') == 'seeking' ? 'selected' : '' }}>باحث عن عمل</option>
                </select>
            </div>
        @endif

        <!-- From Date -->
        <div>
            <label class="block text-[10px] font-extrabold text-muted-foreground/80 uppercase mb-1.5">{{ __('messages.filter_date_range') }} ({{ __('messages.date') }})</label>
            <input type="date" name="from" value="{{ request('from') }}" class="w-full text-xs font-semibold px-3.5 py-2 border border-border rounded-xl outline-none bg-card text-foreground">
        </div>

        <!-- To Date -->
        <div>
            <label class="block text-[10px] font-extrabold text-muted-foreground/80 uppercase mb-1.5">إلى</label>
            <input type="date" name="to" value="{{ request('to') }}" class="w-full text-xs font-semibold px-3.5 py-2 border border-border rounded-xl outline-none bg-card text-foreground">
        </div>

        <!-- Actions -->
        <div class="col-span-2 md:col-span-5 flex justify-end gap-2">
            <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white text-xs font-bold rounded-xl transition-all shadow-sm cursor-pointer">
                {{ __('messages.generate') }}
            </button>
            <a href="{{ route('reports.index', ['report_type' => $reportType]) }}" class="px-4 py-2.5 border border-border text-muted-foreground hover:bg-muted text-xs font-bold rounded-xl transition-all flex items-center justify-center">
                {{ __('messages.reset_filters') }}
            </a>
        </div>
    </form>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-card p-6 border border-border rounded-2xl shadow-sm flex items-center gap-5">
        <span class="p-3 bg-emerald-50 text-emerald-600 dark:bg-emerald-950/20 dark:text-emerald-400 rounded-xl">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </span>
        <div>
            <p class="text-xs text-muted-foreground font-bold uppercase tracking-wider mb-1">{{ $kpis['card1_label'] }}</p>
            <h3 class="text-2xl font-black text-foreground">{{ $kpis['card1_val'] }}</h3>
        </div>
    </div>

    <div class="bg-card p-6 border border-border rounded-2xl shadow-sm flex items-center gap-5">
        <span class="p-3 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-xl">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </span>
        <div>
            <p class="text-xs text-muted-foreground font-bold uppercase tracking-wider mb-1">{{ $kpis['card2_label'] }}</p>
            <h3 class="text-2xl font-black text-foreground">{{ $kpis['card2_val'] }}</h3>
        </div>
    </div>

    <div class="bg-card p-6 border border-border rounded-2xl shadow-sm flex items-center gap-5">
        <span class="p-3 bg-rose-50 text-rose-600 dark:bg-rose-950/20 dark:text-rose-400 rounded-xl">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </span>
        <div>
            <p class="text-xs text-muted-foreground font-bold uppercase tracking-wider mb-1">{{ $kpis['card3_label'] }}</p>
            <h3 class="text-2xl font-black text-foreground">{{ $kpis['card3_val'] }}</h3>
        </div>
    </div>
</div>

<!-- Department distribution Chart -->
<div class="bg-card p-6 border border-border rounded-2xl shadow-sm mb-8 no-print">
    <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-4">
        توزيع السجلات والأعداد حسب القسم
    </h3>
    <div id="deptBarChart" class="min-h-[280px]"></div>
</div>

<!-- Filtered Table -->
<div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        @if($reportType === 'projects')
            <table class="w-full text-start border-collapse">
                <thead>
                    <tr class="bg-muted/50 border-b border-border text-muted-foreground font-extrabold uppercase text-[10px] tracking-wider text-start">
                        <th class="p-4 text-start font-extrabold">#</th>
                        <th class="p-4 text-start font-extrabold">{{ __('messages.title') }}</th>
                        <th class="p-4 text-start font-extrabold">{{ __('messages.filter_dept') }}</th>
                        <th class="p-4 text-start font-extrabold">{{ __('messages.supervisor') }}</th>
                        <th class="p-4 text-start font-extrabold">{{ __('messages.status') }}</th>
                        <th class="p-4 text-center font-extrabold">{{ __('messages.progress') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-xs font-semibold text-foreground">
                    @forelse($groups as $g)
                        <tr class="hover:bg-muted/40 transition-colors">
                            <td class="p-4 text-muted-foreground font-extrabold">GP-{{ 1000 + $g->id }}</td>
                            <td class="p-4 text-foreground font-bold max-w-[280px]">
                                {{ app()->getLocale() == 'ar' ? $g->title_ar : $g->title_en }}
                            </td>
                            <td class="p-4">
                                <span class="inline-flex px-2 py-0.5 bg-muted rounded text-[10px] font-bold text-muted-foreground border border-border/50">
                                    {{ $deptsAr[$g->department] ?? $g->department }}
                                </span>
                            </td>
                            <td class="p-4 text-muted-foreground font-medium">{{ $g->supervisor }}</td>
                            <td class="p-4">
                                @if($g->status == 'completed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        {{ __('messages.status_completed') }}
                                    </span>
                                @elseif($g->status == 'processing')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                        {{ __('messages.status_processing') }}
                                    </span>
                                @elseif($g->status == 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ __('messages.status_pending') }}
                                    </span>
                                @elseif($g->status == 'overdue')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                        {{ __('messages.status_overdue') }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center font-bold">{{ $g->progress }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-muted-foreground font-bold">
                                {{ __('messages.no_data') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @elseif($reportType === 'requests')
            <table class="w-full text-start border-collapse">
                <thead>
                    <tr class="bg-muted/50 border-b border-border text-muted-foreground font-extrabold uppercase text-[10px] tracking-wider text-start">
                        <th class="p-4 text-start font-extrabold">رقم المعاملة</th>
                        <th class="p-4 text-start font-extrabold">اسم الخريج</th>
                        <th class="p-4 text-start font-extrabold">نوع الوثيقة المطلوب</th>
                        <th class="p-4 text-start font-extrabold">القسم</th>
                        <th class="p-4 text-start font-extrabold">الحالة</th>
                        <th class="p-4 text-center font-extrabold">تاريخ تقديم الطلب</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-xs font-semibold text-foreground">
                    @forelse($requestsData as $r)
                        <tr class="hover:bg-muted/40 transition-colors">
                            <td class="p-4 text-muted-foreground font-extrabold">REQ-{{ 1000 + $r->id }}</td>
                            <td class="p-4 text-foreground font-bold">{{ $r->alumnus->name_ar ?? 'غير معروف' }}</td>
                            <td class="p-4 text-muted-foreground font-medium">{{ app()->getLocale() == 'ar' ? $r->doc_type_ar : $r->doc_type_en }}</td>
                            <td class="p-4">
                                <span class="inline-flex px-2 py-0.5 bg-muted rounded text-[10px] font-bold text-muted-foreground border border-border/50">
                                    {{ $deptsAr[$r->alumnus->department ?? ''] ?? ($r->alumnus->department ?? 'غير معروف') }}
                                </span>
                            </td>
                            <td class="p-4">
                                @if($r->status == 'completed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        جاهز للتسليم
                                    </span>
                                @elseif($r->status == 'processing')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                        قيد المراجعة
                                    </span>
                                @elseif($r->status == 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        طلب جديد
                                    </span>
                                @elseif($r->status == 'rejected')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                        مرفوض
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center text-muted-foreground font-medium">{{ $r->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-muted-foreground font-bold">
                                {{ __('messages.no_data') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @elseif($reportType === 'alumni')
            <table class="w-full text-start border-collapse">
                <thead>
                    <tr class="bg-muted/50 border-b border-border text-muted-foreground font-extrabold uppercase text-[10px] tracking-wider text-start">
                        <th class="p-4 text-start font-extrabold">الاسم (عربي)</th>
                        <th class="p-4 text-start font-extrabold">البريد الإلكتروني</th>
                        <th class="p-4 text-start font-extrabold">الهاتف</th>
                        <th class="p-4 text-start font-extrabold">القسم الدراسي</th>
                        <th class="p-4 text-start font-extrabold">سنة التخرج</th>
                        <th class="p-4 text-start font-extrabold">الحالة المهنية</th>
                        <th class="p-4 text-center font-extrabold">مكان العمل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-xs font-semibold text-foreground">
                    @forelse($alumniData as $al)
                        <tr class="hover:bg-muted/40 transition-colors">
                            <td class="p-4 text-foreground font-bold">{{ $al->name_ar }}</td>
                            <td class="p-4 text-muted-foreground font-medium">{{ $al->email }}</td>
                            <td class="p-4 text-muted-foreground font-medium">{{ $al->phone ?? 'غير متوفر' }}</td>
                            <td class="p-4">
                                <span class="inline-flex px-2 py-0.5 bg-muted rounded text-[10px] font-bold text-muted-foreground border border-border/50">
                                    {{ $deptsAr[$al->department ?? ''] ?? ($al->department ?? 'غير محدد') }}
                                </span>
                            </td>
                            <td class="p-4 text-foreground font-bold">{{ $al->alumniProfile->graduation_year ?? 'غير محدد' }}</td>
                            <td class="p-4">
                                @if($al->alumniProfile && $al->alumniProfile->employed)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        موظف
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                        باحث عن عمل
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center text-muted-foreground font-medium">{{ $al->alumniProfile->workplace ?? 'لا يوجد' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-muted-foreground font-bold">
                                {{ __('messages.no_data') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const isRtl = "{{ app()->getLocale() == 'ar' ? true : false }}" === "1";
        
        const deptNames = {!! json_encode(array_column($deptChartData, app()->getLocale() == 'ar' ? 'name_ar' : 'name_en')) !!};
        const deptCounts = {!! json_encode(array_column($deptChartData, 'count')) !!};

        let seriesName = "الأبحاث";
        @if($reportType === 'requests')
            seriesName = "طلبات الوثائق";
        @elseif($reportType === 'alumni')
            seriesName = "الخريجون";
        @endif

        const isDark = document.documentElement.classList.contains('dark');
        const axisColor = isDark ? '#9ca3af' : '#64748b';
        const primaryBarColor = isDark ? 'oklch(0.72 0.14 220)' : 'oklch(0.28 0.11 260)';

        const options = {
            chart: {
                type: 'bar',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'inherit',
                background: 'transparent'
            },
            theme: {
                mode: isDark ? 'dark' : 'light'
            },
            colors: [primaryBarColor],
            series: [{
                name: seriesName,
                data: deptCounts
            }],
            xaxis: {
                categories: deptNames,
                labels: { style: { colors: axisColor, fontWeight: 600 } }
            },
            yaxis: {
                labels: { style: { colors: axisColor, fontWeight: 600 } },
                opposite: isRtl
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '25%',
                }
            },
            grid: {
                borderColor: isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.08)'
            },
            dataLabels: { enabled: false }
        };
        const chart = new ApexCharts(document.querySelector("#deptBarChart"), options);
        chart.render();
    });
</script>
@endpush
@endsection
