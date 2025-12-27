<?php

namespace App\Http\Controllers\Specialist;

use App\Models\DaycareAttendance;
use App\Models\ActivityLog;
use App\Traits\AddsIncentive;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DaycareController extends Controller
{
    use AddsIncentive;
    /**
     * صفحة الرعاية النهارية للمشرف - عرض الطلاب المسندين إليه
     */
    public function index(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $search = $request->get('search');
        $userId = auth()->id();

        // جلب سجلات الحضور لهذا اليوم للطلاب المسندين لهذا المشرف فقط
        $baseQuery = DaycareAttendance::with(['subscription.student', 'subscription.daycareType'])
            ->whereDate('date', $date)
            ->whereHas('subscription', function ($query) use ($userId) {
                $query->where('status', 'active')
                      ->where('supervisor_id', $userId);
            });

        // إذا كان هناك بحث بكود الطالب - البحث بالكود الكامل فقط
        $searchedStudent = null;
        $studentNotFound = false;

        if ($search) {
            // البحث عن الطالب بالكود الكامل فقط
            $searchedStudent = (clone $baseQuery)
                ->whereHas('subscription.student', function ($q) use ($search) {
                    $q->where('code', $search); // كود كامل فقط
                })
                ->first();

            if (!$searchedStudent) {
                $studentNotFound = true;
            }
        }

        // جلب جميع الطلاب للعرض
        $attendances = $baseQuery->get()->sortBy('subscription.student.name');

        // إحصائيات اليوم
        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'pending' => $attendances->where('status', 'pending')->count(),
        ];

        return view('specialist.daycare.index', compact('attendances', 'date', 'stats', 'search', 'searchedStudent', 'studentNotFound'));
    }

    /**
     * تحديث حالة الحضور مع الملاحظات
     */
    public function updateAttendance(Request $request, DaycareAttendance $attendance)
    {
        // التحقق من أن هذا الحضور تابع لهذا المشرف
        if ($attendance->subscription->supervisor_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل هذا السجل',
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,present,absent',
            'notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $attendance->status;

        $attendance->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'updated_by' => auth()->id(),
        ]);

        // إضافة حافز عند تسجيل الحضور لأول مرة
        if ($oldStatus !== 'present' && $request->status === 'present') {
            $studentName = $attendance->subscription->student->name ?? 'غير معروف';
            $daycareType = $attendance->subscription->daycareType->name ?? 'رعاية نهارية';
            $this->addDaycareIncentive(auth()->user(), "{$daycareType} - {$studentName}");
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الحضور بنجاح',
            'attendance' => $attendance,
        ]);
    }

    /**
     * تبديل حالة الحضور بسرعة
     */
    public function toggleAttendance(Request $request, DaycareAttendance $attendance)
    {
        // التحقق من أن هذا الحضور تابع لهذا المشرف
        if ($attendance->subscription->supervisor_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل هذا السجل',
            ], 403);
        }

        $oldStatus = $attendance->status;
        $newStatus = $request->status;

        // إذا لم يتم إرسال حالة محددة، نقوم بالتبديل الدوري
        if (!$newStatus) {
            $newStatus = match ($oldStatus) {
                'pending' => 'present',
                'present' => 'absent',
                'absent' => 'pending',
                default => 'pending',
            };
        }

        $attendance->update([
            'status' => $newStatus,
            'updated_by' => auth()->id(),
        ]);

        // إضافة حافز عند تسجيل الحضور لأول مرة
        if ($oldStatus !== 'present' && $newStatus === 'present') {
            $studentName = $attendance->subscription->student->name ?? 'غير معروف';
            $daycareType = $attendance->subscription->daycareType->name ?? 'رعاية نهارية';
            $this->addDaycareIncentive(auth()->user(), "{$daycareType} - {$studentName}");
        }

        $studentName = $attendance->subscription->student->name ?? 'غير معروف';
        $statusLabels = ['present' => 'حاضر', 'absent' => 'غائب', 'pending' => 'انتظار'];
        ActivityLog::log("تحديث حضور الطالب {$studentName} إلى: {$statusLabels[$newStatus]}", $attendance, 'update');

        $messages = [
            'present' => 'تم تسجيل الحضور',
            'absent' => 'تم تسجيل الغياب',
            'pending' => 'تم إعادة الحالة للانتظار',
        ];

        return response()->json([
            'success' => true,
            'message' => $messages[$newStatus] ?? 'تم التحديث',
            'attendance' => $attendance,
            'status' => $newStatus,
        ]);
    }
}
