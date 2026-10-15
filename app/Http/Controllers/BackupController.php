<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\AlumniProfile;
use App\Models\Group;
use App\Models\DocRequest;

class BackupController extends Controller
{
    /**
     * Display the Dedicated Backup & System Maintenance Control Center.
     */
    public function index()
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            return redirect()->route('dashboard.index')->with('error', __('messages.access_denied'));
        }

        $dbConnection = config('database.default');
        $sqlitePath = database_path('database.sqlite');
        $sqliteSize = file_exists($sqlitePath) ? round(filesize($sqlitePath) / 1024 / 1024, 2) . ' MB' : 'غير متوفر';

        $stats = [
            'total_users'       => User::count(),
            'total_alumni'      => AlumniProfile::count(),
            'total_projects'    => Group::count(),
            'total_requests'    => DocRequest::count(),
            'db_connection'     => strtoupper($dbConnection),
            'sqlite_size'       => $sqliteSize,
            'php_version'       => PHP_VERSION,
            'laravel_version'   => app()->version(),
        ];

        return view('dashboard.backup.index', compact('stats'));
    }

    /**
     * Download direct SQLite database file backup (.sqlite / .bak).
     */
    public function downloadDatabase()
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            abort(403, 'غير مصرح للوصول لهذه الخاصية.');
        }

        $sqlitePath = database_path('database.sqlite');

        if (file_exists($sqlitePath)) {
            $format = request('ext', 'sqlite');
            $fileName = 'ibn_alnafees_full_backup_' . date('Y-m-d_H-i') . '.' . ($format === 'bak' ? 'bak' : 'sqlite');
            return response()->download($sqlitePath, $fileName);
        }

        return redirect()->back()->with('error', 'ملف قاعدة البيانات غير موجود في المسار الحالي.');
    }

    /**
     * Export complete Hostinger/MySQL compatible SQL dump file (.sql).
     */
    public function exportSqlDump()
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            abort(403, 'غير مصرح للوصول لهذه الخاصية.');
        }

        $tables = [
            'users',
            'alumni_profiles',
            'groups',
            'group_student',
            'doc_requests',
            'incentives',
            'incentive_user',
            'comments',
            'project_files',
            'migrations'
        ];

        $sql = "-- ========================================\n";
        $sql .= "-- Ibn Al-Nafees University Full System Database Dump (MySQL/MariaDB/Hostinger Compatible)\n";
        $sql .= "-- Generated at: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- System Version: Laravel " . app()->version() . " | PHP " . PHP_VERSION . "\n";
        $sql .= "-- ========================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $sql .= "-- ----------------------------------------\n";
            $sql .= "-- Table Structure & Data: {$table}\n";
            $sql .= "-- ----------------------------------------\n";

            $rows = DB::table($table)->get();

            foreach ($rows as $row) {
                $array = (array) $row;
                $cols = array_keys($array);
                $vals = array_map(function ($val) {
                    if (is_null($val)) return "NULL";
                    return "'" . addslashes((string)$val) . "'";
                }, array_values($array));

                $colNames = implode('`, `', $cols);
                $valStr = implode(', ', $vals);

                $sql .= "INSERT INTO `{$table}` (`{$colNames}`) VALUES ({$valStr});\n";
            }

            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        $fileName = 'ibn_alnafees_mysql_backup_' . date('Y-m-d_H-i') . '.sql';

        return response($sql, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Restore database from uploaded SQL file.
     */
    public function restoreSqlDump(Request $request)
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['sql', 'sqlite', 'bak', 'txt'])) {
            return redirect()->back()->with('error', 'صيغة الملف غير مدعومة. يرجى رفع ملف (.sql أو .sqlite أو .bak).');
        }

        try {
            if (in_array($extension, ['sqlite', 'bak'])) {
                $destination = database_path('database.sqlite');
                copy($file->getRealPath(), $destination);
                Artisan::call('cache:clear');
                Artisan::call('view:clear');
                return redirect()->back()->with('success', 'تم استعادة ملف قاعدة البيانات SQLite/BAK بنجاح.');
            }

            // Restore from SQL statements
            $sqlContent = file_get_contents($file->getRealPath());
            DB::unprepared($sqlContent);

            Artisan::call('cache:clear');
            Artisan::call('view:clear');

            return redirect()->back()->with('success', 'تم استعادة واستيراد النسخة الاحتياطية SQL بنجاح وتحديث كافة جداول النظام.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء استعادة النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    /**
     * One-click System Optimization and Cache Clear.
     */
    public function cleanCache()
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        try {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return redirect()->back()->with('success', 'تم تنظيف الملفات المؤقتة والذاكرة الوميضية (Cache) وتسريع النظام بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تنظيف الملفات المؤقتة: ' . $e->getMessage());
        }
    }
}
