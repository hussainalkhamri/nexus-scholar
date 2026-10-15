<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Comment;
use App\Models\ProjectFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['super_admin', 'admin', 'projects_officer', 'supervisor', 'student'])) {
            return redirect()->route('dashboard.index')->with('error', __('messages.access_denied'));
        }

        if ($user->role === 'student') {
            $group = $user->groups()->first();
            if ($group) {
                return redirect()->route('projects.show', $group->id);
            }
            $groups = collect();
            $departments = ['Dentistry', 'Pharmacy', 'Clinical Pharmacy', 'Medical Labs', 'Anesthesia', 'Respiratory Care', 'Optometry', 'Physical Therapy', 'Nursing'];
            $supervisors = collect();
            return view('dashboard.projects.index', compact('groups', 'departments', 'supervisors'));
        }

        $query = Group::query();

        // If supervisor, only show their supervised groups
        if ($user->role === 'supervisor') {
            $query->where('supervisor_id', $user->id);
        }

        // Filtering
        if ($request->filled('dept') && $request->dept !== 'all') {
            $query->where('department', $request->dept);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('supervisor') && $request->supervisor !== 'all') {
            $query->where('supervisor', $request->supervisor);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title_ar', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $groups = $query->latest()->get();

        $departments = ['Dentistry', 'Pharmacy', 'Clinical Pharmacy', 'Medical Labs', 'Anesthesia', 'Respiratory Care', 'Optometry', 'Physical Therapy', 'Nursing'];
        $supervisors = User::where('role', 'supervisor')->get();

        return view('dashboard.projects.index', compact('groups', 'departments', 'supervisors'));
    }

    public function store(Request $request)
    {
        // Admin only
        if (!in_array(auth()->user()->role, ['super_admin', 'admin', 'projects_officer'])) {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        $request->validate([
            'title_ar' => 'required|string|max:500',
            'title_en' => 'required|string|max:500',
            'department' => 'required|string|max:100',
            'supervisor_id' => 'required|exists:users,id',
            'semester' => 'required|string|max:50',
        ]);

        $supervisorId = $request->supervisor_id;
        if (auth()->user()->role === 'supervisor') {
            $supervisorId = auth()->id(); // Force supervisor to assign to themselves
        }

        $sup = User::findOrFail($supervisorId);

        Group::create([
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'department' => $request->department,
            'supervisor' => $sup->name_ar,
            'supervisor_id' => $sup->id,
            'stage' => 'proposal',
            'status' => 'pending',
            'progress' => 20,
            'days_in_stage' => 1,
            'semester' => $request->semester,
        ]);

        return redirect()->back()->with('success', __('messages.success'));
    }

    public function show($id)
    {
        $user = auth()->user();
        
        // Security check
        if (!in_array($user->role, ['super_admin', 'admin', 'projects_officer', 'supervisor', 'student'])) {
            return redirect()->route('dashboard.index')->with('error', __('messages.access_denied'));
        }

        $group = Group::with(['students', 'comments.user', 'files.user'])->findOrFail($id);

        if ($user->role === 'student') {
            if (!$group->students->contains($user->id)) {
                return redirect()->route('dashboard.index')->with('error', __('messages.access_denied'));
            }
        } elseif ($user->role === 'supervisor') {
            if ($group->supervisor_id !== $user->id) {
                return redirect()->route('projects.index')->with('error', __('messages.access_denied'));
            }
        }

        return view('dashboard.projects.show', compact('group'));
    }
    public function update(Request $request, $id)
    {
        // Admin only
        if (!in_array(auth()->user()->role, ['super_admin', 'admin', 'projects_officer'])) {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        $group = Group::findOrFail($id);

        $request->validate([
            'title_ar' => 'required|string|max:500',
            'title_en' => 'required|string|max:500',
            'department' => 'required|string|max:100',
            'supervisor_id' => 'required|exists:users,id',
            'semester' => 'required|string|max:50',
        ]);

        $sup = User::findOrFail($request->supervisor_id);

        $group->update([
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'department' => $request->department,
            'supervisor' => $sup->name_ar,
            'supervisor_id' => $sup->id,
            'semester' => $request->semester,
        ]);

        return redirect()->back()->with('success', __('messages.success'));
    }

    public function destroy($id)
    {
        // Admin only
        if (!in_array(auth()->user()->role, ['super_admin', 'admin', 'projects_officer'])) {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        $group = Group::findOrFail($id);
        $group->delete();

        return redirect()->route('projects.index')->with('success', __('messages.success'));
    }

    public function advanceStage(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        
        // Check permissions (only supervisors or admins can advance stage)
        if (!in_array(auth()->user()->role, ['super_admin', 'admin', 'projects_officer', 'supervisor'])) {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        // Supervisor can only manage their own groups
        if (auth()->user()->role === 'supervisor' && $group->supervisor_id !== auth()->id()) {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        if ($group->status === 'completed') {
            return redirect()->back()->with('error', 'المشروع مكتمل بالفعل.');
        }

        if ($group->stage === 'proposal') {
            $group->stage = 'chapters';
            $group->status = 'processing';
            $group->progress = 50;
            $group->save();
            return redirect()->back()->with('success', __('messages.success'));
        } elseif ($group->stage === 'chapters') {
            $group->stage = 'draft';
            $group->status = 'processing';
            $group->progress = 75;
            $group->save();
            return redirect()->back()->with('success', __('messages.success'));
        } elseif ($group->stage === 'draft') {
            $request->validate([
                'plagiarism_percent' => 'required|integer|min:0|max:100',
            ]);

            $plagiarism = intval($request->plagiarism_percent);
            $group->plagiarism_percent = $plagiarism;

            if ($plagiarism > 20) {
                $group->save(); // Save the plagiarism percentage anyway so they see it
                return redirect()->back()->with('error', 'نسبة الاستلال العلمي (' . $plagiarism . '%) تتجاوز الحد الأقصى المسموح به وهو 20%. تم حظر الانتقال للمرحلة التالية.');
            }

            $group->stage = 'defense';
            $group->status = 'processing';
            $group->progress = 90;
            $group->save();
            return redirect()->back()->with('success', __('messages.success'));
        } elseif ($group->stage === 'defense') {
            $request->validate([
                'grade' => 'required|in:passed,passed_revisions,failed',
                'defense_committee' => 'nullable|string',
                'defense_date' => 'nullable|date',
            ]);

            $group->grade = $request->grade;
            $group->defense_committee = $request->defense_committee;
            $group->defense_date = $request->defense_date;

            if ($request->grade === 'passed') {
                $group->status = 'completed';
                $group->progress = 100;
                $group->save();

                // Convert all students to alumni
                foreach ($group->students as $student) {
                    $student->role = 'alumnus';
                    $student->save();

                    // Create Alumni Profile if not exists
                    \App\Models\AlumniProfile::firstOrCreate(
                        ['user_id' => $student->id],
                        [
                            'graduation_year' => date('Y'),
                            'employed' => false,
                            'points' => 100, // starting points
                        ]
                    );
                }

                return redirect()->back()->with('success', 'تم إكمال المشروع بنجاح! تم تحويل دور الطلاب تلقائياً إلى خريجين.');
            } elseif ($request->grade === 'passed_revisions') {
                $group->status = 'processing'; // keep in defense/processing for revisions
                $group->save();
                return redirect()->back()->with('success', 'تم تحديد المشروع كـ ناجح مع التعديلات. يمكن للطلاب رؤية ملاحظات المشرف.');
            } elseif ($request->grade === 'failed') {
                // Reset group
                $group->stage = 'proposal';
                $group->status = 'pending';
                $group->progress = 20;
                $group->save();
                return redirect()->back()->with('error', 'رسب المشروع في المناقشة. تم إعادة ضبط مرحلة المشروع إلى المقترح.');
            }
        }

        return redirect()->back()->with('error', 'انتقال مرحلة غير معروف.');
    }

    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $user = auth()->user();
        Comment::create([
            'group_id' => $id,
            'user_id' => $user->id,
            'author_name' => app()->getLocale() == 'ar' ? $user->name_ar : $user->name_en,
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', __('messages.success'));
    }

    public function uploadFile(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $path = $file->store('project_drafts', 'public');
            
            // File size format
            $bytes = $file->getSize();
            if ($bytes >= 1048576) {
                $size = number_format($bytes / 1048576, 2) . ' MB';
            } elseif ($bytes >= 1024) {
                $size = number_format($bytes / 1024, 2) . ' KB';
            } else {
                $size = $bytes . ' B';
            }

            ProjectFile::create([
                'group_id' => $id,
                'user_id' => auth()->id(),
                'filename' => $filename,
                'file_path' => $path,
                'file_size' => $size,
            ]);

            return redirect()->back()->with('success', __('messages.success'));
        }

        return redirect()->back()->with('error', 'File upload failed.');
    }
}
