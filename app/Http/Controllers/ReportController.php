<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    private function applyProjectsFilters(Request $request)
    {
        $query = Group::query();

        if ($request->filled('dept') && $request->dept !== 'all') {
            $query->where('department', $request->dept);
        }

        if ($request->filled('supervisor') && $request->supervisor !== 'all') {
            $query->where('supervisor', $request->supervisor);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        return $query;
    }

    private function applyRequestsFilters(Request $request)
    {
        $query = \App\Models\DocRequest::with('alumnus');

        if ($request->filled('dept') && $request->dept !== 'all') {
            $query->whereHas('alumnus', function ($q) use ($request) {
                $q->where('department', $request->dept);
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        return $query;
    }

    private function applyAlumniFilters(Request $request)
    {
        $query = User::where('role', 'alumnus')->with('alumniProfile');

        if ($request->filled('dept') && $request->dept !== 'all') {
            $query->where('department', $request->dept);
        }

        if ($request->filled('grad_year') && $request->grad_year !== 'all') {
            $query->whereHas('alumniProfile', function ($q) use ($request) {
                $q->where('graduation_year', $request->grad_year);
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $employed = $request->status === 'employed' ? 1 : 0;
            $query->whereHas('alumniProfile', function ($q) use ($employed) {
                $q->where('employed', $employed);
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['super_admin', 'admin', 'projects_officer', 'alumni_officer'])) {
            return redirect()->route('dashboard.index')->with('error', __('messages.access_denied'));
        }

        $reportType = $request->input('report_type');

        // Determine default report type based on role
        if (!$reportType) {
            if ($user->role === 'alumni_officer') {
                $reportType = 'requests';
            } else {
                $reportType = 'projects';
            }
        }

        // Restrict report type based on role
        if ($user->role === 'projects_officer' && $reportType !== 'projects') {
            return redirect()->route('reports.index', ['report_type' => 'projects'])->with('error', __('messages.access_denied'));
        }

        if ($user->role === 'alumni_officer' && $reportType === 'projects') {
            return redirect()->route('reports.index', ['report_type' => 'requests'])->with('error', __('messages.access_denied'));
        }

        // Lists of departments
        $departments = ['Dentistry', 'Pharmacy', 'Clinical Pharmacy', 'Medical Labs', 'Anesthesia', 'Respiratory Care', 'Optometry', 'Physical Therapy', 'Nursing'];
        $deptsAr = [
            'Dentistry' => 'طب وجراحة الفم والأسنان',
            'Pharmacy' => 'الصيدلة العامة',
            'Clinical Pharmacy' => 'الصيدلة السريرية',
            'Medical Labs' => 'المختبرات الطبية',
            'Anesthesia' => 'تكنولوجيا التخدير',
            'Respiratory Care' => 'الرعاية التنفسية',
            'Optometry' => 'البصريات وعلوم الرؤية',
            'Physical Therapy' => 'العلاج الطبيعي والتأهيل',
            'Nursing' => 'التمريض والقِبالة',
        ];

        // Supervisors list for filters
        $supervisors = User::where('role', 'supervisor')->get();
        // Graduation years list for filters
        $graduationYears = \App\Models\AlumniProfile::select('graduation_year')->distinct()->orderBy('graduation_year', 'desc')->pluck('graduation_year');

        $groups = collect();
        $requestsData = collect();
        $alumniData = collect();

        $kpis = [
            'card1_label' => '', 'card1_val' => 0,
            'card2_label' => '', 'card2_val' => 0,
            'card3_label' => '', 'card3_val' => 0,
        ];

        if ($reportType === 'projects') {
            $query = $this->applyProjectsFilters($request);
            $groups = $query->latest()->get();

            $kpis['card1_label'] = 'الأبحاث المكتملة';
            $kpis['card1_val'] = $groups->where('status', 'completed')->count();

            $kpis['card2_label'] = 'أبحاث قيد العمل';
            $kpis['card2_val'] = $groups->where('status', 'processing')->count();

            $kpis['card3_label'] = 'أبحاث معلقة/متأخرة';
            $kpis['card3_val'] = $groups->whereIn('status', ['pending', 'overdue'])->count();

        } elseif ($reportType === 'requests') {
            $query = $this->applyRequestsFilters($request);
            $requestsData = $query->latest()->get();

            $kpis['card1_label'] = 'الطلبات المكتملة';
            $kpis['card1_val'] = $requestsData->where('status', 'completed')->count();

            $kpis['card2_label'] = 'طلبات قيد المعالجة';
            $kpis['card2_val'] = $requestsData->where('status', 'processing')->count();

            $kpis['card3_label'] = 'طلبات جديدة/معلقة';
            $kpis['card3_val'] = $requestsData->where('status', 'pending')->count();

        } elseif ($reportType === 'alumni') {
            $query = $this->applyAlumniFilters($request);
            $alumniData = $query->latest()->get();

            $kpis['card1_label'] = 'إجمالي الخريجين';
            $kpis['card1_val'] = $alumniData->count();

            $kpis['card2_label'] = 'الخريجون الموظفون';
            $kpis['card2_val'] = $alumniData->filter(fn($u) => $u->alumniProfile && $u->alumniProfile->employed)->count();

            $kpis['card3_label'] = 'الباحثون عن عمل';
            $kpis['card3_val'] = $alumniData->filter(fn($u) => $u->alumniProfile && !$u->alumniProfile->employed)->count();
        }

        // Department chart data (calculated based on active report type)
        $deptChartData = [];
        foreach ($departments as $dept) {
            if ($reportType === 'projects') {
                $count = $groups->where('department', $dept)->count();
            } elseif ($reportType === 'requests') {
                $count = $requestsData->filter(fn($r) => $r->alumnus && $r->alumnus->department === $dept)->count();
            } else {
                $count = $alumniData->where('department', $dept)->count();
            }

            $deptChartData[] = [
                'dept' => $dept,
                'name_ar' => $deptsAr[$dept],
                'name_en' => $dept,
                'count' => $count,
            ];
        }

        return view('dashboard.reports.index', compact(
            'reportType',
            'groups',
            'requestsData',
            'alumniData',
            'kpis',
            'deptChartData',
            'supervisors',
            'departments',
            'deptsAr',
            'graduationYears'
        ));
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['super_admin', 'admin', 'projects_officer', 'alumni_officer'])) {
            abort(403);
        }

        $reportType = $request->input('report_type', 'projects');

        if ($user->role === 'projects_officer' && $reportType !== 'projects') {
            abort(403);
        }

        if ($user->role === 'alumni_officer' && $reportType === 'projects') {
            abort(403);
        }
        $lang = app()->getLocale();

        $groups = collect();
        $requestsData = collect();
        $alumniData = collect();
        $usersData = collect();

        if ($reportType === 'projects') {
            $groups = $this->applyProjectsFilters($request)->latest()->get();
        } elseif ($reportType === 'requests' || $reportType === 'tracking') {
            $requestsData = $this->applyRequestsFilters($request)->latest()->get();
        } elseif ($reportType === 'alumni') {
            $alumniData = $this->applyAlumniFilters($request)->latest()->get();
        } elseif ($reportType === 'users') {
            $usersData = User::latest()->get();
        }

        $fileName = 'Report-' . $reportType . '-' . date('Ymd-His') . '.xls';
        
        $content = view('exports.excel', compact('reportType', 'groups', 'requestsData', 'alumniData', 'usersData', 'lang'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['super_admin', 'admin', 'projects_officer', 'alumni_officer'])) {
            abort(403);
        }

        $reportType = $request->input('report_type', 'projects');

        if ($user->role === 'projects_officer' && $reportType !== 'projects') {
            abort(403);
        }

        if ($user->role === 'alumni_officer' && $reportType === 'projects') {
            abort(403);
        }
        $lang = app()->getLocale();

        $groups = collect();
        $requestsData = collect();
        $alumniData = collect();

        if ($reportType === 'projects') {
            $groups = $this->applyProjectsFilters($request)->latest()->get();
            $pdf = Pdf::loadView('exports.pdf', compact('reportType', 'groups', 'requestsData', 'alumniData', 'lang'));
        } elseif ($reportType === 'requests') {
            $requestsData = $this->applyRequestsFilters($request)->latest()->get();
            $pdf = Pdf::loadView('exports.pdf', compact('reportType', 'groups', 'requestsData', 'alumniData', 'lang'));
        } else {
            $alumniData = $this->applyAlumniFilters($request)->latest()->get();
            $academicYear = '2024/2025';
            $pdf = Pdf::loadView('exports.academic_graduates_pdf', compact('alumniData', 'lang', 'academicYear'))->setPaper('a4', 'landscape');
        }

        return $pdf->download('report-' . $reportType . '-' . date('Ymd-His') . '.pdf');
    }
}
