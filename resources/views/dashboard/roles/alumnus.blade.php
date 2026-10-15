<!-- Certificate Issuance Progress Tracker & Clearance Checklist -->
<div class="bg-card border border-border rounded-2xl p-6 shadow-sm mb-6 transition-colors duration-200">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Right Section: Timeline Progress (col-span-2) -->
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-1">حالة معاملة وثيقة التخرج</h3>
                    <p class="text-[10px] text-muted-foreground font-semibold">متابعة إجراءات استخراج ومصادقة وثيقتك الجامعية</p>
                </div>
                <div>
                    <!-- Dynamic status badge -->
                    @if($certRequest->status == 'completed')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900">
                            جاهزة للتنزيل
                        </span>
                    @elseif($certRequest->status == 'processing')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-900">
                            قيد المراجعة والاعتماد
                        </span>
                    @elseif($certRequest->status == 'pending')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-900">
                            قيد التدقيق والانتظار
                        </span>
                    @elseif($certRequest->status == 'overdue')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900">
                            متأخرة / تتطلب إجراء
                        </span>
                    @endif
                </div>
            </div>

            <!-- Stepper Container -->
            <div class="relative flex items-center justify-between w-full mt-8 mb-4">
                <!-- Progress Line -->
                @php
                    $stepPercent = 0;
                    if($certRequest->status == 'completed') {
                        $stepPercent = 100;
                    } elseif($certRequest->status == 'processing') {
                        $stepPercent = 66;
                    } elseif($certRequest->status == 'pending') {
                        $stepPercent = 33;
                    } elseif($certRequest->status == 'overdue') {
                        $stepPercent = 50;
                    }
                @endphp
                <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-1 bg-muted rounded-full z-0">
                    <div class="h-full rounded-full transition-all duration-300 {{ $certRequest->status == 'completed' ? 'bg-emerald-500' : ($certRequest->status == 'overdue' ? 'bg-rose-500' : 'bg-primary') }}" style="width: {{ $stepPercent }}%"></div>
                </div>

                <!-- Step 1: Academic Audit -->
                @php
                    $step1Active = true;
                    $step1Done = in_array($certRequest->status, ['processing', 'completed']);
                @endphp
                <div class="relative flex flex-col items-center z-10">
                    <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-300
                        {{ $step1Done ? 'bg-emerald-500 border-emerald-500 text-white' : ($step1Active ? 'bg-card border-primary text-primary shadow-sm' : 'bg-muted border-muted text-muted-foreground') }}">
                        @if($step1Done)
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        @else
                            1
                        @endif
                    </div>
                    <span class="text-[10px] font-bold text-foreground mt-2">التدقيق الأكاديمي</span>
                </div>

                <!-- Step 2: Finance Clearance -->
                @php
                    $step2Active = in_array($certRequest->status, ['pending', 'processing', 'completed', 'overdue']);
                    $step2Done = in_array($certRequest->status, ['processing', 'completed']);
                    $step2Failed = ($certRequest->status == 'overdue');
                @endphp
                <div class="relative flex flex-col items-center z-10">
                    <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-300
                        {{ $step2Done ? 'bg-emerald-500 border-emerald-500 text-white' : ($step2Failed ? 'bg-rose-500 border-rose-500 text-white animate-pulse' : ($step2Active && $certRequest->status == 'pending' ? 'bg-card border-primary text-primary shadow-sm' : 'bg-muted border-muted text-muted-foreground')) }}">
                        @if($step2Done)
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        @elseif($step2Failed)
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        @else
                            2
                        @endif
                    </div>
                    <span class="text-[10px] font-bold text-foreground mt-2">براءة الذمة المالية</span>
                </div>

                <!-- Step 3: Deanship Approval -->
                @php
                    $step3Active = in_array($certRequest->status, ['processing', 'completed']);
                    $step3Done = ($certRequest->status == 'completed');
                @endphp
                <div class="relative flex flex-col items-center z-10">
                    <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-300
                        {{ $step3Done ? 'bg-emerald-500 border-emerald-500 text-white' : ($step3Active ? 'bg-card border-primary text-primary shadow-sm' : 'bg-muted border-muted text-muted-foreground') }}">
                        @if($step3Done)
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        @else
                            3
                        @endif
                    </div>
                    <span class="text-[10px] font-bold text-foreground mt-2">اعتماد عمادة الطلاب</span>
                </div>

                <!-- Step 4: Ready for Pick-up -->
                @php
                    $step4Active = ($certRequest->status == 'completed');
                @endphp
                <div class="relative flex flex-col items-center z-10">
                    <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-300
                        {{ $step4Active ? 'bg-emerald-500 border-emerald-500 text-white shadow-md' : 'bg-muted border-muted text-muted-foreground' }}">
                        @if($step4Active)
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        @else
                            4
                        @endif
                    </div>
                    <span class="text-[10px] font-bold text-foreground mt-2">جاهزية الوثيقة</span>
                </div>
            </div>

            <!-- Optional message or download action -->
            <div class="mt-6 pt-4 border-t border-border flex items-center justify-between">
                @if($certRequest->status == 'completed')
                    <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>تهانينا! تمت الموافقة على معاملتك بالكامل ووثيقتك جاهزة للتحميل.</span>
                    </p>
                    <a href="{{ route('alumni.requests.download', $certRequest->id) }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-md flex items-center gap-1.5 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        <span>تحميل وثيقة التخرج الرقمية</span>
                    </a>
                @elseif($certRequest->status == 'overdue')
                    <p class="text-[11px] text-rose-600 dark:text-rose-400 font-bold flex items-center gap-1">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span>يرجى التوجّه إلى الإدارة المالية لحل مشكلة براءة الذمة المالية الموقوفة.</span>
                    </p>
                @else
                    <p class="text-[11px] text-muted-foreground font-semibold flex items-center gap-1">
                        <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>تأخذ هذه المعاملة عادةً من 3 إلى 7 أيام عمل لتكتمل بجميع براءات الذمة.</span>
                    </p>
                @endif
            </div>
        </div>

        <!-- Left Section: Clearance Checklist (col-span-1) -->
        <div class="border-t lg:border-t-0 lg:border-s border-border pt-6 lg:pt-0 lg:ps-8">
            <h4 class="font-extrabold text-foreground text-xs mb-4">متطلبات التخرج وبراءة الذمة</h4>
            
            <ul class="space-y-3.5 text-xs font-semibold text-foreground">
                <!-- ID card surrender -->
                <li class="flex items-start gap-3">
                    <span class="h-5 w-5 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    </span>
                    <div>
                        <p class="text-[11px] text-foreground font-bold">تسليم البطاقة الجامعية</p>
                        <span class="text-[9px] text-emerald-600 font-bold">مكتمل ومعتمد</span>
                    </div>
                </li>

                <!-- Library Clearance -->
                <li class="flex items-start gap-3">
                    <span class="h-5 w-5 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    </span>
                    <div>
                        <p class="text-[11px] text-foreground font-bold">براءة ذمة المكتبة الجامعية</p>
                        <span class="text-[9px] text-emerald-600 font-bold">مكتمل ومعتمد</span>
                    </div>
                </li>

                <!-- Financial Settlement -->
                @php
                    $finCleared = in_array($certRequest->status, ['processing', 'completed']);
                @endphp
                <li class="flex items-start gap-3">
                    @if($finCleared)
                        <span class="h-5 w-5 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </span>
                        <div>
                            <p class="text-[11px] text-foreground font-bold">سداد الرسوم الدراسية والمالية</p>
                            <span class="text-[9px] text-emerald-600 font-bold">مكتمل ومعتمد</span>
                        </div>
                    @else
                        <span class="h-5 w-5 rounded-full bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </span>
                        <div>
                            <p class="text-[11px] text-foreground font-bold">سداد الرسوم الدراسية والمالية</p>
                            <span class="text-[9px] text-amber-600 font-bold">معلق - يرجى مراجعة الشؤون المالية</span>
                        </div>
                    @endif
                </li>

                <!-- Graduation Thesis Submission -->
                <li class="flex items-start gap-3">
                    <span class="h-5 w-5 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    </span>
                    <div>
                        <p class="text-[11px] text-foreground font-bold">تسليم نسخة من بحث التخرج</p>
                        <span class="text-[9px] text-emerald-600 font-bold">مكتمل ومعتمد</span>
                    </div>
                </li>

                <!-- Document fees -->
                @php
                    $feesPaid = ($certRequest->status == 'completed');
                @endphp
                <li class="flex items-start gap-3">
                    @if($feesPaid)
                        <span class="h-5 w-5 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </span>
                        <div>
                            <p class="text-[11px] text-foreground font-bold">سداد رسوم استخراج الوثيقة</p>
                            <span class="text-[9px] text-emerald-600 font-bold">مكتمل ومعتمد</span>
                        </div>
                    @else
                        <span class="h-5 w-5 rounded-full bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </span>
                        <div>
                            <p class="text-[11px] text-foreground font-bold">سداد رسوم استخراج الوثيقة</p>
                            <span class="text-[9px] text-amber-600 font-bold">مطلوب السداد (2000 ريال)</span>
                        </div>
                    @endif
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Profile Card & Points -->
    <div class="bg-card text-foreground p-6 border border-border rounded-2xl shadow-sm flex flex-col justify-between">
        <div>
            <h3 class="font-extrabold text-gray-900 dark:text-foreground text-sm tracking-wide mb-4">ملفي المهني الشخصي</h3>
            <div class="flex items-center gap-3 mb-4">
                <div class="h-10 w-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-sm">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-foreground">{{ auth()->user()->name_ar }}</h4>
                    <span class="text-[10px] text-gray-400 font-semibold capitalize">{{ auth()->user()->department }} دفعة سنة {{ $profile->graduation_year }}</span>
                </div>
            </div>

            <!-- Employment Badge -->
            <div class="mb-4">
                @if($profile->employed)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900">
                        موظف لدى: {{ $profile->workplace ?? 'جهة العمل' }}
                    </span>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold mt-1">{{ $profile->position }}</p>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-900">
                        يبحث بنشاط عن فرص عمل
                    </span>
                @endif
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 dark:border-border flex items-center justify-end">
            <a href="{{ route('profile.edit') }}" class="text-[10px] text-primary font-bold hover:underline">تعديل الملف الشخصي</a>
        </div>
    </div>

    <!-- Career Incentives Feed -->
    <div class="bg-card text-foreground p-6 border border-border rounded-2xl shadow-sm lg:col-span-2 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide">الفرص والمحفزات المهنية المقترحة</h3>
            <a href="{{ route('incentives.index') }}" class="text-xs text-primary font-bold hover:underline">عرض الكل</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 flex-grow">
            @forelse($incentives as $item)
                <div class="p-4 bg-gray-50 dark:bg-muted/40 border border-gray-100 dark:border-border rounded-xl flex flex-col justify-between hover:shadow-sm transition-all text-xs font-semibold">
                    <div>
                        <span class="text-[8px] font-extrabold uppercase mb-1.5 inline-block {{ $item->kind == 'job' ? 'text-emerald-600 dark:text-emerald-400' : ($item->kind == 'course' ? 'text-blue-600 dark:text-blue-400' : 'text-rose-600 dark:text-rose-400') }}">
                            {{ $item->kind == 'course' ? __('messages.courses') : ($item->kind == 'job' ? __('messages.jobs') : __('messages.discounts')) }}
                        </span>
                        <h4 class="font-bold text-foreground line-clamp-1 mb-1">{{ app()->getLocale() == 'ar' ? $item->title_ar : $item->title_en }}</h4>
                        <p class="text-[10px] text-muted-foreground line-clamp-2 leading-relaxed mb-3">{{ app()->getLocale() == 'ar' ? $item->detail_ar : $item->detail_en }}</p>
                    </div>
                    @if(auth()->user()->incentives->contains($item->id))
                        <button disabled class="w-full py-1.5 bg-gray-100 dark:bg-muted border border-gray-200 dark:border-border text-gray-400 dark:text-gray-500 rounded-lg text-[10px] font-bold text-center cursor-not-allowed">
                            @php
                                $pivot = auth()->user()->incentives->find($item->id)->pivot;
                            @endphp
                            {{ $pivot->status == 'enrolled' ? 'مسجل' : ($pivot->status == 'redeemed' ? 'مسترد' : 'متقدم') }}
                        </button>
                    @else
                        <form action="{{ route('incentives.claim', $item->id) }}" method="POST" class="w-full" onsubmit="return confirm('هل أنت متأكد من التقديم على هذه الفرصة/المحفز؟')">
                            @csrf
                            <button type="submit" class="w-full py-1.5 bg-card border border-border text-foreground hover:bg-primary hover:text-white dark:hover:bg-primary rounded-lg text-[10px] font-bold transition-all text-center cursor-pointer">
                                @if($item->kind == 'course') تسجيل @elseif($item->kind == 'job') تقديم @else استرداد @endif
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="text-xs text-muted-foreground font-semibold col-span-3 text-center py-6">لا توجد محفزات مهنية مقترحة حالياً.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Document Requests List -->
<div class="bg-card text-foreground border border-border rounded-2xl shadow-sm overflow-hidden">
    <div class="p-5 border-b border-border flex items-center justify-between">
        <div>
            <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-1">طلبات الوثائق الخاصة بي</h3>
            <p class="text-[10px] text-muted-foreground font-semibold uppercase">متابعة خطابات التخرج، كشوفات الدرجات، وصور الشهادات المعمدة</p>
        </div>
        <a href="{{ route('alumni.requests.index') }}" class="text-xs text-primary font-bold hover:underline">
            عرض تفاصيل المعاملة
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-start border-collapse text-xs font-semibold text-foreground">
            <thead>
                <tr class="bg-muted/40 border-b border-border text-muted-foreground font-extrabold uppercase text-[9px] tracking-wider text-start">
                    <th class="p-4 text-start font-extrabold">رمز الطلب</th>
                    <th class="p-4 text-start font-extrabold">نوع الوثيقة</th>
                    <th class="p-4 text-start font-extrabold">تاريخ الطلب</th>
                    <th class="p-4 text-start font-extrabold">فترة الانتظار المتوقعة</th>
                    <th class="p-4 text-start font-extrabold">الحالة</th>
                    <th class="p-4 text-end font-extrabold no-print">الإجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($requests as $r)
                    <tr class="hover:bg-muted/30 transition-colors">
                        <td class="p-4 font-bold text-muted-foreground">REQ-{{ 3000 + $r->id }}</td>
                        <td class="p-4 font-bold text-foreground">
                            {{ app()->getLocale() == 'ar' ? $r->doc_type_ar : $r->doc_type_en }}
                        </td>
                        <td class="p-4 text-muted-foreground font-medium">{{ $r->created_at->format('Y-m-d') }}</td>
                        <td class="p-4 text-muted-foreground">{{ $r->waiting_days }} يوم</td>
                        <td class="p-4">
                            @if($r->status == 'completed')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900">
                                    جاهزة / مُسلمة
                                </span>
                            @elseif($r->status == 'processing')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-900">
                                    قيد المراجعة والتدقيق
                                </span>
                            @elseif($r->status == 'pending')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-900">
                                    قيد الانتظار
                                </span>
                            @elseif($r->status == 'overdue')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900">
                                    متأخرة
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-end no-print">
                            @if($r->status == 'completed')
                                <a href="{{ route('alumni.requests.download', $r->id) }}" target="_blank" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-[9px] shadow-sm transition-all inline-block">
                                    تحميل الوثيقة
                                </a>
                            @else
                                <span class="text-muted-foreground font-bold text-[9px]">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-muted-foreground font-medium">لم تقم بطلب أي وثائق بعد.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
