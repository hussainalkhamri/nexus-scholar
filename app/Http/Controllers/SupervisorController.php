<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    public function index()
    {
        // Check permissions (Admin only)
        if (!in_array(auth()->user()->role, ['super_admin', 'admin', 'projects_officer'])) {
            return redirect()->route('dashboard.index')->with('error', __('messages.access_denied'));
        }

        // Get all supervisors and compute aggregates
        $supervisors = User::where('role', 'supervisor')->get()->map(function($sup) {
            $groups = Group::where('supervisor_id', $sup->id)->get();
            $groupsCount = $groups->count();
            
            $completedCount = $groups->where('status', 'completed')->count();
            $completionRate = $groupsCount > 0 ? round(($completedCount / $groupsCount) * 100) : 0;
            
            $stuckGroups = $groups->where('status', 'overdue')->count();
            $avgDays = $groupsCount > 0 ? round($groups->avg('days_in_stage'), 1) : 0;

            return [
                'id' => 'SUP-' . $sup->id,
                'name_ar' => $sup->name_ar,
                'name_en' => $sup->name_en,
                'department' => $sup->department,
                'groupsCount' => $groupsCount,
                'completionRate' => $completionRate,
                'stuckGroups' => $stuckGroups,
                'avgDays' => $avgDays,
            ];
        });

        return view('dashboard.supervisors.index', compact('supervisors'));
    }
}
