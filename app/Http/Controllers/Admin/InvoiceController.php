<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use App\Models\Student;
use App\Models\InvoiceItem;
use App\Models\InvoiceType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * طباعة الفاتورة A4
     */
    public function print(Invoice $invoice)
    {
        $invoice->load(['student', 'items', 'payments.creator', 'creator']);
        return view('admin.invoices.print', compact('invoice'));
    }

    /**
     * إنشاء فاتورة يدوية للطالب
     */
    public function store(Request $request, Student $student)
    {
        $request->validate([
            'invoice_type_id' => 'required|exists:invoice_types,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ], [
            'invoice_type_id.required' => 'نوع الفاتورة مطلوب',
            'invoice_type_id.exists' => 'نوع الفاتورة غير صالح',
            'description.required' => 'الوصف مطلوب',
            'amount.required' => 'القيمة مطلوبة',
            'amount.min' => 'القيمة يجب أن تكون أكبر من صفر',
        ]);

        try {
            DB::beginTransaction();

            // إنشاء الفاتورة
            $invoice = Invoice::create([
                'student_id' => $student->id,
                'invoice_type_id' => $request->invoice_type_id,
                'description' => $request->description,
                'total_amount' => $request->amount,
                'discount' => 0,
                'paid_amount' => 0,
                'status' => 'pending',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // إضافة بند واحد بالوصف والقيمة
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'assessment_id' => null,
                'assessment_name' => $request->description,
                'price' => $request->amount,
                'quantity' => 1,
                'total' => $request->amount,
                'assessment_status' => 'completed',
            ]);

            ActivityLog::log("إنشاء فاتورة للطالب: {$student->name} بقيمة {$request->amount} د.ل", $invoice, 'create');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الفاتورة بنجاح',
                'invoice' => $invoice->load('items'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * إضافة نوع فاتورة جديد
     */
    public function storeType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:invoice_types,name',
        ], [
            'name.required' => 'اسم النوع مطلوب',
            'name.unique' => 'هذا النوع موجود مسبقاً',
        ]);

        $type = InvoiceType::create([
            'name' => $request->name,
            'is_active' => true,
        ]);

        ActivityLog::log("إضافة نوع فاتورة: {$type->name}", $type, 'create');

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة نوع الفاتورة بنجاح',
            'type' => $type,
        ]);
    }

    /**
     * تحميل بيانات الفاتورة للتعديل
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $invoiceTypes = InvoiceType::active()->get();

        return response()->json([
            'success' => true,
            'invoice' => $invoice,
            'invoice_types' => $invoiceTypes,
        ]);
    }

    /**
     * تحديث الفاتورة
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'invoice_type_id' => 'nullable|exists:invoice_types,id',
            'description' => 'nullable|string|max:255',
            'paid_amount' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'items.required' => 'يجب إضافة عنصر واحد على الأقل',
            'items.min' => 'يجب إضافة عنصر واحد على الأقل',
            'items.*.name.required' => 'اسم العنصر مطلوب',
            'items.*.price.required' => 'سعر العنصر مطلوب',
            'items.*.quantity.required' => 'الكمية مطلوبة',
        ]);

        try {
            DB::beginTransaction();

            // حساب الإجمالي من العناصر
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['price'] * $item['quantity'];
            }

            $paidAmount = $request->paid_amount ?? $invoice->paid_amount;
            $discount = $request->discount ?? 0;

            // حساب الحالة تلقائياً بناءً على المبلغ المدفوع
            $balance = $totalAmount - $paidAmount - $discount;
            if ($balance <= 0) {
                $status = 'paid';
            } elseif ($paidAmount > 0) {
                $status = 'partial';
            } else {
                $status = 'pending';
            }

            // تحديث الفاتورة
            $invoice->update([
                'invoice_type_id' => $request->invoice_type_id,
                'description' => $request->description,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'discount' => $discount,
                'status' => $status,
                'notes' => $request->notes,
            ]);

            // حذف العناصر القديمة (عدا عناصر دراسة الحالة)
            $invoice->items()->whereNull('assessment_id')->orWhere('assessment_id', '!=', 1)->delete();

            // إضافة العناصر الجديدة
            foreach ($request->items as $item) {
                // تجاهل عناصر دراسة الحالة (سيتم الاحتفاظ بها)
                if (isset($item['is_case_study']) && $item['is_case_study']) {
                    continue;
                }

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'assessment_id' => $item['assessment_id'] ?? null,
                    'assessment_name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['price'] * $item['quantity'],
                    'assessment_status' => $item['assessment_status'] ?? 'completed',
                    'assessment_result' => $item['assessment_result'] ?? null,
                    'assessment_notes' => $item['assessment_notes'] ?? null,
                ]);
            }

            $studentName = $invoice->student->name ?? 'غير معروف';
            ActivityLog::log("تحديث فاتورة للطالب: {$studentName}", $invoice, 'update');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الفاتورة بنجاح',
                'invoice' => $invoice->load('items'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الفاتورة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * حذف الفاتورة
     */
    public function destroy(Invoice $invoice)
    {
        // التحقق من عدم وجود مدفوعات
        if ($invoice->paid_amount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف فاتورة تم دفع جزء منها',
            ], 400);
        }

        $studentName = $invoice->student->name ?? 'غير معروف';

        // حذف البنود أولاً
        $invoice->items()->delete();

        // حذف الفاتورة
        $invoice->delete();

        ActivityLog::log("حذف فاتورة للطالب: {$studentName}", null, 'delete');

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الفاتورة بنجاح',
        ]);
    }
}
