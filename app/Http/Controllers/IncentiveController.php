<?php

namespace App\Http\Controllers;

use App\Models\Incentive;
use Illuminate\Http\Request;

class IncentiveController extends Controller
{
    public function index()
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer', 'alumnus'])) {
            return redirect()->route('dashboard.index')->with('error', __('messages.access_denied'));
        }

        if (in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer'])) {
            $incentives = Incentive::with(['users.alumniProfile'])->get();
        } else {
            $incentives = Incentive::all();
        }

        return view('dashboard.incentives.index', compact('incentives'));
    }

    public function store(Request $request)
    {
        // Admin only
        if (!in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer'])) {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        $request->validate([
            'kind' => 'required|in:course,job,discount',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'detail_ar' => 'required|string',
            'detail_en' => 'required|string',
        ]);

        Incentive::create([
            'kind' => $request->kind,
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'detail_ar' => $request->detail_ar,
            'detail_en' => $request->detail_en,
        ]);

        return redirect()->back()->with('success', __('messages.success'));
    }

    public function update(Request $request, $id)
    {
        // Admin only
        if (!in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer'])) {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        $request->validate([
            'kind' => 'required|in:course,job,discount',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'detail_ar' => 'required|string',
            'detail_en' => 'required|string',
        ]);

        $incentive = Incentive::findOrFail($id);
        $incentive->update([
            'kind' => $request->kind,
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'detail_ar' => $request->detail_ar,
            'detail_en' => $request->detail_en,
        ]);

        return redirect()->back()->with('success', __('messages.success'));
    }

    public function destroy($id)
    {
        // Admin only
        if (!in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer'])) {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        $incentive = Incentive::findOrFail($id);
        $incentive->delete();

        return redirect()->back()->with('success', __('messages.success'));
    }

    public function claim(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->role !== 'alumnus') {
            return redirect()->back()->with('error', __('messages.access_denied'));
        }

        $incentive = Incentive::findOrFail($id);
        $profile = $user->alumniProfile;

        if (!$profile) {
            return redirect()->back()->with('error', 'لم يتم العثور على ملف تعريف الخريج.');
        }

        // Check if already claimed/applied
        if ($user->incentives()->where('incentive_id', $incentive->id)->exists()) {
            return redirect()->back()->with('error', 'لقد قمت بالتقديم/التسجيل في هذه الفرصة بالفعل.');
        }

        // Apply rules depending on kind
        if ($incentive->kind === 'course') {
            $user->incentives()->attach($incentive->id, ['status' => 'enrolled']);
            return redirect()->back()->with('success', 'تم التسجيل بنجاح في هذا البرنامج التدريبي.');
        } elseif ($incentive->kind === 'discount') {
            $user->incentives()->attach($incentive->id, ['status' => 'redeemed']);
            return redirect()->back()->with('success', 'تم الحصول على الخصم بنجاح.');
        } elseif ($incentive->kind === 'job') {
            $user->incentives()->attach($incentive->id, ['status' => 'applied']);
            return redirect()->back()->with('success', 'تم إرسال الطلب بنجاح! تم مشاركة ملفك التعريفي وملخص مشروع تخرجك مع جهة العمل.');
        }

        return redirect()->back()->with('error', 'إجراء غير صالح.');
    }
}
