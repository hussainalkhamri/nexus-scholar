<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\DocRequest;
use App\Models\Comment;
use App\Models\ProjectFile;
use App\Models\User;
use App\Models\Incentive;
use App\Models\AlumniProfile;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role;

        if (in_array($role, ['super_admin', 'admin', 'projects_officer', 'alumni_officer'])) {
            return $this->adminDashboard();
        } elseif ($role === 'supervisor') {
            return $this->supervisorDashboard();
        } elseif ($role === 'student') {
            return $this->studentDashboard();
        } elseif ($role === 'alumnus') {
            return $this->alumniDashboard();
        }

        return redirect()->route('home');
    }

    private function adminDashboard()
    {
        $role = auth()->user()->role;

        // 1. Calculate KPI Metrics
        $completedGroups = Group::where('status', 'completed')->count();
        $completedDocs = DocRequest::where('status', 'completed')->count();

        $processingGroups = Group::where('status', 'processing')->count();
        $processingDocs = DocRequest::where('status', 'processing')->count();

        $pendingGroups = Group::whereIn('status', ['pending', 'overdue'])->count();
        $pendingDocs = DocRequest::whereIn('status', ['pending', 'overdue'])->count();

        if ($role === 'projects_officer') {
            $totalCompleted = $completedGroups;
            $totalProcessing = $processingGroups;
            $totalPending = $pendingGroups;
        } elseif ($role === 'alumni_officer') {
            $totalCompleted = $completedDocs;
            $totalProcessing = $processingDocs;
            $totalPending = $pendingDocs;
        } else {
            // super_admin / admin
            $totalCompleted = $completedGroups + $completedDocs;
            $totalProcessing = $processingGroups + $processingDocs;
            $totalPending = $pendingGroups + $pendingDocs;
        }

        // 2. Department Completion Stats (Chart 1)
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
        
        $deptChartData = [];
        foreach ($departments as $dept) {
            if ($role === 'alumni_officer') {
                $completed = DocRequest::whereHas('alumnus', function($q) use ($dept) { $q->where('department', $dept); })->where('status', 'completed')->count();
                $processing = DocRequest::whereHas('alumnus', function($q) use ($dept) { $q->where('department', $dept); })->where('status', 'processing')->count();
                $pending = DocRequest::whereHas('alumnus', function($q) use ($dept) { $q->where('department', $dept); })->whereIn('status', ['pending', 'overdue'])->count();
            } else {
                $completed = Group::where('department', $dept)->where('status', 'completed')->count();
                $processing = Group::where('department', $dept)->where('status', 'processing')->count();
                $pending = Group::where('department', $dept)->whereIn('status', ['pending', 'overdue'])->count();
            }

            $deptChartData[] = [
                'dept' => $dept,
                'name_ar' => $deptsAr[$dept],
                'name_en' => $dept,
                'completed' => $completed,
                'processing' => $processing,
                'pending' => $pending,
            ];
        }

        // 3. Status Distribution (Chart 2)
        if ($role === 'alumni_officer') {
            $statusDistribution = [
                'completed' => DocRequest::where('status', 'completed')->count(),
                'processing' => DocRequest::where('status', 'processing')->count(),
                'pending' => DocRequest::where('status', 'pending')->count(),
                'overdue' => DocRequest::where('status', 'overdue')->count(),
            ];
        } else {
            $statusDistribution = [
                'completed' => Group::where('status', 'completed')->count(),
                'processing' => Group::where('status', 'processing')->count(),
                'pending' => Group::where('status', 'pending')->count(),
                'overdue' => Group::where('status', 'overdue')->count(),
            ];
        }

        // 4. Weekly Trend (Chart 3)
        $weeklyTrend = [];
        for ($i = 0; $i < 8; $i++) {
            $weeklyTrend[] = [
                'week' => 'W' . ($i + 1),
                'completed' => 4 + (($i * 3) % 12),
                'processing' => 2 + (($i * 5) % 8),
            ];
        }

        // 5. Latest Transactions
        $recentGroups = collect();
        if ($role !== 'alumni_officer') {
            $recentGroups = Group::latest()->take(5)->get()->map(function($g) {
                return [
                    'type' => 'project',
                    'title_ar' => $g->title_ar,
                    'title_en' => $g->title_en,
                    'status' => $g->status,
                    'date' => $g->created_at->format('Y-m-d'),
                    'id' => $g->id,
                ];
            });
        }

        $recentDocs = collect();
        if ($role !== 'projects_officer') {
            $recentDocs = DocRequest::latest()->take(5)->get()->map(function($d) {
                return [
                    'type' => 'document',
                    'title_ar' => $d->doc_type_ar,
                    'title_en' => $d->doc_type_en,
                    'status' => $d->status,
                    'date' => $d->created_at->format('Y-m-d'),
                    'id' => $d->id,
                ];
            });
        }

        $latestTransactions = $recentGroups->concat($recentDocs)->sortByDesc('date')->take(6)->values()->all();

        return view('dashboard.index', compact(
            'totalCompleted',
            'totalProcessing',
            'totalPending',
            'deptChartData',
            'statusDistribution',
            'weeklyTrend',
            'latestTransactions'
        ));
    }

    private function supervisorDashboard()
    {
        $supervisorId = auth()->id();
        $groups = Group::where('supervisor_id', $supervisorId)->get();

        $totalCompleted = $groups->where('status', 'completed')->count();
        $totalProcessing = $groups->where('status', 'processing')->count();
        $totalPending = $groups->whereIn('status', ['pending', 'overdue'])->count();

        // Find stuck groups (e.g. days in stage > 10)
        $stuckGroups = $groups->where('days_in_stage', '>', 10)->values();

        // Latest comments from supervised groups
        $groupIds = $groups->pluck('id');
        $latestComments = Comment::whereIn('group_id', $groupIds)
            ->with(['user', 'group'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'groups',
            'totalCompleted',
            'totalProcessing',
            'totalPending',
            'stuckGroups',
            'latestComments'
        ));
    }

    private function studentDashboard()
    {
        $student = auth()->user();
        $group = $student->groups()->first();

        $files = collect();
        $comments = collect();
        $supervisor = null;

        if ($group) {
            $files = ProjectFile::where('group_id', $group->id)->with('user')->latest()->take(5)->get();
            $comments = Comment::where('group_id', $group->id)->with('user')->latest()->take(8)->get();
            $supervisor = User::find($group->supervisor_id);
        }

        return view('dashboard.index', compact('group', 'files', 'comments', 'supervisor'));
    }

    private function alumniDashboard()
    {
        $alumnus = auth()->user();
        $profile = $alumnus->alumniProfile;

        // Ensure profile exists
        if (!$profile) {
            $profile = AlumniProfile::create([
                'user_id' => $alumnus->id,
                'graduation_year' => date('Y'),
                'employed' => false,
                'points' => 100,
            ]);
        }

        // Find or create the certificate request ("وثيقة تخرج")
        $certRequest = DocRequest::where('alumnus_id', $alumnus->id)
            ->where(function($q) {
                $q->where('doc_type_ar', 'وثيقة تخرج')
                  ->orWhere('doc_type_en', 'Diploma Copy');
            })
            ->first();

        if (!$certRequest) {
            $certRequest = DocRequest::create([
                'alumnus_id' => $alumnus->id,
                'doc_type_ar' => 'وثيقة تخرج',
                'doc_type_en' => 'Diploma Copy',
                'status' => 'pending',
                'waiting_days' => 7,
            ]);
        }

        // Get the requests list - ONLY showing their certificate transaction
        $requests = DocRequest::where('alumnus_id', $alumnus->id)
            ->where(function($q) {
                $q->where('doc_type_ar', 'وثيقة تخرج')
                  ->orWhere('doc_type_en', 'Diploma Copy');
            })
            ->get();

        $incentives = Incentive::latest()->take(3)->get();

        return view('dashboard.index', compact('profile', 'requests', 'certRequest', 'incentives'));
    }
}

