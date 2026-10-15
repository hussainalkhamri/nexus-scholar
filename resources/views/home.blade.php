@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col bg-background text-foreground transition-colors duration-200">
    <!-- Header -->
    <header class="bg-[#1a3a6b] dark:bg-[#0f2340] text-white border-b border-[#15325e]/50 py-4 px-6 sm:px-12 flex justify-between items-center shadow-md transition-colors duration-200 no-print">
        <div class="flex items-center gap-3">
            <img src="{{ asset('logo.svg') }}" alt="Logo" class="h-10 w-auto rounded-lg shadow-sm border border-white/20 object-contain bg-slate-900/80 p-1">
            <div>
                <h1 class="font-extrabold text-white text-lg leading-tight">{{ __('messages.app_name') }}</h1>
                <p class="text-xs text-blue-200 font-semibold">{{ __('messages.university') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <!-- Theme Toggle Button -->
            <button type="button" onclick="toggleTheme()" class="inline-flex items-center justify-center p-2 border border-white/20 rounded-lg text-white bg-white/10 hover:bg-white/20 transition-colors shadow-sm cursor-pointer" title="{{ __('messages.theme_dark') }}">
                <svg id="sunIcon" class="h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
                <svg id="moonIcon" class="h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            @auth
                <a href="{{ route('dashboard.index') }}" class="px-5 py-2 rounded-lg bg-[#c8a84e] hover:bg-[#b8962e] dark:bg-[#c8a84e] dark:hover:bg-[#b8962e] text-white text-xs font-bold transition-all shadow-md border border-[#b8962e]/50">
                    {{ __('messages.nav_dashboard') }}
                </a>
            @else
                <a href="{{ route('login') }}" class="px-5 py-2 rounded-lg bg-[#c8a84e] hover:bg-[#b8962e] dark:bg-[#c8a84e] dark:hover:bg-[#b8962e] text-white text-xs font-bold transition-all shadow-md border border-[#b8962e]/50">
                    {{ __('messages.nav_login') }}
                </a>
            @endauth
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-6 sm:px-12 py-12 flex flex-col items-center">
        <!-- Hero Section -->
        <section class="w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center py-12 lg:py-16 relative">
            <!-- Accent Blur effects -->
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-primary/10 dark:bg-primary/20 rounded-full blur-3xl -z-10"></div>
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-accent/10 dark:bg-accent/20 rounded-full blur-3xl -z-10"></div>

            <!-- Text Content -->
            <div class="flex flex-col items-start text-start {{ app()->getLocale() == 'ar' ? 'lg:pl-8' : 'lg:pr-8' }}">
                <!-- <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-accent/10 text-accent border border-accent/20 mb-6">
                    {{ __('messages.dept') }}
                </span> -->
                <h2 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight mb-6 premium-gradient-text">
                    {{ __('messages.hero_title') }}
                </h2>
                <p class="text-muted-foreground text-sm sm:text-base leading-relaxed mb-10 max-w-2xl">
                    {{ __('messages.hero_sub') }}
                </p>

                <div class="flex flex-wrap gap-4">
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="px-8 py-4 bg-[#c8a84e] hover:bg-[#b8962e] dark:bg-[#c8a84e] dark:hover:bg-[#b8962e] text-white text-sm font-bold rounded-xl transition-all shadow-md inline-flex items-center gap-2 border border-[#b8962e]/30">
                            <span>{{ __('messages.nav_dashboard') }}</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-8 py-4 bg-[#c8a84e] hover:bg-[#b8962e] dark:bg-[#c8a84e] dark:hover:bg-[#b8962e] text-white text-sm font-bold rounded-xl transition-all shadow-md inline-flex items-center gap-2 border border-[#b8962e]/30">
                            <span>{{ __('messages.hero_cta') }}</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Image Content -->
            <div class="relative w-full flex justify-center">
                <div class="relative bg-card p-3 rounded-3xl shadow-xl border border-border hover:shadow-2xl transition-all duration-500 hover:scale-[1.01] w-full max-w-lg group overflow-hidden">
                    <div class="relative aspect-[4/3] rounded-2xl overflow-hidden bg-muted">
                        <img src="{{ asset('campus_hero.jpg') }}" alt="{{ __('messages.university') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-90"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white text-start">
                            <span class="text-[10px] font-extrabold tracking-wider uppercase text-accent/90">{{ __('messages.university') }}</span>
                            <h4 class="text-sm font-bold leading-snug mt-0.5">{{ __('messages.app_name') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Indicators / Counters Grid -->
        <section class="w-full max-w-5xl grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
            <!-- Projects Count -->
            <div class="bg-card/70 backdrop-blur-sm p-6 border border-border rounded-2xl shadow-sm flex flex-col items-center premium-hover">
                <span class="text-3xl font-extrabold text-primary mb-2">{{ $totalProjects }}</span>
                <span class="text-xs text-muted-foreground font-semibold uppercase">{{ __('messages.total_projects') }}</span>
            </div>
            <!-- Alumni Count -->
            <div class="bg-card/70 backdrop-blur-sm p-6 border border-border rounded-2xl shadow-sm flex flex-col items-center premium-hover">
                <span class="text-3xl font-extrabold text-accent mb-2">{{ $totalAlumni }}</span>
                <span class="text-xs text-muted-foreground font-semibold uppercase">{{ __('messages.total_alumni') }}</span>
            </div>
            <!-- Supervisors Count -->
            <div class="bg-card/70 backdrop-blur-sm p-6 border border-border rounded-2xl shadow-sm flex flex-col items-center premium-hover">
                <span class="text-3xl font-extrabold text-blue-500 mb-2">{{ $totalSupervisors }}</span>
                <span class="text-xs text-muted-foreground font-semibold uppercase">{{ __('messages.total_supervisors') }}</span>
            </div>
            <!-- Active Requests Count -->
            <div class="bg-card/70 backdrop-blur-sm p-6 border border-border rounded-2xl shadow-sm flex flex-col items-center premium-hover">
                <span class="text-3xl font-extrabold text-emerald-500 mb-2">{{ $activeRequests }}</span>
                <span class="text-xs text-muted-foreground font-semibold uppercase">{{ __('messages.active_requests') }}</span>
            </div>
        </section>

        <!-- Roles cards -->
        <section class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-4 gap-6 text-start">
            <!-- Student Card -->
            <div class="bg-card/65 backdrop-blur-sm p-6 border border-border rounded-2xl shadow-sm flex flex-col premium-hover">
                <span class="p-2.5 bg-blue-50/85 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 rounded-xl w-fit mb-4">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </span>
                <h4 class="font-black text-foreground mb-2">{{ __('messages.role_student') }}</h4>
                <p class="text-xs text-muted-foreground leading-relaxed">{{ __('messages.role_student_desc') }}</p>
            </div>

            <!-- Supervisor Card -->
            <div class="bg-card/65 backdrop-blur-sm p-6 border border-border rounded-2xl shadow-sm flex flex-col premium-hover">
                <span class="p-2.5 bg-amber-50/85 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 rounded-xl w-fit mb-4">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </span>
                <h4 class="font-black text-foreground mb-2">{{ __('messages.role_supervisor') }}</h4>
                <p class="text-xs text-muted-foreground leading-relaxed">{{ __('messages.role_supervisor_desc') }}</p>
            </div>

            <!-- Admin Card -->
            <div class="bg-card/65 backdrop-blur-sm p-6 border border-border rounded-2xl shadow-sm flex flex-col premium-hover">
                <span class="p-2.5 bg-purple-50/85 dark:bg-purple-950/20 text-purple-600 dark:text-purple-400 rounded-xl w-fit mb-4">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </span>
                <h4 class="font-black text-foreground mb-2">{{ __('messages.role_admin') }}</h4>
                <p class="text-xs text-muted-foreground leading-relaxed">{{ __('messages.role_admin_desc') }}</p>
            </div>

            <!-- Alumnus Card -->
            <div class="bg-card/65 backdrop-blur-sm p-6 border border-border rounded-2xl shadow-sm flex flex-col premium-hover">
                <span class="p-2.5 bg-emerald-50/85 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 rounded-xl w-fit mb-4">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                </span>
                <h4 class="font-black text-foreground mb-2">{{ __('messages.role_alumni') }}</h4>
                <p class="text-xs text-muted-foreground leading-relaxed">{{ __('messages.role_alumni_desc') }}</p>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-[#1a3a6b] dark:bg-[#0f2340] text-white border-t border-[#15325e]/50 py-6 px-12 text-center text-xs font-semibold no-print transition-colors duration-200">
        {{ __('messages.footer') }}
    </footer>
</div>

@push('scripts')
<script>
    function updateThemeToggleUI() {
        const isDark = document.documentElement.classList.contains('dark');
        const sun = document.getElementById('sunIcon');
        const moon = document.getElementById('moonIcon');
        if (sun && moon) {
            if (isDark) {
                sun.classList.remove('hidden');
                moon.classList.add('hidden');
            } else {
                sun.classList.add('hidden');
                moon.classList.remove('hidden');
            }
        }
    }
    function toggleTheme() {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
        updateThemeToggleUI();
    }
    document.addEventListener('DOMContentLoaded', updateThemeToggleUI);
    updateThemeToggleUI();
</script>
@endpush
@endsection
