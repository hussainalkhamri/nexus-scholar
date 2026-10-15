@extends('layouts.dashboard')

@section('title', __('messages.nav_profile'))

@section('dashboard_content')
<!-- Header -->
<div class="mb-8">
    <h2 class="text-2xl font-black text-foreground tracking-tight mb-1">
        {{ __('messages.nav_profile') }}
    </h2>
    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">
        {{ __('messages.app_name') }}
    </p>
</div>

<!-- Grid Layout: Preview card (left/top) + Edit Forms (right/bottom) -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: LinkedIn-style Profile Preview Card -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden flex flex-col transition-colors duration-200">
            <!-- Header Banner -->
            <div class="h-28 bg-gradient-to-r from-primary to-[#1e3a8a] relative">
                <!-- LinkedIn logo watermark or style -->
                <span class="absolute top-4 end-4 text-white/35 font-black text-xs tracking-widest select-none">PROFESSIONAL</span>
            </div>
            
            <!-- Avatar & Profile Details -->
            <div class="px-6 pb-6 relative flex flex-col items-center -mt-12">
                <div class="h-24 w-24 rounded-full border-4 border-card bg-[#1e3a8a]/10 dark:bg-muted text-primary dark:text-foreground flex items-center justify-center font-black text-2xl shadow-md select-none shrink-0">
                    {{ mb_substr($user->name_ar, 0, 1) }}
                </div>
                
                <h3 class="mt-4 font-black text-foreground text-base text-center leading-tight">
                    {{ $user->name_ar }}
                </h3>
                <p class="text-xs text-muted-foreground text-center font-bold mt-1">
                    {{ $user->name_en }}
                </p>
                @if($user->headline)
                    <p class="text-xs text-foreground/90 text-center font-bold mt-2 px-4 leading-relaxed">
                        {{ $user->headline }}
                    </p>
                @endif
                <p class="text-[10px] text-primary dark:text-gray-300 font-extrabold uppercase bg-primary/10 px-2 py-0.5 rounded-full mt-2 w-fit">
                    {{ __('messages.role_' . $user->role) }}
                </p>
                
                <div class="w-full text-center mt-3 text-[10px] font-semibold text-muted-foreground">
                    <span>{{ $user->department }}</span>
                    @if($user->role === 'alumnus' && isset($user->alumniProfile->graduation_year))
                        <span> • دفعة {{ $user->alumniProfile->graduation_year }}</span>
                    @endif
                </div>

                @if($user->linkedin_url)
                    <a href="{{ $user->linkedin_url }}" target="_blank" class="mt-4 px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-[10px] font-bold shadow-sm transition-all flex items-center gap-1">
                        <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.779-1.75-1.75s.784-1.75 1.75-1.75 1.75.779 1.75 1.75-.784 1.75-1.75 1.75zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                        <span>ملف LinkedIn المهني</span>
                    </a>
                @endif
            </div>

            <!-- Profile Sections -->
            <div class="px-6 pb-6 border-t border-border pt-4 space-y-5">
                
                <!-- Work Experience -->
                @if(in_array($user->role, ['student', 'alumnus']))
                    <div>
                        <h4 class="text-[10px] font-extrabold text-muted-foreground uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>الخبرات المهنية ومحطات العمل</span>
                        </h4>
                        @if(is_array($user->experience) && count($user->experience) > 0)
                            <ul class="space-y-2 text-xs font-semibold text-foreground/90">
                                @foreach($user->experience as $exp)
                                    <li class="flex items-start gap-2 bg-muted/30 p-2 border border-border rounded-xl">
                                        <span class="text-primary font-black mt-0.5">•</span>
                                        <span>{{ $exp }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-[10px] text-muted-foreground italic">لا توجد خبرات عمل مضافة بعد.</p>
                        @endif
                    </div>
                @endif

                <!-- Conferences -->
                @if(in_array($user->role, ['student', 'alumnus']))
                    <div>
                        <h4 class="text-[10px] font-extrabold text-muted-foreground uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z" />
                            </svg>
                            <span>المؤتمرات والندوات المشارك فيها</span>
                        </h4>
                        @if(is_array($user->conferences) && count($user->conferences) > 0)
                            <ul class="space-y-2 text-xs font-semibold text-foreground/90">
                                @foreach($user->conferences as $conf)
                                    <li class="flex items-start gap-2 bg-muted/30 p-2 border border-border rounded-xl">
                                        <span class="text-emerald-600 font-black mt-0.5">•</span>
                                        <span>{{ $conf }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-[10px] text-muted-foreground italic">لم يتم إضافة مؤتمرات بعد.</p>
                        @endif
                    </div>
                @endif

                <!-- Achievements & Projects -->
                @if(in_array($user->role, ['student', 'alumnus']))
                    <div>
                        <h4 class="text-[10px] font-extrabold text-muted-foreground uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L11 3z" />
                            </svg>
                            <span>الإنجازات والمشاريع المنجزة</span>
                        </h4>
                        @if(is_array($user->achievements) && count($user->achievements) > 0)
                            <ul class="space-y-2 text-xs font-semibold text-foreground/90">
                                @foreach($user->achievements as $ach)
                                    <li class="flex items-start gap-2 bg-muted/30 p-2 border border-border rounded-xl">
                                        <span class="text-amber-500 font-black mt-0.5">•</span>
                                        <span>{{ $ach }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-[10px] text-muted-foreground italic">لم يتم إضافة إنجازات بعد.</p>
                        @endif
                    </div>
                @endif

                <!-- Education -->
                @if(in_array($user->role, ['student', 'alumnus']))
                    <div>
                        <h4 class="text-[10px] font-extrabold text-muted-foreground uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                            <span>التعليم الأكاديمي</span>
                        </h4>
                        @if(is_array($user->education) && count($user->education) > 0)
                            <ul class="space-y-2 text-xs font-semibold text-foreground/90">
                                @foreach($user->education as $edu)
                                    <li class="flex items-start gap-2 bg-muted/30 p-2 border border-border rounded-xl">
                                        <span class="text-primary font-black mt-0.5">•</span>
                                        <span>{{ $edu }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-[10px] text-muted-foreground italic">لم يتم إضافة تعليم أكاديمي بعد.</p>
                        @endif
                    </div>
                @endif

                <!-- Certifications -->
                @if(in_array($user->role, ['student', 'alumnus']))
                    <div>
                        <h4 class="text-[10px] font-extrabold text-muted-foreground uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            <span>الشهادات المهنية والدورات</span>
                        </h4>
                        @if(is_array($user->certifications) && count($user->certifications) > 0)
                            <ul class="space-y-2 text-xs font-semibold text-foreground/90">
                                @foreach($user->certifications as $cert)
                                    <li class="flex items-start gap-2 bg-muted/30 p-2 border border-border rounded-xl">
                                        <span class="text-violet-500 font-black mt-0.5">•</span>
                                        <span>{{ $cert }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-[10px] text-muted-foreground italic">لم يتم إضافة شهادات مهنية بعد.</p>
                        @endif
                    </div>
                @endif

                <!-- Languages -->
                @if(in_array($user->role, ['student', 'alumnus']))
                    <div>
                        <h4 class="text-[10px] font-extrabold text-muted-foreground uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h2a2.5 2.5 0 002.5-2.5V10a2 2 0 00-2-2h-1a2 2 0 00-2-2v-1a2 2 0 00-2-2H9.5A1.5 1.5 0 008 3.935z" />
                            </svg>
                            <span>اللغات</span>
                        </h4>
                        @if(is_array($user->languages) && count($user->languages) > 0)
                            <ul class="space-y-2 text-xs font-semibold text-foreground/90">
                                @foreach($user->languages as $lang)
                                    <li class="flex items-start gap-2 bg-muted/30 p-2 border border-border rounded-xl">
                                        <span class="text-indigo-500 font-black mt-0.5">•</span>
                                        <span>{{ $lang }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-[10px] text-muted-foreground italic">لم يتم إضافة لغات بعد.</p>
                        @endif
                    </div>
                @endif

                <!-- Skills (Alumni only) -->
                @if($user->role === 'alumnus' && $user->alumniProfile && count($user->alumniProfile->skills ?? []) > 0)
                    <div>
                        <h4 class="text-[10px] font-extrabold text-muted-foreground uppercase tracking-wider mb-2.5">المهارات والخبرات التقنية</h4>
                        <div class="flex flex-wrap gap-1">
                            @foreach($user->alumniProfile->skills as $skill)
                                <span class="inline-flex px-2 py-0.5 bg-primary/10 text-[9px] font-bold text-primary rounded-lg">
                                    {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right/Bottom Column: Profile Edit Form -->
    <div class="lg:col-span-2">
        <div class="bg-card border border-border rounded-2xl p-6 shadow-sm transition-colors duration-200">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-6 border-b border-border pb-3">إعدادات وبيانات الملف الشخصي</h3>
            
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-6" onsubmit="return confirm('هل أنت متأكد من حفظ التعديلات الجديدة؟')">
                @csrf
                
                <!-- Basic Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.full_name') }} (AR)</label>
                        <input type="text" name="name_ar" value="{{ old('name_ar', $user->name_ar) }}" required class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.full_name') }} (EN)</label>
                        <input type="text" name="name_en" value="{{ old('name_en', $user->name_en) }}" required class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.email') }}</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.phone') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                </div>

                <!-- Alumni Extra Fields -->
                @if($user->role === 'alumnus')
                    <div class="border-t border-border pt-6 space-y-4">
                        <h4 class="font-black text-foreground text-xs uppercase tracking-wider mb-2">إخلاء طرف وبيانات التخرج</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.graduation_year') }}</label>
                                <input type="number" name="graduation_year" value="{{ old('graduation_year', $user->alumniProfile->graduation_year ?? '') }}" required class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.status') }}</label>
                                <select name="employed" required class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary bg-white">
                                    <option value="1" {{ (old('employed', $user->alumniProfile->employed ?? '') == '1') ? 'selected' : '' }}>{{ __('messages.employed') }}</option>
                                    <option value="0" {{ (old('employed', $user->alumniProfile->employed ?? '') == '0') ? 'selected' : '' }}>{{ __('messages.seeking') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.workplace') }}</label>
                                <input type="text" name="workplace" value="{{ old('workplace', $user->alumniProfile->workplace ?? '') }}" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.position') }}</label>
                                <input type="text" name="position" value="{{ old('position', $user->alumniProfile->position ?? '') }}" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.skills') }} (مفصولة بفواصل)</label>
                            <input type="text" name="skills" value="{{ old('skills', is_array($user->alumniProfile->skills ?? null) ? implode(', ', $user->alumniProfile->skills) : '') }}" placeholder="React, Python, SQL..." class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        </div>
                    </div>
                @endif

                <!-- Dynamic LinkedIn-style Lists Section -->
                @if(in_array($user->role, ['student', 'alumnus']))
                    <div class="border-t border-border pt-6 space-y-6">
                        <h4 class="font-black text-foreground text-xs uppercase tracking-wider mb-2">معلومات الملف المهني المهيكل (LinkedIn)</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">رابط ملف لينكد إن (LinkedIn)</label>
                                <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $user->linkedin_url) }}" placeholder="https://linkedin.com/in/username" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">العنوان المهني (Headline)</label>
                                <input type="text" name="headline" value="{{ old('headline', $user->headline) }}" placeholder="طالب صيدلة | مهتم بأبحاث الدواء" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            </div>
                        </div>

                        <!-- 1. Work Experience List Builder -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase">الخبرات المهنية وأماكن العمل السابقة</label>
                                <button type="button" onclick="addExperience()" class="px-3 py-1 bg-primary/10 text-primary hover:bg-primary hover:text-white text-[10px] font-black rounded-xl transition-all cursor-pointer">
                                    + إضافة جهة عمل
                                </button>
                            </div>
                            
                            <div id="experience-container" class="space-y-2.5">
                                @if(is_array($user->experience) && count($user->experience) > 0)
                                    @foreach($user->experience as $exp)
                                        <div class="flex items-center gap-2">
                                            <input type="text" name="experience[]" value="{{ $exp }}" required class="flex-1 text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary">
                                            <button type="button" onclick="this.parentElement.remove()" class="p-2 border border-border text-rose-600 hover:bg-rose-50 hover:border-rose-200 dark:hover:bg-rose-950/20 rounded-xl transition-all cursor-pointer">حذف</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- 2. Conferences List Builder -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase">المؤتمرات العلمية والندوات المشارك فيها</label>
                                <button type="button" onclick="addConference()" class="px-3 py-1 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-400 hover:bg-emerald-600 hover:text-white text-[10px] font-black rounded-xl transition-all cursor-pointer">
                                    + إضافة مؤتمر
                                </button>
                            </div>
                            
                            <div id="conferences-container" class="space-y-2.5">
                                @if(is_array($user->conferences) && count($user->conferences) > 0)
                                    @foreach($user->conferences as $conf)
                                        <div class="flex items-center gap-2">
                                            <input type="text" name="conferences[]" value="{{ $conf }}" required class="flex-1 text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary">
                                            <button type="button" onclick="this.parentElement.remove()" class="p-2 border border-border text-rose-600 hover:bg-rose-50 hover:border-rose-200 dark:hover:bg-rose-950/20 rounded-xl transition-all cursor-pointer">حذف</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- 3. Achievements List Builder -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase">الإنجازات والجوائز والمشاريع</label>
                                <button type="button" onclick="addAchievement()" class="px-3 py-1 bg-amber-50 text-amber-700 dark:bg-amber-950/20 dark:text-amber-400 hover:bg-amber-500 hover:text-white text-[10px] font-black rounded-xl transition-all cursor-pointer">
                                    + إضافة إنجاز
                                </button>
                            </div>
                            
                            <div id="achievements-container" class="space-y-2.5">
                                @if(is_array($user->achievements) && count($user->achievements) > 0)
                                    @foreach($user->achievements as $ach)
                                        <div class="flex items-center gap-2">
                                            <input type="text" name="achievements[]" value="{{ $ach }}" required class="flex-1 text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary">
                                            <button type="button" onclick="this.parentElement.remove()" class="p-2 border border-border text-rose-600 hover:bg-rose-50 hover:border-rose-200 dark:hover:bg-rose-950/20 rounded-xl transition-all cursor-pointer">حذف</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- 4. Education List Builder -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase">التعليم الأكاديمي والشهادات الدراسية</label>
                                <button type="button" onclick="addEducation()" class="px-3 py-1 bg-blue-50 text-blue-700 dark:bg-blue-950/20 dark:text-blue-400 hover:bg-blue-600 hover:text-white text-[10px] font-black rounded-xl transition-all cursor-pointer">
                                    + إضافة تعليم
                                </button>
                            </div>
                            
                            <div id="education-container" class="space-y-2.5">
                                @if(is_array($user->education) && count($user->education) > 0)
                                    @foreach($user->education as $edu)
                                        <div class="flex items-center gap-2">
                                            <input type="text" name="education[]" value="{{ $edu }}" required class="flex-1 text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary">
                                            <button type="button" onclick="this.parentElement.remove()" class="p-2 border border-border text-rose-600 hover:bg-rose-50 hover:border-rose-200 dark:hover:bg-rose-950/20 rounded-xl transition-all cursor-pointer">حذف</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- 5. Certifications List Builder -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase">الشهادات المهنية والدورات والورش</label>
                                <button type="button" onclick="addCertification()" class="px-3 py-1 bg-violet-50 text-violet-700 dark:bg-violet-950/20 dark:text-violet-400 hover:bg-violet-600 hover:text-white text-[10px] font-black rounded-xl transition-all cursor-pointer">
                                    + إضافة شهادة/دورة
                                </button>
                            </div>
                            
                            <div id="certifications-container" class="space-y-2.5">
                                @if(is_array($user->certifications) && count($user->certifications) > 0)
                                    @foreach($user->certifications as $cert)
                                        <div class="flex items-center gap-2">
                                            <input type="text" name="certifications[]" value="{{ $cert }}" required class="flex-1 text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary">
                                            <button type="button" onclick="this.parentElement.remove()" class="p-2 border border-border text-rose-600 hover:bg-rose-50 hover:border-rose-200 dark:hover:bg-rose-950/20 rounded-xl transition-all cursor-pointer">حذف</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- 6. Languages List Builder -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase">اللغات والمهارات اللغوية</label>
                                <button type="button" onclick="addLanguage()" class="px-3 py-1 bg-indigo-50 text-indigo-700 dark:bg-indigo-950/20 dark:text-indigo-400 hover:bg-indigo-600 hover:text-white text-[10px] font-black rounded-xl transition-all cursor-pointer">
                                    + إضافة لغة
                                </button>
                            </div>
                            
                            <div id="languages-container" class="space-y-2.5">
                                @if(is_array($user->languages) && count($user->languages) > 0)
                                    @foreach($user->languages as $lang)
                                        <div class="flex items-center gap-2">
                                            <input type="text" name="languages[]" value="{{ $lang }}" required class="flex-1 text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary">
                                            <button type="button" onclick="this.parentElement.remove()" class="p-2 border border-border text-rose-600 hover:bg-rose-50 hover:border-rose-200 dark:hover:bg-rose-950/20 rounded-xl transition-all cursor-pointer">حذف</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="pt-4 border-t border-border flex justify-end gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-all shadow-md cursor-pointer">
                        {{ __('messages.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Inline Javascript list builders -->
<script>
    function createInputRow(name, placeholderText) {
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2';
        div.innerHTML = `
            <input type="text" name="${name}[]" placeholder="${placeholderText}" required class="flex-1 text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            <button type="button" onclick="this.parentElement.remove()" class="p-2 border border-border text-rose-600 hover:bg-rose-50 hover:border-rose-200 dark:hover:bg-rose-950/20 rounded-xl transition-all cursor-pointer">حذف</button>
        `;
        return div;
    }

    function addExperience() {
        const container = document.getElementById('experience-container');
        container.appendChild(createInputRow('experience', 'مثال: مهندس برمجيات لدى شركة الحلول الذكية، 2025'));
    }

    function addConference() {
        const container = document.getElementById('conferences-container');
        container.appendChild(createInputRow('conferences', 'مثال: المؤتمر السنوي للتكنولوجيا الطبية، جدة، 2026'));
    }

    function addAchievement() {
        const container = document.getElementById('achievements-container');
        container.appendChild(createInputRow('achievements', 'مثال: الحصول على المركز الأول في هكاثون الجامعة، 2026'));
    }

    function addEducation() {
        const container = document.getElementById('education-container');
        container.appendChild(createInputRow('education', 'مثال: بكالوريوس طب وجراحة الفم والأسنان، جامعة نكسوس، 2026'));
    }

    function addCertification() {
        const container = document.getElementById('certifications-container');
        container.appendChild(createInputRow('certifications', 'مثال: شهادة ممارسة سريرية، نقابة أطباء الأسنان، 2026'));
    }

    function addLanguage() {
        const container = document.getElementById('languages-container');
        container.appendChild(createInputRow('languages', 'مثال: العربية (اللغة الأم)، الإنجليزية (متقدم)'));
    }
</script>
@endsection
