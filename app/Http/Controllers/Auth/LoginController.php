<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AlumniProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'login_type' => 'required|in:student,staff',
        ]);

        $loginType = $request->input('login_type');
        $loginValue = $request->input('email');
        $password = $request->input('password');

        if ($loginType === 'student') {
            $user = User::where('academic_id', $loginValue)->first();
            if ($user && in_array($user->role, ['student', 'alumnus'])) {
                if (Auth::attempt(['academic_id' => $loginValue, 'password' => $password], $request->boolean('remember'))) {
                    $user->update(['last_active_at' => now()]);
                    return redirect()->route('dashboard.index');
                }
            }
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->with('error', 'الرقم الأكاديمي أو كلمة المرور غير صحيحة، أو الحساب غير مصرح به كطالب/خريج.');
        } else {
            $user = User::where('email', $loginValue)->first();
            if ($user && !in_array($user->role, ['student', 'alumnus'])) {
                if (Auth::attempt(['email' => $loginValue, 'password' => $password], $request->boolean('remember'))) {
                    $user->update(['last_active_at' => now()]);
                    return redirect()->route('dashboard.index');
                }
            }
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->with('error', 'البريد الإلكتروني أو كلمة المرور غير صحيحة، أو الحساب غير مصرح به كأكاديمي/موظف.');
        }
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:8',
                'regex:/[a-z]/',      // lowercase
                'regex:/[A-Z]/',      // uppercase
                'regex:/[0-9]/',      // number
                'regex:/[@$!%*#?&]/'  // special char
            ],
            'role' => 'required|in:student,alumnus',
            'department' => 'required|string|max:100',
            'phone' => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'name' => $request->name_en,
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department' => $request->department,
            'phone' => $request->phone,
        ]);

        if ($user->role === 'alumnus') {
            AlumniProfile::create([
                'user_id' => $user->id,
                'graduation_year' => date('Y'),
                'employed' => false,
            ]);
        }

        Auth::login($user);
        $user->update(['last_active_at' => now()]);

        return redirect()->route('dashboard.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}

