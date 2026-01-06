<?php

namespace App\Http\Controllers\Accountant;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Treasury;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Models\TransactionCategory;
use App\Models\FinancialTransaction;
use App\Http\Controllers\Controller;

class DuesController extends Controller
{
    /**
     * قائمة المستحقات - الطلاب الذين عليهم مديونية
     */
    public function index(Request $request)
    {
        $query = Student::whereHas('invoices', function ($q) {
            $q->whereIn('status', ['pending', 'partial']);
        })->with(['invoices' => function ($q) {
            $q->whereIn('status', ['pending', 'partial']);
        }]);

        // فلترة بالاسم أو الكود
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // فلترة بالحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة بنطاق المديونية
        if ($request->filled('min_balance')) {
            $query->whereHas('invoices', function ($q) use ($request) {
                $q->whereIn('status', ['pending', 'partial'])
                    ->havingRaw('SUM(total_amount - discount - paid_amount) >= ?', [$request->min_balance]);
            });
        }

        if ($request->filled('max_balance')) {
            $query->whereHas('invoices', function ($q) use ($request) {
                $q->whereIn('status', ['pending', 'partial'])
                    ->havingRaw('SUM(total_amount - discount - paid_amount) <= ?', [$request->max_balance]);
            });
        }

        $students = $query->latest()->paginate(20)->withQueryString();

        // حساب المديونية لكل طالب
        foreach ($students as $student) {
            $student->total_dues = $student->invoices->sum('balance');
        }

        // إحصائيات
        $stats = [
            'total_students' => Student::whereHas('invoices', function ($q) {
                $q->whereIn('status', ['pending', 'partial']);
            })->count(),
            'total_dues' => Invoice::whereIn('status', ['pending', 'partial'])
                ->selectRaw('SUM(total_amount - discount - paid_amount) as total')
                ->value('total') ?? 0,
        ];

        return view('accountant.dues.index', compact('students', 'stats'));
    }

    /**
     * تفاصيل مستحقات طالب معين
     */
    public function show(Student $student)
    {
        // جلب جميع الفواتير (المسددة وغير المسددة)
        $student->load(['invoices' => function ($q) {
            $q->with('payments')->latest();
        }]);

        // الخزائن المخولة للمستخدم الحالي
        $treasuries = Treasury::active()
            ->whereHas('authorizedUsers', function ($q) {
                $q->where('user_id', auth()->id());
            })->get();

        // إذا لم يكن هناك خزائن مخولة، اجلب كل الخزائن (للـ admin)
        if ($treasuries->isEmpty()) {
            $treasuries = Treasury::active()->get();
        }

        // حساب المستحقات (الفواتير غير المسددة فقط)
        $totalDues = $student->invoices->whereIn('status', ['pending', 'partial'])->sum('balance');

        return view('accountant.dues.show', compact('student', 'treasuries', 'totalDues'));
    }

    /**
     * إضافة دفعة لفاتورة معينة
     */
    public function storePayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->balance,
            'treasury_id' => 'required|exists:treasuries,id',
            'payment_method' => 'required|in:cash,bank_transfer',
            'bank_name' => 'nullable|required_if:payment_method,bank_transfer|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        // التحقق من أن الخزينة مخولة للمستخدم
        $treasury = Treasury::findOrFail($request->treasury_id);

        // إنشاء الحركة المالية في الخزينة
        $category = TransactionCategory::firstOrCreate(
            ['name' => 'تحصيل مستحقات', 'type' => 'income', 'for_system' => true],
            ['is_active' => true]
        );

        $balanceAfter = $treasury->current_balance + $request->amount;

        $transaction = FinancialTransaction::create([
            'treasury_id' => $treasury->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => $request->amount,
            'description' => "تحصيل دفعة من الطالب: {$invoice->student->name} - فاتورة رقم: {$invoice->invoice_number}",
            'payment_method' => $request->payment_method,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'recipient_name' => $invoice->student->guardian_name ?? $invoice->student->name,
            'balance_after' => $balanceAfter,
            'created_by' => auth()->id(),
        ]);

        // إنشاء الدفعة
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'treasury_id' => $treasury->id,
            'transaction_id' => $transaction->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        ActivityLog::log(
            "تحصيل دفعة مالية: {$request->amount} د.ل من الطالب {$invoice->student->name}",
            $payment,
            'create'
        );

        return redirect()->route('accountant.dues.show', $invoice->student)
            ->with('success', 'تم تسجيل الدفعة بنجاح')
            ->with('print_payment_id', $payment->id);
    }

    /**
     * طباعة إيصال الدفعة
     */
    public function printPayment(Payment $payment)
    {
        $payment->load(['invoice.student', 'treasury', 'creator']);
        return view('accountant.dues.print-payment', compact('payment'));
    }

    /**
     * طباعة كشف المستحقات
     */
    public function printReport(Request $request)
    {
        $query = Student::whereHas('invoices', function ($q) {
            $q->whereIn('status', ['pending', 'partial']);
        })->with(['invoices' => function ($q) {
            $q->whereIn('status', ['pending', 'partial']);
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $students = $query->get();

        foreach ($students as $student) {
            $student->total_dues = $student->invoices->sum('balance');
        }

        $totalDues = $students->sum('total_dues');

        return view('accountant.dues.print-report', compact('students', 'totalDues'));
    }
}
