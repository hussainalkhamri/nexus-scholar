<!-- Supervisor KPIs -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-card p-6 border border-border rounded-2xl shadow-sm flex items-center gap-5 premium-hover">
        <span class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </span>
        <div>
            <p class="text-xs text-muted-foreground font-bold uppercase tracking-wider mb-1">الأبحاث المكتملة المعمدة</p>
            <h3 class="text-2xl font-black text-foreground">{{ $totalCompleted }}</h3>
            <p class="text-[10px] text-muted-foreground mt-1 font-medium">مشاريع تم مناقشتها واعتمادها بالكامل</p>
        </div>
    </div>

    <div class="bg-card p-6 border border-border rounded-2xl shadow-sm flex items-center gap-5 premium-hover">
        <span class="p-3 bg-amber-50 text-amber-600 rounded-xl">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </span>
        <div>
            <p class="text-xs text-muted-foreground font-bold uppercase tracking-wider mb-1">أبحاث قيد المراجعة</p>
            <h3 class="text-2xl font-black text-foreground">{{ $totalProcessing }}</h3>
            <p class="text-[10px] text-muted-foreground mt-1 font-medium">مجموعات في مرحلة إعداد وتعديل المسودات حالياً</p>
        </div>
    </div>

    <div class="bg-card p-6 border border-border rounded-2xl shadow-sm flex items-center gap-5 premium-hover">
        <span class="p-3 bg-blue-50 text-blue-600 rounded-xl">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </span>
        <div>
            <p class="text-xs text-muted-foreground font-bold uppercase tracking-wider mb-1">أبحاث قيد الانتظار / متأخرة</p>
            <h3 class="text-2xl font-black text-foreground">{{ $totalPending }}</h3>
            <p class="text-[10px] text-muted-foreground mt-1 font-medium">بانتظار تسليم المسودات أو متأخرة عن الجدول الزمني</p>
        </div>
    </div>
</div>

<!-- Stuck Groups Alerts -->
@if($stuckGroups->count() > 0)
    <div class="mb-8 p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/30 text-rose-800 dark:text-rose-300 flex flex-col gap-2 shadow-sm rounded-2xl">
        <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wide">
            <svg class="h-5 w-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>تنبيه المجموعات المتعثرة (بدون أي نشاط لأكثر من 10 أيام)</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 mt-1">
            @foreach($stuckGroups as $g)
                <a href="{{ route('projects.show', $g->id) }}" class="p-3 bg-card border border-border hover:border-rose-300 dark:hover:border-rose-900 rounded-xl flex flex-col justify-between hover:shadow-sm transition-all text-start">
                    <h4 class="text-xs font-bold text-foreground line-clamp-1">
                        {{ app()->getLocale() == 'ar' ? $g->title_ar : $g->title_en }}
                    </h4>
                    <span class="text-[9px] text-rose-600 dark:text-rose-450 font-bold mt-2">
                        متعثرة في مرحلة "{{ __('messages.stage_' . $g->stage) }}" منذ {{ $g->days_in_stage }} يوم
                    </span>
                </a>
            @endforeach
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Supervised Groups List -->
    <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden lg:col-span-2">
        <div class="p-5 border-b border-border flex items-center justify-between">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide">مجموعاتي البحثية تحت الإشراف</h3>
            <span class="text-[10px] bg-muted text-muted-foreground px-2 py-0.5 rounded-full font-bold">الإجمالي: {{ $groups->count() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-start border-collapse text-xs font-semibold text-foreground">
                <thead>
                    <tr class="bg-muted/40 border-b border-border text-muted-foreground font-extrabold uppercase text-[9px] tracking-wider text-start">
                        <th class="p-4 text-start font-extrabold">الرمز</th>
                        <th class="p-4 text-start font-extrabold">عنوان المشروع</th>
                        <th class="p-4 text-start font-extrabold">المرحلة</th>
                        <th class="p-4 text-start font-extrabold">نسبة الإنجاز</th>
                        <th class="p-4 text-end font-extrabold no-print">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($groups as $g)
                        <tr class="hover:bg-muted/30 transition-colors">
                            <td class="p-4 font-bold text-muted-foreground">GP-{{ 1000 + $g->id }}</td>
                            <td class="p-4 font-bold text-foreground">
                                <a href="{{ route('projects.show', $g->id) }}" class="hover:text-primary transition-colors">
                                    {{ app()->getLocale() == 'ar' ? $g->title_ar : $g->title_en }}
                                </a>
                            </td>
                            <td class="p-4 capitalize">
                                <span class="px-2 py-0.5 rounded bg-muted text-muted-foreground text-[10px] font-bold">
                                    {{ __('messages.stage_' . $g->stage) }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-muted rounded-full h-1 overflow-hidden">
                                        <div class="bg-primary h-full" style="width: {{ $g->progress }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-muted-foreground">{{ $g->progress }}%</span>
                                </div>
                            </td>
                            <td class="p-4 text-end no-print">
                                <a href="{{ route('projects.show', $g->id) }}" class="text-primary hover:underline font-bold">إدارة</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-muted-foreground font-medium">لا توجد مشاريع قيد الإشراف حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Comments / Notes -->
    <div class="bg-card p-6 border border-border rounded-2xl shadow-sm flex flex-col premium-hover">
        <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-4">المناقشات الأخيرة</h3>
        <div class="flex-grow space-y-4 overflow-y-auto max-h-[300px]">
            @forelse($latestComments as $c)
                <div class="border-b border-border pb-3 last:border-b-0 last:pb-0">
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="text-xs font-bold text-foreground">{{ $c->user ? ($c->user->name_ar ?? $c->author_name) : ($c->author_name ?? __('messages.deleted_user')) }}</span>
                        <span class="text-[9px] text-muted-foreground font-semibold">{{ $c->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-muted-foreground font-medium line-clamp-2 leading-normal mb-1.5">
                        "{{ $c->content }}"
                    </p>
                    <a href="{{ route('projects.show', $c->group_id) }}" class="text-[10px] text-primary font-bold hover:underline">
                        في: {{ app()->getLocale() == 'ar' ? $c->group->title_ar : $c->group->title_en }}
                    </a>
                </div>
            @empty
                <p class="text-xs text-muted-foreground font-semibold">لا توجد تعليقات مؤخراً.</p>
            @endforelse
        </div>
    </div>
</div>
