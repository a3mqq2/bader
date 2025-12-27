<?php

namespace App\Http\Controllers\Admin;

use App\Models\TherapySession;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TherapySessionController extends Controller
{
    public function index()
    {
        $sessions = TherapySession::latest()->get();
        return view('admin.therapy-sessions.index', compact('sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'اسم الجلسة مطلوب',
            'price.required' => 'السعر مطلوب',
            'price.min' => 'السعر يجب أن يكون 0 أو أكثر',
        ]);

        $session = TherapySession::create([
            'name' => $request->name,
            'price' => $request->price,
            'is_active' => true,
        ]);

        ActivityLog::log("إضافة نوع جلسة: {$session->name}", $session, 'create');

        return redirect()->route('admin.therapy-sessions.index')
            ->with('success', 'تم إضافة الجلسة بنجاح');
    }

    public function update(Request $request, TherapySession $therapySession)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'اسم الجلسة مطلوب',
            'price.required' => 'السعر مطلوب',
            'price.min' => 'السعر يجب أن يكون 0 أو أكثر',
        ]);

        $therapySession->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        ActivityLog::log("تحديث نوع الجلسة: {$therapySession->name}", $therapySession, 'update');

        return redirect()->route('admin.therapy-sessions.index')
            ->with('success', 'تم تحديث الجلسة بنجاح');
    }

    public function toggleStatus(TherapySession $therapySession)
    {
        $therapySession->update([
            'is_active' => !$therapySession->is_active
        ]);

        $status = $therapySession->is_active ? 'تفعيل' : 'تعطيل';
        ActivityLog::log("{$status} نوع الجلسة: {$therapySession->name}", $therapySession, 'update');

        return redirect()->route('admin.therapy-sessions.index')
            ->with('success', "تم {$status} الجلسة بنجاح");
    }

    public function destroy(TherapySession $therapySession)
    {
        $sessionName = $therapySession->name;
        $therapySession->delete();

        ActivityLog::log("حذف نوع الجلسة: {$sessionName}", null, 'delete');

        return redirect()->route('admin.therapy-sessions.index')
            ->with('success', 'تم حذف الجلسة بنجاح');
    }
}
