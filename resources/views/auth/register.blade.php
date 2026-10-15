@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col bg-[#fcfdfd]">
    <!-- Header -->
    <header class="bg-[#1a3a6b] dark:bg-[#0f2340] text-white border-b border-[#15325e]/50 py-4 px-6 sm:px-12 flex justify-between items-center shadow-md no-print">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ asset('logo.svg') }}" alt="Logo" class="h-10 w-auto rounded-lg shadow-sm border border-white/20 object-contain bg-slate-900/80 p-1">
            <div>
                <h1 class="font-extrabold text-white text-lg leading-tight">{{ __('messages.app_name') }}</h1>
                <p class="text-xs text-blue-200 font-semibold">{{ __('messages.university') }}</p>
            </div>
        </a>

        <!-- Language Switcher -->
        <a href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'en' : 'ar') }}" class="inline-flex items-center justify-center px-4 py-2 border border-white/20 rounded-lg text-xs font-bold text-white bg-white/10 hover:bg-white/20 transition-colors gap-1.5 shadow-sm">
            <svg class="h-4 w-4 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h2a2.5 2.5 0 002.5-2.5V14a2 2 0 012-2h.09M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ __('messages.lang_switch') }}</span>
        </a>
    </header>

    <!-- Content -->
    <main class="flex-1 max-w-xl w-full mx-auto px-6 py-12 flex flex-col justify-center">
        <div class="bg-white border border-gray-150 rounded-2xl shadow-sm p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-black text-gray-900 leading-tight mb-2">
                    Create Account
                </h2>
                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider">
                    Sign up as a Student or Alumni
                </p>
            </div>

            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-semibold">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.full_name') }} (AR)</label>
                        <input type="text" name="name_ar" required value="{{ old('name_ar') }}" class="w-full text-xs font-semibold px-3.5 py-2.5 border border-gray-300 rounded-xl focus:border-[#1e3a8a] focus:ring-1 focus:ring-[#1e3a8a] outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.full_name') }} (EN)</label>
                        <input type="text" name="name_en" required value="{{ old('name_en') }}" class="w-full text-xs font-semibold px-3.5 py-2.5 border border-gray-300 rounded-xl focus:border-[#1e3a8a] focus:ring-1 focus:ring-[#1e3a8a] outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.email') }}</label>
                    <input type="email" name="email" required value="{{ old('email') }}" class="w-full text-xs font-semibold px-3.5 py-2.5 border border-gray-300 rounded-xl focus:border-[#1e3a8a] focus:ring-1 focus:ring-[#1e3a8a] outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.password') }}</label>
                        <input type="password" name="password" required class="w-full text-xs font-semibold px-3.5 py-2.5 border border-gray-300 rounded-xl focus:border-[#1e3a8a] focus:ring-1 focus:ring-[#1e3a8a] outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="w-full text-xs font-semibold px-3.5 py-2.5 border border-gray-300 rounded-xl focus:border-[#1e3a8a] focus:ring-1 focus:ring-[#1e3a8a] outline-none">
                    </div>
                </div>
                <p class="text-[9px] text-gray-400 font-semibold leading-normal">
                    Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character (e.g. @$!%*#?&).
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.role') }}</label>
                        <select name="role" required class="w-full text-xs font-semibold px-3.5 py-2.5 border border-gray-300 rounded-xl outline-none bg-white focus:border-[#1e3a8a]">
                            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>{{ __('messages.role_student') }}</option>
                            <option value="alumnus" {{ old('role') == 'alumnus' ? 'selected' : '' }}>{{ __('messages.role_alumni') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.filter_dept') }}</label>
                        <select name="department" required class="w-full text-xs font-semibold px-3.5 py-2.5 border border-gray-300 rounded-xl outline-none bg-white focus:border-[#1e3a8a]">
                            <option value="Dentistry" {{ old('department') == 'Dentistry' ? 'selected' : '' }}>طب وجراحة الفم والأسنان</option>
                            <option value="Pharmacy" {{ old('department') == 'Pharmacy' ? 'selected' : '' }}>الصيدلة العامة</option>
                            <option value="Clinical Pharmacy" {{ old('department') == 'Clinical Pharmacy' ? 'selected' : '' }}>الصيدلة السريرية</option>
                            <option value="Medical Labs" {{ old('department') == 'Medical Labs' ? 'selected' : '' }}>المختبرات الطبية</option>
                            <option value="Anesthesia" {{ old('department') == 'Anesthesia' ? 'selected' : '' }}>تكنولوجيا التخدير</option>
                            <option value="Respiratory Care" {{ old('department') == 'Respiratory Care' ? 'selected' : '' }}>الرعاية التنفسية</option>
                            <option value="Optometry" {{ old('department') == 'Optometry' ? 'selected' : '' }}>البصريات وعلوم الرؤية</option>
                            <option value="Physical Therapy" {{ old('department') == 'Physical Therapy' ? 'selected' : '' }}>العلاج الطبيعي والتأهيل</option>
                            <option value="Nursing" {{ old('department') == 'Nursing' ? 'selected' : '' }}>التمريض والقِبالة</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-1.5">{{ __('messages.phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full text-xs font-semibold px-3.5 py-2.5 border border-gray-300 rounded-xl focus:border-[#1e3a8a] focus:ring-1 focus:ring-[#1e3a8a] outline-none">
                </div>

                <button type="submit" class="w-full py-2.5 mt-2 bg-[#1e3a8a] hover:bg-[#1a337a] text-white text-xs font-bold rounded-xl shadow-md transition-all">
                    Register Account
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500 font-medium">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-[#1e3a8a] font-bold hover:underline">
                        {{ __('messages.nav_login') }}
                    </a>
                </p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-[#1a3a6b] dark:bg-[#0f2340] text-white border-t border-[#15325e]/50 py-6 px-12 text-center text-xs font-semibold no-print transition-colors duration-200">
        {{ __('messages.footer') }}
    </footer>
</div>
@endsection
