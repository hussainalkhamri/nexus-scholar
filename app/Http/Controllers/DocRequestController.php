<?php

namespace App\Http\Controllers;

use App\Models\DocRequest;
use App\Models\User;
use Illuminate\Http\Request;

class DocRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['super_admin', 'admin', 'alumni_officer', 'alumnus'])) {
            return redirect()->route('dashboard.index')->with('error', __('messages.access_denied'));
        }

        $query = DocRequest::with('alumnus');

        // RBAC: Alumnus only sees their own certificate requests
        if ($user->role === 'alumnus') {
            // Ensure they have a certificate request
            $certRequest = DocRequest::where('alumnus_id', $user->id)
                ->where(function($q) {
                    $q->where('doc_type_ar', 'وثيقة تخرج')
                      ->orWhere('doc_type_en', 'Diploma Copy');
                })
                ->first();

            if (!$certRequest) {
                $certRequest = DocRequest::create([
                    'alumnus_id' => $user->id,
                    'doc_type_ar' => 'وثيقة تخرج',
                    'doc_type_en' => 'Diploma Copy',
                    'status' => 'pending',
                    'waiting_days' => 7,
                ]);
            }

            $query->where('alumnus_id', $user->id)
                  ->where(function($q) {
                      $q->where('doc_type_ar', 'وثيقة تخرج')
                        ->orWhere('doc_type_en', 'Diploma Copy');
                  });
        }

        // Filtering
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('alumnus', function($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $requests = $query->latest()->get();

        $docTypes = [
            ['ar' => 'كشف درجات', 'en' => 'Transcript'],
            ['ar' => 'إفادة تخرج', 'en' => 'Graduation Letter'],
            ['ar' => 'وثيقة تخرج', 'en' => 'Diploma Copy'],
            ['ar' => 'إفادة قيد', 'en' => 'Enrollment Letter'],
        ];

        return view('dashboard.alumni.requests', compact('requests', 'docTypes'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'alumnus') {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        $request->validate([
            'doc_type' => 'required|string',
        ]);

        // Decode translation selection
        $docTypes = [
            'Transcript' => ['ar' => 'كشف درجات', 'en' => 'Transcript'],
            'Graduation Letter' => ['ar' => 'إفادة تخرج', 'en' => 'Graduation Letter'],
            'Diploma Copy' => ['ar' => 'وثيقة تخرج', 'en' => 'وثيقة تخرج'],
            'Enrollment Letter' => ['ar' => 'إفادة قيد', 'en' => 'Enrollment Letter'],
        ];

        $typeData = $docTypes[$request->doc_type] ?? ['ar' => $request->doc_type, 'en' => $request->doc_type];

        DocRequest::create([
            'alumnus_id' => $user->id,
            'doc_type_ar' => $typeData['ar'],
            'doc_type_en' => $typeData['en'],
            'status' => 'pending',
            'waiting_days' => 1,
        ]);

        return redirect()->back()->with('success', __('messages.success'));
    }

    public function updateStatus(Request $request, $id)
    {
        // Admin or Alumni Officer only
        if (!in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer'])) {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        $request->validate([
            'status' => 'required|in:completed,processing,pending,overdue',
            'ministry_status' => 'nullable|in:in_ministry,approved,ready,delivered,returned',
            'submitted_to_ministry_at' => 'nullable|date',
            'delivered_to_student_at' => 'nullable|date',
            'modification_reason_type' => 'nullable|string',
            'modification_details' => 'nullable|string',
        ]);

        $doc = DocRequest::findOrFail($id);
        $doc->status = $request->status;

        if ($request->filled('ministry_status')) {
            $doc->ministry_status = $request->ministry_status;
        }

        if ($request->filled('submitted_to_ministry_at')) {
            $doc->submitted_to_ministry_at = $request->submitted_to_ministry_at;
        }

        if ($request->filled('delivered_to_student_at')) {
            $doc->delivered_to_student_at = $request->delivered_to_student_at;
        }

        if ($request->filled('modification_reason_type')) {
            $doc->modification_reason_type = $request->modification_reason_type;
        }

        if ($request->exists('modification_details')) {
            $doc->modification_details = $request->modification_details;
        }

        if (($request->status === 'completed' || $request->ministry_status === 'delivered') && !$doc->verification_code) {
            $doc->verification_code = 'VERIFY-' . strtoupper(\Illuminate\Support\Str::random(12));
            $doc->file_path = 'documents/doc_' . $doc->id . '.pdf';
            if (!$doc->delivered_to_student_at) {
                $doc->delivered_to_student_at = now()->toDateString();
            }
        }

        $doc->save();

        return redirect()->back()->with('success', __('messages.success'));
    }

    public function downloadDocument($id)
    {
        $user = auth()->user();
        $doc = DocRequest::with('alumnus.alumniProfile')->findOrFail($id);

        // Access check
        if (!in_array($user->role, ['super_admin', 'admin', 'alumni_officer', 'alumnus'])) {
            return redirect()->route('dashboard.index')->with('error', __('messages.access_denied'));
        }

        if ($user->role === 'alumnus' && $doc->alumnus_id !== $user->id) {
            return redirect()->route('dashboard.index')->with('error', __('messages.access_denied'));
        }

        if ($doc->status !== 'completed') {
            return redirect()->back()->with('error', 'الوثيقة ليست جاهزة للتحميل بعد.');
        }

        return view('dashboard.alumni.document_template', compact('doc'));
    }

    public function verifyDocument($code)
    {
        $doc = DocRequest::with('alumnus.alumniProfile')->where('verification_code', $code)->first();

        return view('public.verify_document', compact('doc'));
    }
}
