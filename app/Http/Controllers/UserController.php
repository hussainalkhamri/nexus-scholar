<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AlumniProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Super Admin or Admin only can view users and roles
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            return redirect()->route('dashboard.index')->with('error', __('messages.access_denied'));
        }

        $users = User::latest()->get();

        // Permissions Matrix Data
        $routeKeys = [
            ['path' => '/dashboard',                 'label_key' => 'nav_dashboard', 'roles' => ['super_admin', 'admin', 'projects_officer', 'alumni_officer', 'supervisor', 'student', 'alumni']],
            ['path' => '/dashboard/projects',        'label_key' => 'nav_projects', 'roles' => ['super_admin', 'admin', 'projects_officer', 'supervisor', 'student']],
            ['path' => '/dashboard/supervisors',     'label_key' => 'nav_supervisors', 'roles' => ['super_admin', 'admin', 'projects_officer']],
            ['path' => '/dashboard/alumni',          'label_key' => 'nav_alumni', 'roles' => ['super_admin', 'admin', 'alumni_officer']],
            ['path' => '/dashboard/alumni/requests', 'label_key' => 'nav_requests', 'roles' => ['super_admin', 'admin', 'alumni_officer', 'alumni']],
            ['path' => '/dashboard/incentives',      'label_key' => 'nav_incentives', 'roles' => ['super_admin', 'admin', 'alumni_officer', 'alumni']],
            ['path' => '/dashboard/reports',         'label_key' => 'nav_reports', 'roles' => ['super_admin', 'admin', 'projects_officer', 'alumni_officer']],
            ['path' => '/dashboard/profile',         'label_key' => 'nav_profile', 'roles' => ['super_admin', 'admin', 'projects_officer', 'alumni_officer', 'supervisor', 'student', 'alumni']],
        ];

        $allRoles = ['super_admin', 'admin', 'projects_officer', 'alumni_officer', 'supervisor', 'student', 'alumnus'];

        return view('dashboard.users.index', compact('users', 'routeKeys', 'allRoles'));
    }

    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        $request->validate([
            'name_ar'     => 'required|string|max:255',
            'name_en'     => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:users,email',
            'academic_id' => 'nullable|string|max:100|unique:users,academic_id',
            'role'        => 'required|in:super_admin,admin,projects_officer,alumni_officer,student,supervisor,alumnus',
            'department'  => 'nullable|string|max:100',
            'phone'       => 'nullable|string|max:50',
            'password'    => 'nullable|string|min:6',
        ]);

        if (in_array($request->role, ['student', 'alumnus']) && empty($request->academic_id)) {
            return redirect()->back()->withInput()->with('error', 'الرقم الأكاديمي مطلوب للطلاب والخريجين.');
        }

        $user = User::create([
            'name'        => $request->name_en,
            'name_ar'     => $request->name_ar,
            'name_en'     => $request->name_en,
            'email'       => $request->email,
            'academic_id' => in_array($request->role, ['student', 'alumnus']) ? $request->academic_id : null,
            'password'    => Hash::make($request->password ?: 'password'),
            'role'        => $request->role,
            'department'  => $request->department,
            'phone'       => $request->phone,
        ]);

        if ($request->role === 'alumnus') {
            AlumniProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'graduation_year' => date('Y'),
                    'department'      => $request->department ?: 'عام',
                    'employed'        => false,
                ]
            );
        }

        return redirect()->back()->with('success', __('messages.success'));
    }

    public function update(Request $request, $id)
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        $user = User::findOrFail($id);

        if ($request->has('name_ar')) {
            $request->validate([
                'name_ar'     => 'required|string|max:255',
                'name_en'     => 'required|string|max:255',
                'email'       => 'required|email|max:255|unique:users,email,' . $id,
                'academic_id' => 'nullable|string|max:100|unique:users,academic_id,' . $id,
                'role'        => 'required|in:super_admin,admin,projects_officer,alumni_officer,student,supervisor,alumnus',
                'department'  => 'nullable|string|max:100',
                'phone'       => 'nullable|string|max:50',
                'password'    => 'nullable|string|min:6',
            ]);

            $data = [
                'name'        => $request->name_en,
                'name_ar'     => $request->name_ar,
                'name_en'     => $request->name_en,
                'email'       => $request->email,
                'academic_id' => in_array($request->role, ['student', 'alumnus']) ? $request->academic_id : null,
                'role'        => $request->role,
                'department'  => $request->department,
                'phone'       => $request->phone,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            if ($request->role === 'alumnus') {
                AlumniProfile::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'graduation_year' => date('Y'),
                        'department'      => $request->department ?: 'عام',
                        'employed'        => false,
                    ]
                );
            }
        } else {
            $request->validate([
                'role' => 'required|in:super_admin,admin,projects_officer,alumni_officer,student,supervisor,alumnus',
            ]);
            $user->role = $request->role;
            $user->save();

            if ($user->role === 'alumnus') {
                AlumniProfile::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'graduation_year' => date('Y'),
                        'department'      => $user->department ?: 'عام',
                        'employed'        => false,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', __('messages.success'));
    }

    public function destroy($id)
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'لا يمكنك حذف حسابك الحالي.');
        }

        $user->delete();

        return redirect()->back()->with('success', __('messages.success'));
    }

    public function import(Request $request)
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $rows = [];

        try {
            if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
            }
        } catch (\Exception $e) {
            $rows = [];
        }

        // Fallback to robust CSV parsing if PhpSpreadsheet didn't run or returned empty
        if (empty($rows)) {
            $handle = fopen($filePath, 'r');
            if ($handle) {
                // Remove UTF-8 BOM if present
                $bom = fread($handle, 3);
                if ($bom !== "\xEF\xBB\xBF") {
                    rewind($handle);
                }

                $firstLine = fgets($handle);
                rewind($handle);
                if ($bom !== "\xEF\xBB\xBF") {
                    // re-skip BOM if present
                    $b = fread($handle, 3);
                    if ($b !== "\xEF\xBB\xBF") rewind($handle);
                }

                $separator = (strpos($firstLine, ';') !== false) ? ';' : ',';

                while (($line = fgetcsv($handle, 2000, $separator)) !== false) {
                    if (array_filter($line)) {
                        $rows[] = $line;
                    }
                }
                fclose($handle);
            }
        }

        if (empty($rows)) {
            return redirect()->back()->with('error', 'فشل في قراءة بيانات الملف المرفوع. تأكد من سلامة صيغة الملف.');
        }

        // Extract and clean header row
        $header = array_map(function($h) {
            return trim(mb_strtolower((string)$h));
        }, array_shift($rows));

        // Map column names dynamically
        $colMap = [
            'name_ar'     => 0,
            'name_en'     => 1,
            'email'       => 2,
            'academic_id' => 3,
            'phone'       => 4,
            'department'  => 5,
            'role'        => 6,
        ];

        foreach ($header as $idx => $colName) {
            if (in_array($colName, ['name_ar', 'الاسم (عربي)', 'الاسم بالعربي', 'الاسم عربي', 'الاسم'])) {
                $colMap['name_ar'] = $idx;
            } elseif (in_array($colName, ['name_en', 'الاسم (إنجليزي)', 'الاسم بالإنجليزي', 'الاسم انجليزي', 'english name'])) {
                $colMap['name_en'] = $idx;
            } elseif (in_array($colName, ['email', 'البريد', 'البريد الإلكتروني', 'البريد الالكترونى'])) {
                $colMap['email'] = $idx;
            } elseif (in_array($colName, ['academic_id', 'الرقم الأكاديمي', 'الرقم الاكاديمي', 'رقم القيد', 'الكود الأكاديمي'])) {
                $colMap['academic_id'] = $idx;
            } elseif (in_array($colName, ['phone', 'الهاتف', 'رقم الهاتف', 'الجوال'])) {
                $colMap['phone'] = $idx;
            } elseif (in_array($colName, ['department', 'القسم', 'التخصص', 'الكلية'])) {
                $colMap['department'] = $idx;
            } elseif (in_array($colName, ['role', 'الدور', 'صلاحية المستخدم', 'نوع الحساب'])) {
                $colMap['role'] = $idx;
            }
        }

        $roleMapping = [
            'طالب'                => 'student',
            'student'             => 'student',
            'خريج'                => 'alumnus',
            'alumnus'             => 'alumnus',
            'alumni'              => 'alumnus',
            'مشرف'                => 'supervisor',
            'مشرف أكاديمي'         => 'supervisor',
            'supervisor'          => 'supervisor',
            'إدارة القسم'          => 'admin',
            'ادارة القسم'          => 'admin',
            'مدير'                => 'admin',
            'admin'               => 'admin',
            'مدير النظام العام'    => 'super_admin',
            'سوبر أدمن'           => 'super_admin',
            'super_admin'         => 'super_admin',
            'مسؤول مشاريع التخرج' => 'projects_officer',
            'مسؤول مشاريع'        => 'projects_officer',
            'projects_officer'    => 'projects_officer',
            'مسؤول شؤون الخريجين'  => 'alumni_officer',
            'مسؤول خريجين'        => 'alumni_officer',
            'alumni_officer'      => 'alumni_officer',
        ];

        $imported = 0;
        $errors = [];

        foreach ($rows as $rIdx => $row) {
            $rowNum = $rIdx + 2;
            if (!is_array($row) || count(array_filter($row)) === 0) continue;

            $nameAr     = trim($row[$colMap['name_ar']] ?? '');
            $nameEn     = trim($row[$colMap['name_en']] ?? '');
            $email      = trim($row[$colMap['email']] ?? '');
            $academicId = trim($row[$colMap['academic_id']] ?? '');
            $phone      = trim($row[$colMap['phone']] ?? '');
            $department = trim($row[$colMap['department']] ?? '');
            $rawRole    = trim($row[$colMap['role']] ?? 'student');

            if (empty($nameAr) && !empty($nameEn)) $nameAr = $nameEn;
            if (empty($nameEn) && !empty($nameAr)) $nameEn = $nameAr;

            if (empty($nameAr) || empty($email)) {
                $errors[] = "السطر {$rowNum}: بيانات الحقول الأساسية (الاسم والبريد الإلكتروني) فارغة.";
                continue;
            }

            $mappedRole = $roleMapping[mb_strtolower($rawRole)] ?? ($roleMapping[$rawRole] ?? 'student');

            if (in_array($mappedRole, ['student', 'alumnus']) && empty($academicId)) {
                $errors[] = "السطر {$rowNum}: يجب توفير الرقم الأكاديمي للطلاب والخريجين ({$nameAr}).";
                continue;
            }

            if (!in_array($mappedRole, ['student', 'alumnus'])) {
                $academicId = null;
            }

            try {
                $u = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name'        => $nameEn,
                        'name_ar'     => $nameAr,
                        'name_en'     => $nameEn,
                        'academic_id' => $academicId,
                        'password'    => Hash::make('password'),
                        'role'        => $mappedRole,
                        'department'  => $department ?: null,
                        'phone'       => $phone ?: null,
                    ]
                );

                if ($mappedRole === 'alumnus') {
                    AlumniProfile::firstOrCreate(
                        ['user_id' => $u->id],
                        [
                            'graduation_year' => date('Y'),
                            'department'      => $department ?: 'عام',
                            'employed'        => false,
                        ]
                    );
                }

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "السطر {$rowNum}: تعذر الحفظ (ربما البريد الإلكتروني أو الرقم الأكاديمي مكرر).";
            }
        }

        $msg = "تم استيراد / تحديث {$imported} مستخدم بنجاح.";
        if (count($errors) > 0) {
            $msg .= " توجيهات الأخطاء: " . implode(" | ", array_slice($errors, 0, 5));
            return redirect()->back()->with('error', $msg);
        }

        return redirect()->back()->with('success', $msg);
    }
}
