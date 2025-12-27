<?php

namespace App\Http\Controllers\Admin;

use App\Models\InvoiceItem;
use App\Models\Student;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssessmentResultController extends Controller
{
    /**
     * عرض نموذج التقييم
     */
    public function edit(InvoiceItem $item)
    {
        $item->load(['invoice.student', 'assessment', 'assessor']);

        return response()->json([
            'success' => true,
            'item' => $item,
            'student' => $item->invoice->student,
        ]);
    }

    /**
     * حفظ نتيجة التقييم
     */
    public function update(Request $request, InvoiceItem $item)
    {
        $request->validate([
            'assessment_result' => 'required|string',
            'assessment_notes' => 'nullable|string',
        ]);

        $item->update([
            'assessment_status' => 'completed',
            'assessment_result' => $request->assessment_result,
            'assessment_notes' => $request->assessment_notes,
            'assessed_by' => auth()->id(),
            'assessed_at' => now(),
        ]);

        $studentName = $item->invoice->student->name ?? 'غير معروف';
        $assessmentName = $item->assessment_name ?? 'تقييم';
        ActivityLog::log("تسجيل نتيجة {$assessmentName} للطالب: {$studentName}", $item, 'update');

        // التحقق من اكتمال جميع التقييمات
        $this->checkAndUpdateStudentStatus($item);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ التقييم بنجاح',
            'item' => $item->fresh(['assessor']),
            'all_completed' => $this->allAssessmentsCompleted($item),
        ]);
    }

    /**
     * التحقق من اكتمال جميع التقييمات وتحديث حالة الطالب
     */
    private function checkAndUpdateStudentStatus(InvoiceItem $item)
    {
        if ($this->allAssessmentsCompleted($item)) {
            $student = $item->invoice->student;
            $student->update(['status' => 'active']);

            // تحديث حالة دراسة الحالة إلى مكتملة
            if ($item->invoice->studentCase) {
                $item->invoice->studentCase->update(['status' => 'completed']);
            }
        }
    }

    /**
     * التحقق من اكتمال جميع التقييمات (باستثناء دراسة الحالة)
     */
    private function allAssessmentsCompleted(InvoiceItem $item): bool
    {
        $invoice = $item->invoice;

        // نستثني دراسة الحالة (assessment_id = 1) من الحساب لأنها لا تُقيَّم
        $pendingItems = $invoice->items()
            ->where('assessment_id', '!=', 1)
            ->where('assessment_status', 'pending')
            ->count();

        return $pendingItems === 0;
    }
}
