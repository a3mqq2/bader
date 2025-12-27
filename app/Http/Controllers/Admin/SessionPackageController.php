<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Models\User;
use App\Models\TherapySession;
use App\Models\SessionPackage;
use App\Models\StudentSession;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SessionPackageController extends Controller
{
    /**
     * الحصول على بيانات إنشاء باقة جلسات
     */
    public function create(Student $student)
    {
        $therapySessions = TherapySession::active()->get();
        $specialists = User::role('specialist')->get();

        return response()->json([
            'success' => true,
            'therapySessions' => $therapySessions,
            'specialists' => $specialists,
            'days' => SessionPackage::$dayNames,
        ]);
    }

    /**
     * معاينة الجلسات قبل الحفظ
     */
    public function preview(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'session_time' => 'required',
            'days' => 'required|array|min:1',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $days = $request->days;

        $sessions = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dayName = strtolower($currentDate->format('l'));

            if (in_array($dayName, $days)) {
                $sessions[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'date_formatted' => $currentDate->format('Y/m/d'),
                    'day_name' => $dayName,
                    'day' => SessionPackage::$dayNames[$dayName] ?? $dayName,
                    'time' => $request->session_time,
                    'time_formatted' => date('h:i A', strtotime($request->session_time)),
                ];
            }

            $currentDate->addDay();
        }

        return response()->json([
            'success' => true,
            'sessions' => $sessions,
            'sessions_count' => count($sessions),
        ]);
    }

    /**
     * حفظ باقة الجلسات
     */
    public function store(Request $request, Student $student)
    {
        $request->validate([
            'therapy_session_id' => 'required|exists:therapy_sessions,id',
            'specialist_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'session_time' => 'required',
            'session_duration' => 'required|integer|min:15',
            'days' => 'required|array|min:1',
            'sessions' => 'required|array|min:1',
        ], [
            'therapy_session_id.required' => 'نوع الجلسة مطلوب',
            'specialist_id.required' => 'الأخصائي مطلوب',
            'start_date.required' => 'تاريخ البداية مطلوب',
            'end_date.required' => 'تاريخ النهاية مطلوب',
            'session_time.required' => 'وقت الجلسة مطلوب',
            'days.required' => 'يجب اختيار يوم واحد على الأقل',
            'sessions.required' => 'يجب توليد الجلسات أولاً',
        ]);

        try {
            DB::beginTransaction();

            $therapySession = TherapySession::find($request->therapy_session_id);
            $sessionsData = $request->sessions;
            $sessionsCount = count($sessionsData);
            $totalPrice = $sessionsCount * $therapySession->price;

            // الحصول على أو إنشاء نوع فاتورة الجلسات
            $invoiceType = InvoiceType::firstOrCreate(
                ['name' => 'جلسات علاجية'],
                ['is_active' => true]
            );

            // إنشاء الفاتورة
            $invoice = Invoice::create([
                'student_id' => $student->id,
                'invoice_type_id' => $invoiceType->id,
                'description' => $therapySession->name . ' (' . $sessionsCount . ' جلسة)',
                'total_amount' => $totalPrice,
                'paid_amount' => 0,
                'discount' => 0,
                'status' => 'pending',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // إنشاء بند الفاتورة (جلسات)
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'assessment_name' => $therapySession->name,
                'price' => $therapySession->price,
                'quantity' => $sessionsCount,
                'total' => $totalPrice,
            ]);

            // إنشاء الباقة
            $package = SessionPackage::create([
                'student_id' => $student->id,
                'therapy_session_id' => $request->therapy_session_id,
                'specialist_id' => $request->specialist_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'session_time' => $request->session_time,
                'session_duration' => $request->session_duration,
                'days' => $request->days,
                'total_price' => $totalPrice,
                'sessions_count' => $sessionsCount,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'invoice_id' => $invoice->id,
            ]);

            // إنشاء الجلسات الفردية
            foreach ($sessionsData as $sessionData) {
                StudentSession::create([
                    'session_package_id' => $package->id,
                    'student_id' => $student->id,
                    'specialist_id' => $request->specialist_id,
                    'session_date' => $sessionData['date'],
                    'session_time' => $sessionData['time'],
                    'duration' => $request->session_duration,
                    'status' => 'scheduled',
                ]);
            }

            ActivityLog::log("إنشاء باقة جلسات للطالب: {$student->name} ({$sessionsCount} جلسة)", $package, 'create');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء باقة الجلسات والفاتورة بنجاح',
                'package' => $package->load('sessions', 'therapySession', 'specialist', 'invoice'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * تحديث جلسة فردية
     */
    public function updateSession(Request $request, StudentSession $session)
    {
        $request->validate([
            'session_date' => 'nullable|date',
            'session_time' => 'nullable',
            'status' => 'nullable|in:scheduled,completed,cancelled,absent',
            'notes' => 'nullable|string',
        ]);

        $session->update($request->only(['session_date', 'session_time', 'status', 'notes']));

        $studentName = $session->student->name ?? 'غير معروف';
        ActivityLog::log("تحديث جلسة للطالب: {$studentName}", $session, 'update');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الجلسة بنجاح',
            'session' => $session,
        ]);
    }

    /**
     * حذف جلسة فردية
     */
    public function destroySession(StudentSession $session)
    {
        $package = $session->package;
        $studentName = $session->student->name ?? 'غير معروف';
        $session->delete();

        ActivityLog::log("حذف جلسة للطالب: {$studentName}", null, 'delete');

        // تحديث عدد الجلسات والسعر الإجمالي
        $remainingSessions = $package->sessions()->count();
        $therapySession = $package->therapySession;
        $newTotal = $remainingSessions * $therapySession->price;

        $package->update([
            'sessions_count' => $remainingSessions,
            'total_price' => $newTotal,
        ]);

        // تحديث الفاتورة المرتبطة
        if ($package->invoice) {
            $package->invoice->update(['total_amount' => $newTotal]);
            $package->invoice->items()->update([
                'quantity' => $remainingSessions,
                'total' => $newTotal,
            ]);
            $package->invoice->updateStatus();
        }

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الجلسة بنجاح',
            'new_count' => $remainingSessions,
            'new_total' => number_format($newTotal, 2) . ' د.ل',
        ]);
    }

    /**
     * حذف باقة كاملة
     */
    public function destroy(SessionPackage $package)
    {
        $studentName = $package->student->name ?? 'غير معروف';

        // حذف الفاتورة المرتبطة إذا لم يكن هناك دفعات
        if ($package->invoice && $package->invoice->paid_amount <= 0) {
            $package->invoice->items()->delete();
            $package->invoice->delete();
        }

        $package->delete();

        ActivityLog::log("حذف باقة جلسات للطالب: {$studentName}", null, 'delete');

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الباقة بنجاح',
        ]);
    }

    /**
     * عرض تفاصيل الباقة
     */
    public function show(SessionPackage $package)
    {
        $package->load(['student', 'therapySession', 'specialist', 'sessions', 'creator']);

        return view('admin.session-packages.show', compact('package'));
    }

    /**
     * طباعة جدول الباقة
     */
    public function print(SessionPackage $package)
    {
        $package->load(['student', 'therapySession', 'specialist', 'sessions']);

        return view('admin.session-packages.print', compact('package'));
    }
}
