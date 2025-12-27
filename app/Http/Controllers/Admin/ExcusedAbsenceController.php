<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Models\ExcusedAbsence;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExcusedAbsenceController extends Controller
{
    /**
     * الحصول على بيانات إنشاء غياب بإذن
     */
    public function create(Student $student)
    {
        return response()->json([
            'success' => true,
            'student' => $student,
            'types' => ExcusedAbsence::getTypes(),
            'reasons' => ExcusedAbsence::getReasons(),
        ]);
    }

    /**
     * حفظ غياب بإذن جديد
     */
    public function store(Request $request, Student $student)
    {
        $request->validate([
            'type' => 'required|in:sessions,daycare',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|in:illness,travel,family,other',
            'reason_details' => 'required_if:reason,other|nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ], [
            'type.required' => 'نوع الغياب مطلوب',
            'start_date.required' => 'تاريخ البداية مطلوب',
            'end_date.required' => 'تاريخ النهاية مطلوب',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
            'reason.required' => 'سبب الغياب مطلوب',
            'reason_details.required_if' => 'يرجى كتابة تفاصيل السبب',
        ]);

        // إنشاء سجل الغياب بإذن
        $excusedAbsence = ExcusedAbsence::create([
            'student_id' => $student->id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'reason_details' => $request->reason_details,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        // تطبيق الغياب بإذن على السجلات الموجودة
        $excusedAbsence->applyToRecords();

        $typeText = ExcusedAbsence::getTypes()[$request->type];
        $reasonText = ExcusedAbsence::getReasons()[$request->reason];
        ActivityLog::log("تسجيل غياب بإذن للطالب: {$student->name} ({$typeText} - {$reasonText})", $excusedAbsence, 'create');

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الغياب بإذن بنجاح',
            'excusedAbsence' => $excusedAbsence->load('creator'),
        ]);
    }

    /**
     * عرض قائمة الغياب بإذن للطالب
     */
    public function index(Student $student)
    {
        $excusedAbsences = $student->excusedAbsences()
            ->with('creator')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'excusedAbsences' => $excusedAbsences,
        ]);
    }

    /**
     * حذف غياب بإذن
     */
    public function destroy(ExcusedAbsence $excusedAbsence)
    {
        $studentName = $excusedAbsence->student->name;
        $typeText = $excusedAbsence->type_text;

        // إزالة تأثير الغياب بإذن من السجلات
        $excusedAbsence->removeFromRecords();

        $excusedAbsence->delete();

        ActivityLog::log("حذف غياب بإذن للطالب: {$studentName} ({$typeText})", null, 'delete');

        return response()->json([
            'success' => true,
            'message' => 'تم حذف سجل الغياب بإذن',
        ]);
    }

    /**
     * الحصول على الطلاب في حالة خطر
     */
    public function atRiskStudents()
    {
        $students = Student::where('status', 'active')
            ->get()
            ->filter(function ($student) {
                return $student->is_at_risk;
            })
            ->values();

        return response()->json([
            'success' => true,
            'students' => $students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'code' => $student->code,
                    'name' => $student->name,
                    'consecutive_days' => $student->consecutive_unexcused_absence_days,
                    'last_attendance' => $student->last_attendance_date?->format('Y-m-d'),
                    'days_since_last' => $student->days_since_last_attendance,
                ];
            }),
            'count' => $students->count(),
        ]);
    }
}
