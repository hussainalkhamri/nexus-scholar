<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AlumniProfile;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer'])) {
            return redirect()->route('dashboard.index')->with('error', __('messages.access_denied'));
        }

        $query = User::where('role', 'alumnus')->with('alumniProfile');

        // Filtering
        if ($request->filled('dept') && $request->dept !== 'all') {
            $query->where('department', $request->dept);
        }

        if ($request->filled('year') && $request->year !== 'all') {
            $year = intval($request->year);
            $query->whereHas('alumniProfile', function($q) use ($year) {
                $q->where('graduation_year', $year);
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $employed = $request->status === 'employed';
            $query->whereHas('alumniProfile', function($q) use ($employed) {
                $q->where('employed', $employed);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('alumniProfile', function($subQ) use ($search) {
                      $subQ->where('skills', 'like', "%{$search}%")
                           ->orWhere('workplace', 'like', "%{$search}%")
                           ->orWhere('position', 'like', "%{$search}%");
                  });
            });
        }

        $alumni = $query->latest()->get();

        $departments = ['Dentistry', 'Pharmacy', 'Clinical Pharmacy', 'Medical Labs', 'Anesthesia', 'Respiratory Care', 'Optometry', 'Physical Therapy', 'Nursing'];
        $years = AlumniProfile::select('graduation_year')->distinct()->orderBy('graduation_year', 'desc')->pluck('graduation_year');

        return view('dashboard.alumni.index', compact('alumni', 'departments', 'years'));
    }
}
