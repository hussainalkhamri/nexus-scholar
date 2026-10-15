@extends('layouts.dashboard')

@section('title', app()->getLocale() == 'ar' ? $group->title_ar : $group->title_en)

@section('dashboard_content')
<!-- Top Navigation and Actions -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 transition-colors duration-200">
    <div class="flex items-center gap-3">
        <a href="{{ route('projects.index') }}" class="p-2 border border-border rounded-xl hover:bg-muted text-foreground bg-card transition-all shadow-sm cursor-pointer">
            <svg class="h-4 w-4 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <span class="text-[10px] font-extrabold text-muted-foreground uppercase">GP-{{ 1000 + $group->id }}</span>
            <h2 class="text-xl font-black text-foreground leading-snug">
                {{ app()->getLocale() == 'ar' ? $group->title_ar : $group->title_en }}
            </h2>
        </div>
    </div>

    <!-- Project Status Badge -->
    <div class="flex items-center gap-2">
        @if($group->status == 'completed')
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 border border-emerald-250">
                مكتمل ومؤرشف بالكامل
            </span>
        @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary border border-primary/20">
                مجموعة نشطة (إنجاز {{ $group->progress }}%)
            </span>
        @endif
    </div>
</div>

<!-- Milestone Stepper Component -->
<div class="bg-card border border-border rounded-2xl p-6 shadow-sm mb-8 transition-colors duration-200">
    <div class="grid grid-cols-4 relative items-center justify-between gap-2 md:gap-4 select-none">
        
        @php
            $stages = ['proposal', 'chapters', 'draft', 'defense'];
            $activeStageIndex = array_search($group->stage, $stages);
            $isCompleted = $group->status === 'completed';
        @endphp

        <!-- Connecting Line in Stepper -->
        <div class="absolute h-0.5 bg-muted start-[12.5%] end-[12.5%] top-[18px] md:top-[22px] z-0">
            <div class="absolute top-0 start-0 h-full bg-emerald-500 rounded transition-all duration-300" style="width: {{ $isCompleted ? 100 : ($activeStageIndex / 3) * 100 }}%"></div>
        </div>

        @foreach($stages as $index => $stg)
            @php
                $stgActive = $index === $activeStageIndex && !$isCompleted;
                $stgCompleted = $index < $activeStageIndex || $isCompleted;
                $stgPending = $index > $activeStageIndex && !$isCompleted;
            @endphp
            
            <div class="flex flex-col items-center text-center z-10">
                <!-- Node Icon -->
                <div class="h-9 w-9 md:h-11 md:w-11 rounded-full flex items-center justify-center font-extrabold text-xs md:text-sm border-2 transition-all duration-300
                    {{ $stgCompleted ? 'bg-emerald-500 border-emerald-500 text-white shadow-sm' : '' }}
                    {{ $stgActive ? 'bg-card border-primary text-primary shadow-md scale-105 ring-4 ring-primary/10' : '' }}
                    {{ $stgPending ? 'bg-card border-border text-muted-foreground' : '' }}
                ">
                    @if($stgCompleted)
                        <!-- Checkmark SVG -->
                        <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    @else
                        {{ $index + 1 }}
                    @endif
                </div>

                <!-- Label -->
                <span class="mt-3 text-[10px] md:text-xs font-bold transition-all duration-300
                    {{ $stgCompleted ? 'text-emerald-600 dark:text-emerald-400' : '' }}
                    {{ $stgActive ? 'text-primary' : '' }}
                    {{ $stgPending ? 'text-muted-foreground' : '' }}
                ">
                    {{ __('messages.stage_' . $stg) }}
                </span>
            </div>
        @endforeach

    </div>
</div>

<!-- Project Evaluation Metrics Card -->
@if($group->plagiarism_percent !== null || $group->grade !== null || $group->defense_date !== null)
    <div class="bg-card border border-border rounded-2xl p-6 shadow-sm mb-8 transition-colors duration-200">
        <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-4 uppercase">حالة التقييم الأكاديمي للمشروع</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 text-xs font-semibold">
            @if($group->plagiarism_percent !== null)
                <div class="p-3 bg-muted/30 border border-border rounded-xl">
                    <p class="text-[9px] text-muted-foreground font-extrabold uppercase mb-0.5">نسبة الاستلال العلمي</p>
                    <p class="text-base {{ $group->plagiarism_percent > 20 ? 'text-rose-600 font-black' : 'text-emerald-600 font-black' }}">
                        {{ $group->plagiarism_percent }}% 
                        <span class="text-[10px] font-bold block mt-0.5">
                            {{ $group->plagiarism_percent > 20 ? 'يتجاوز الحد المسموح 20%' : 'آمن / مقبول' }}
                        </span>
                    </p>
                </div>
            @endif
            @if($group->defense_date !== null)
                <div class="p-3 bg-muted/30 border border-border rounded-xl">
                    <p class="text-[9px] text-muted-foreground font-extrabold uppercase mb-0.5">تاريخ المناقشة المقررة</p>
                    <p class="text-xs text-foreground font-extrabold">
                        {{ \Carbon\Carbon::parse($group->defense_date)->format('Y-m-d H:i') }}
                    </p>
                </div>
            @endif
            @if($group->defense_committee !== null)
                <div class="p-3 bg-muted/30 border border-border rounded-xl md:col-span-1">
                    <p class="text-[9px] text-muted-foreground font-extrabold uppercase mb-0.5">لجنة المناقشة والتحكيم</p>
                    <p class="text-xs text-foreground/80 font-extrabold line-clamp-2">
                        {{ $group->defense_committee }}
                    </p>
                </div>
            @endif
            @if($group->grade !== null)
                <div class="p-3 bg-muted/30 border border-border rounded-xl">
                    <p class="text-[9px] text-muted-foreground font-extrabold uppercase mb-0.5">القرار النهائي والنتيجة</p>
                    <p class="text-base font-extrabold capitalize {{ $group->grade === 'passed' ? 'text-emerald-600 font-bold' : ($group->grade === 'passed_revisions' ? 'text-amber-600 font-bold' : 'text-rose-600 font-bold') }}">
                        {{ $group->grade === 'passed' ? 'ناجح (تخرج)' : ($group->grade === 'passed_revisions' ? 'ناجح مع التعديلات' : 'غير مقبول / راسب') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
@endif

<!-- Supervisor Control Panel -->
@if(in_array(auth()->user()->role, ['admin', 'supervisor']) && $group->status !== 'completed')
    <div class="bg-card border-2 border-primary/20 rounded-2xl p-6 shadow-md mb-8 transition-colors duration-200">
        <div class="flex items-center gap-2 mb-4">
            <span class="p-1.5 bg-primary/10 text-primary rounded-lg">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </span>
            <h3 class="font-extrabold text-foreground text-sm uppercase tracking-wide">إجراءات وإدارة المشرف الأكاديمي للبحث</h3>
        </div>

        <form action="{{ route('projects.stage.advance', $group->id) }}" method="POST" class="space-y-4" onsubmit="return confirm('هل أنت متأكد من الانتقال بالمشروع إلى المرحلة التالية؟')">
            @csrf

            @if($group->stage === 'proposal')
                <div class="p-4 bg-primary/10 rounded-xl border border-primary/20 text-xs font-semibold text-foreground/90 leading-relaxed">
                    <strong>المرحلة الحالية: مقترح البحث (Proposal)</strong><br>
                    لقد قدمت المجموعة مقترح مشروع التخرج الخاص بها. راجع تفاصيل المشروع وانقر على زر الاعتماد أدناه لنقل المجموعة إلى مرحلة كتابة فصول البحث.
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>اعتماد مقترح العنوان والمشروع</span>
                    </button>
                </div>

            @elseif($group->stage === 'chapters')
                <div class="p-4 bg-primary/10 rounded-xl border border-primary/20 text-xs font-semibold text-foreground/90 leading-relaxed">
                    <strong>المرحلة الحالية: صياغة فصول البحث (Chapters)</strong><br>
                    يقوم الطلاب حالياً برفع مسودات فصول البحث وطلب المراجعة. بمجرد اكتمال ومراجعة كافة الفصول، انقر على زر الاعتماد أدناه للمضي قدماً إلى مرحلة تقديم المسودة النهائية.
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>اعتماد الفصول ومراجعة المسودة النهائية</span>
                    </button>
                </div>

            @elseif($group->stage === 'draft')
                <div class="p-4 bg-amber-500/10 rounded-xl border border-amber-500/25 text-xs font-semibold text-foreground/90 leading-relaxed space-y-4">
                    <div>
                        <strong>المرحلة الحالية: مراجعة المسودة النهائية وفحص الاستلال (Draft)</strong><br>
                        حدد نسبة الاستلال العلمي (نسبة الانتحال) لملف البحث النهائي المرفوع.
                        <span class="text-rose-600 dark:text-rose-400 font-bold block mt-1">سياسة النظام: إذا تجاوزت نسبة الاستلال 20%، سيقوم النظام تلقائياً بحظر الانتقال لمرحلة المناقشة.</span>
                    </div>
                    <div class="max-w-xs">
                        <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">نسبة الاستلال العلمي (%)</label>
                        <div class="relative rounded-xl shadow-sm">
                            <input type="number" name="plagiarism_percent" min="0" max="100" required value="{{ old('plagiarism_percent', $group->plagiarism_percent ?? 0) }}" class="w-full text-xs font-bold px-3.5 py-2.5 bg-card border border-border text-foreground rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-xs font-extrabold text-muted-foreground">%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                        <span>رصد الاستلال والمضي للمناقشة</span>
                    </button>
                </div>

            @elseif($group->stage === 'defense')
                <div class="p-4 bg-emerald-500/10 rounded-xl border border-emerald-500/25 text-xs font-semibold text-foreground/90 space-y-4">
                    <div>
                        <strong>المرحلة الحالية: لجنة المناقشة ورصد النتيجة النهائية (Defense)</strong><br>
                        استكمل البيانات المطلوبة أدناه لتسجيل التقييم النهائي لبحث التخرج.
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">أعضاء لجنة المناقشة والتحكيم</label>
                            <input type="text" name="defense_committee" value="{{ old('defense_committee', $group->defense_committee) }}" placeholder="مثال: د. أحمد، د. سالم" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card border border-border text-foreground rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">تاريخ ووقت المناقشة</label>
                            <input type="datetime-local" name="defense_date" value="{{ old('defense_date', $group->defense_date ? \Carbon\Carbon::parse($group->defense_date)->format('Y-m-d\TH:i') : '') }}" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card border border-border text-foreground rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">القرار النهائي والنتيجة</label>
                            <select name="grade" required class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card border border-border text-foreground rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                <option value="passed" {{ old('grade', $group->grade) == 'passed' ? 'selected' : '' }}>ناجح (تخرج الطلاب - مكتمل بنسبة 100%)</option>
                                <option value="passed_revisions" {{ old('grade', $group->grade) == 'passed_revisions' ? 'selected' : '' }}>ناجح مع التعديلات</option>
                                <option value="failed" {{ old('grade', $group->grade) == 'failed' ? 'selected' : '' }}>غير مقبول / راسب (إعادة ضبط لتقديم المقترح)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>رصد التقييم النهائي واعتمد تخرج الطلاب</span>
                    </button>
                </div>
            @endif
        </form>
    </div>
@endif

<!-- Tabs Selectors -->
<div class="border-b border-border mb-6 flex gap-4 no-print select-none">
    <button onclick="switchTab('members')" id="tabBtn-members" class="tab-btn pb-3 px-2 border-b-2 border-transparent text-sm font-bold text-muted-foreground hover:text-foreground transition-colors cursor-pointer">
        {{ __('messages.tab_members') }}
    </button>
    <button onclick="switchTab('files')" id="tabBtn-files" class="tab-btn pb-3 px-2 border-b-2 border-transparent text-sm font-bold text-muted-foreground hover:text-foreground transition-colors cursor-pointer">
        {{ __('messages.tab_files') }}
    </button>
    <button onclick="switchTab('discussion')" id="tabBtn-discussion" class="tab-btn pb-3 px-2 border-b-2 border-transparent text-sm font-bold text-muted-foreground hover:text-foreground transition-colors cursor-pointer">
        {{ __('messages.tab_discussion') }}
    </button>
</div>

<!-- Tabs Content Panels -->
<div>
    <!-- Tab 1: Members Panel -->
    <div id="tabPanel-members" class="tab-panel hidden space-y-6">
        <!-- Students list -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-sm transition-colors duration-200">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-4 flex items-center gap-2">
                <svg class="h-5 w-5 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>{{ __('messages.students') }}</span>
            </h3>
            <div class="divide-y divide-border">
                @forelse($group->students as $student)
                    <div class="py-4 first:pt-0 last:pb-0 flex items-center gap-4">
                        <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
                            {{ mb_substr($student->name_ar, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-foreground">
                                {{ app()->getLocale() == 'ar' ? $student->name_ar : $student->name_en }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-muted-foreground font-semibold py-2">{{ __('messages.no_data') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Supervisor information -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-sm transition-colors duration-200">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-4 flex items-center gap-2">
                <svg class="h-5 w-5 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                <span>{{ __('messages.supervisor') }}</span>
            </h3>
            
            @if($group->supervisorUser)
                <div class="flex items-center gap-4">
                    <div class="h-10 w-10 rounded-full bg-accent/10 text-accent flex items-center justify-center font-bold text-sm">
                        {{ mb_substr($group->supervisorUser->name_ar, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-foreground">
                            {{ app()->getLocale() == 'ar' ? $group->supervisorUser->name_ar : $group->supervisorUser->name_en }}
                        </p>
                    </div>
                </div>
            @else
                <p class="text-xs font-semibold text-foreground">{{ $group->supervisor }}</p>
            @endif
        </div>
    </div>

    <!-- Tab 2: Files/Drafts Panel -->
    <div id="tabPanel-files" class="tab-panel hidden space-y-6">
        <!-- File list -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-sm transition-colors duration-200">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-4 flex items-center gap-2">
                <svg class="h-5 w-5 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>{{ __('messages.tab_files') }}</span>
            </h3>
            
            <div class="divide-y divide-border">
                @forelse($group->files as $file)
                    @php
                        $ext = strtolower(pathinfo($file->filename, PATHINFO_EXTENSION));
                        $iconColor = 'text-blue-500 bg-blue-50 dark:bg-blue-950/20';
                        if ($ext === 'pdf') {
                            $iconColor = 'text-rose-500 bg-rose-50 dark:bg-rose-950/20';
                        } elseif (in_array($ext, ['doc', 'docx'])) {
                            $iconColor = 'text-blue-600 bg-blue-50 dark:bg-blue-950/20';
                        } elseif (in_array($ext, ['zip', 'rar'])) {
                            $iconColor = 'text-amber-500 bg-amber-50 dark:bg-amber-950/20';
                        } elseif (in_array($ext, ['xlsx', 'xls', 'csv'])) {
                            $iconColor = 'text-emerald-500 bg-emerald-50 dark:bg-emerald-950/20';
                        }
                    @endphp
                    <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                        <div class="min-w-0 flex-1 flex items-center gap-3">
                            <span class="p-2 rounded-lg shrink-0 {{ $iconColor }}">
                                @if($ext === 'pdf')
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                @elseif(in_array($ext, ['zip', 'rar']))
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                @elseif(in_array($ext, ['xlsx', 'xls', 'csv']))
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @endif
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-foreground truncate">
                                    {{ $file->filename }}
                                </p>
                                <p class="text-[10px] text-muted-foreground font-semibold">
                                    {{ $file->file_size }} • {{ app()->getLocale() == 'ar' ? $file->user->name_ar : $file->user->name_en }} • {{ $file->created_at->format('Y-m-d') }}
                                </p>
                            </div>
                        </div>
                        
                        <a href="{{ asset('storage/' . $file->file_path) }}" download class="text-xs font-extrabold text-primary hover:underline whitespace-nowrap cursor-pointer">
                            {{ __('messages.view') }}
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-muted-foreground font-semibold py-2">{{ __('messages.no_data') }}</p>
                @endforelse
            </div>
        </div>

        <!-- File Upload Form (Only active if not completed) -->
        @if($group->status !== 'completed')
            <div class="bg-card border border-border rounded-2xl p-6 shadow-sm transition-colors duration-200">
                <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-4">
                    {{ __('messages.upload_file') }}
                </h3>
                <form action="{{ route('projects.upload', $group->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4" onsubmit="return confirm('هل أنت متأكد من رفع هذا الملف؟')">
                    @csrf
                    <div>
                        <input type="file" name="file" required class="w-full text-xs font-semibold file:me-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 border border-border rounded-xl p-1.5 focus:outline-none">
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-all shadow-sm cursor-pointer">
                        {{ __('messages.upload_file') }}
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Tab 3: Discussion Panel -->
    <div id="tabPanel-discussion" class="tab-panel hidden space-y-6">
        <!-- Messages Board -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-sm transition-colors duration-200">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-6 flex items-center gap-2">
                <svg class="h-5 w-5 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                </svg>
                <span>{{ __('messages.tab_discussion') }}</span>
            </h3>

            <div class="space-y-6 max-h-[400px] overflow-y-auto mb-6 pr-2">
                @forelse($group->comments as $comment)
                    @php
                        $isMe = $comment->user_id && $comment->user_id === auth()->id();
                        $userExists = !is_null($comment->user);
                    @endphp
                    <div class="flex flex-col gap-1 {{ $isMe ? 'items-end' : 'items-start' }}">
                        <div class="flex items-center gap-2 text-[10px] font-bold text-muted-foreground">
                            <span>
                                @if($userExists)
                                    {{ app()->getLocale() == 'ar' ? $comment->user->name_ar : $comment->user->name_en }}
                                @else
                                    {{ $comment->author_name ?? __('messages.deleted_user') }}
                                @endif
                            </span>
                            @if($userExists)
                                <span class="inline-flex px-1.5 py-0.5 rounded bg-muted text-muted-foreground uppercase text-[8px]">{{ __('messages.role_' . $comment->user->role) }}</span>
                            @else
                                <span class="inline-flex px-1.5 py-0.5 rounded bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 uppercase text-[8px]">{{ __('messages.deleted_user') }}</span>
                            @endif
                            <span>•</span>
                            <span>{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="p-3.5 rounded-2xl text-xs font-semibold leading-relaxed max-w-[85%]
                            {{ $isMe ? 'bg-primary text-white rounded-te-none' : 'bg-muted/60 dark:bg-card text-foreground border border-border rounded-ts-none' }}
                        ">
                            {{ $comment->content }}
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-muted-foreground font-semibold py-4 text-center">{{ __('messages.no_data') }}</p>
                @endforelse
            </div>

            <!-- Post message form -->
            <form action="{{ route('projects.comment.store', $group->id) }}" method="POST" class="mt-4 flex gap-2">
                @csrf
                <textarea name="content" required placeholder="{{ __('messages.add_comment') }}" rows="1" class="flex-1 text-xs font-semibold px-4 py-3 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none"></textarea>
                <button type="submit" class="px-5 py-3 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center justify-center cursor-pointer">
                    {{ __('messages.send') }}
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Simple Blade client-side tab switching script
    function switchTab(tabId) {
        // Hide all panels
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        // Show selected panel
        document.getElementById('tabPanel-' + tabId).classList.remove('hidden');

        // Reset all buttons style
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-primary', 'text-primary');
            btn.classList.add('border-transparent', 'text-muted-foreground');
        });

        // Highlight selected button
        const activeBtn = document.getElementById('tabBtn-' + tabId);
        activeBtn.classList.remove('border-transparent', 'text-muted-foreground');
        activeBtn.classList.add('border-primary', 'text-primary');

        // Save active tab state in localStorage
        localStorage.setItem('activeProjectTab-{{ $group->id }}', tabId);
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Restore active tab or default to members
        const activeTab = localStorage.getItem('activeProjectTab-{{ $group->id }}') || 'members';
        switchTab(activeTab);
    });
</script>
@endsection
