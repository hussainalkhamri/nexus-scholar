<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\AlumniProfile;
use App\Models\DocRequest;
use App\Models\Incentive;
use App\Models\Comment;
use App\Models\ProjectFile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Covers all roles, stages, statuses, and edge cases for comprehensive testing.
     */
    public function run(): void
    {
        $passHash = Hash::make('password');

        DB::transaction(function () use ($passHash) {

        // =========================================================================
        // 1. CORE ADMIN & OFFICER ACCOUNTS
        // =========================================================================

        $superAdmin = User::create([
            'name'        => 'Super Administrator',
            'name_ar'     => 'مدير النظام العام',
            'name_en'     => 'Super Administrator',
            'email'       => 'superadmin@nexus-scholar.edu',
            'password'    => $passHash,
            'role'        => 'super_admin',
            'department'  => 'Dentistry',
            'phone'       => '771000001',
        ]);

        $admin = User::create([
            'name'        => 'Department Admin',
            'name_ar'     => 'إدارة القسم',
            'name_en'     => 'Department Admin',
            'email'       => 'admin@nexus-scholar.edu',
            'password'    => $passHash,
            'role'        => 'admin',
            'department'  => 'Dentistry',
            'phone'       => '771000002',
        ]);

        $projectsOfficer = User::create([
            'name'        => 'Projects Officer',
            'name_ar'     => 'مسؤول مشاريع التخرج',
            'name_en'     => 'Projects Officer',
            'email'       => 'projectsofficer@nexus-scholar.edu',
            'password'    => $passHash,
            'role'        => 'projects_officer',
            'department'  => 'Pharmacy',
            'phone'       => '771000003',
        ]);

        $alumniOfficer = User::create([
            'name'        => 'Alumni Officer',
            'name_ar'     => 'مسؤول شؤون الخريجين',
            'name_en'     => 'Alumni Officer',
            'email'       => 'alumniofficer@nexus-scholar.edu',
            'password'    => $passHash,
            'role'        => 'alumni_officer',
            'department'  => 'Clinical Pharmacy',
            'phone'       => '771000004',
        ]);

        // =========================================================================
        // 2. SUPERVISORS (12 total — one primary test account)
        // =========================================================================

        $depts = [
            'Dentistry', 'Pharmacy', 'Clinical Pharmacy', 'Medical Labs',
            'Anesthesia', 'Respiratory Care', 'Optometry', 'Physical Therapy', 'Nursing',
        ];

        $supervisorData = [
            ['ar' => 'د. أحمد المنصور',    'en' => 'Dr. Ahmad Al-Mansour',   'dept' => 'Clinical Pharmacy', 'email' => 'supervisor@nexus-scholar.edu', 'phone' => '771001001'],
            ['ar' => 'د. سارة الزبيدي',    'en' => 'Dr. Sarah Al-Zubaidi',   'dept' => 'Dentistry',         'email' => 'supervisor2@nexus-scholar.edu','phone' => '771001002'],
            ['ar' => 'د. خالد العمري',      'en' => 'Dr. Khaled Al-Omari',    'dept' => 'Medical Labs',       'email' => 'supervisor3@nexus-scholar.edu','phone' => '771001003'],
            ['ar' => 'د. ريم الحسيني',      'en' => 'Dr. Reem Al-Husseini',   'dept' => 'Anesthesia',         'email' => 'supervisor4@nexus-scholar.edu','phone' => '771001004'],
            ['ar' => 'د. عماد الشامي',      'en' => 'Dr. Emad Al-Shami',      'dept' => 'Nursing',            'email' => 'supervisor5@nexus-scholar.edu','phone' => '771001005'],
            ['ar' => 'د. هدى ناصر',         'en' => 'Dr. Huda Nasser',        'dept' => 'Pharmacy',           'email' => 'supervisor6@nexus-scholar.edu','phone' => '771001006'],
            ['ar' => 'د. وائل القحطاني',    'en' => 'Dr. Wael Al-Qahtani',    'dept' => 'Respiratory Care',   'email' => 'supervisor7@nexus-scholar.edu','phone' => '771001007'],
            ['ar' => 'د. ليلى الكبسي',      'en' => 'Dr. Layla Al-Kibsi',     'dept' => 'Optometry',          'email' => 'supervisor8@nexus-scholar.edu','phone' => '771001008'],
            ['ar' => 'د. سامي السلمي',      'en' => 'Dr. Sami Al-Salmi',      'dept' => 'Physical Therapy',   'email' => 'supervisor9@nexus-scholar.edu','phone' => '771001009'],
            ['ar' => 'د. منى البريهي',      'en' => 'Dr. Mona Al-Buraihi',    'dept' => 'Clinical Pharmacy',  'email' => 'supervisor10@nexus-scholar.edu','phone' => '771001010'],
            ['ar' => 'د. طارق الأهدل',      'en' => 'Dr. Tariq Al-Ahdal',     'dept' => 'Dentistry',          'email' => 'supervisor11@nexus-scholar.edu','phone' => '771001011'],
            ['ar' => 'د. فاطمة الإرياني',  'en' => 'Dr. Fatima Al-Iryani',   'dept' => 'Pharmacy',           'email' => 'supervisor12@nexus-scholar.edu','phone' => '771001012'],
        ];

        $supervisors = [];
        foreach ($supervisorData as $s) {
            $supervisors[] = User::create([
                'name'       => $s['en'],
                'name_ar'    => $s['ar'],
                'name_en'    => $s['en'],
                'email'      => $s['email'],
                'password'   => $passHash,
                'role'       => 'supervisor',
                'department' => $s['dept'],
                'phone'      => $s['phone'],
            ]);
        }

        // =========================================================================
        // 3. PRIMARY TEST STUDENT ACCOUNT
        // =========================================================================

        $primaryStudent = User::create([
            'name'        => 'Ali Al-Saqqaf',
            'name_ar'     => 'علي السقاف',
            'name_en'     => 'Ali Al-Saqqaf',
            'email'       => 'student@nexus-scholar.edu',
            'academic_id' => '441001',
            'password'    => $passHash,
            'role'        => 'student',
            'department'  => 'Clinical Pharmacy',
            'phone'       => '771002001',
        ]);

        // =========================================================================
        // 4. PRIMARY TEST ALUMNI ACCOUNT
        // =========================================================================

        $primaryAlumnus = User::create([
            'name'        => 'Mohammed Al-Absi',
            'name_ar'     => 'محمد العبسي',
            'name_en'     => 'Mohammed Al-Absi',
            'email'       => 'alumni@nexus-scholar.edu',
            'academic_id' => '442001',
            'password'    => $passHash,
            'role'        => 'alumnus',
            'department'  => 'Dentistry',
            'phone'       => '771003001',
            'linkedin_url'=> 'https://linkedin.com/in/mohammed-alabsi',
            'headline'    => 'طبيب أسنان | مستشفى الثورة العام',
            'experience'  => ['طبيب أسنان مقيم - مستشفى الثورة العام (2024 - حتى الآن)', 'متدرب كلي طب الأسنان (2023)'],
            'achievements'=> ['تخرج بمرتبة الشرف', 'جائزة أفضل بحث تخرج 2024'],
        ]);

        AlumniProfile::updateOrCreate(
            ['user_id' => $primaryAlumnus->id],
            [
                'graduation_year' => 2024,
                'birth_place'     => 'أمانة العاصمة',
                'birth_date'      => date('2000-m-d'),
                'enrollment_year' => 2020,
                'workplace'       => 'مستشفى الثورة العام',
                'position'        => 'طبيب أسنان مقيم',
                'employed'        => true,
                'skills'          => ['Dental Surgery', 'Patient Care', 'X-Ray Diagnosis', 'Oral Hygiene', 'Periodontics'],
                'points'          => 250,
                'linkedin_url'    => 'https://linkedin.com/in/mohammed-alabsi',
            ]
        );

        // =========================================================================
        // 5. ADDITIONAL ALUMNI (29 more for a total of 30)
        // =========================================================================

        $alumniNames = [
            ['ar' => 'أحمد الشرعبي',   'en' => 'Ahmed Al-Sharabi',    'dept' => 'Pharmacy',         'year' => 2023, 'job' => 'صيدلي سريري',           'work' => 'مستشفى الجمهوري التعليمي', 'employed' => true],
            ['ar' => 'ياسر الحداد',    'en' => 'Yasser Al-Haddad',    'dept' => 'Medical Labs',     'year' => 2022, 'job' => 'أخصائي مختبرات طبية',  'work' => 'مختبرات العولقي', 'employed' => true],
            ['ar' => 'أمل الجوفي',     'en' => 'Amal Al-Joufi',       'dept' => 'Anesthesia',       'year' => 2023, 'job' => 'أخصائي تخدير',         'work' => 'مستشفى الجمهوري', 'employed' => true],
            ['ar' => 'نور الحمادي',    'en' => 'Noor Al-Hammadi',     'dept' => 'Nursing',          'year' => 2021, 'job' => null,                   'work' => null, 'employed' => false],
            ['ar' => 'سعيد الأكوع',    'en' => 'Said Al-Akwa',        'dept' => 'Respiratory Care', 'year' => 2022, 'job' => 'أخصائي رعاية تنفسية', 'work' => 'وحدة عناية مركزة', 'employed' => true],
            ['ar' => 'بشير اليافعي',   'en' => 'Bashir Al-Yafai',     'dept' => 'Optometry',        'year' => 2020, 'job' => 'أخصائي بصريات',        'work' => 'مركز العيون الطبي', 'employed' => true],
            ['ar' => 'إيمان النهاري',  'en' => 'Iman Al-Nahari',      'dept' => 'Physical Therapy', 'year' => 2023, 'job' => 'أخصائي علاج طبيعي',    'work' => 'عيادة الأمل للعلاج', 'employed' => true],
            ['ar' => 'زينب الصيادي',   'en' => 'Zainab Al-Sayyadi',   'dept' => 'Clinical Pharmacy','year' => 2024, 'job' => null,                   'work' => null, 'employed' => false],
            ['ar' => 'حسام المخلافي',  'en' => 'Houssam Al-Mikhlafi', 'dept' => 'Dentistry',        'year' => 2021, 'job' => 'طبيب أسنان',           'work' => 'عيادة أسنان خاصة', 'employed' => true],
            ['ar' => 'رنا العولقي',    'en' => 'Rana Al-Awlaqi',      'dept' => 'Pharmacy',         'year' => 2022, 'job' => 'صيدلانية',             'work' => 'صيدلية الشفاء', 'employed' => true],
            ['ar' => 'بشرى الصيادي',   'en' => 'Bushra Al-Sayyadi',   'dept' => 'Medical Labs',     'year' => 2020, 'job' => null,                   'work' => null, 'employed' => false],
            ['ar' => 'طه الحكيمي',     'en' => 'Taha Al-Hakimi',      'dept' => 'Nursing',          'year' => 2021, 'job' => 'ممرض قانوني',          'work' => 'مستشفى الثورة', 'employed' => true],
            ['ar' => 'سلمى القرشي',    'en' => 'Salma Al-Qurashi',    'dept' => 'Anesthesia',       'year' => 2022, 'job' => 'أخصائية تخدير',        'work' => 'مستشفى خاص', 'employed' => true],
            ['ar' => 'فيصل الدبعي',    'en' => 'Faisal Al-Daba\'i',   'dept' => 'Respiratory Care', 'year' => 2023, 'job' => null,                   'work' => null, 'employed' => false],
            ['ar' => 'مريم العامري',   'en' => 'Mariam Al-Ameri',     'dept' => 'Optometry',        'year' => 2020, 'job' => 'أخصائية بصريات',       'work' => 'مركز رؤية', 'employed' => true],
            ['ar' => 'لطفي الأسد',     'en' => 'Lutfi Al-Asad',       'dept' => 'Physical Therapy', 'year' => 2024, 'job' => 'أخصائي تأهيل',         'work' => 'مركز صحة خاص', 'employed' => true],
            ['ar' => 'عزة المقدشي',    'en' => 'Azza Al-Maqdashi',    'dept' => 'Dentistry',        'year' => 2021, 'job' => null,                   'work' => null, 'employed' => false],
            ['ar' => 'حمد النقيب',     'en' => 'Hamad Al-Naqib',      'dept' => 'Clinical Pharmacy','year' => 2022, 'job' => 'صيدلي سريري',           'work' => 'وزارة الصحة', 'employed' => true],
            ['ar' => 'منيرة الزيدي',   'en' => 'Munira Al-Zaidi',     'dept' => 'Pharmacy',         'year' => 2023, 'job' => 'صيدلانية',             'work' => 'صيدلية حكومية', 'employed' => true],
            ['ar' => 'محمد الصريمي',   'en' => 'Mohammed Al-Suraimi', 'dept' => 'Medical Labs',     'year' => 2020, 'job' => 'أخصائي مختبرات',       'work' => 'مختبرات خاصة', 'employed' => true],
            ['ar' => 'دينا النمر',     'en' => 'Dina Al-Namr',        'dept' => 'Nursing',          'year' => 2024, 'job' => null,                   'work' => null, 'employed' => false],
            ['ar' => 'حيدر المقطري',  'en' => 'Haidar Al-Maqtari',   'dept' => 'Anesthesia',       'year' => 2021, 'job' => 'أخصائي تخدير',         'work' => 'مستشفى تعز', 'employed' => true],
            ['ar' => 'لمياء الحداد',   'en' => 'Lamia Al-Haddad',     'dept' => 'Respiratory Care', 'year' => 2022, 'job' => 'أخصائية تنفسية',       'work' => 'مركز صحي', 'employed' => true],
            ['ar' => 'محمود الهمداني', 'en' => 'Mahmoud Al-Hamdani',  'dept' => 'Optometry',        'year' => 2023, 'job' => null,                   'work' => null, 'employed' => false],
            ['ar' => 'نجلاء الرازحي',  'en' => 'Najla Al-Razahi',     'dept' => 'Physical Therapy', 'year' => 2020, 'job' => 'أخصائية علاج طبيعي',  'work' => 'عيادة خاصة', 'employed' => true],
            ['ar' => 'أسامة الحجوري', 'en' => 'Osama Al-Hajoori',    'dept' => 'Dentistry',        'year' => 2021, 'job' => 'طبيب أسنان',           'work' => 'مركز أسنان حضرموت', 'employed' => true],
            ['ar' => 'خلود الشامي',    'en' => 'Kholoud Al-Shami',    'dept' => 'Clinical Pharmacy','year' => 2022, 'job' => null,                   'work' => null, 'employed' => false],
            ['ar' => 'غانم الزياني',   'en' => 'Ghanem Al-Zayani',    'dept' => 'Pharmacy',         'year' => 2023, 'job' => 'صيدلي',                'work' => 'مستشفى عدن', 'employed' => true],
            ['ar' => 'صفاء القدسي',    'en' => 'Safaa Al-Qudsi',      'dept' => 'Medical Labs',     'year' => 2024, 'job' => 'أخصائية تحاليل',       'work' => 'مختبرات المدينة', 'employed' => true],
        ];

        $alumniList = [$primaryAlumnus];
        $allSkillSets = [
            ['Patient Care', 'Medical Diagnostics', 'Pharmacology'],
            ['Dental Surgery', 'Oral Hygiene', 'X-Ray Diagnosis'],
            ['Clinical Lab', 'PCR Testing', 'Blood Analysis'],
            ['Anaesthetic Safety', 'ICU Support', 'Ventilator Management'],
            ['Respiratory Therapy', 'Pulmonary Function', 'Aerosol Therapy'],
            ['Refraction Testing', 'Low Vision Rehab', 'Contact Lenses'],
            ['Manual Therapy', 'Rehabilitation', 'Sports Medicine'],
            ['Nursing Care', 'Wound Management', 'Patient Education'],
            ['Drug Dispensing', 'Pharmacovigilance', 'Clinical Pharmacy'],
        ];

        foreach ($alumniNames as $idx => $a) {
            $user = User::create([
                'name'        => $a['en'],
                'name_ar'     => $a['ar'],
                'name_en'     => $a['en'],
                'email'       => 'alumni' . ($idx + 2) . '@nexus-scholar.edu',
                'academic_id' => '44200' . str_pad($idx + 2, 2, '0', STR_PAD_LEFT),
                'password'    => $passHash,
                'role'        => 'alumnus',
                'department'  => $a['dept'],
                'phone'       => '77100' . str_pad(3000 + $idx, 4, '0', STR_PAD_LEFT),
            ]);
            $alumniList[] = $user;

            $deptIdx = array_search($a['dept'], $depts) ?? 0;
            $birthPlaces = ['المحويت', 'صنعاء', 'أمانة العاصمة', 'ذمار', 'عمران', 'حجة', 'إب', 'تعز', 'المهرة'];
            $issuers = ['التميز الأهلية/المحويت', 'أبوبكر الصديق/الميناء', 'الرواد الأهلية/ذمار', 'العالمية الأهلية/معين', 'الثورة/المشنة'];

            AlumniProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'graduation_year'           => $a['year'],
                    'birth_place'               => $birthPlaces[$idx % count($birthPlaces)],
                    'birth_date'                => '2001-0' . (($idx % 9) + 1) . '-' . (10 + ($idx * 3) % 18),
                    'enrollment_year'           => $a['year'] - 4,
                    'graduation_session'        => ($idx % 3 == 0) ? 'ثاني' : 'أول',
                    'prev_qualification_type'   => ($idx % 5 == 0) ? 'دبلوم ثلاث سنوات' : 'ثانوية عامة علمي',
                    'prev_qualification_year'   => $a['year'] - 5,
                    'prev_qualification_issuer' => $issuers[$idx % count($issuers)],
                    'prev_qualification_gpa'    => 75.00 + (($idx * 3.7) % 23.5),
                    'workplace'                 => $a['work'],
                    'position'                  => $a['job'],
                    'employed'                  => $a['employed'],
                    'skills'                    => $allSkillSets[$deptIdx % count($allSkillSets)],
                    'points'                    => 50 + ($idx * 15) % 300,
                    'linkedin_url'              => $a['employed'] ? 'https://linkedin.com/in/' . Str::slug($a['en']) : null,
                ]
            );
        }

        // =========================================================================
        // 6. GRADUATION GROUPS — ALL STAGES AND STATUSES
        // =========================================================================

        $groupDefinitions = [
            // ID 1: GP-1 — Primary student's group, proposal/pending (active)
            [
                'title_ar'  => 'تقييم انتشار تسوس الأسنان واستراتيجيات الوقاية',
                'title_en'  => 'Dental Caries Prevalence and Prevention Strategies',
                'dept'      => 'Clinical Pharmacy',
                'supIdx'    => 0, // د. أحمد المنصور (primary supervisor)
                'stage'     => 'proposal',
                'status'    => 'pending',
                'progress'  => 20,
                'days'      => 5,
                'semester'  => '2025-2026/1',
                'comments'  => [
                    ['role' => 'supervisor', 'text' => 'المشروع في مرحلة المقترح، يرجى رفع الملخص التنفيذي.'],
                    ['role' => 'student', 'text' => 'تم استلام الملاحظات، سنعمل على المقترح خلال الأسبوع القادم.'],
                    ['role' => 'supervisor', 'text' => 'ممتاز، أرجو مراجعة المراجع العلمية من مصادر حديثة (2020-2024).'],
                ],
                'students'  => [
                    ['ar' => 'علي السقاف',      'en' => 'Ali Al-Saqqaf',   'id' => '441001', 'is_primary' => true],
                    ['ar' => 'مصطفى العبادي',  'en' => 'Mustafa Al-Abadi','id' => '441002', 'dept' => 'Clinical Pharmacy'],
                    ['ar' => 'هبة الصرمي',     'en' => 'Heba Al-Surami',  'id' => '441003', 'dept' => 'Clinical Pharmacy'],
                ],
            ],
            // ID 2: GP-2 — chapters/processing with files
            [
                'title_ar'  => 'الآثار الجانبية للأدوية لدى مرضى الأمراض المزمنة',
                'title_en'  => 'Adverse Drug Reactions in Chronic Disease Patients',
                'dept'      => 'Pharmacy',
                'supIdx'    => 1, // د. سارة الزبيدي
                'stage'     => 'chapters',
                'status'    => 'processing',
                'progress'  => 50,
                'days'      => 12,
                'semester'  => '2025-2026/1',
                'comments'  => [
                    ['role' => 'supervisor', 'text' => 'الفصل الثاني يحتاج إلى مزيد من المراجع في قسم الدراسات السابقة.'],
                    ['role' => 'student', 'text' => 'تم إضافة 8 مراجع جديدة وإعادة كتابة المقدمة.'],
                    ['role' => 'supervisor', 'text' => 'جيد جداً، يمكن الانتقال إلى الفصل الثالث (المنهجية).'],
                    ['role' => 'student', 'text' => 'هل يمكن تحديد موعد لمراجعة الفصل الثالث؟'],
                ],
                'students'  => [
                    ['ar' => 'كريم العمراني',  'en' => 'Kareem Al-Omrani',   'id' => '441010', 'dept' => 'Pharmacy'],
                    ['ar' => 'لمياء الزرقاء',  'en' => 'Lamia Al-Zarqa',     'id' => '441011', 'dept' => 'Pharmacy'],
                    ['ar' => 'سيف الحربي',     'en' => 'Saif Al-Harbi',      'id' => '441012', 'dept' => 'Pharmacy'],
                ],
            ],
            // ID 3: GP-3 — draft/processing with plagiarism within limit (15%)
            [
                'title_ar'  => 'معدل انتشار جرثومة المعدة الحلزونية وعلاقتها بنمط الحياة',
                'title_en'  => 'Prevalence and Risk Factors of H. pylori Infections',
                'dept'      => 'Medical Labs',
                'supIdx'    => 2, // د. خالد العمري
                'stage'     => 'draft',
                'status'    => 'processing',
                'progress'  => 75,
                'days'      => 8,
                'semester'  => '2025-2026/1',
                'plagiarism' => 15,
                'comments'  => [
                    ['role' => 'supervisor', 'text' => 'نسبة الاستلال 15% مقبولة، المسودة جاهزة للمراجعة النهائية.'],
                    ['role' => 'student', 'text' => 'شكراً دكتور، متى يتم رفع المسودة للمناقشة؟'],
                    ['role' => 'supervisor', 'text' => 'بعد توقيع لجنة المناقشة، سنحدد الموعد خلال أسبوعين.'],
                ],
                'students'  => [
                    ['ar' => 'نادية المنصوري', 'en' => 'Nadia Al-Mansoori', 'id' => '441020', 'dept' => 'Medical Labs'],
                    ['ar' => 'فهد السهلي',     'en' => 'Fahd Al-Sahli',     'id' => '441021', 'dept' => 'Medical Labs'],
                ],
            ],
            // ID 4: GP-4 — draft/processing with plagiarism OVER limit (28%) — blocked advancement
            [
                'title_ar'  => 'الموثوقية التشخيصية للمؤشرات الدموية في الكشف عن الأنيميا',
                'title_en'  => 'Diagnostic Reliability of Hematological Markers in Anemia Detection',
                'dept'      => 'Medical Labs',
                'supIdx'    => 2, // د. خالد العمري
                'stage'     => 'draft',
                'status'    => 'processing',
                'progress'  => 60,
                'days'      => 20,
                'semester'  => '2025-2026/1',
                'plagiarism' => 28,
                'comments'  => [
                    ['role' => 'supervisor', 'text' => 'تحذير: نسبة الاستلال (28%) تتجاوز الحد المسموح. يجب إعادة كتابة الأقسام المنقولة.'],
                    ['role' => 'student', 'text' => 'نعم دكتور، نعمل على تعديل المقاطع المكررة وإعادة صياغتها بالكامل.'],
                    ['role' => 'supervisor', 'text' => 'لديكم أسبوعان لتقليل النسبة دون 20% وإلا سيُرجأ المشروع.'],
                ],
                'students'  => [
                    ['ar' => 'عبدالرحمن الشيبي', 'en' => 'Abdulrahman Al-Shaibi', 'id' => '441030', 'dept' => 'Medical Labs'],
                    ['ar' => 'وداد الهواري',      'en' => 'Widad Al-Hawari',       'id' => '441031', 'dept' => 'Medical Labs'],
                ],
            ],
            // ID 5: GP-5 — defense/processing + passed_revisions (needs revisions)
            [
                'title_ar'  => 'التدبير التخديري للمرضى الأطفال في الجراحات الكبرى',
                'title_en'  => 'Anesthetic Management in Pediatric Major Surgery',
                'dept'      => 'Anesthesia',
                'supIdx'    => 3, // د. ريم الحسيني
                'stage'     => 'defense',
                'status'    => 'processing',
                'progress'  => 90,
                'days'      => 15,
                'semester'  => '2025-2026/1',
                'plagiarism' => 12,
                'grade'     => 'passed_revisions',
                'defense_committee' => 'أ.د. عمر الزيدي، د. سارة المطري، د. حمد النصري',
                'defense_date' => '2026-05-15 10:00:00',
                'comments'  => [
                    ['role' => 'supervisor', 'text' => 'المناقشة اجريت بنجاح، لكن اللجنة طلبت تعديل الجداول الإحصائية وإضافة مقارنة دولية.'],
                    ['role' => 'student', 'text' => 'استلمنا ملاحظات اللجنة، سنبدأ التعديلات فوراً وتسليم النسخة المعدلة خلال أسبوع.'],
                    ['role' => 'supervisor', 'text' => 'راجعوا تحديداً الفصل الخامس (النتائج والمناقشة) بالكامل.'],
                ],
                'students'  => [
                    ['ar' => 'تيسير القيسي',    'en' => 'Tayseer Al-Qaisi',   'id' => '441040', 'dept' => 'Anesthesia'],
                    ['ar' => 'أروى المطري',     'en' => 'Arwa Al-Matri',      'id' => '441041', 'dept' => 'Anesthesia'],
                    ['ar' => 'سليمان الشرقي',  'en' => 'Suleiman Al-Sharqi', 'id' => '441042', 'dept' => 'Anesthesia'],
                ],
            ],
            // ID 6: GP-6 — defense/completed + passed → students converted to alumni
            [
                'title_ar'  => 'تقييم انتشار تسوس الأسنان واستراتيجيات الوقاية في المدارس الابتدائية',
                'title_en'  => 'Dental Caries in Primary Schools: Assessment and Prevention',
                'dept'      => 'Dentistry',
                'supIdx'    => 1, // د. سارة الزبيدي
                'stage'     => 'defense',
                'status'    => 'completed',
                'progress'  => 100,
                'days'      => 2,
                'semester'  => '2024-2025/2',
                'plagiarism' => 8,
                'grade'     => 'passed',
                'defense_committee' => 'أ.د. أحمد الحسيني، د. منى البريهي، د. طارق الأهدل',
                'defense_date' => '2025-12-10 09:00:00',
                'comments'  => [
                    ['role' => 'supervisor', 'text' => 'تهانينا! اجتاز المشروع المناقشة بتقدير ممتاز. عمل رائع.'],
                    ['role' => 'student', 'text' => 'شكراً جزيلاً دكتور، لولا توجيهاتكم ما وصلنا لهذا.'],
                    ['role' => 'supervisor', 'text' => 'تم تحويل صفتكم رسمياً إلى خريجين. بالتوفيق في مسيرتكم المهنية.'],
                ],
                'students'  => [
                    ['ar' => 'بدر الغامدي',    'en' => 'Badr Al-Ghamdi',    'id' => '441050', 'dept' => 'Dentistry', 'convert_to_alumni' => true],
                    ['ar' => 'شيماء الحارثي', 'en' => 'Sheima Al-Harthi',  'id' => '441051', 'dept' => 'Dentistry', 'convert_to_alumni' => true],
                ],
            ],
            // ID 7: GP-7 — proposal/overdue (stuck for too long)
            [
                'title_ar'  => 'جودة الرعاية التمريضية في أقسام الولادة والنساء',
                'title_en'  => 'Nursing Care Quality in Maternity and Gynecology Wards',
                'dept'      => 'Nursing',
                'supIdx'    => 4, // د. عماد الشامي
                'stage'     => 'proposal',
                'status'    => 'overdue',
                'progress'  => 15,
                'days'      => 35,
                'semester'  => '2025-2026/1',
                'comments'  => [
                    ['role' => 'supervisor', 'text' => 'تحذير: المشروع تأخر 35 يوماً في مرحلة المقترح. يجب تسليم المقترح هذا الأسبوع.'],
                    ['role' => 'student', 'text' => 'نعتذر دكتور، هناك ظروف خاصة. سنرفع المقترح خلال يومين.'],
                ],
                'students'  => [
                    ['ar' => 'جمانة الحيمي',   'en' => 'Jumana Al-Haimi',   'id' => '441060', 'dept' => 'Nursing'],
                    ['ar' => 'حمزة الوريث',    'en' => 'Hamza Al-Warith',   'id' => '441061', 'dept' => 'Nursing'],
                ],
            ],
            // ID 8: GP-8 — chapters/processing with active discussion
            [
                'title_ar'  => 'فعالية التهوية غير الباضعة لمرضى الانسداد الرئوي المزمن',
                'title_en'  => 'Efficacy of Non-Invasive Ventilation in COPD Patients',
                'dept'      => 'Respiratory Care',
                'supIdx'    => 6, // د. وائل القحطاني
                'stage'     => 'chapters',
                'status'    => 'processing',
                'progress'  => 45,
                'days'      => 9,
                'semester'  => '2025-2026/1',
                'comments'  => [
                    ['role' => 'supervisor', 'text' => 'الفصل الثاني جيد، لكن أحتاج تفاصيل أكثر في دراسة الحالات.'],
                    ['role' => 'student', 'text' => 'سنضيف 3 حالات إضافية من قاعدة بيانات المستشفى.'],
                ],
                'students'  => [
                    ['ar' => 'سلطان البلوشي', 'en' => 'Sultan Al-Baloshi', 'id' => '441070', 'dept' => 'Respiratory Care'],
                    ['ar' => 'رهام القاسمي',  'en' => 'Reham Al-Qasimi',  'id' => '441071', 'dept' => 'Respiratory Care'],
                ],
            ],
            // ID 9: GP-9 — defense/completed + failed → reset to proposal
            [
                'title_ar'  => 'الأخطاء الانكسارية للرؤية لدى أطفال المدارس الابتدائية',
                'title_en'  => 'Refractive Errors among Primary School Children',
                'dept'      => 'Optometry',
                'supIdx'    => 7, // د. ليلى الكبسي
                'stage'     => 'proposal',
                'status'    => 'pending',
                'progress'  => 20,
                'days'      => 3,
                'semester'  => '2025-2026/2',
                'grade'     => 'failed',
                'comments'  => [
                    ['role' => 'supervisor', 'text' => 'للأسف، رسب المشروع في المناقشة. تم إعادة ضبط المرحلة. يرجى مراجعة التوصيات وإعادة البدء.'],
                    ['role' => 'student', 'text' => 'مفهوم دكتور، سنستفيد من ملاحظات اللجنة ونبدأ من جديد بشكل أفضل.'],
                ],
                'students'  => [
                    ['ar' => 'مروان الشهراني', 'en' => 'Marwan Al-Shahrani', 'id' => '441080', 'dept' => 'Optometry'],
                    ['ar' => 'ميساء الدوسري',  'en' => 'Maisa Al-Dosari',    'id' => '441081', 'dept' => 'Optometry'],
                ],
            ],
            // ID 10-20: More groups with varied stages
            [
                'title_ar'  => 'تأهيل العلاج الطبيعي لمرضى السكتات الدماغية',
                'title_en'  => 'Physical Therapy Rehabilitation after Stroke',
                'dept'      => 'Physical Therapy',
                'supIdx'    => 8, // د. سامي السلمي
                'stage'     => 'chapters',
                'status'    => 'processing',
                'progress'  => 55,
                'days'      => 11,
                'semester'  => '2025-2026/1',
                'comments'  => [
                    ['role' => 'supervisor', 'text' => 'عمل جيد في جمع البيانات الميدانية. الفصل الرابع يحتاج إلى توحيد المصطلحات.'],
                ],
                'students' => [
                    ['ar' => 'أيوب السيد',    'en' => 'Ayoub Al-Sayed',   'id' => '441090', 'dept' => 'Physical Therapy'],
                    ['ar' => 'فوزية النجار',  'en' => 'Fawzia Al-Najjar', 'id' => '441091', 'dept' => 'Physical Therapy'],
                ],
            ],
            [
                'title_ar'  => 'تركيب وتقييم الهلاميات العشبية الدوائية',
                'title_en'  => 'Formulation and Evaluation of Herbal Pharmaceutical Gels',
                'dept'      => 'Pharmacy',
                'supIdx'    => 5, // د. هدى ناصر
                'stage'     => 'draft',
                'status'    => 'processing',
                'progress'  => 70,
                'days'      => 7,
                'semester'  => '2025-2026/1',
                'plagiarism' => 10,
                'students' => [
                    ['ar' => 'جاسم اليامي',   'en' => 'Jasem Al-Yami',    'id' => '441100', 'dept' => 'Pharmacy'],
                    ['ar' => 'وفاء المنجي',   'en' => 'Wafa Al-Munji',    'id' => '441101', 'dept' => 'Pharmacy'],
                ],
                'comments' => [],
            ],
            [
                'title_ar'  => 'التقييم السريري لإساءة استخدام المضادات الحيوية',
                'title_en'  => 'Clinical Evaluation of Antibiotic Misuse',
                'dept'      => 'Clinical Pharmacy',
                'supIdx'    => 9, // د. منى البريهي
                'stage'     => 'defense',
                'status'    => 'completed',
                'progress'  => 100,
                'days'      => 1,
                'semester'  => '2024-2025/2',
                'plagiarism' => 6,
                'grade'     => 'passed',
                'defense_committee' => 'أ.د. عمر المطري، د. خالد العمري، د. ليلى الكبسي',
                'defense_date' => '2026-01-20 11:00:00',
                'comments' => [
                    ['role' => 'supervisor', 'text' => 'أحسنتم! مشروع متميز حقق أعلى تقييم في الدفعة.'],
                ],
                'students'  => [
                    ['ar' => 'مها الزعبي',    'en' => 'Maha Al-Zaabi',    'id' => '441110', 'dept' => 'Clinical Pharmacy', 'convert_to_alumni' => true],
                    ['ar' => 'عمر الكيلاني',  'en' => 'Omar Al-Kilani',   'id' => '441111', 'dept' => 'Clinical Pharmacy', 'convert_to_alumni' => true],
                ],
            ],
            [
                'title_ar'  => 'بروتوكولات الرعاية التنفسية في وحدات العناية المركزة لحديثي الولادة',
                'title_en'  => 'Neonatal ICU Respiratory Care Protocols',
                'dept'      => 'Respiratory Care',
                'supIdx'    => 6,
                'stage'     => 'proposal',
                'status'    => 'pending',
                'progress'  => 20,
                'days'      => 2,
                'semester'  => '2025-2026/2',
                'students' => [
                    ['ar' => 'رشيد الزعابي',  'en' => 'Rashid Al-Zaabi',  'id' => '441120', 'dept' => 'Respiratory Care'],
                    ['ar' => 'سوسن الكندي',   'en' => 'Sawsan Al-Kindi',  'id' => '441121', 'dept' => 'Respiratory Care'],
                ],
                'comments' => [],
            ],
            [
                'title_ar'  => 'التمارين الوقائية لآلام أسفل الظهر لدى الموظفين المكتبيين',
                'title_en'  => 'Ergonomic Exercises for Lower Back Pain in Office Workers',
                'dept'      => 'Physical Therapy',
                'supIdx'    => 8,
                'stage'     => 'chapters',
                'status'    => 'processing',
                'progress'  => 40,
                'days'      => 14,
                'semester'  => '2025-2026/1',
                'students' => [
                    ['ar' => 'تركي الغامدي',  'en' => 'Turki Al-Ghamdi',  'id' => '441130', 'dept' => 'Physical Therapy'],
                    ['ar' => 'ضياء العنزي',   'en' => 'Diaa Al-Anazi',    'id' => '441131', 'dept' => 'Physical Therapy'],
                ],
                'comments' => [
                    ['role' => 'supervisor', 'text' => 'يجب إضافة مجموعة ضابطة في تصميم الدراسة.'],
                ],
            ],
            [
                'title_ar'  => 'تأثير التغذية العلاجية على مستويات السكر لمرضى النوع الثاني',
                'title_en'  => 'Impact of Medical Nutrition Therapy on Type 2 Diabetes',
                'dept'      => 'Clinical Pharmacy',
                'supIdx'    => 0,
                'stage'     => 'draft',
                'status'    => 'processing',
                'progress'  => 78,
                'days'      => 6,
                'semester'  => '2025-2026/1',
                'plagiarism' => 18,
                'students' => [
                    ['ar' => 'وليد الحوراني', 'en' => 'Walid Al-Hourani',  'id' => '441140', 'dept' => 'Clinical Pharmacy'],
                    ['ar' => 'غادة السلمي',   'en' => 'Ghada Al-Salmi',   'id' => '441141', 'dept' => 'Clinical Pharmacy'],
                ],
                'comments' => [
                    ['role' => 'supervisor', 'text' => 'المسودة على الطريق الصحيح، يرجى مراجعة نتائج HbA1c بدقة أكبر.'],
                ],
            ],
            [
                'title_ar'  => 'الحشوات التجميلية في طب الأسنان الحديث',
                'title_en'  => 'Aesthetic Restorations in Modern Dentistry',
                'dept'      => 'Dentistry',
                'supIdx'    => 10, // د. طارق الأهدل
                'stage'     => 'chapters',
                'status'    => 'processing',
                'progress'  => 48,
                'days'      => 10,
                'semester'  => '2025-2026/2',
                'students' => [
                    ['ar' => 'ريان الثقفي',   'en' => 'Rayan Al-Thaqafi', 'id' => '441150', 'dept' => 'Dentistry'],
                    ['ar' => 'دانة العتيبي',  'en' => 'Dana Al-Otaibi',   'id' => '441151', 'dept' => 'Dentistry'],
                ],
                'comments' => [
                    ['role' => 'supervisor', 'text' => 'ممتاز على الفصل الأول، الرجاء إكمال الفصل الثاني قبل نهاية الأسبوع.'],
                ],
            ],
            [
                'title_ar'  => 'بروتوكولات تمريض الطوارئ للتعامل مع السكتة القلبية المفاجئة',
                'title_en'  => 'Emergency Nursing Protocols for Sudden Cardiac Arrest',
                'dept'      => 'Nursing',
                'supIdx'    => 4, // د. عماد الشامي
                'stage'     => 'defense',
                'status'    => 'processing',
                'progress'  => 88,
                'days'      => 18,
                'semester'  => '2025-2026/1',
                'plagiarism' => 9,
                'grade'     => 'passed_revisions',
                'defense_committee' => 'أ.د. وائل القحطاني، د. هدى ناصر، د. سامي السلمي',
                'defense_date' => '2026-06-05 14:00:00',
                'comments' => [
                    ['role' => 'supervisor', 'text' => 'المناقشة تمت، اللجنة طلبت تعديل بروتوكول CPR مع الإشارة إلى معايير AHA 2024.'],
                    ['role' => 'student', 'text' => 'تم الاطلاع على ملاحظات اللجنة، سنعدل البروتوكول هذا الأسبوع.'],
                ],
                'students' => [
                    ['ar' => 'هيا العمراني',  'en' => 'Haya Al-Omrani',   'id' => '441160', 'dept' => 'Nursing'],
                    ['ar' => 'أمير السعيد',   'en' => 'Amir Al-Saeed',    'id' => '441161', 'dept' => 'Nursing'],
                ],
            ],
            [
                'title_ar'  => 'الكشف المبكر عن التهابات الجلد البكتيرية في أقسام الطوارئ',
                'title_en'  => 'Early Detection of Bacterial Skin Infections in Emergency Departments',
                'dept'      => 'Medical Labs',
                'supIdx'    => 2,
                'stage'     => 'chapters',
                'status'    => 'processing',
                'progress'  => 52,
                'days'      => 8,
                'semester'  => '2025-2026/2',
                'students' => [
                    ['ar' => 'نواف المزروع', 'en' => 'Nawaf Al-Mazroo',   'id' => '441170', 'dept' => 'Medical Labs'],
                    ['ar' => 'صبا الحربي',   'en' => 'Saba Al-Harbi',     'id' => '441171', 'dept' => 'Medical Labs'],
                ],
                'comments' => [
                    ['role' => 'supervisor', 'text' => 'بيانات جيدة، الفصل الثالث يحتاج إحصاءات أوضح.'],
                ],
            ],
            [
                'title_ar'  => 'تقنيات التحكم في الألم بعد العمليات الجراحية القيصرية',
                'title_en'  => 'Postoperative Pain Control after Cesarean Section',
                'dept'      => 'Anesthesia',
                'supIdx'    => 3,
                'stage'     => 'proposal',
                'status'    => 'pending',
                'progress'  => 20,
                'days'      => 4,
                'semester'  => '2025-2026/2',
                'students' => [
                    ['ar' => 'سهاد النعيمي',  'en' => 'Suhad Al-Nuaimi',  'id' => '441180', 'dept' => 'Anesthesia'],
                    ['ar' => 'قيس البريك',    'en' => 'Qays Al-Buraik',   'id' => '441181', 'dept' => 'Anesthesia'],
                ],
                'comments' => [],
            ],
            [
                'title_ar'  => 'التأهيل البصري لمرضى ضعف البصر الشديد',
                'title_en'  => 'Visual Rehabilitation in Severe Low Vision Patients',
                'dept'      => 'Optometry',
                'supIdx'    => 7,
                'stage'     => 'defense',
                'status'    => 'completed',
                'progress'  => 100,
                'days'      => 1,
                'semester'  => '2024-2025/2',
                'plagiarism' => 7,
                'grade'     => 'passed',
                'defense_committee' => 'أ.د. خالد العمري، د. فاطمة الإرياني، د. منى البريهي',
                'defense_date' => '2025-11-22 09:30:00',
                'comments' => [
                    ['role' => 'supervisor', 'text' => 'مشروع ممتاز، اجتاز المناقشة بجدارة.'],
                ],
                'students' => [
                    ['ar' => 'حصة الزهراني', 'en' => 'Hessa Al-Zahrani',  'id' => '441190', 'dept' => 'Optometry', 'convert_to_alumni' => true],
                    ['ar' => 'عادل المطيري', 'en' => 'Adel Al-Mutairi',   'id' => '441191', 'dept' => 'Optometry', 'convert_to_alumni' => true],
                ],
            ],
        ];

        $createdGroups = [];
        foreach ($groupDefinitions as $gIdx => $gDef) {
            $sup = $supervisors[$gDef['supIdx']];

            $group = Group::create([
                'title_ar'          => $gDef['title_ar'],
                'title_en'          => $gDef['title_en'],
                'department'        => $gDef['dept'],
                'supervisor'        => $sup->name_ar,
                'supervisor_id'     => $sup->id,
                'stage'             => $gDef['stage'],
                'status'            => $gDef['status'],
                'progress'          => $gDef['progress'],
                'days_in_stage'     => $gDef['days'],
                'semester'          => $gDef['semester'],
                'plagiarism_percent'=> $gDef['plagiarism'] ?? null,
                'grade'             => $gDef['grade'] ?? null,
                'defense_committee' => $gDef['defense_committee'] ?? null,
                'defense_date'      => $gDef['defense_date'] ?? null,
            ]);
            $createdGroups[] = $group;

            // Attach students
            foreach ($gDef['students'] as $stu) {
                if (!empty($stu['is_primary'])) {
                    // Link the primary test student account to this group
                    $group->students()->attach($primaryStudent->id);
                } else {
                    $convertToAlumni = !empty($stu['convert_to_alumni']);
                    $stuRole = $convertToAlumni ? 'alumnus' : 'student';

                    $stuUser = User::create([
                        'name'        => $stu['en'],
                        'name_ar'     => $stu['ar'],
                        'name_en'     => $stu['en'],
                        'email'       => Str::slug($stu['en']) . '-' . $stu['id'] . '@nexus-scholar.edu',
                        'academic_id' => $stu['id'],
                        'password'    => $passHash,
                        'role'        => $stuRole,
                        'department'  => $stu['dept'],
                        'phone'       => '771' . $stu['id'],
                    ]);

                    if ($convertToAlumni) {
                        AlumniProfile::firstOrCreate(
                            ['user_id' => $stuUser->id],
                            [
                                'graduation_year' => 2025,
                                'employed'        => false,
                                'points'          => 100,
                            ]
                        );
                    }

                    $group->students()->attach($stuUser->id);
                }
            }

            // Add comments
            foreach ($gDef['comments'] as $cIdx => $comment) {
                $commenter = ($comment['role'] === 'supervisor') ? $sup : $primaryStudent;
                Comment::create([
                    'group_id'    => $group->id,
                    'user_id'     => $commenter->id,
                    'author_name' => $commenter->name_ar,
                    'content'     => $comment['text'],
                    'created_at'  => now()->subDays(count($gDef['comments']) - $cIdx),
                ]);
            }

            // Add a project file to processing/completed groups
            if (in_array($gDef['status'], ['processing', 'completed']) && $gDef['stage'] !== 'proposal') {
                ProjectFile::create([
                    'group_id'   => $group->id,
                    'user_id'    => $primaryStudent->id,
                    'filename'   => 'research-draft-' . $group->id . '.pdf',
                    'file_path'  => 'project_drafts/research-draft-' . $group->id . '.pdf',
                    'file_size'  => (1 + $gIdx % 5) . '.' . ($gIdx * 7 % 99) . ' MB',
                    'created_at' => now()->subDays(5),
                ]);
            }
        }

        // =========================================================================
        // 7. DOCUMENT REQUESTS
        // =========================================================================

        $docTypes = [
            ['ar' => 'كشف درجات',  'en' => 'Transcript'],
            ['ar' => 'إفادة تخرج', 'en' => 'Graduation Letter'],
            ['ar' => 'وثيقة تخرج', 'en' => 'Diploma Copy'],
            ['ar' => 'إفادة قيد',  'en' => 'Enrollment Letter'],
        ];

        $docStatuses = ['completed', 'processing', 'pending', 'pending', 'overdue'];

        // Primary alumni — two requests (one completed with verification code)
        $completedDoc = DocRequest::create([
            'alumnus_id'        => $primaryAlumnus->id,
            'doc_type_ar'       => 'وثيقة تخرج',
            'doc_type_en'       => 'Diploma Copy',
            'status'            => 'completed',
            'waiting_days'      => 0,
            'verification_code' => 'VERIFY-' . strtoupper(Str::random(12)),
            'file_path'         => 'documents/diploma_' . $primaryAlumnus->id . '.pdf',
            'created_at'        => now()->subDays(30),
        ]);

        DocRequest::create([
            'alumnus_id'  => $primaryAlumnus->id,
            'doc_type_ar' => 'كشف درجات',
            'doc_type_en' => 'Transcript',
            'status'      => 'processing',
            'waiting_days'=> 3,
            'created_at'  => now()->subDays(5),
        ]);

        // Requests for other alumni (varied statuses)
        foreach ($alumniList as $aIdx => $alum) {
            if ($alum->id === $primaryAlumnus->id) continue;
            $numRequests = 1 + ($aIdx % 3);

            for ($r = 0; $r < $numRequests; $r++) {
                $docType = $docTypes[($aIdx + $r) % count($docTypes)];
                $status = $docStatuses[($aIdx + $r) % count($docStatuses)];
                $verificationCode = null;
                $filePath = null;
                if ($status === 'completed') {
                    $verificationCode = 'VERIFY-' . strtoupper(Str::random(12));
                    $filePath = 'documents/doc_' . $alum->id . '_' . $r . '.pdf';
                }

                DocRequest::create([
                    'alumnus_id'        => $alum->id,
                    'doc_type_ar'       => $docType['ar'],
                    'doc_type_en'       => $docType['en'],
                    'status'            => $status,
                    'waiting_days'      => $status === 'completed' ? 0 : (1 + $aIdx % 10),
                    'verification_code' => $verificationCode,
                    'file_path'         => $filePath,
                    'created_at'        => now()->subDays($aIdx * 2 + $r),
                ]);
            }
        }

        // =========================================================================
        // 8. INCENTIVES (10 incentives)
        // =========================================================================

        $incentivesList = [
            [
                'kind'     => 'discount',
                'title_ar' => 'خصم 25% على رسوم الدراسات العليا',
                'title_en' => '25% off Postgraduate Tuition',
                'detail_ar'=> 'للخريجين النشطين بمعدل أعلى من 80% — صالح حتى نهاية 2026',
                'detail_en'=> 'For active alumni with GPA > 80% — valid until end of 2026',
            ],
            [
                'kind'     => 'course',
                'title_ar' => 'دورة مكافحة العدوى والتدابير السريرية المعتمدة',
                'title_en' => 'Accredited Clinical Infection Control Course',
                'detail_ar'=> '30 ساعة معتمدة | ابتداء من أغسطس 2026 | عبر الإنترنت وحضوري',
                'detail_en'=> '30 accredited hours | Starting August 2026 | Online & In-person',
            ],
            [
                'kind'     => 'course',
                'title_ar' => 'ورشة كتابة البحث الطبي العلمي وإعداد بحوث التخرج',
                'title_en' => 'Medical Scientific Writing & Graduation Research Workshop',
                'detail_ar'=> '5 جلسات عملية | الأربعاء والخميس | مجانية للخريجين',
                'detail_en'=> '5 practical sessions | Wednesday & Thursday | Free for alumni',
            ],
            [
                'kind'     => 'job',
                'title_ar' => 'طبيب أسنان مقيم — مستشفى الثورة العام',
                'title_en' => 'Resident Dentist — Al-Thawra General Hospital',
                'detail_ar'=> 'دوام كامل | صنعاء | الراتب: حسب الاتفاق | مطلوب: تخرج 2023-2025',
                'detail_en'=> 'Full-time | Sanaa | Salary: Negotiable | Required: Graduated 2023-2025',
            ],
            [
                'kind'     => 'job',
                'title_ar' => 'أخصائي رعاية تنفسية — وحدة العناية المركزة للأطفال',
                'title_en' => 'Respiratory Care Specialist — Pediatric ICU',
                'detail_ar'=> 'دوام كامل | مستشفى الجمهوري التعليمي | بدل إضافي لساعات الليل',
                'detail_en'=> 'Full-time | Al-Jumhouri Teaching Hospital | Night shift allowance',
            ],
            [
                'kind'     => 'discount',
                'title_ar' => 'خصم 40% على الدبلوم المهني في التخدير السريري',
                'title_en' => '40% off Clinical Anesthesia Professional Diploma',
                'detail_ar'=> 'للخريجين خلال 3 سنوات من التخرج | مع إمكانية التقسيط',
                'detail_en'=> 'For alumni within 3 years of graduation | Installment available',
            ],
            [
                'kind'     => 'course',
                'title_ar' => 'برنامج مهارات التواصل والعلاقات مع المرضى',
                'title_en' => 'Patient Communication & Relationship Skills Program',
                'detail_ar'=> '2 أيام مكثفة | شهادة معتمدة | مجانية لخريجي 2024',
                'detail_en'=> '2 intensive days | Accredited certificate | Free for 2024 graduates',
            ],
            [
                'kind'     => 'job',
                'title_ar' => 'صيدلاني سريري — مستشفى الجمهوري التعليمي',
                'title_en' => 'Clinical Pharmacist — Al-Jumhouri Teaching Hospital',
                'detail_ar'=> 'مناوبات | راتب تنافسي | مطلوب تخصص صيدلة أو صيدلة سريرية',
                'detail_en'=> 'Shift-based | Competitive salary | Pharmacy or Clinical Pharmacy required',
            ],
            [
                'kind'     => 'discount',
                'title_ar' => 'خصم 20% على الاشتراك السنوي في المكتبة الطبية الإلكترونية',
                'title_en' => '20% off Annual Medical e-Library Subscription',
                'detail_ar'=> 'وصول لأكثر من 5000 مصدر طبي | مجلات، كتب، وأبحاث',
                'detail_en'=> 'Access 5000+ medical resources | Journals, books, and research',
            ],
            [
                'kind'     => 'course',
                'title_ar' => 'دورة الإسعافات الأولية المتقدمة (ACLS)',
                'title_en' => 'Advanced Cardiac Life Support (ACLS) Certification',
                'detail_ar'=> 'معتمدة دولياً من AHA | يونيو 2026 | محدودية الأماكن',
                'detail_en'=> 'Internationally accredited by AHA | June 2026 | Limited seats',
            ],
        ];

        $createdIncentives = [];
        foreach ($incentivesList as $inc) {
            $createdIncentives[] = Incentive::create($inc);
        }

        // Attach some incentives to alumni (simulate claims)
        if (count($createdIncentives) >= 3 && count($alumniList) >= 3) {
            // Primary alumni has claimed 2 incentives
            $primaryAlumnus->incentives()->attach($createdIncentives[0]->id, ['status' => 'redeemed']);
            $primaryAlumnus->incentives()->attach($createdIncentives[3]->id, ['status' => 'applied']);

            // A couple more alumni have claimed incentives
            $alumniList[1]->incentives()->attach($createdIncentives[1]->id, ['status' => 'enrolled']);
            $alumniList[2]->incentives()->attach($createdIncentives[4]->id, ['status' => 'applied']);
            $alumniList[3]->incentives()->attach($createdIncentives[2]->id, ['status' => 'enrolled']);
            if (isset($alumniList[5])) {
                $alumniList[5]->incentives()->attach($createdIncentives[5]->id, ['status' => 'redeemed']);
            }
        }
        });
    }
}
