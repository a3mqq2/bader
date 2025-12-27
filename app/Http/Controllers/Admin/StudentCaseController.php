<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Models\Assessment;
use App\Models\StudentCase;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StudentCaseController extends Controller
{
    /**
     * الحصول على بيانات إنشاء دراسة حالة جديدة
     */
    public function create(Student $student)
    {
        // التحقق من أن الطالب في حالة "جديد"
        if ($student->status !== 'new') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إنشاء دراسة حالة لهذا الطالب'
            ], 400);
        }

        // الحصول على دراسة الحالة الأساسية (id=1)
        $caseStudy = Assessment::find(1);

        // الحصول على المقاييس الأخرى المفعلة
        $assessments = Assessment::where('id', '!=', 1)
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'caseStudy' => $caseStudy,
            'assessments' => $assessments,
            'student' => $student
        ]);
    }

    /**
     * إنشاء دراسة حالة جديدة مع الفاتورة
     */
    public function store(Request $request, Student $student)
    {
        // التحقق من أن الطالب في حالة "جديد"
        if ($student->status !== 'new') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إنشاء دراسة حالة لهذا الطالب'
            ], 400);
        }

        $request->validate([
            'assessments' => 'nullable|array',
            'assessments.*' => 'exists:assessments,id',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // إنشاء دراسة الحالة
            $studentCase = StudentCase::create([
                'student_id' => $student->id,
                'notes' => $request->notes,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            // حساب المجموع
            $totalAmount = 0;
            $invoiceItems = [];

            // إضافة دراسة الحالة الأساسية (id=1) بشكل إجباري
            $caseStudy = Assessment::find(1);
            if ($caseStudy) {
                $invoiceItems[] = [
                    'assessment_id' => $caseStudy->id,
                    'assessment_name' => $caseStudy->name,
                    'price' => $caseStudy->price,
                    'quantity' => 1,
                    'total' => $caseStudy->price,
                ];
                $totalAmount += $caseStudy->price;
            }

            // إضافة المقاييس المختارة
            if ($request->has('assessments') && is_array($request->assessments)) {
                foreach ($request->assessments as $assessmentId) {
                    // تجاهل دراسة الحالة الأساسية إذا تم اختيارها
                    if ($assessmentId == 1) continue;

                    $assessment = Assessment::find($assessmentId);
                    if ($assessment && $assessment->is_active) {
                        $invoiceItems[] = [
                            'assessment_id' => $assessment->id,
                            'assessment_name' => $assessment->name,
                            'price' => $assessment->price,
                            'quantity' => 1,
                            'total' => $assessment->price,
                        ];
                        $totalAmount += $assessment->price;
                    }
                }
            }

            // الحصول على أو إنشاء نوع فاتورة دراسة الحالة
            $caseStudyType = InvoiceType::firstOrCreate(
                ['name' => 'دراسة حالة'],
                ['is_active' => true]
            );

            // إنشاء الفاتورة
            $invoice = Invoice::create([
                'student_id' => $student->id,
                'student_case_id' => $studentCase->id,
                'invoice_type_id' => $caseStudyType->id,
                'description' => 'فاتورة دراسة الحالة',
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'discount' => 0,
                'status' => 'pending',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // إنشاء عناصر الفاتورة
            foreach ($invoiceItems as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'assessment_id' => $item['assessment_id'],
                    'assessment_name' => $item['assessment_name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['total'],
                ]);
            }

            // تحديث حالة الطالب إلى "تحت التقييم"
            $student->update(['status' => 'under_assessment']);

            ActivityLog::log("إنشاء دراسة حالة للطالب: {$student->name}", $studentCase, 'create');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء دراسة الحالة والفاتورة بنجاح',
                'studentCase' => $studentCase->load('invoice.items'),
                'invoice' => $invoice->load('items'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء دراسة الحالة: ' . $e->getMessage()
            ], 500);
        }
    }
}
