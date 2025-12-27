<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\StudentSession;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StudentSessionController extends Controller
{
    /**
     * عرض قائمة جميع الجلسات
     */
    public function index(Request $request)
    {
        $query = StudentSession::with(['student', 'specialist', 'package.therapySession']);

        // فلترة بالحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة بالأخصائي
        if ($request->filled('specialist_id')) {
            $query->where('specialist_id', $request->specialist_id);
        }

        // فلترة بالتاريخ
        if ($request->filled('date')) {
            $query->whereDate('session_date', $request->date);
        }

        // فلترة بنطاق التاريخ
        if ($request->filled('from_date')) {
            $query->whereDate('session_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('session_date', '<=', $request->to_date);
        }

        // البحث بالاسم
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $sessions = $query->orderBy('session_date', 'desc')
                         ->orderBy('session_time', 'asc')
                         ->paginate(20);

        $specialists = User::role('specialist')->get();
        $statuses = StudentSession::getStatuses();

        return view('admin.sessions.index', compact('sessions', 'specialists', 'statuses'));
    }

    /**
     * تحميل بيانات الجلسة للتعديل
     */
    public function edit(StudentSession $session)
    {
        $session->load(['student', 'specialist', 'package.therapySession']);
        $specialists = User::role('specialist')->get();

        return response()->json([
            'success' => true,
            'session' => $session,
            'specialists' => $specialists,
            'statuses' => StudentSession::getStatuses(),
        ]);
    }

    /**
     * تحديث الجلسة
     */
    public function update(Request $request, StudentSession $session)
    {
        $request->validate([
            'specialist_id' => 'required|exists:users,id',
            'session_date' => 'required|date',
            'session_time' => 'required',
            'status' => 'required|in:scheduled,completed,postponed,cancelled',
            'notes' => 'nullable|string',
        ], [
            'specialist_id.required' => 'الأخصائي مطلوب',
            'session_date.required' => 'تاريخ الجلسة مطلوب',
            'session_time.required' => 'وقت الجلسة مطلوب',
            'status.required' => 'حالة الجلسة مطلوبة',
        ]);

        $session->update([
            'specialist_id' => $request->specialist_id,
            'session_date' => $request->session_date,
            'session_time' => $request->session_time,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الجلسة بنجاح',
            'session' => $session->load(['student', 'specialist']),
        ]);
    }

    /**
     * جلسات اليوم
     */
    public function today()
    {
        $sessions = StudentSession::with(['student', 'specialist', 'package.therapySession'])
            ->whereDate('session_date', today())
            ->orderBy('session_time', 'asc')
            ->get();

        $specialists = User::role('specialist')->get();
        $statuses = StudentSession::getStatuses();

        return view('admin.sessions.today', compact('sessions', 'specialists', 'statuses'));
    }
}
