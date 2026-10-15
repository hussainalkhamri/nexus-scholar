@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col md:flex-row bg-background text-foreground transition-colors duration-200">
    <!-- Sidebar Navigation -->
    <aside id="dashboardSidebar" class="no-print w-full md:w-64 bg-card border-e border-border text-foreground flex flex-col shrink-0 shadow-sm md:sticky md:top-0 md:h-screen transition-all duration-300 ease-in-out overflow-hidden">
        <!-- Brand / Logo -->
        <div class="h-16 px-4 border-b border-primary/20 bg-slate-900 text-white flex items-center gap-3 shrink-0">
            <img src="{{ asset('logo.svg') }}" alt="Logo" class="h-10 w-10 rounded-xl bg-slate-800 p-1 object-contain shadow-md">
            <div>
                <h1 class="font-bold text-sm tracking-wide leading-tight">نكسوس سكولار</h1>
                <p class="text-[10px] text-teal-300 font-medium">Nexus Scholar</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <!-- Home -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-muted dark:hover:text-white transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>{{ __('messages.nav_home') }}</span>
            </a>

            <!-- Dashboard Index -->
            <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard.index') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-muted dark:hover:text-white' }} transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                </svg>
                <span>{{ __('messages.nav_dashboard') }}</span>
            </a>

            <!-- Projects (Students, Supervisors, Admins) -->
            @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'projects_officer', 'supervisor', 'student']))
            <a href="{{ route('projects.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('projects.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-muted dark:hover:text-white' }} transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>{{ __('messages.nav_projects') }}</span>
            </a>
            @endif

            <!-- Supervisors (Admin only) -->
            @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'projects_officer']))
            <a href="{{ route('supervisors.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('supervisors.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-muted dark:hover:text-white' }} transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                </svg>
                <span>{{ __('messages.nav_supervisors') }}</span>
            </a>
            @endif

            <!-- Alumni Directory (Admin only) -->
            @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer']))
            <a href="{{ route('alumni.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('alumni.index') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-muted dark:hover:text-white' }} transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>{{ __('messages.nav_alumni') }}</span>
            </a>
            @endif

            <!-- Document Requests (Admin or Alumnus only) -->
            @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer', 'alumnus']))
            <a href="{{ route('alumni.requests.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('alumni.requests.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-muted dark:hover:text-white' }} transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>{{ __('messages.nav_requests') }}</span>
            </a>
            @endif

            <!-- Incentives & Opportunities (Admin or Alumnus only) -->
            @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer', 'alumnus']))
            <a href="{{ route('incentives.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('incentives.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-muted dark:hover:text-white' }} transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>{{ __('messages.nav_incentives') }}</span>
            </a>
            @endif

            <!-- Reports (Admin only) -->
            @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'projects_officer', 'alumni_officer']))
            <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('reports.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-muted dark:hover:text-white' }} transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                </svg>
                <span>{{ __('messages.nav_reports') }}</span>
            </a>
            @endif

            <!-- Users & Roles (Admin only) -->
            @if(in_array(auth()->user()->role, ['super_admin', 'admin']))
            <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('users.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-muted dark:hover:text-white' }} transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>{{ __('messages.users_roles') }}</span>
            </a>

            <a href="{{ route('backup.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('backup.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-muted dark:hover:text-white' }} transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                </svg>
                <span>النسخ الاحتياطي والصيانة</span>
            </a>
            @endif

            <!-- Profile -->
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('profile.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-muted dark:hover:text-white' }} transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>{{ __('messages.nav_profile') }}</span>
            </a>
        </nav>

        <!-- User Information in Sidebar Footer -->
        <div class="p-4 border-t border-border bg-muted/30 dark:bg-card">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-foreground truncate">
                        {{ app()->getLocale() == 'ar' ? auth()->user()->name_ar : auth()->user()->name_en }}
                    </p>
                    <p class="text-[10px] text-muted-foreground font-medium truncate capitalize">
                        {{ __('messages.role_' . auth()->user()->role) }}
                    </p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Header Bar -->
        <header class="no-print h-16 bg-[#1a3a6b] dark:bg-[#0f2340] text-white border-b border-[#15325e]/50 flex items-center justify-between px-6 sticky top-0 z-20 shadow-md transition-colors duration-200">
            <div class="flex items-center gap-3">
                <!-- Sidebar Toggle Button -->
                <button type="button" onclick="toggleSidebar()" class="hidden md:inline-flex items-center justify-center p-2 rounded-lg text-white bg-white/10 hover:bg-white/20 transition-colors cursor-pointer" title="طي/توسيع القائمة">
                    <svg id="sidebarExpandIcon" class="h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="sidebarCollapseIcon" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
                <span class="text-sm font-bold text-blue-100 hidden sm:block">
                    {{ __('messages.app_name') }}
                </span>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-4 ms-auto">
                <!-- Theme Toggle Button -->
                <button type="button" onclick="toggleTheme()" class="inline-flex items-center justify-center p-2 border border-white/20 rounded-lg text-white bg-white/10 hover:bg-white/20 transition-colors shadow-sm cursor-pointer" title="{{ __('messages.theme_dark') }}">
                    <svg id="sunIcon" class="h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                    <svg id="moonIcon" class="h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- User Role Label and Logout -->
                <div class="flex items-center gap-3 border-s border-white/20 ps-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/10 text-white border border-white/20">
                        {{ __('messages.role_' . auth()->user()->role) }}
                    </span>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg text-blue-100 hover:text-white hover:bg-white/15 transition-colors" title="{{ __('messages.nav_logout') }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Inner Content -->
        <main class="flex-1 p-6 overflow-y-auto">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-950/25 border border-emerald-250 text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded-lg bg-rose-50 dark:bg-rose-950/25 border border-rose-250 text-rose-800 dark:text-rose-300 text-sm flex items-center gap-2">
                    <svg class="h-5 w-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

             @if(auth()->check() && auth()->user()->isBirthdayToday())
                <div class="mb-6 p-6 rounded-3xl bg-gradient-to-r from-amber-500 via-rose-500 to-primary text-white shadow-xl relative overflow-hidden animate-fade-in no-print">
                    <div class="absolute -left-6 -bottom-6 opacity-20 text-white pointer-events-none">
                        <svg class="h-40 w-40" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                    </div>
                    <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="p-3.5 bg-white/20 backdrop-blur-md rounded-2xl text-3xl shrink-0 shadow-inner">
                                🎂🎉
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-black tracking-tight flex items-center gap-2">
                                    <span>كل عام وأنت بخير، {{ auth()->user()->name_ar }}!</span> 🎈✨
                                </h3>
                                <p class="text-xs text-white/90 font-semibold mt-1 leading-relaxed">
                                    تتقدم لك أسرة جامعة نكسوس للعلوم والتكنولوجيا بأطيب التهاني والتبريكات بمناسبة عيد ميلادك السعيد، متمنين لك عاماً حافلاً بالنجاح والتألق في مسيرتك الأكاديمية والمهنية.
                                </p>
                            </div>
                        </div>
                        <div class="shrink-0 bg-white/20 hover:bg-white/30 transition-all backdrop-blur-md px-4 py-2 rounded-2xl text-xs font-bold border border-white/30 text-white shadow-sm">
                            جامعة نكسوس 🎓
                        </div>
                    </div>
                </div>
             @endif
             @yield('dashboard_content')
        </main>
    </div>
</div>

<!-- Custom Premium Confirmation Modal -->
<div id="customConfirmModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm hidden no-print">
    <div class="bg-card/95 border border-border rounded-3xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden transform transition-all duration-200">
        <!-- Header / Warning Icon -->
        <div class="p-6 pb-4 flex flex-col items-center text-center">
            <span class="p-3 bg-amber-50 dark:bg-amber-950/20 text-amber-500 rounded-full mb-4">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </span>
            <h3 class="font-extrabold text-foreground text-base tracking-wide mb-2">تأكيد الإجراء</h3>
            <p id="customConfirmModalMessage" class="text-xs text-muted-foreground font-semibold leading-relaxed px-2">
                هل أنت متأكد من رغبتك في الاستمرار بهذا الإجراء؟
            </p>
        </div>

        <!-- Action Controls -->
        <div class="px-6 py-4 bg-muted/40 dark:bg-muted/10 border-t border-border flex justify-end gap-2.5">
            <button id="customConfirmCancelBtn" type="button" class="px-4.5 py-2 border border-border text-foreground hover:bg-muted text-xs font-bold rounded-xl transition-all cursor-pointer">
                إلغاء
            </button>
            <button id="customConfirmConfirmBtn" type="button" class="px-5 py-2 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-all shadow-sm cursor-pointer">
                تأكيد
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let confirmCallback = null;
    let cancelCallback = null;

    window.showConfirmModal = function(message, onConfirm, onCancel) {
        const modal = document.getElementById('customConfirmModal');
        const messageEl = document.getElementById('customConfirmModalMessage');
        const card = modal.querySelector('.bg-card\\/95');
        
        messageEl.textContent = message;
        confirmCallback = onConfirm;
        cancelCallback = onCancel;

        modal.classList.remove('hidden');
        modal.classList.add('animate-fade-in');
        if (card) {
            card.classList.add('animate-scale-up');
        }
    };

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

    // Sidebar collapse/expand functionality
    function toggleSidebar() {
        const sidebar = document.getElementById('dashboardSidebar');
        const expandIcon = document.getElementById('sidebarExpandIcon');
        const collapseIcon = document.getElementById('sidebarCollapseIcon');
        
        if (!sidebar) return;
        
        const isCollapsed = sidebar.classList.contains('md:w-0');
        
        if (isCollapsed) {
            // Expand
            sidebar.classList.remove('md:w-0', 'md:border-e-0');
            sidebar.classList.add('md:w-64');
            if (expandIcon) expandIcon.classList.add('hidden');
            if (collapseIcon) collapseIcon.classList.remove('hidden');
            localStorage.setItem('sidebarCollapsed', 'false');
        } else {
            // Collapse
            sidebar.classList.remove('md:w-64');
            sidebar.classList.add('md:w-0', 'md:border-e-0');
            if (expandIcon) expandIcon.classList.remove('hidden');
            if (collapseIcon) collapseIcon.classList.add('hidden');
            localStorage.setItem('sidebarCollapsed', 'true');
        }
    }

    function initSidebarState() {
        const sidebar = document.getElementById('dashboardSidebar');
        const expandIcon = document.getElementById('sidebarExpandIcon');
        const collapseIcon = document.getElementById('sidebarCollapseIcon');
        
        if (!sidebar) return;
        
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        
        if (isCollapsed) {
            sidebar.classList.remove('md:w-64');
            sidebar.classList.add('md:w-0', 'md:border-e-0');
            if (expandIcon) expandIcon.classList.remove('hidden');
            if (collapseIcon) collapseIcon.classList.add('hidden');
        }
    }

    // Initialize sidebar state immediately (before DOMContentLoaded to avoid flicker)
    initSidebarState();

    document.addEventListener("DOMContentLoaded", function() {
        updateThemeToggleUI();
        initSidebarState();

        const modal = document.getElementById('customConfirmModal');
        const card = modal.querySelector('.bg-card\\/95');
        const confirmBtn = document.getElementById('customConfirmConfirmBtn');
        const cancelBtn = document.getElementById('customConfirmCancelBtn');

        function hideModal() {
            modal.classList.add('hidden');
            modal.classList.remove('animate-fade-in');
            if (card) {
                card.classList.remove('animate-scale-up');
            }
        }

        if (confirmBtn && cancelBtn) {
            confirmBtn.addEventListener("click", function() {
                hideModal();
                if (confirmCallback) confirmCallback();
            });

            cancelBtn.addEventListener("click", function() {
                hideModal();
                if (cancelCallback) cancelCallback();
            });
        }

        // Scan form submissions and select changes to intercept confirm() calls
        function scanAndInterceptConfirmations() {
            // Form interceptor
            document.querySelectorAll("form").forEach(form => {
                const onsubmitAttr = form.getAttribute("onsubmit");
                if (onsubmitAttr && onsubmitAttr.includes("confirm(")) {
                    const match = onsubmitAttr.match(/confirm\(['"](.+?)['"]\)/);
                    const msg = match ? match[1] : "هل أنت متأكد من الاستمرار؟";
                    
                    form.removeAttribute("onsubmit");
                    form.addEventListener("submit", function(e) {
                        e.preventDefault();
                        window.showConfirmModal(msg, () => {
                            form.submit();
                        });
                    });
                }
            });

            // Select change interceptor
            document.querySelectorAll("select").forEach(select => {
                const onchangeAttr = select.getAttribute("onchange");
                if (onchangeAttr && onchangeAttr.includes("confirm(")) {
                    const match = onchangeAttr.match(/confirm\(['"](.+?)['"]\)/);
                    const msg = match ? match[1] : "هل أنت متأكد؟";

                    let previousValue = select.value;
                    select.addEventListener("focus", function() {
                        previousValue = select.value;
                    });

                    select.removeAttribute("onchange");
                    select.addEventListener("change", function(e) {
                        const newValue = select.value;
                        select.value = previousValue; // Revert to old value temporarily

                        window.showConfirmModal(msg, () => {
                            select.value = newValue;
                            previousValue = newValue;
                            
                            // submit parent form if select has it
                            if (select.closest('form')) {
                                select.closest('form').submit();
                            }
                        }, () => {
                            select.value = previousValue; // Revert
                        });
                    });
                }
            });
        }

        scanAndInterceptConfirmations();
        
        // Expose a function to scan newly added elements if dynamic loading happens
        window.scanConfirmations = scanAndInterceptConfirmations;
    });
</script>
@endpush
@endsection
