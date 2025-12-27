<?php

namespace App\Http\Controllers\Accountant;

use App\Models\User;
use App\Models\Treasury;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Models\TransactionCategory;
use App\Models\FinancialTransaction;
use App\Http\Controllers\Controller;
use App\Models\EmployeeAccountTransaction;

class EmployeeAccountController extends Controller
{
    /**
     * قائمة حسابات الموظفين
     */
    public function index(Request $request)
    {
        $query = User::withCount('accountTransactions');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('balance_status')) {
            switch ($request->balance_status) {
                case 'positive':
                    $query->where('account_balance', '>', 0);
                    break;
                case 'negative':
                    $query->where('account_balance', '<', 0);
                    break;
                case 'zero':
                    $query->where('account_balance', 0);
                    break;
            }
        }

        $employees = $query->orderBy('name')->paginate(20)->withQueryString();

        // إحصائيات
        $stats = [
            'total_employees' => User::count(),
            'positive_balance' => User::where('account_balance', '>', 0)->sum('account_balance'),
            'negative_balance' => User::where('account_balance', '<', 0)->sum('account_balance'),
        ];

        // الخزائن المخولة للمستخدم الحالي
        $treasuries = Treasury::active()
            ->whereHas('authorizedUsers', function ($q) {
                $q->where('user_id', auth()->id());
            })->get();

        // إذا لم يكن هناك خزائن مخولة، اجلب كل الخزائن (للـ admin)
        if ($treasuries->isEmpty()) {
            $treasuries = Treasury::active()->get();
        }

        return view('accountant.employee-accounts.index', compact('employees', 'stats', 'treasuries'));
    }

    /**
     * تفاصيل حساب موظف
     */
    public function show(Request $request, User $user)
    {
        $query = $user->accountTransactions()->with(['treasury', 'creator']);

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();

        // الخزائن المخولة للمستخدم الحالي
        $treasuries = Treasury::active()
            ->whereHas('authorizedUsers', function ($q) {
                $q->where('user_id', auth()->id());
            })->get();

        // إذا لم يكن هناك خزائن مخولة، اجلب كل الخزائن (للـ admin)
        if ($treasuries->isEmpty()) {
            $treasuries = Treasury::active()->get();
        }

        return view('accountant.employee-accounts.show', compact('user', 'transactions', 'treasuries'));
    }

    /**
     * إضافة حركة مالية للموظف (صرف)
     */
    public function storeTransaction(Request $request, User $user)
    {
        $rules = [
            'transaction_type' => 'required|in:advance,bonus,deduction',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required_if:transaction_type,advance|nullable|in:cash,bank_transfer',
            'treasury_id' => 'nullable|exists:treasuries,id',
            'bank_name' => 'required_if:payment_method,bank_transfer|nullable|string|max:100',
            'bank_account_number' => 'required_if:payment_method,bank_transfer|nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:100',
            'save_bank_account' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
        ];

        // الخزينة مطلوبة فقط إذا كان الدفع نقدي
        if ($request->transaction_type === 'advance' && $request->payment_method === 'cash') {
            $rules['treasury_id'] = 'required|exists:treasuries,id';
        }

        $request->validate($rules);

        $transactionType = $request->transaction_type;
        $amount = $request->amount;
        $paymentMethod = $request->payment_method;

        // تحديد نوع الحركة (له/عليه)
        $type = in_array($transactionType, ['advance', 'bonus']) ? 'credit' : 'debit';

        // حساب الرصيد بعد الحركة
        $balanceAfter = $type === 'credit'
            ? $user->account_balance + $amount
            : $user->account_balance - $amount;

        $financialTransaction = null;
        $treasuryId = null;
        $bankName = null;
        $bankAccountNumber = null;
        $bankAccountName = null;

        // السلفة فقط تؤثر على الخزينة (في حالة الدفع النقدي)
        if ($transactionType === 'advance') {
            if ($paymentMethod === 'cash') {
                // دفع نقدي - يؤثر على الخزينة
                $treasury = Treasury::findOrFail($request->treasury_id);
                $treasuryId = $treasury->id;

                $category = TransactionCategory::firstOrCreate(
                    ['name' => 'سلف الموظفين', 'type' => 'expense', 'for_system' => true],
                    ['is_active' => true]
                );

                $treasuryBalanceAfter = $treasury->current_balance - $amount;

                $financialTransaction = FinancialTransaction::create([
                    'treasury_id' => $treasury->id,
                    'category_id' => $category->id,
                    'type' => 'expense',
                    'amount' => $amount,
                    'description' => 'سلفة للموظف: ' . $user->name . ($request->description ? ' - ' . $request->description : ''),
                    'payment_method' => 'cash',
                    'recipient_name' => $user->name,
                    'balance_after' => $treasuryBalanceAfter,
                    'created_by' => auth()->id(),
                ]);
            } else {
                // تحويل بنكي - لا يؤثر على الخزينة
                $bankName = $request->bank_name;
                $bankAccountNumber = $request->bank_account_number;
                $bankAccountName = $request->bank_account_name ?: $user->name;

                // حفظ بيانات الحساب البنكي للموظف إذا تم اختيار ذلك
                if ($request->save_bank_account) {
                    $user->update([
                        'bank_name' => $bankName,
                        'bank_account_number' => $bankAccountNumber,
                        'bank_account_name' => $bankAccountName,
                    ]);
                }
            }
        }

        // إنشاء حركة في حساب الموظف
        $transaction = EmployeeAccountTransaction::create([
            'user_id' => $user->id,
            'treasury_id' => $treasuryId,
            'financial_transaction_id' => $financialTransaction?->id,
            'type' => $type,
            'transaction_type' => $transactionType,
            'amount' => $amount,
            'balance_after' => $balanceAfter,
            'description' => $request->description,
            'payment_method' => $paymentMethod,
            'bank_name' => $bankName,
            'bank_account_number' => $bankAccountNumber,
            'bank_account_name' => $bankAccountName,
            'created_by' => auth()->id(),
        ]);

        $typeTexts = [
            'advance' => 'صرف سلفة',
            'bonus' => 'إضافة مكافأة',
            'deduction' => 'تسجيل خصم',
        ];

        $methodText = $paymentMethod === 'bank_transfer' ? ' (تحويل بنكي)' : ($paymentMethod === 'cash' ? ' (نقدي)' : '');

        ActivityLog::log(
            "{$typeTexts[$transactionType]} بمبلغ {$amount} د.ل للموظف {$user->name}{$methodText}",
            $transaction,
            'create'
        );

        return redirect()->route('accountant.employee-accounts.show', $user)
            ->with('success', "تم {$typeTexts[$transactionType]} بنجاح");
    }

    /**
     * تحديث بيانات الحساب البنكي للموظف
     */
    public function updateBankAccount(Request $request, User $user)
    {
        $request->validate([
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:100',
        ]);

        $user->update([
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_name' => $request->bank_account_name,
        ]);

        return redirect()->route('accountant.employee-accounts.show', $user)
            ->with('success', 'تم تحديث بيانات الحساب البنكي بنجاح');
    }

    /**
     * طباعة كشف حساب موظف
     */
    public function printStatement(Request $request, User $user)
    {
        $query = $user->accountTransactions()->with(['treasury', 'creator']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->oldest()->get();

        $totals = [
            'credits' => $transactions->where('type', 'credit')->sum('amount'),
            'debits' => $transactions->where('type', 'debit')->sum('amount'),
            'advances' => $transactions->where('transaction_type', 'advance')->sum('amount'),
            'bonuses' => $transactions->where('transaction_type', 'bonus')->sum('amount'),
            'deductions' => $transactions->where('transaction_type', 'deduction')->sum('amount'),
        ];

        return view('accountant.employee-accounts.print-statement', compact('user', 'transactions', 'totals'));
    }

    /**
     * طباعة كشف جميع حسابات الموظفين
     */
    public function printAll(Request $request)
    {
        $query = User::withCount('accountTransactions');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('balance_status')) {
            switch ($request->balance_status) {
                case 'positive':
                    $query->where('account_balance', '>', 0);
                    break;
                case 'negative':
                    $query->where('account_balance', '<', 0);
                    break;
                case 'zero':
                    $query->where('account_balance', 0);
                    break;
            }
        }

        $employees = $query->orderBy('name')->get();

        // إحصائيات
        $stats = [
            'total_employees' => $employees->count(),
            'positive_balance' => $employees->where('account_balance', '>', 0)->sum('account_balance'),
            'negative_balance' => $employees->where('account_balance', '<', 0)->sum('account_balance'),
        ];

        return view('accountant.employee-accounts.print-all', compact('employees', 'stats'));
    }
}
