<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Document Verification - Ibn Al-Nafis University</title>
    <!-- Tailwind CSS (via CDN) and fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800;900&family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', 'Outfit', sans-serif;
            background: radial-gradient(circle at top, #0f172a 0%, #020617 100%);
        }
    </style>
</head>
<body class="min-h-screen text-slate-100 flex flex-col justify-between py-12 px-4 selection:bg-amber-500 selection:text-slate-950">

    <div class="w-full max-w-lg mx-auto my-auto">
        <!-- Logo and Header -->
        <div class="text-center mb-8">
            <div class="inline-flex bg-white p-1 rounded-2xl border border-slate-700/50 mb-3.5 shadow-md">
                <img src="{{ asset('logo.svg') }}" alt="Logo" class="h-12 w-12 rounded-xl object-contain bg-slate-900/80 p-1">
            </div>
            <h1 class="text-lg font-black text-white tracking-wide">Nexus University of Science & Technology</h1>
            <p class="text-[10px] text-amber-500 font-extrabold uppercase tracking-widest mt-1">Public Verification Portal</p>
        </div>

        @if($doc)
            <!-- Verification Success Card -->
            <div class="bg-slate-900/60 backdrop-blur-xl border border-emerald-500/30 rounded-3xl p-6 md:p-8 shadow-2xl relative overflow-hidden">
                <!-- Watermark seal background -->
                <div class="absolute -right-10 -bottom-10 h-40 w-40 text-emerald-500/5 pointer-events-none">
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM13 16h-2v2h2v-2zm0-6h-2v4h2v-4z"/>
                    </svg>
                </div>

                <!-- Green Badge -->
                <div class="flex flex-col items-center text-center mb-6">
                    <div class="h-14 w-14 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center mb-4 shadow-lg shadow-emerald-500/5 scale-105 animate-pulse">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-black text-emerald-400 tracking-tight">DOCUMENT AUTHENTIC & VERIFIED</h2>
                    <p class="text-[10px] text-slate-400 font-extrabold uppercase mt-0.5">تم التحقق من صحة المستند بنجاح</p>
                </div>

                <!-- Details List -->
                <div class="space-y-4 pt-4 border-t border-slate-800">
                    <div class="grid grid-cols-2 gap-2 text-xs py-1">
                        <div>
                            <span class="block text-[9px] text-slate-500 font-extrabold uppercase">Document ID / رقم المستند</span>
                            <span class="font-bold text-white uppercase">REQ-{{ 3000 + $doc->id }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] text-slate-500 font-extrabold uppercase">Type / نوع المستند</span>
                            <span class="font-bold text-white">{{ $doc->doc_type_en }}</span>
                        </div>
                    </div>

                    <div class="py-1">
                        <span class="block text-[9px] text-slate-500 font-extrabold uppercase">Student Name (Bilingual) / اسم الطالب</span>
                        <span class="block font-black text-slate-100 text-sm mt-0.5">{{ $doc->alumnus->name_ar }}</span>
                        <span class="block font-bold text-slate-300 text-xs mt-0.5">{{ $doc->alumnus->name_en }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs py-1">
                        <div>
                            <span class="block text-[9px] text-slate-500 font-extrabold uppercase">Department / التخصص</span>
                            <span class="font-bold text-amber-500">{{ $doc->alumnus->department }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] text-slate-500 font-extrabold uppercase">Graduation / دفعة التخرج</span>
                            <span class="font-bold text-slate-200">{{ $doc->alumnus->alumniProfile->graduation_year ?? date('Y') }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs py-1">
                        <div>
                            <span class="block text-[9px] text-slate-500 font-extrabold uppercase">Release Date / تاريخ الإصدار</span>
                            <span class="font-bold text-slate-200">{{ $doc->updated_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] text-slate-500 font-extrabold uppercase">Authority / جهة التدقيق</span>
                            <span class="font-bold text-emerald-400">Office of the Registrar</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-800 text-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 text-[9px] font-black uppercase text-emerald-400 rounded-full">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        Secure Blockchain Hash Valid
                    </span>
                </div>
            </div>
        @else
            <!-- Verification Failure Card -->
            <div class="bg-slate-900/60 backdrop-blur-xl border border-rose-500/30 rounded-3xl p-6 md:p-8 shadow-2xl text-center">
                <div class="h-14 w-14 rounded-full bg-rose-500/10 border border-rose-500/30 text-rose-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h2 class="text-lg font-black text-rose-400 tracking-tight">VERIFICATION FAILED / غير صالح</h2>
                <p class="text-[10px] text-slate-400 font-extrabold uppercase mt-0.5">لم يتم العثور على أي مستند بهذا الرمز</p>
                
                <p class="text-xs text-slate-400 font-semibold leading-relaxed mt-4 max-w-sm mx-auto">
                    The verification code provided is invalid, has been revoked, or the document was not issued by the Deanship of Nexus University. Please make sure the URL was entered correctly.
                </p>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="text-center mt-12">
        <p class="text-[10px] text-slate-600 font-bold uppercase tracking-wider">© 2026 Nexus University of Science & Technology. All Rights Reserved.</p>
    </div>

</body>
</html>
