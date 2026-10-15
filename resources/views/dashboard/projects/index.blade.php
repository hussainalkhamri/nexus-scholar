@extends('layouts.dashboard')

@section('title', __('messages.nav_projects'))

@section('dashboard_content')
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-black text-foreground tracking-tight mb-1">
            {{ __('messages.nav_projects') }}
        </h2>
        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">
            {{ __('messages.app_name') }}
        </p>
    </div>
    
    @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'projects_officer']))
        <button onclick="openModal('createProjectModal')" class="px-5 py-2.5 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl shadow-md transition-all inline-flex items-center gap-1.5 cursor-pointer">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>{{ __('messages.add') }}</span>
        </button>
    @endif
</div>

<!-- Filters Bar -->
<div class="bg-card border border-border rounded-2xl p-4 shadow-sm mb-6 no-print">
    <form action="{{ route('projects.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
        <!-- Search -->
        <div class="md:col-span-2">
            <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.search') }}</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search') }}" class="w-full text-xs font-semibold px-3.5 py-2 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
        </div>

        <!-- Department -->
        <div>
            <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.filter_dept') }}</label>
            <select name="dept" class="w-full text-xs font-semibold px-3.5 py-2 bg-card text-foreground border border-border rounded-xl outline-none">
                <option value="all" class="bg-card">{{ __('messages.all') }}</option>
                @foreach($departments as $d)
                    <option value="{{ $d }}" {{ request('dept') == $d ? 'selected' : '' }} class="bg-card">{{ $d }}</option>
                @endforeach
            </select>
        </div>

        <!-- Status -->
        <div>
            <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.filter_status') }}</label>
            <select name="status" class="w-full text-xs font-semibold px-3.5 py-2 bg-card text-foreground border border-border rounded-xl outline-none">
                <option value="all" class="bg-card">{{ __('messages.all') }}</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }} class="bg-card">{{ __('messages.status_completed') }}</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }} class="bg-card">{{ __('messages.status_processing') }}</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }} class="bg-card">{{ __('messages.status_pending') }}</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }} class="bg-card">{{ __('messages.status_overdue') }}</option>
            </select>
        </div>

        <!-- Actions -->
        <div class="flex gap-2">
            <button type="submit" class="flex-grow px-4 py-2 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-all shadow-sm cursor-pointer">
                {{ __('messages.generate') }}
            </button>
            <a href="{{ route('projects.index') }}" class="px-4 py-2 border border-border text-muted-foreground hover:bg-muted bg-card text-xs font-bold rounded-xl transition-all flex items-center justify-center cursor-pointer">
                {{ __('messages.reset_filters') }}
            </a>
        </div>
    </form>
</div>

<!-- Project Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($groups as $group)
        <div class="bg-card border border-border rounded-2xl shadow-sm flex flex-col premium-hover">
            <!-- Card Header -->
            <div class="p-6 border-b border-border">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="text-[10px] font-extrabold text-muted-foreground uppercase">
                        GP-{{ 1000 + $group->id }}
                    </span>
                    
                    <!-- Status Badge -->
                    @if($group->status == 'completed')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-450 border border-emerald-100 dark:border-emerald-900/30">
                            {{ __('messages.status_completed') }}
                        </span>
                    @elseif($group->status == 'processing')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-450 border border-amber-100 dark:border-amber-900/30">
                            {{ __('messages.status_processing') }}
                        </span>
                    @elseif($group->status == 'pending')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-450 border border-blue-100 dark:border-blue-900/30">
                            {{ __('messages.status_pending') }}
                        </span>
                    @elseif($group->status == 'overdue')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-450 border border-rose-100 dark:border-rose-900/30">
                            {{ __('messages.status_overdue') }}
                        </span>
                    @endif
                </div>

                <a href="{{ route('projects.show', $group->id) }}" class="block">
                    <h3 class="font-extrabold text-foreground text-sm leading-snug hover:text-primary transition-colors line-clamp-2 min-h-[40px] mb-2">
                        {{ app()->getLocale() == 'ar' ? $group->title_ar : $group->title_en }}
                    </h3>
                </a>

                <div class="flex items-center gap-2 text-[10px] font-bold text-muted-foreground uppercase">
                    <span>{{ $group->department }}</span>
                    <span>•</span>
                    <span>{{ $group->semester }}</span>
                </div>
            </div>

            <!-- Card Body (Progress & Supervisor) -->
            <div class="p-6 flex-1 flex flex-col justify-between">
                <div class="mb-4">
                    <div class="flex justify-between text-xs font-semibold text-muted-foreground mb-1.5">
                        <span>{{ __('messages.stage_' . $group->stage) }}</span>
                        <span>{{ $group->progress }}%</span>
                    </div>
                    <!-- Custom progress bar -->
                    <div class="w-full bg-muted rounded-full h-1.5 overflow-hidden">
                        <div class="h-full rounded-full {{ $group->status == 'completed' ? 'bg-emerald-500' : ($group->status == 'overdue' ? 'bg-rose-500' : 'bg-primary') }}" style="width: {{ $group->progress }}%"></div>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] text-muted-foreground font-extrabold uppercase mb-1">{{ __('messages.supervisor') }}</p>
                    <p class="text-xs font-semibold text-foreground/90">{{ $group->supervisor }}</p>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="px-6 py-4 bg-muted/30 dark:bg-muted/10 rounded-b-2xl border-t border-border flex items-center justify-between gap-4">
                <div class="flex items-center gap-1.5">
                    <span class="text-[10px] text-muted-foreground font-semibold me-2">
                        {{ __('messages.days') }}: {{ $group->days_in_stage }}
                    </span>
                    
                    @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'projects_officer']))
                        <button onclick="openEditModal(this)" 
                            data-id="{{ $group->id }}" 
                            data-title-ar="{{ $group->title_ar }}" 
                            data-title-en="{{ $group->title_en }}" 
                            data-dept="{{ $group->department }}" 
                            data-semester="{{ $group->semester }}" 
                            data-supervisor-id="{{ $group->supervisor_id }}"
                            class="p-1.5 border border-border hover:border-primary/20 text-muted-foreground hover:text-primary hover:bg-primary/5 rounded-lg transition-colors cursor-pointer" title="{{ __('messages.edit') }}">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                        <form action="{{ route('projects.destroy', $group->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المشروع؟')">
                            @csrf
                            <button type="submit" class="p-1.5 border border-border hover:border-rose-200 dark:hover:border-rose-900 text-muted-foreground hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/25 rounded-lg transition-colors cursor-pointer" title="{{ __('messages.delete') }}">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
                
                <a href="{{ route('projects.show', $group->id) }}" class="inline-flex items-center justify-center px-4 py-2 border border-border rounded-xl text-xs font-bold text-foreground bg-card hover:bg-muted transition-all gap-1 shadow-sm cursor-pointer">
                    <span>{{ __('messages.view') }}</span>
                    <svg class="h-3.5 w-3.5 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
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

@if(in_array(auth()->user()->role, ['super_admin', 'admin', 'projects_officer']))
<!-- Create Project Modal -->
<div id="createProjectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden no-print">
    <div class="bg-card rounded-2xl border border-border shadow-xl max-w-md w-full mx-4 overflow-hidden">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-border flex items-center justify-between">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide">
                {{ __('messages.add') }}
            </h3>
            <button onclick="closeModal('createProjectModal')" class="text-muted-foreground hover:text-foreground transition-colors cursor-pointer">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Form -->
        <form action="{{ route('projects.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.title') }} (AR)</label>
                <input type="text" name="title_ar" required class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.title') }} (EN)</label>
                <input type="text" name="title_en" required class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.filter_dept') }}</label>
                    <select name="department" required class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary">
                        @foreach($departments as $d)
                            <option value="{{ $d }}" class="bg-card">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.filter_semester') }}</label>
                    <input type="text" name="semester" required placeholder="e.g. Spring 2026" class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.supervisor') }}</label>
                <select name="supervisor_id" required class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary">
                    @foreach($supervisors as $s)
                        <option value="{{ $s->id }}" class="bg-card">{{ app()->getLocale() == 'ar' ? $s->name_ar : $s->name_en }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-border flex justify-end gap-2">
                <button type="button" onclick="closeModal('createProjectModal')" class="px-4 py-2 border border-border text-foreground bg-card hover:bg-muted text-xs font-bold rounded-xl transition-colors cursor-pointer">
                    إلغاء
                </button>
                <button type="submit" class="px-5 py-2 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">
                    {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Project Modal -->
<div id="editProjectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden no-print">
    <div class="bg-card rounded-2xl border border-border shadow-xl max-w-md w-full mx-4 overflow-hidden">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-border flex items-center justify-between">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide">
                {{ __('messages.edit') }}
            </h3>
            <button onclick="closeModal('editProjectModal')" class="text-muted-foreground hover:text-foreground transition-colors cursor-pointer">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="editProjectForm" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.title') }} (AR)</label>
                <input type="text" name="title_ar" id="edit_title_ar" required class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.title') }} (EN)</label>
                <input type="text" name="title_en" id="edit_title_en" required class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.filter_dept') }}</label>
                    <select name="department" id="edit_department" required class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary">
                        @foreach($departments as $d)
                            <option value="{{ $d }}" class="bg-card">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.filter_semester') }}</label>
                    <input type="text" name="semester" id="edit_semester" required class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.supervisor') }}</label>
                <select name="supervisor_id" id="edit_supervisor_id" required class="w-full text-xs font-semibold px-3 py-2 bg-card text-foreground border border-border rounded-xl outline-none focus:border-primary">
                    @foreach($supervisors as $s)
                        <option value="{{ $s->id }}" class="bg-card">{{ app()->getLocale() == 'ar' ? $s->name_ar : $s->name_en }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-border flex justify-end gap-2">
                <button type="button" onclick="closeModal('editProjectModal')" class="px-4 py-2 border border-border text-foreground bg-card hover:bg-muted text-xs font-bold rounded-xl transition-colors cursor-pointer">
                    إلغاء
                </button>
                <button type="submit" class="px-5 py-2 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">
                    {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
    function openEditModal(button) {
        const id = button.getAttribute('data-id');
        const titleAr = button.getAttribute('data-title-ar');
        const titleEn = button.getAttribute('data-title-en');
        const dept = button.getAttribute('data-dept');
        const semester = button.getAttribute('data-semester');
        const supervisorId = button.getAttribute('data-supervisor-id');

        document.getElementById('edit_title_ar').value = titleAr;
        document.getElementById('edit_title_en').value = titleEn;
        document.getElementById('edit_department').value = dept;
        document.getElementById('edit_semester').value = semester;
        document.getElementById('edit_supervisor_id').value = supervisorId;

        // Set action route dynamically
        const form = document.getElementById('editProjectForm');
        form.action = `/dashboard/projects/${id}/edit`;

        openModal('editProjectModal');
    }
</script>
@endsection
