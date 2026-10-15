<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Group;
use App\Models\User;
use App\Models\DocRequest;

class HomeController extends Controller
{
    public function index()
    {
        // Dynamic KPIs for landing page
        $totalProjects = Group::count();
        $totalAlumni = User::where('role', 'alumnus')->count();
        $totalSupervisors = User::where('role', 'supervisor')->count();
        $activeRequests = DocRequest::whereIn('status', ['pending', 'processing'])->count();

        return view('home', compact('totalProjects', 'totalAlumni', 'totalSupervisors', 'activeRequests'));
    }

    public function switchLang($locale)
    {
        if (in_array($locale, ['ar', 'en'])) {
            Session::put('locale', $locale);
        }
        return redirect()->back();
    }
}
