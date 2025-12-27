<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Student;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\DaycareType;
use App\Models\DaycareSubscription;
use App\Models\DaycareAttendance;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DaycareController extends Controller
{
    /**
     * صفحة إدارة الرعاية النهارية - عرض الطلاب حسب اليوم
     */
    public function index(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));

        // جلب سجلات الحضور لهذا اليوم مع بيانات الاشتراك والطالب
        $attendances = DaycareAttendance::with(['subscription.student', 'subscription.daycareType', 'subscription.supervisor'])
            ->whereDate('date', $date)
            ->whereHas('subscription', function ($query) {
                $query->where('status', 'active');
            })
            ->get()
            ->sortBy('subscription.student.name');

        // إحصائيات اليوم
        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'pending' => $attendances->where('status', 'pending')->count(),
        ];

        // أنواع الرعاية للفلترة
        $daycareTypes = DaycareType::active()->get();

        return view('admin.daycare.index', compact('attendances', 'date', 'stats', 'daycareTypes'));
    }

    /**
     * عرض نموذج إضافة اشتراك رعاية نهارية
     */
    public function create(Student $student)
    {
        $daycareTypes = DaycareType::active()->get();
        $supervisors = User::where('is_active', true)->get();

        return view('admin.daycare.create', compact('student', 'daycareTypes', 'supervisors'));
    }

    /**
     * حفظ اشتراك رعاية نهارية جديد
     */
    public function store(Request $request, Student $student)
    {
        $request->validate([
            'daycare_type_id' => 'required|exists:daycare_types,id',
            'supervisor_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ], [
            'daycare_type_id.required' => 'نوع الرعاية مطلوب',
            'supervisor_id.required' => 'المشرف مطلوب',
            'start_date.required' => 'تاريخ البداية مطلوب',
            'end_date.required' => 'تاريخ النهاية مطلوب',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
        ]);

        $daycareType = DaycareType::findOrFail($request->daycare_type_id);

        DB::beginTransaction();
        try {
            // إنشاء الفاتورة
            $invoice = Invoice::create([
                'student_id' => $student->id,
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'invoice_type_id' => null,
                'description' => 'رعاية نهارية - ' . $daycareType->name,
                'total_amount' => $daycareType->price,
                'paid_amount' => 0,
                'discount' => 0,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            // إضافة بند الفاتورة
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'assessment_name' => $daycareType->name . ' (' . $request->start_date . ' - ' . $request->end_date . ')',
                'price' => $daycareType->price,
                'quantity' => 1,
                'total' => $daycareType->price,
            ]);

            // إنشاء الاشتراك
            $subscription = DaycareSubscription::create([
                'student_id' => $student->id,
                'daycare_type_id' => $request->daycare_type_id,
                'supervisor_id' => $request->supervisor_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'price' => $daycareType->price,
                'invoice_id' => $invoice->id,
                'status' => 'active',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // توليد أيام الحضور تلقائياً
            $subscription->generateAttendances();

            ActivityLog::log("إضافة اشتراك رعاية نهارية للطالب: {$student->name}", $subscription, 'create');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة اشتراك الرعاية النهارية وإنشاء الفاتورة بنجاح',
                'subscription' => $subscription->load(['daycareType', 'supervisor']),
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
     * عرض تفاصيل اشتراك الرعاية النهارية
     */
    public function show(DaycareSubscription $subscription)
    {
        $subscription->load(['student', 'daycareType', 'supervisor', 'attendances', 'invoice']);

        return view('admin.daycare.show', compact('subscription'));
    }

    /**
     * طباعة سجل الحضور
     */
    public function print(DaycareSubscription $subscription)
    {
        $subscription->load(['student', 'daycareType', 'supervisor', 'attendances']);

        return view('admin.daycare.print', compact('subscription'));
    }

    /**
     * تحديث حالة الحضور
     */
    public function updateAttendance(Request $request, DaycareAttendance $attendance)
    {
        $request->validate([
            'status' => 'required|in:pending,present,absent',
        ]);

        $attendance->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        $studentName = $attendance->subscription->student->name ?? 'غير معروف';
        $statusLabels = ['present' => 'حاضر', 'absent' => 'غائب', 'pending' => 'انتظار'];
        ActivityLog::log("تحديث حضور الطالب {$studentName} إلى: {$statusLabels[$request->status]}", $attendance, 'update');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الحضور',
            'attendance' => $attendance,
        ]);
    }

    /**
     * تغيير حالة الحضور
     */
    public function toggleAttendance(Request $request, DaycareAttendance $attendance)
    {
        $newStatus = $request->status;

        // إذا لم يتم إرسال حالة محددة، نقوم بالتبديل الدوري
        if (!$newStatus) {
            $newStatus = match ($attendance->status) {
                'pending' => 'present',
                'present' => 'absent',
                'absent' => 'pending',
                default => 'pending',
            };
        }

        $attendance->update([
            'status' => $newStatus,
        ]);

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

    /**
     * إلغاء اشتراك الرعاية النهارية
     */
    public function cancel(DaycareSubscription $subscription)
    {
        $subscription->update([
            'status' => 'cancelled',
        ]);

        $studentName = $subscription->student->name ?? 'غير معروف';
        ActivityLog::log("إلغاء اشتراك رعاية نهارية للطالب: {$studentName}", $subscription, 'update');

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الاشتراك',
        ]);
    }

    /**
     * حذف اشتراك الرعاية النهارية
     */
    public function destroy(DaycareSubscription $subscription)
    {
        $studentName = $subscription->student->name ?? 'غير معروف';
        $subscription->delete();

        ActivityLog::log("حذف اشتراك رعاية نهارية للطالب: {$studentName}", null, 'delete');

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الاشتراك',
        ]);
    }
}
