@extends('layouts.app')

@section('content')
<div class="min-h-screen grid grid-cols-1 md:grid-cols-3 bg-background text-foreground transition-colors duration-200">
    <!-- Left Column: Form (1/3 width) -->
    <div class="md:col-span-1 flex flex-col justify-center items-center p-6 sm:p-10 md:p-8 lg:p-12 bg-muted/20 text-foreground transition-colors duration-200 relative">
        

        <div class="w-full max-w-md animate-fade-in">
            <!-- Modern Form Wrapper Card -->
            <div class="bg-card border border-border/80 rounded-3xl p-8 shadow-xl transition-all duration-300">
                <h2 class="text-2xl font-black text-foreground mb-1 text-center md:text-start">
                    {{ __('messages.nav_login') }}
                </h2>
                <p class="text-xs text-muted-foreground mb-6 text-center md:text-start font-semibold">
                    جامعة نكسوس للعلوم والتكنولوجيا
                </p>

                @if(session('error'))
                    <div class="mb-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-950/25 border border-rose-250 text-rose-800 dark:text-rose-300 text-xs font-semibold text-center">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="login_type" id="loginTypeInput" value="student">

                    <!-- Modern Pill Tab Switcher (Segmented Control) -->
                    <div class="bg-muted p-1 rounded-2xl flex border border-border/40 mb-6 gap-1">
                        <button type="button" onclick="switchLoginTab('student')" id="tabBtnStudent" class="flex-grow py-2.5 text-xs font-black rounded-xl bg-card text-foreground shadow-sm border border-border/10 transition-all text-center cursor-pointer">
                            الطلاب والخريجين
                        </button>
                        <button type="button" onclick="switchLoginTab('staff')" id="tabBtnStaff" class="flex-grow py-2.5 text-xs font-black rounded-xl text-muted-foreground hover:text-foreground hover:bg-card/20 transition-all text-center cursor-pointer">
                            الموظفين والأكاديميين
                        </button>
                    </div>

                    <!-- Input Fields -->
                    <div class="space-y-4">
                        <div>
                            <label id="loginLabel" class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.academic_id') }}</label>
                            <input type="text" name="email" id="inputEmail" required value="{{ old('email') }}" placeholder="مثال: 441001" class="w-full text-xs font-semibold px-4 py-3 bg-muted/40 text-foreground border border-border/70 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary focus:bg-card outline-none transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.password') }}</label>
                            <div class="relative">
                                <input type="password" name="password" id="inputPassword" required class="w-full text-xs font-semibold px-4 py-3 pe-10 bg-muted/40 text-foreground border border-border/70 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary focus:bg-card outline-none transition-all duration-200">
                                <button type="button" onclick="togglePasswordVisibility()" class="absolute top-1/2 -translate-y-1/2 end-3.5 text-muted-foreground hover:text-foreground focus:outline-none cursor-pointer transition-colors duration-150">
                                    <svg id="eyeOpenIcon" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="eyeClosedIcon" class="h-4.5 w-4.5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-2 py-3 bg-primary text-white hover:bg-primary/95 text-xs font-bold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 cursor-pointer text-center">
                        {{ __('messages.nav_login') }}
                    </button>

                    <div class="text-center mt-4">
                        <a href="{{ route('home') }}" class="text-[11px] font-bold text-muted-foreground hover:text-primary transition-colors inline-flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>العودة للصفحة الرئيسية</span>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Designer Credit Footer -->
            <div class="mt-8 text-center text-[10px] text-muted-foreground font-semibold tracking-wider space-y-1 select-none">
                <div>© 2026 جامعة نكسوس للعلوم والتكنولوجيا - بوابة أبحاث التخرج وشؤون الخريجين</div>
            </div>
        </div>
    </div>

    <!-- Right Column: Campus Image (2/3 width) -->
    <div class="md:col-span-2 hidden md:block relative overflow-hidden bg-muted">
        <img src="{{ asset('campus_hero.jpg') }}" alt="{{ __('messages.university') }}" class="absolute inset-0 w-full h-full object-cover">
        <!-- Dark overlay to ensure high text contrast -->
        <div class="absolute inset-0 bg-black/55 backdrop-blur-[0.5px]"></div>
        
        <!-- Left-aligned content container matching the reference design -->
        <div class="absolute inset-x-0 bottom-0 p-12 lg:p-20 flex flex-col justify-end h-full text-white text-start">
            <div class="max-w-2xl animate-fade-in">
                <!-- Branding logo above text headings -->
                <img src="{{ asset('logo.svg') }}" alt="Logo" class="h-16 w-auto rounded-xl bg-slate-900/80 p-2 mb-6 shadow-lg border border-white/20 object-contain">
                
                <h2 class="text-4xl lg:text-5xl font-black mb-4 leading-tight">
                    أهلاً بك مجدداً
                </h2>
                
                <p class="text-sm lg:text-base text-gray-200/90 font-medium leading-relaxed mb-8 max-w-xl">
                    نظام نكسوس الأكاديمي وبوابة الخريجين لجامعة نكسوس للعلوم والتكنولوجيا. منصة متكاملة لمتابعة الأبحاث وإجراءات التخرج وإصدار الوثائق بيسر وسهولة.
                </p>
                
                <!-- Social media icons list matching the reference image layout -->
                <div class="flex items-center gap-4 text-white/70">
                    <a href="#" class="hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.8c4.56-.93 8-4.96 8-9.8z"/>
                        </svg>
                    </a>
                    <a href="#" class="hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                    <a href="#" class="hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('inputPassword');
        const eyeOpen = document.getElementById('eyeOpenIcon');
        const eyeClosed = document.getElementById('eyeClosedIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }

    function switchLoginTab(type) {
        document.getElementById('loginTypeInput').value = type;

        const tabBtnStudent = document.getElementById('tabBtnStudent');
        const tabBtnStaff = document.getElementById('tabBtnStaff');
        const loginLabel = document.getElementById('loginLabel');
        const inputEmail = document.getElementById('inputEmail');

        if (type === 'student') {
            tabBtnStudent.className = "flex-grow py-2.5 text-xs font-black rounded-xl bg-card text-foreground shadow-sm border border-border/10 transition-all text-center cursor-pointer";
            tabBtnStaff.className = "flex-grow py-2.5 text-xs font-black rounded-xl text-muted-foreground hover:text-foreground hover:bg-card/25 transition-all text-center cursor-pointer";

            loginLabel.innerText = "{{ __('messages.academic_id') }}";
            inputEmail.type = 'text';
            inputEmail.placeholder = 'مثال: 441001';
        } else {
            tabBtnStaff.className = "flex-grow py-2.5 text-xs font-black rounded-xl bg-card text-foreground shadow-sm border border-border/10 transition-all text-center cursor-pointer";
            tabBtnStudent.className = "flex-grow py-2.5 text-xs font-black rounded-xl text-muted-foreground hover:text-foreground hover:bg-card/25 transition-all text-center cursor-pointer";

            loginLabel.innerText = "{{ __('messages.email') }}";
            inputEmail.type = 'email';
            inputEmail.placeholder = 'username@nexus-scholar.edu';
        }
    }

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

    document.addEventListener('DOMContentLoaded', () => {
        updateThemeToggleUI();
        switchLoginTab('student'); // default tab
    });
    updateThemeToggleUI();
</script>
@endsection
