@if($group)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Project Info Card -->
        <div class="bg-card p-6 border border-border rounded-2xl shadow-sm lg:col-span-2 flex flex-col justify-between transition-colors duration-200">
            <div>
                <div class="flex items-center justify-between gap-2 mb-4">
                    <span class="text-[10px] font-extrabold text-primary bg-primary/10 px-2.5 py-1 rounded-lg">
                        GP-{{ 1000 + $group->id }}
                    </span>
                    <span class="text-xs font-bold text-muted-foreground">{{ $group->semester }}</span>
                </div>
                <h3 class="text-lg font-black text-foreground leading-snug mb-3">
                    {{ app()->getLocale() == 'ar' ? $group->title_ar : $group->title_en }}
                </h3>
                <div class="flex items-center gap-4 text-xs font-semibold text-muted-foreground mb-6">
                    <div>
                        <p class="text-[10px] text-muted-foreground/80 font-extrabold uppercase mb-0.5">{{ __('messages.supervisor') }}</p>
                        <p class="text-foreground font-bold">{{ $supervisor ? (app()->getLocale() == 'ar' ? $supervisor->name_ar : $supervisor->name_en) : $group->supervisor }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-muted-foreground/80 font-extrabold uppercase mb-0.5">{{ __('messages.filter_dept') }}</p>
                        <p class="text-foreground font-bold">{{ $group->department }}</p>
                    </div>
                </div>
            </div>

            <!-- Progress -->
            <div>
                <div class="flex justify-between text-xs font-bold text-muted-foreground mb-2">
                    <span class="uppercase">المرحلة الحالية: {{ __('messages.stage_' . $group->stage) }}</span>
                    <span>{{ $group->progress }}%</span>
                </div>
                <div class="w-full bg-muted rounded-full h-2 overflow-hidden mb-1">
                    <div class="bg-primary h-full rounded-full transition-all duration-300" style="width: {{ $group->progress }}%"></div>
                </div>
            </div>

            <!-- Assessment Details -->
            @if($group->plagiarism_percent !== null || $group->grade !== null || $group->defense_date !== null)
                <div class="mt-4 pt-4 border-t border-border grid grid-cols-3 gap-2 text-[10px] font-semibold text-muted-foreground">
                    @if($group->plagiarism_percent !== null)
                        <div>
                            <p class="text-[8px] text-muted-foreground/80 font-extrabold uppercase mb-0.5">نسبة الاستلال العلمي</p>
                            <p class="font-bold {{ $group->plagiarism_percent > 20 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $group->plagiarism_percent }}%
                            </p>
                        </div>
                    @endif
                    @if($group->defense_date !== null)
                        <div>
                            <p class="text-[8px] text-muted-foreground/80 font-extrabold uppercase mb-0.5">المناقشة</p>
                            <p class="text-foreground font-bold truncate" title="{{ \Carbon\Carbon::parse($group->defense_date)->format('Y-m-d H:i') }}">
                                {{ \Carbon\Carbon::parse($group->defense_date)->format('Y-m-d H:i') }}
                            </p>
                        </div>
                    @endif
                    @if($group->grade !== null)
                        <div>
                            <p class="text-[8px] text-muted-foreground/80 font-extrabold uppercase mb-0.5">النتيجة</p>
                            <p class="font-extrabold capitalize {{ $group->grade === 'passed' ? 'text-emerald-600' : ($group->grade === 'passed_revisions' ? 'text-amber-600' : 'text-rose-600') }}">
                                {{ $group->grade === 'passed' ? 'ناجح' : ($group->grade === 'passed_revisions' ? 'ناجح مع التعديلات' : 'راسب') }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Milestones Lifecycle Policy Card -->
        <div class="bg-card p-6 border border-border rounded-2xl shadow-sm flex flex-col justify-between transition-colors duration-200">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-4">مراحل ومحطات المشروع</h3>
            <div class="relative ps-6 space-y-4">
                <!-- Line -->
                <div class="absolute start-[7px] top-1 bottom-1 w-0.5 bg-border"></div>

                <!-- Milestone 1 -->
                @php
                    $isProposalDone = $group->stage !== 'proposal';
                @endphp
                <div class="relative flex items-center gap-3">
                    <span class="absolute -start-[23px] flex items-center justify-center h-4 w-4 rounded-full border-2 {{ $isProposalDone ? 'bg-emerald-500 border-emerald-500' : 'bg-card border-border' }}">
                        @if($isProposalDone)
                            <svg class="h-2 w-2 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                    </span>
                    <div>
                        <h4 class="text-xs font-bold {{ $group->stage == 'proposal' ? 'text-primary' : 'text-foreground' }}">{{ __('messages.stage_proposal') }}</h4>
                        <p class="text-[9px] text-muted-foreground font-medium">اعتماد المقترح وتسجيل عنوان البحث</p>
                    </div>
                </div>

                <!-- Milestone 2 -->
                @php
                    $isChaptersDone = in_array($group->stage, ['draft', 'defense']);
                @endphp
                <div class="relative flex items-center gap-3">
                    <span class="absolute -start-[23px] flex items-center justify-center h-4 w-4 rounded-full border-2 {{ $isChaptersDone ? 'bg-emerald-500 border-emerald-500' : 'bg-card border-border' }}">
                        @if($isChaptersDone)
                            <svg class="h-2 w-2 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                    </span>
                    <div>
                        <h4 class="text-xs font-bold {{ $group->stage == 'chapters' ? 'text-primary' : 'text-foreground' }}">{{ __('messages.stage_chapters') }}</h4>
                        <p class="text-[9px] text-muted-foreground font-medium">كتابة الفصول والمراجعة المهيكلة</p>
                    </div>
                </div>

                <!-- Milestone 3 -->
                @php
                    $isDraftDone = $group->stage == 'defense';
                @endphp
                <div class="relative flex items-center gap-3">
                    <span class="absolute -start-[23px] flex items-center justify-center h-4 w-4 rounded-full border-2 {{ $isDraftDone ? 'bg-emerald-500 border-emerald-500' : 'bg-card border-border' }}">
                        @if($isDraftDone)
                            <svg class="h-2 w-2 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                    </span>
                    <div>
                        <h4 class="text-xs font-bold {{ $group->stage == 'draft' ? 'text-primary' : 'text-foreground' }}">{{ __('messages.stage_draft') }}</h4>
                        <p class="text-[9px] text-muted-foreground font-medium">رفع المسودة النهائية وفحص الاستلال العلمي</p>
                    </div>
                </div>

                <!-- Milestone 4 -->
                @php
                    $isDefenseDone = $group->status == 'completed';
                @endphp
                <div class="relative flex items-center gap-3">
                    <span class="absolute -start-[23px] flex items-center justify-center h-4 w-4 rounded-full border-2 {{ $isDefenseDone ? 'bg-emerald-500 border-emerald-500' : 'bg-card border-border' }}">
                        @if($isDefenseDone)
                            <svg class="h-2 w-2 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                    </span>
                    <div>
                        <h4 class="text-xs font-bold {{ $group->stage == 'defense' ? 'text-primary' : 'text-foreground' }}">{{ __('messages.stage_defense') }}</h4>
                        <p class="text-[9px] text-muted-foreground font-medium">جدولة لجنة المناقشة والتقييم النهائي</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Files and Discussion Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Project Files list -->
        <div class="bg-card p-6 border border-border rounded-2xl shadow-sm flex flex-col transition-colors duration-200">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-3">مسودات وملفات المشروع</h3>
            <div class="flex-grow space-y-3 overflow-y-auto mb-4 max-h-[220px]">
                @forelse($files as $file)
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
                    <div class="p-3 bg-muted/30 dark:bg-card/45 border border-border rounded-xl flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0 flex-1 flex items-center gap-2.5">
                            <span class="p-2 rounded-lg shrink-0 {{ $iconColor }}">
                                @if($ext === 'pdf')
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                @elseif(in_array($ext, ['zip', 'rar']))
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                @elseif(in_array($ext, ['xlsx', 'xls', 'csv']))
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @else
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @endif
                            </span>
                            <div class="min-w-0">
                                <p class="font-bold text-foreground truncate" title="{{ $file->filename }}">{{ $file->filename }}</p>
                                <p class="text-[9px] text-muted-foreground font-medium">رُفع بواسطة {{ app()->getLocale() == 'ar' ? $file->user->name_ar : $file->user->name_en }} ({{ $file->file_size }})</p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="p-1.5 bg-card border border-border text-primary rounded-lg hover:bg-muted transition-colors shadow-sm cursor-pointer" title="تنزيل">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-muted-foreground font-semibold text-center py-6">لا توجد مسودات مرفوعة بعد.</p>
                @endforelse
            </div>

            <!-- Upload file form -->
            <form action="{{ route('projects.upload', $group->id) }}" method="POST" enctype="multipart/form-data" class="pt-4 border-t border-border flex items-center gap-2" onsubmit="return confirm('هل أنت متأكد من رفع هذا الملف؟')">
                @csrf
                <input type="file" name="file" required class="text-xs font-semibold text-muted-foreground w-full file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                <button type="submit" class="px-4 py-1.5 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl shadow-sm transition-all shrink-0 cursor-pointer">
                    رفع الملف
                </button>
            </form>
        </div>

        <!-- Discussion Thread -->
        <div class="bg-card p-6 border border-border rounded-2xl shadow-sm lg:col-span-2 flex flex-col transition-colors duration-200">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-3">مناقشات المجموعة</h3>
            <div class="flex-grow space-y-3 overflow-y-auto mb-4 p-2 bg-muted/20 rounded-xl max-h-[200px]">
                @forelse($comments as $comment)
                    @php
                        $userExists = !is_null($comment->user);
                    @endphp
                    <div class="p-3 bg-card border border-border rounded-xl shadow-sm">
                        <div class="flex justify-between items-center gap-2 mb-1">
                            <span class="text-xs font-extrabold text-primary">
                                @if($userExists)
                                    {{ app()->getLocale() == 'ar' ? $comment->user->name_ar : $comment->user->name_en }}
                                @else
                                    {{ $comment->author_name ?? __('messages.deleted_user') }} ({{ __('messages.deleted_user') }})
                                @endif
                            </span>
                            <span class="text-[9px] text-muted-foreground font-semibold">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-foreground font-medium leading-relaxed">
                            {{ $comment->content }}
                        </p>
                    </div>
                @empty
                    <p class="text-xs text-muted-foreground font-semibold text-center py-8">ابدأ نقاشاً مع مشرفك الأكاديمي للبحث!</p>
                @endforelse
            </div>

            <form action="{{ route('projects.comment.store', $group->id) }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="content" placeholder="اكتب ملاحظة أو استفسار هنا..." required class="flex-grow text-xs font-semibold px-4 py-2 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                <button type="submit" class="px-5 py-2 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-all shadow-sm cursor-pointer">
                    {{ __('messages.send') }}
                </button>
            </form>
        </div>
    </div>
@else
    <div class="bg-card border border-border rounded-2xl p-12 text-center max-w-2xl mx-auto shadow-sm transition-colors duration-200">
        <!-- Graduation Cap -->
        <span class="p-4 bg-primary/10 text-primary rounded-full inline-flex items-center justify-center mb-4">
            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </span>
        <h3 class="font-extrabold text-foreground text-base mb-2">مرحباً بك في بوابة أبحاث التخرج بجامعة نكسوس للعلوم والتكنولوجيا</h3>
        <p class="text-xs text-muted-foreground font-semibold leading-relaxed max-w-md mx-auto mb-6">
            أنت غير مسجل حالياً في أي مجموعة بحث تخرج. يرجى مراجعة إدارة القسم لتسجيلك في مجموعة البحث وتعيين مشرف أكاديمي لك.
        </p>
        <a href="{{ route('profile.edit') }}" class="px-5 py-2.5 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
            تحديث بيانات الملف الشخصي
        </a>
    </div>
@endif
