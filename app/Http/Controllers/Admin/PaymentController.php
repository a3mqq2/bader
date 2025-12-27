<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    /**
     * إضافة دفعة جديدة
     */
    public function store(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->balance,
            'payment_method' => 'required|in:cash,bank_transfer,card',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'المبلغ مطلوب',
            'amount.numeric' => 'المبلغ يجب أن يكون رقم',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
            'amount.max' => 'المبلغ يجب ألا يتجاوز المبلغ المتبقي (' . number_format($invoice->balance, 2) . ' د.ل)',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_method.in' => 'طريقة الدفع غير صالحة',
        ]);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        $studentName = $invoice->student->name ?? 'غير معروف';
        ActivityLog::log("تسجيل دفعة {$request->amount} د.ل للطالب: {$studentName}", $payment, 'payment');

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الدفعة بنجاح',
            'payment' => $payment->load('creator'),
            'invoice' => $invoice->fresh(),
        ]);
    }

    /**
     * حذف دفعة
     */
    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;
        $amount = $payment->amount;
        $studentName = $invoice->student->name ?? 'غير معروف';

        $payment->delete();

        ActivityLog::log("حذف دفعة {$amount} د.ل للطالب: {$studentName}", null, 'delete');

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الدفعة بنجاح',
            'invoice' => $invoice->fresh(),
        ]);
    }

    /**
     * طباعة إيصال الدفع
     */
    public function printReceipt(Payment $payment)
    {
        $payment->load(['invoice.student', 'creator']);
        return view('admin.payments.receipt', compact('payment'));
    }
}
