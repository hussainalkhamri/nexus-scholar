@extends('layouts.dashboard')

@section('title', __('messages.alumni_directory'))

@section('dashboard_content')
<!-- Header -->
<div class="mb-8 transition-colors duration-200">
    <h2 class="text-2xl font-black text-foreground tracking-tight mb-1">
        {{ __('messages.alumni_directory') }}
    </h2>
    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">
        {{ __('messages.app_name') }}
    </p>
</div>

<!-- Filters Bar -->
<div class="bg-card border border-border rounded-2xl p-4 shadow-sm mb-6 no-print transition-colors duration-200">
    <form action="{{ route('alumni.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
        <!-- Search -->
        <div class="md:col-span-2">
            <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.search') }}</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search') }}" class="w-full text-xs font-semibold px-3.5 py-2 bg-card border border-border text-foreground rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
        </div>

        <!-- Department -->
        <div>
            <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.filter_dept') }}</label>
            <select name="dept" class="w-full text-xs font-semibold px-3.5 py-2 bg-card border border-border text-foreground rounded-xl outline-none">
                <option value="all">{{ __('messages.all') }}</option>
                @foreach($departments as $d)
                    <option value="{{ $d }}" {{ request('dept') == $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
            </select>
        </div>

        <!-- Graduation Year -->
        <div>
            <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.graduation_year') }}</label>
            <select name="year" class="w-full text-xs font-semibold px-3.5 py-2 bg-card border border-border text-foreground rounded-xl outline-none">
                <option value="all">{{ __('messages.all') }}</option>
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>

        <!-- Employment status -->
        <div>
            <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.status') }}</label>
            <select name="status" class="w-full text-xs font-semibold px-3.5 py-2 bg-card border border-border text-foreground rounded-xl outline-none">
                <option value="all">{{ __('messages.all') }}</option>
                <option value="employed" {{ request('status') == 'employed' ? 'selected' : '' }}>{{ __('messages.employed') }}</option>
                <option value="seeking" {{ request('status') == 'seeking' ? 'selected' : '' }}>{{ __('messages.seeking') }}</option>
            </select>
        </div>

        <!-- Actions -->
        <div class="flex gap-2 md:col-span-5 md:justify-end">
            <button type="submit" class="px-6 py-2 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-all shadow-sm cursor-pointer">
                {{ __('messages.generate') }}
            </button>
            <a href="{{ route('alumni.index') }}" class="px-4 py-2 border border-border text-muted-foreground hover:bg-muted text-xs font-bold rounded-xl transition-all flex items-center justify-center cursor-pointer" title="{{ __('messages.reset_filters') }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 15.89M9 11l3-3m0 0l3 3m-3-3v12" />
                </svg>
            </a>
        </div>
    </form>
</div>

<!-- Alumni Directory Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($alumni as $alumnus)
        <div class="bg-card border border-border rounded-2xl shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-all duration-200">
            <!-- Header (Avatar + Name) -->
            <div>
                <div class="flex items-start gap-4 mb-4">
                    <div class="h-12 w-12 rounded-full bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-base shrink-0">
                        {{ mb_substr($alumnus->name_en, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-extrabold text-foreground text-sm leading-snug truncate">
                            {{ app()->getLocale() == 'ar' ? $alumnus->name_ar : $alumnus->name_en }}
                        </h3>
                        
                        <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                            <span class="inline-flex px-2 py-0.5 bg-muted text-muted-foreground rounded text-[9px] font-bold uppercase">
                                {{ $alumnus->department }}
                            </span>
                            <span class="inline-flex px-2 py-0.5 bg-primary/10 text-primary rounded text-[9px] font-bold">
                                {{ __('messages.graduation_year') }}: {{ $alumnus->alumniProfile->graduation_year ?? '—' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Career status -->
                <div class="mb-4 p-3 bg-muted/30 dark:bg-card border border-border rounded-xl min-h-[58px] flex flex-col justify-center">
                    @if($alumnus->alumniProfile && $alumnus->alumniProfile->employed)
                        <p class="text-[10px] text-muted-foreground font-extrabold uppercase mb-0.5">{{ __('messages.workplace') }}</p>
                        <p class="text-xs font-bold text-foreground/90">
                            {{ $alumnus->alumniProfile->position }} - {{ $alumnus->alumniProfile->workplace }}
                        </p>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-accent/10 text-accent border border-accent/20 w-fit">
                            {{ __('messages.seeking') }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Skills array -->
            @if($alumnus->alumniProfile && count($alumnus->alumniProfile->skills ?? []) > 0)
                <div class="border-t border-border pt-4">
                    <p class="text-[9px] text-muted-foreground font-extrabold uppercase mb-2">{{ __('messages.skills') }}</p>
                    <div class="flex flex-wrap gap-1">
                        @foreach($alumnus->alumniProfile->skills as $skill)
                            <span class="inline-flex px-2 py-0.5 bg-primary/10 text-[10px] font-bold text-primary rounded-lg">
                                {{ $skill }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-card border border-border rounded-2xl p-12 text-center col-span-full transition-colors duration-200">
            <svg class="h-12 w-12 text-muted-foreground/30 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="font-extrabold text-foreground text-sm mb-1">{{ __('messages.no_data') }}</h3>
            <p class="text-xs text-muted-foreground font-medium">{{ __('messages.no_data') }}</p>
        </div>
    @endforelse
</div>
@endsection
