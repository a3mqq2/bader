<?php

namespace App\Http\Controllers\Specialist;

use App\Models\StudentSession;
use App\Models\ActivityLog;
use App\Traits\AddsIncentive;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SessionController extends Controller
{
    use AddsIncentive;
 
    
    public function index(Request $request)
    {
        $search = $request->get('search');
        $date = $request->filled('date') ? $request->date : today()->format('Y-m-d');

        $searchedStudent = null;
        $studentNotFound = false;

        $query = StudentSession::with(['student', 'package.therapySession'])
            ->where('specialist_id', auth()->id())
            ->whereDate('session_date', $date); 

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($search) {
            $searchedStudent = (clone $query)
                ->whereHas('student', function($q) use ($search) {
                    $q->where('code', $search); // كود كامل فقط
                })
                ->first();

            if (!$searchedStudent) {
                $studentNotFound = true;
            }
        }

        $sessions = $query->orderBy('session_date', 'desc')
                         ->orderBy('session_time', 'asc')
                         ->get();
        $statuses = StudentSession::getStatuses();

        // إحصائيات
        $stats = [
            'total' => $sessions->count(),
            'scheduled' => $sessions->where('status', 'scheduled')->count(),
            'completed' => $sessions->where('status', 'completed')->count(),
            'absent' => $sessions->where('status', 'absent')->count(),
            'postponed' => $sessions->where('status', 'postponed')->count(),
            'cancelled' => $sessions->where('status', 'cancelled')->count(),
        ];

        return view('specialist.sessions.index', compact('sessions', 'statuses', 'stats', 'date', 'search', 'searchedStudent', 'studentNotFound'));
    }

    /**
     * عرض تفاصيل الجلسة
     */
    public function show(StudentSession $session)
    {
        // السماح بعرض الجلسة إذا كانت مخصصة للأخصائي الحالي أو بدون أخصائي
        if ($session->specialist_id != null && $session->specialist_id != auth()->id()) {
            abort(403);
        }

        $session->load(['student', 'package.therapySession']);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'session' => $session,
            ]);
        }

        return view('specialist.sessions.show', compact('session'));
    }

    /**
     * تحديث الجلسة - الأخصائي يقدر يغير الملاحظات أو يحدد كمكتملة أو غائب
     */
    public function update(Request $request, StudentSession $session)
    {
        // السماح بتحديث الجلسة إذا كانت مخصصة للأخصائي الحالي أو بدون أخصائي
        if ($session->specialist_id != null && $session->specialist_id != auth()->id()) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
        }

        $request->validate([
            'notes' => 'nullable|string',
            'status' => 'nullable|in:completed,absent', // الأخصائي يقدر يحدد كمكتملة أو غائب
        ]);

        $data = [];

        // حفظ الملاحظات دائماً
        if ($request->has('notes')) {
            $data['notes'] = $request->notes;
        }

        // تغيير الحالة لمكتملة أو غائب فقط إذا كانت مجدولة
        $wasScheduled = $session->status == 'scheduled';
        if (in_array($request->status, ['completed', 'absent']) && $wasScheduled) {
            $data['status'] = $request->status;
        }

        $session->update($data);

        // إضافة حافز للأخصائي عند إكمال الجلسة
        if ($request->status == 'completed' && $wasScheduled) {
            $sessionType = $session->package->therapySession->name ?? 'جلسة';
            $this->addSessionIncentive(auth()->user(), "{$sessionType} - " . ($session->student->name ?? 'غير معروف'));
        }

        $studentName = $session->student->name ?? 'غير معروف';
        ActivityLog::log("تحديث جلسة الطالب: {$studentName}", $session, 'update');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الجلسة بنجاح',
            'session' => $session->load(['student', 'package.therapySession']),
        ]);
    }

   
    public function complete(StudentSession $session)
    {
        // السماح لأي أخصائي بإكمال الجلسة
        $session->update([
            'status' => 'completed',
            'specialist_id' => $session->specialist_id ?: auth()->id()
        ]);

        $studentName = $session->student->name ?? 'غير معروف';
        $sessionType = $session->package->therapySession->name ?? 'جلسة';
        $this->addSessionIncentive(auth()->user(), "{$sessionType} - {$studentName}");

        ActivityLog::log("إكمال جلسة الطالب: {$studentName}", $session, 'update');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديد الجلسة كمكتملة',
            'session' => $session,
        ]);
    }
}
