@extends('layouts.dashboard')

@section('title', __('messages.users_roles'))

@section('dashboard_content')
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-black text-foreground tracking-tight mb-1">
            {{ __('messages.users_roles') }}
        </h2>
        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">
            {{ __('messages.users_roles_desc') }}
        </p>
    </div>
    
    <!-- Actions -->
    <div class="no-print shrink-0 flex flex-wrap gap-2">
        <a href="{{ route('reports.excel', ['report_type' => 'users']) }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/40 text-xs font-bold rounded-xl transition-all shadow-xs gap-1.5 cursor-pointer" title="تصدير جدول المستخدمين كملف Excel">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>تصدير المستخدمين Excel</span>
        </a>

        <button onclick="openImportUserModal()" class="inline-flex items-center justify-center px-4 py-2.5 border border-border text-muted-foreground hover:bg-muted bg-card text-xs font-bold rounded-xl transition-all shadow-sm gap-1.5 cursor-pointer">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            <span>{{ __('messages.import_users') }}</span>
        </button>

        <button onclick="openAddUserModal()" class="inline-flex items-center justify-center px-4 py-2.5 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-all shadow-md gap-1.5 cursor-pointer">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>{{ __('messages.add_user') }}</span>
        </button>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/25 border border-emerald-250 text-emerald-800 dark:text-emerald-300 text-xs font-semibold text-center">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-950/25 border border-rose-250 text-rose-800 dark:text-rose-300 text-xs font-semibold text-center">
        {{ session('error') }}
    </div>
@endif
@if ($errors->any())
    <div class="mb-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-950/25 border border-rose-250 text-rose-800 dark:text-rose-300 text-xs font-semibold">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Tabs Navigation -->
<div class="flex flex-wrap gap-1 mb-6 border-b border-border pb-px no-print">
    <button onclick="switchUserTab('tab-admin')" id="btn-tab-admin" class="user-tab-btn px-5 py-3 text-xs font-black transition-all border-b-2 border-primary text-primary bg-primary/10 rounded-t-xl cursor-pointer">
        إدارة القسم ({{ $users->where('role', 'admin')->count() }})
    </button>
    <button onclick="switchUserTab('tab-supervisor')" id="btn-tab-supervisor" class="user-tab-btn px-5 py-3 text-xs font-black transition-all border-b-2 border-transparent text-muted-foreground hover:text-foreground hover:bg-muted/50 rounded-t-xl cursor-pointer">
        المشرفون الأكاديميون ({{ $users->where('role', 'supervisor')->count() }})
    </button>
    <button onclick="switchUserTab('tab-student')" id="btn-tab-student" class="user-tab-btn px-5 py-3 text-xs font-black transition-all border-b-2 border-transparent text-muted-foreground hover:text-foreground hover:bg-muted/50 rounded-t-xl cursor-pointer">
        الطلاب ({{ $users->where('role', 'student')->count() }})
    </button>
    <button onclick="switchUserTab('tab-alumnus')" id="btn-tab-alumnus" class="user-tab-btn px-5 py-3 text-xs font-black transition-all border-b-2 border-transparent text-muted-foreground hover:text-foreground hover:bg-muted/50 rounded-t-xl cursor-pointer">
        الخريجون ({{ $users->where('role', 'alumnus')->count() }})
    </button>
</div>

<!-- Users List Card -->
<div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden mb-8 transition-colors duration-200">
    <!-- 1. Admin Tab Content -->
    <div id="tab-admin" class="user-tab-content overflow-x-auto">
        @include('dashboard.users.partials.table', ['filteredUsers' => $users->where('role', 'admin')])
    </div>

    <!-- 2. Supervisor Tab Content -->
    <div id="tab-supervisor" class="user-tab-content hidden overflow-x-auto">
        @include('dashboard.users.partials.table', ['filteredUsers' => $users->where('role', 'supervisor')])
    </div>

    <!-- 3. Student Tab Content -->
    <div id="tab-student" class="user-tab-content hidden overflow-x-auto">
        @include('dashboard.users.partials.table', ['filteredUsers' => $users->where('role', 'student')])
    </div>

    <!-- 4. Alumnus Tab Content -->
    <div id="tab-alumnus" class="user-tab-content hidden overflow-x-auto">
        @include('dashboard.users.partials.table', ['filteredUsers' => $users->where('role', 'alumnus')])
    </div>
</div>

<!-- Permissions Matrix Card -->
<div class="bg-card border border-border rounded-2xl shadow-sm p-6 overflow-hidden transition-colors duration-200">
    <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-4">
        {{ __('messages.permissions_matrix') }}
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-start border-collapse">
            <thead>
                <tr class="bg-muted/40 border-b border-border text-muted-foreground font-extrabold uppercase text-[10px] tracking-wider text-start">
                    <th class="p-4 text-start font-extrabold">{{ __('messages.nav_dashboard') }}</th>
                    @foreach($allRoles as $role)
                        <th class="p-4 text-center font-extrabold">{{ __('messages.role_' . $role) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-border text-xs font-semibold text-foreground">
                @foreach($routeKeys as $r)
                    <tr class="hover:bg-muted/30 transition-colors">
                        <td class="p-4 font-bold text-foreground">
                            {{ __('messages.' . $r['label_key']) }}
                        </td>
                        @foreach($allRoles as $role)
                            @php
                                // Map alumni to alumni role inside config array
                                $targetRole = $role == 'alumnus' ? 'alumni' : $role;
                                $hasAccess = in_array($targetRole, $r['roles']);
                            @endphp
                            <td class="p-4 text-center">
                                @if($hasAccess)
                                    <!-- Allowed (Checkmark) -->
                                    <svg class="h-5 w-5 text-emerald-600 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    <!-- Denied (X mark) -->
                                    <svg class="h-4 w-4 text-muted-foreground/30 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-card border border-border w-full max-w-lg rounded-2xl shadow-xl overflow-hidden animate-scale-up">
        <div class="p-6 border-b border-border flex items-center justify-between">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide">
                {{ __('messages.add_user') }}
            </h3>
            <button onclick="closeAddUserModal()" class="text-muted-foreground hover:text-foreground cursor-pointer">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.name_ar') }}</label>
                    <input type="text" name="name_ar" required placeholder="علي السقاف" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.name_en') }}</label>
                    <input type="text" name="name_en" required placeholder="Ali Al-Saqqaf" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.email') }}</label>
                    <input type="email" name="email" required placeholder="student@nexus-scholar.edu" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.academic_id') }} (للطلاب والخريجين)</label>
                    <input type="text" name="academic_id" placeholder="441001" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.phone') }}</label>
                    <input type="text" name="phone" placeholder="771000000" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.filter_dept') }}</label>
                    <select name="department" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="Dentistry">طب وجراحة الفم والأسنان</option>
                        <option value="Pharmacy">الصيدلة العامة</option>
                        <option value="Clinical Pharmacy">الصيدلة السريرية</option>
                        <option value="Medical Labs">المختبرات الطبية</option>
                        <option value="Anesthesia">تكنولوجيا التخدير</option>
                        <option value="Respiratory Care">الرعاية التنفسية</option>
                        <option value="Optometry">البصريات وعلوم الرؤية</option>
                        <option value="Physical Therapy">العلاج الطبيعي والتأهيل</option>
                        <option value="Nursing">التمريض والقِبالة</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.role') }}</label>
                    <select name="role" required class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                        <option value="student">{{ __('messages.role_student') }}</option>
                        <option value="alumnus">{{ __('messages.role_alumni') }}</option>
                        <option value="supervisor">{{ __('messages.role_supervisor') }}</option>
                        <option value="admin">{{ __('messages.role_admin') }}</option>
                        <option value="super_admin">{{ __('messages.role_super_admin') }}</option>
                        <option value="projects_officer">{{ __('messages.role_projects_officer') }}</option>
                        <option value="alumni_officer">{{ __('messages.role_alumni_officer') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.password') }} (افتراضي: password)</label>
                    <input type="password" name="password" placeholder="******" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeAddUserModal()" class="px-4 py-2.5 border border-border text-muted-foreground hover:bg-muted text-xs font-bold rounded-xl transition-all cursor-pointer">
                    إلغاء
                </button>
                <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl shadow-md transition-all cursor-pointer">
                    {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Import Users Modal -->
<div id="importUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-card border border-border w-full max-w-lg rounded-2xl shadow-xl overflow-hidden animate-scale-up">
        <div class="p-6 border-b border-border flex items-center justify-between">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide">
                {{ __('messages.import_users') }}
            </h3>
            <button onclick="closeImportUserModal()" class="text-muted-foreground hover:text-foreground cursor-pointer">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-foreground mb-2">اختر ملف إكسل للاستيراد (.xlsx, .xls, .csv)</label>
                <input type="file" name="file" accept=".xlsx, .xls, .csv" required class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
            </div>

            <!-- Formatting Guide -->
            <div class="p-4 bg-muted/40 border border-border rounded-xl text-xs space-y-2">
                <p class="font-bold text-foreground">التعليمات وتنسيق ملف Excel المقبول:</p>
                <p class="text-muted-foreground text-[11px] leading-relaxed">
                    يمكنك رفع ملف <strong class="text-foreground">Excel (.xlsx / .xls)</strong> أو CSV يحتوي على الترويسة التالية:
                </p>
                <div class="bg-card border border-border rounded-lg p-2 font-mono text-[10px] text-foreground overflow-x-auto text-center" dir="ltr">
                    name_ar, name_en, email, academic_id, phone, department, role
                </div>
                <ul class="list-disc list-inside text-muted-foreground text-[10px] space-y-1 mt-2">
                    <li><strong class="text-foreground">الأدوار المتاحة:</strong> student (طالب)، alumnus (خريج)، supervisor (مشرف)، admin (إدارة القسم)، super_admin (سوبر أدمن).</li>
                    <li>يجب توفير <strong class="text-foreground">الرقم الأكاديمي (academic_id)</strong> للطلاب والخريجين.</li>
                    <li>سيتم تعيين كلمة المرور الافتراضية للحسابات المستوردة لتكون: <strong class="text-foreground">password</strong>.</li>
                    <li>تُقَبَل المسميات العربية للأعمدة والأدوار تلقائياً (مثلاً: الاسم عربي، البريد، الرقم الأكاديمي).</li>
                </ul>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeImportUserModal()" class="px-4 py-2.5 border border-border text-muted-foreground hover:bg-muted text-xs font-bold rounded-xl transition-all cursor-pointer">
                    إلغاء
                </button>
                <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl shadow-md transition-all cursor-pointer">
                    {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-card border border-border w-full max-w-lg rounded-2xl shadow-xl overflow-hidden animate-scale-up">
        <div class="p-6 border-b border-border flex items-center justify-between">
            <h3 class="font-extrabold text-foreground text-sm tracking-wide">
                {{ __('messages.edit_user') }}
            </h3>
            <button onclick="closeEditUserModal()" class="text-muted-foreground hover:text-foreground cursor-pointer">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="editUserForm" action="" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.name_ar') }}</label>
                    <input type="text" name="name_ar" id="edit_name_ar" required class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.name_en') }}</label>
                    <input type="text" name="name_en" id="edit_name_en" required class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.email') }}</label>
                <input type="email" name="email" id="edit_email" required class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.phone') }}</label>
                    <input type="text" name="phone" id="edit_phone" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.filter_dept') }}</label>
                    <select name="department" id="edit_department" class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="Dentistry">طب وجراحة الفم والأسنان</option>
                        <option value="Pharmacy">الصيدلة العامة</option>
                        <option value="Clinical Pharmacy">الصيدلة السريرية</option>
                        <option value="Medical Labs">المختبرات الطبية</option>
                        <option value="Anesthesia">تكنولوجيا التخدير</option>
                        <option value="Respiratory Care">الرعاية التنفسية</option>
                        <option value="Optometry">البصريات وعلوم الرؤية</option>
                        <option value="Physical Therapy">العلاج الطبيعي والتأهيل</option>
                        <option value="Nursing">التمريض والقِبالة</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-extrabold text-muted-foreground uppercase mb-1.5">{{ __('messages.role') }}</label>
                <select name="role" id="edit_role" required class="w-full text-xs font-semibold px-3.5 py-2.5 bg-card text-foreground border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                    <option value="student">{{ __('messages.role_student') }}</option>
                    <option value="supervisor">{{ __('messages.role_supervisor') }}</option>
                    <option value="alumnus">{{ __('messages.role_alumni') }}</option>
                    <option value="admin">{{ __('messages.role_admin') }}</option>
                    <option value="super_admin">{{ __('messages.role_super_admin') }}</option>
                    <option value="projects_officer">{{ __('messages.role_projects_officer') }}</option>
                    <option value="alumni_officer">{{ __('messages.role_alumni_officer') }}</option>
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditUserModal()" class="px-4 py-2.5 border border-border text-muted-foreground hover:bg-muted text-xs font-bold rounded-xl transition-all cursor-pointer">
                    إلغاء
                </button>
                <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl shadow-md transition-all cursor-pointer">
                    {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchUserTab(tabId) {
        document.querySelectorAll('.user-tab-content').forEach(el => {
            el.classList.add('hidden');
        });
        document.getElementById(tabId).classList.remove('hidden');

        document.querySelectorAll('.user-tab-btn').forEach(btn => {
            btn.classList.remove('border-primary', 'text-primary', 'bg-primary/10');
            btn.classList.add('border-transparent', 'text-muted-foreground');
        });

        const activeBtn = document.getElementById('btn-' + tabId);
        activeBtn.classList.remove('border-transparent', 'text-muted-foreground');
        activeBtn.classList.add('border-primary', 'text-primary', 'bg-primary/10');
    }

    function openAddUserModal() {
        document.getElementById('addUserModal').classList.remove('hidden');
        document.getElementById('addUserModal').classList.add('flex');
    }

    function closeAddUserModal() {
        document.getElementById('addUserModal').classList.remove('flex');
        document.getElementById('addUserModal').classList.add('hidden');
    }

    function openEditUserModal(user) {
        document.getElementById('editUserForm').action = '/dashboard/users/' + user.id + '/update';
        document.getElementById('edit_name_ar').value = user.name_ar;
        document.getElementById('edit_name_en').value = user.name_en;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_phone').value = user.phone || '';
        document.getElementById('edit_department').value = user.department || '';
        document.getElementById('edit_role').value = user.role;

        document.getElementById('editUserModal').classList.remove('hidden');
        document.getElementById('editUserModal').classList.add('flex');
    }

    function closeEditUserModal() {
        document.getElementById('editUserModal').classList.remove('flex');
        document.getElementById('editUserModal').classList.add('hidden');
    }

    function openImportUserModal() {
        document.getElementById('importUserModal').classList.remove('hidden');
        document.getElementById('importUserModal').classList.add('flex');
    }

    function closeImportUserModal() {
        document.getElementById('importUserModal').classList.remove('flex');
        document.getElementById('importUserModal').classList.add('hidden');
    }
</script>
@endsection
