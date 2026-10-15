@extends('layouts.dashboard')

@section('title', __('messages.incentives_title'))

@section('dashboard_content')
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-black text-foreground tracking-tight mb-1">
            {{ __('messages.incentives_title') }}
        </h2>
        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">
            {{ __('messages.app_name') }}
        </p>
    </div>
    
    @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer']))
        <button onclick="openModal('createIncentiveModal')" class="px-5 py-2.5 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl shadow-md transition-all inline-flex items-center gap-1.5 cursor-pointer">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>{{ __('messages.add') }}</span>
        </button>
    @endif
</div>


<!-- Grid Lists -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($incentives as $item)
        <div class="bg-card border border-border rounded-2xl shadow-sm p-6 flex flex-col justify-between premium-hover">
            <div>
                <!-- Icon and Type Header -->
                <div class="flex items-center gap-3 mb-4">
                    @if($item->kind === 'discount')
                        <span class="p-2.5 bg-rose-50 dark:bg-rose-950/20 text-rose-600 rounded-xl">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <span class="text-[10px] font-extrabold text-rose-600 uppercase">{{ __('messages.discounts') }}</span>
                    @elseif($item->kind === 'course')
                        <span class="p-2.5 bg-blue-50 dark:bg-blue-950/20 text-primary rounded-xl">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </span>
                        <span class="text-[10px] font-extrabold text-primary uppercase">{{ __('messages.courses') }}</span>
                    @elseif($item->kind === 'job')
                        <span class="p-2.5 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 rounded-xl">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <span class="text-[10px] font-extrabold text-emerald-600 uppercase">{{ __('messages.jobs') }}</span>
                    @endif
                </div>

                <h3 class="font-extrabold text-foreground text-sm leading-snug mb-2">
                    {{ app()->getLocale() == 'ar' ? $item->title_ar : $item->title_en }}
                </h3>
                <p class="text-xs text-muted-foreground font-semibold leading-relaxed mb-6">
                    {{ app()->getLocale() == 'ar' ? $item->detail_ar : $item->detail_en }}
                </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2 mt-4 flex-grow-0">
                @if(auth()->user()->role === 'alumnus')
                    @if(auth()->user()->incentives->contains($item->id))
                        <button disabled class="flex-grow py-2.5 bg-muted border border-border text-muted-foreground rounded-xl text-xs font-bold text-center cursor-not-allowed">
                            @php
                                $pivot = auth()->user()->incentives->find($item->id)->pivot;
                            @endphp
                            {{ $pivot->status == 'enrolled' ? 'مسجل' : ($pivot->status == 'redeemed' ? 'مسترد' : 'متقدم') }}
                        </button>
                    @else
                        <form action="{{ route('incentives.claim', $item->id) }}" method="POST" class="flex-grow" onsubmit="return confirm('هل أنت متأكد من التقديم على هذه الفرصة/المحفز؟')">
                            @csrf
                            <button type="submit" class="w-full py-2.5 border border-primary/20 rounded-xl text-xs font-bold text-primary bg-primary/5 hover:bg-primary hover:text-white transition-all text-center cursor-pointer">
                                @if($item->kind === 'course')
                                    {{ __('messages.enroll') }}
                                @elseif($item->kind === 'job')
                                    {{ __('messages.apply') }}
                                @else
                                    استرداد
                                @endif
                            </button>
                        </form>
                    @endif
                @else
                    <button disabled class="flex-grow py-2.5 bg-muted border border-border text-muted-foreground/60 rounded-xl text-xs font-bold text-center cursor-not-allowed">
                        للخريجين فقط
                    </button>
                @endif
                @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer']))
                    @php
                        $applicantsData = $item->users->map(function($u) {
                            return [
                                'id' => $u->id,
                                'name_ar' => $u->name_ar,
                                'name_en' => $u->name_en,
                                'academic_id' => $u->academic_id ?? 'غير متوفر',
                                'email' => $u->email,
                                'phone' => $u->phone ?? '',
                                'department' => $u->department ?? 'غير محدد',
                                'graduation_year' => $u->alumniProfile->graduation_year ?? 'غير محدد',
                                'linkedin_url' => $u->linkedin_url ?? null,
                                'status' => $u->pivot->status,
                                'applied_at' => $u->pivot->created_at ? $u->pivot->created_at->format('Y-m-d H:i') : '',
                            ];
                        });
                    @endphp
                    <button type="button"
                        data-title="{{ app()->getLocale() == 'ar' ? $item->title_ar : $item->title_en }}"
                        data-applicants='@json($applicantsData)'
                        onclick="openApplicantsModalFromBtn(this)"
                        class="px-3 py-2 border border-primary/20 bg-primary/5 hover:bg-primary hover:text-white text-primary rounded-xl transition-all text-xs font-bold flex items-center gap-1.5 cursor-pointer shadow-sm">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>المتقدمون ({{ $item->users->count() }})</span>
                    </button>

                    <button onclick="openEditIncentiveModal(this)"
                        data-id="{{ $item->id }}"
                        data-kind="{{ $item->kind }}"
                        data-title-ar="{{ $item->title_ar }}"
                        data-title-en="{{ $item->title_en }}"
                        data-detail-ar="{{ $item->detail_ar }}"
                        data-detail-en="{{ $item->detail_en }}"
                        class="p-2.5 border border-border hover:border-primary/20 text-muted-foreground hover:text-primary hover:bg-primary/5 rounded-xl transition-colors cursor-pointer" title="{{ __('messages.edit') }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </button>
                    <form action="{{ route('incentives.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المحفز؟')">
                        @csrf
                        <button type="submit" class="p-2.5 border border-border hover:border-rose-200 text-muted-foreground hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/25 rounded-xl transition-colors cursor-pointer" title="{{ __('messages.delete') }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-card border border-border rounded-2xl p-12 text-center col-span-full">
            <svg class="h-12 w-12 text-muted-foreground/30 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="font-extrabold text-foreground text-sm mb-1">{{ __('messages.no_data') }}</h3>
            <p class="text-xs text-muted-foreground font-medium">{{ __('messages.no_data') }}</p>
        </div>
    @endforelse
</div>

@if(in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer']))
<!-- Create Incentive Modal -->
<div id="createIncentiveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md hidden no-print">
    <div class="bg-card rounded-3xl border border-border shadow-2xl max-w-md w-full mx-4 overflow-hidden">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/20">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide">
                {{ __('messages.add') }}
            </h3>
            <button onclick="closeModal('createIncentiveModal')" class="text-muted-foreground hover:text-foreground transition-colors cursor-pointer">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Form -->
        <form action="{{ route('incentives.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.type') }}</label>
                <select name="kind" required class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary">
                    <option value="course" class="bg-card">{{ __('messages.courses') }}</option>
                    <option value="job" class="bg-card">{{ __('messages.jobs') }}</option>
                    <option value="discount" class="bg-card">{{ __('messages.discounts') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.title') }} (AR)</label>
                <input type="text" name="title_ar" required class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.title') }} (EN)</label>
                <input type="text" name="title_en" required class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.requirements') }} (AR)</label>
                <textarea name="detail_ar" required rows="3" class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary"></textarea>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.requirements') }} (EN)</label>
                <textarea name="detail_en" required rows="3" class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary"></textarea>
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-border flex justify-end gap-2">
                <button type="button" onclick="closeModal('createIncentiveModal')" class="px-4 py-2 border border-border text-foreground bg-card hover:bg-muted text-xs font-bold rounded-xl transition-colors cursor-pointer">
                    إلغاء
                </button>
                <button type="submit" class="px-5 py-2 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">
                    {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Incentive Modal -->
<div id="editIncentiveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md hidden no-print">
    <div class="bg-card rounded-3xl border border-border shadow-2xl max-w-md w-full mx-4 overflow-hidden">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/20">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide">
                {{ __('messages.edit') }}
            </h3>
            <button onclick="closeModal('editIncentiveModal')" class="text-muted-foreground hover:text-foreground transition-colors cursor-pointer">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="editIncentiveForm" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.type') }}</label>
                <select name="kind" id="edit_kind" required class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary">
                    <option value="course" class="bg-card">{{ __('messages.courses') }}</option>
                    <option value="job" class="bg-card">{{ __('messages.jobs') }}</option>
                    <option value="discount" class="bg-card">{{ __('messages.discounts') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.title') }} (AR)</label>
                <input type="text" name="title_ar" id="edit_title_ar" required class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.title') }} (EN)</label>
                <input type="text" name="title_en" id="edit_title_en" required class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.requirements') }} (AR)</label>
                <textarea name="detail_ar" id="edit_detail_ar" required rows="3" class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary"></textarea>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.requirements') }} (EN)</label>
                <textarea name="detail_en" id="edit_detail_en" required rows="3" class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary"></textarea>
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-border flex justify-end gap-2">
                <button type="button" onclick="closeModal('editIncentiveModal')" class="px-4 py-2 border border-border text-foreground bg-card hover:bg-muted text-xs font-bold rounded-xl transition-colors cursor-pointer">
                    إلغاء
                </button>
                <button type="submit" class="px-5 py-2 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">
                    {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Floating Premium Applicants Modal -->
<div id="applicantsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md hidden no-print p-4">
    <div class="bg-card rounded-3xl border border-border shadow-2xl max-w-3xl w-full overflow-hidden flex flex-col max-h-[85vh]">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/20">
            <div>
                <h3 class="font-extrabold text-foreground text-sm tracking-wide">
                    قائمة المتقدمين والمسجلين
                </h3>
                <p class="text-xs text-muted-foreground font-semibold">
                    الفرصة: <span id="applicants_incentive_title" class="text-primary font-bold"></span>
                </p>
            </div>
            <button onclick="closeModal('applicantsModal')" class="p-1.5 text-muted-foreground hover:text-foreground hover:bg-muted rounded-xl transition-colors cursor-pointer">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Search Bar inside Modal -->
        <div class="px-6 py-3 border-b border-border bg-muted/10">
            <input type="text" id="applicants_search_input" onkeyup="filterApplicantsInModal()" placeholder="ابحث باسم المتقدم، البريد، الهاتف، أو الرقم الأكاديمي..." class="w-full text-xs font-semibold px-4 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
        </div>

        <!-- Modal Body (Applicants List) -->
        <div class="p-6 overflow-y-auto flex-grow">
            <div id="applicants_list_container" class="space-y-3">
                <!-- Dynamically populated -->
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-border bg-muted/20 flex justify-between items-center">
            <span id="applicants_count_footer" class="text-xs font-bold text-muted-foreground">إجمالي المتقدمين: 0</span>
            <button type="button" onclick="closeModal('applicantsModal')" class="px-5 py-2.5 bg-primary hover:bg-primary/90 text-white text-xs font-bold rounded-xl transition-all shadow-sm cursor-pointer">
                إغلاق
            </button>
        </div>
    </div>
</div>
@endif

<script>
    let currentApplicantsData = [];

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openEditIncentiveModal(button) {
        const id = button.getAttribute('data-id');
        const kind = button.getAttribute('data-kind');
        const titleAr = button.getAttribute('data-title-ar');
        const titleEn = button.getAttribute('data-title-en');
        const detailAr = button.getAttribute('data-detail-ar');
        const detailEn = button.getAttribute('data-detail-en');

        document.getElementById('edit_kind').value = kind;
        document.getElementById('edit_title_ar').value = titleAr;
        document.getElementById('edit_title_en').value = titleEn;
        document.getElementById('edit_detail_ar').value = detailAr;
        document.getElementById('edit_detail_en').value = detailEn;

        const form = document.getElementById('editIncentiveForm');
        form.action = `/dashboard/incentives/${id}/edit`;

        openModal('editIncentiveModal');
    }

    function openApplicantsModalFromBtn(btn) {
        const title = btn.getAttribute('data-title');
        const rawData = btn.getAttribute('data-applicants');
        try {
            currentApplicantsData = JSON.parse(rawData || '[]');
        } catch (e) {
            currentApplicantsData = [];
        }

        document.getElementById('applicants_incentive_title').textContent = title;
        document.getElementById('applicants_search_input').value = '';
        renderApplicantsList(currentApplicantsData);
        openModal('applicantsModal');
    }

    function filterApplicantsInModal() {
        const query = document.getElementById('applicants_search_input').value.toLowerCase().trim();
        if (!query) {
            renderApplicantsList(currentApplicantsData);
            return;
        }

        const filtered = currentApplicantsData.filter(app => {
            return (app.name_ar && app.name_ar.toLowerCase().includes(query)) ||
                   (app.name_en && app.name_en.toLowerCase().includes(query)) ||
                   (app.email && app.email.toLowerCase().includes(query)) ||
                   (app.phone && app.phone.toLowerCase().includes(query)) ||
                   (app.academic_id && app.academic_id.toLowerCase().includes(query)) ||
                   (app.department && app.department.toLowerCase().includes(query));
        });

        renderApplicantsList(filtered);
    }

    function renderApplicantsList(applicants) {
        const container = document.getElementById('applicants_list_container');
        const footerCount = document.getElementById('applicants_count_footer');
        container.innerHTML = '';
        footerCount.textContent = `إجمالي المتقدمين: ${applicants.length}`;

        if (applicants.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12 bg-muted/10 border border-dashed border-border rounded-2xl">
                    <svg class="h-12 w-12 text-muted-foreground/30 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-xs text-muted-foreground font-bold">لا يوجد متقدمون مطبقون لهذه الشروط حالياً.</p>
                </div>
            `;
            return;
        }

        applicants.forEach(app => {
            const statusMap = {
                'applied': 'متقدم للفرصة',
                'enrolled': 'مسجل في البرنامج',
                'redeemed': 'مسترد للخصم'
            };
            const statusText = statusMap[app.status] || app.status;

            const phoneClean = app.phone ? app.phone.replace(/[^0-9]/g, '') : '';
            const whatsappUrl = phoneClean ? `https://wa.me/${phoneClean}` : null;

            const linkedinHtml = app.linkedin_url ? `
                <a href="${app.linkedin_url}" target="_blank" class="px-2.5 py-1.5 bg-[#0077b5] hover:bg-[#006297] text-white rounded-xl text-[10px] font-bold inline-flex items-center gap-1 transition-all shadow-sm">
                    <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24">
                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.779-1.75-1.75s.784-1.75 1.75-1.75 1.75.779 1.75 1.75-.784 1.75-1.75 1.75zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                    </svg>
                    <span>LinkedIn</span>
                </a>
            ` : '';

            const whatsappHtml = whatsappUrl ? `
                <a href="${whatsappUrl}" target="_blank" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[10px] font-bold inline-flex items-center gap-1 transition-all shadow-sm">
                    <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                    </svg>
                    <span>واتساب</span>
                </a>
            ` : '';

            const mailtoHtml = app.email ? `
                <a href="mailto:${app.email}" class="px-2.5 py-1.5 bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200 rounded-xl text-[10px] font-bold inline-flex items-center gap-1 hover:bg-blue-100 transition-all">
                    <span>مراسلة</span>
                </a>
            ` : '';

            const div = document.createElement('div');
            div.className = 'p-4 border border-border bg-card hover:bg-muted/30 rounded-2xl flex flex-col sm:flex-row justify-between sm:items-center gap-4 transition-all shadow-xs';
            div.innerHTML = `
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h4 class="font-extrabold text-foreground text-xs">${app.name_ar || app.name_en}</h4>
                        <span class="text-[9px] font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-full">${app.department}</span>
                        <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">${statusText}</span>
                    </div>
                    <p class="text-[11px] text-muted-foreground font-semibold flex items-center gap-3 flex-wrap">
                        <span>الرقم الأكاديمي: <strong class="text-foreground">${app.academic_id}</strong></span>
                        <span>دفعة: <strong class="text-foreground">${app.graduation_year}</strong></span>
                        ${app.applied_at ? `<span>تاريخ التقديم: <strong class="text-foreground">${app.applied_at}</strong></span>` : ''}
                    </p>
                    <p class="text-[10px] text-muted-foreground font-medium">
                        البريد: <strong class="text-foreground">${app.email}</strong> ${app.phone ? `| الهاتف: <strong class="text-foreground">${app.phone}</strong>` : ''}
                    </p>
                </div>
                <div class="flex items-center gap-1.5 self-start sm:self-center shrink-0">
                    ${mailtoHtml}
                    ${whatsappHtml}
                    ${linkedinHtml}
                </div>
            `;
            container.appendChild(div);
        });
    }
</script>
@endsection
