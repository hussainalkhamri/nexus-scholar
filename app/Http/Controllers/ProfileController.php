<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AlumniProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user()->load('alumniProfile');
        return view('dashboard.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
        ];

        if ($user->role === 'alumnus') {
            $rules['graduation_year'] = 'required|integer';
            $rules['workplace'] = 'nullable|string|max:255';
            $rules['position'] = 'nullable|string|max:255';
            $rules['employed'] = 'required|boolean';
            $rules['skills'] = 'nullable|string'; // comma separated tags
        }

        if (in_array($user->role, ['student', 'alumnus'])) {
            $rules['linkedin_url'] = 'nullable|url|max:255';
            $rules['headline'] = 'nullable|string|max:255';
            $rules['experience'] = 'nullable|array';
            $rules['experience.*'] = 'nullable|string|max:255';
            $rules['conferences'] = 'nullable|array';
            $rules['conferences.*'] = 'nullable|string|max:255';
            $rules['achievements'] = 'nullable|array';
            $rules['achievements.*'] = 'nullable|string|max:255';
            $rules['education'] = 'nullable|array';
            $rules['education.*'] = 'nullable|string|max:255';
            $rules['languages'] = 'nullable|array';
            $rules['languages.*'] = 'nullable|string|max:255';
            $rules['certifications'] = 'nullable|array';
            $rules['certifications.*'] = 'nullable|string|max:255';
        }

        $request->validate($rules);

        // Update basic info
        $user->name = $request->name_en; // fallback standard name field
        $user->name_ar = $request->name_ar;
        $user->name_en = $request->name_en;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if (in_array($user->role, ['student', 'alumnus'])) {
            $user->linkedin_url = $request->linkedin_url;
            $user->headline = $request->headline;
            $user->experience = $request->filled('experience') ? array_values(array_filter(array_map('trim', $request->experience))) : [];
            $user->conferences = $request->filled('conferences') ? array_values(array_filter(array_map('trim', $request->conferences))) : [];
            $user->achievements = $request->filled('achievements') ? array_values(array_filter(array_map('trim', $request->achievements))) : [];
            $user->education = $request->filled('education') ? array_values(array_filter(array_map('trim', $request->education))) : [];
            $user->languages = $request->filled('languages') ? array_values(array_filter(array_map('trim', $request->languages))) : [];
            $user->certifications = $request->filled('certifications') ? array_values(array_filter(array_map('trim', $request->certifications))) : [];
        }

        $user->save();

        // Update alumni extra profile
        if ($user->role === 'alumnus') {
            $skillsArray = [];
            if ($request->filled('skills')) {
                $skillsArray = array_map('trim', explode(',', $request->skills));
            }

            AlumniProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'graduation_year' => $request->graduation_year,
                    'workplace' => $request->workplace,
                    'position' => $request->position,
                    'employed' => $request->employed,
                    'skills' => $skillsArray,
                    'linkedin_url' => $request->linkedin_url,
                ]
            );
        }

        return redirect()->back()->with('success', __('messages.success'));
    }
}
