<?php

namespace App\Http\Controllers\Accountant;

use App\Models\User;
use App\Models\Treasury;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Models\TransactionCategory;
use App\Models\FinancialTransaction;
use App\Http\Controllers\Controller;

class FinanceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | الخزائن المالية
    |--------------------------------------------------------------------------
    */

    public function treasuriesIndex(Request $request)
    {
        $query = Treasury::with(['authorizedUsers', 'creator']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $treasuries = $query->latest()->paginate(12)->withQueryString();

        return view('accountant.finance.treasuries.index', compact('treasuries'));
    }

    public function treasuriesCreate()
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('accountant.finance.treasuries.create', compact('users'));
    }

    public function treasuriesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'opening_balance' => 'required|numeric|min:0',
            'authorized_users' => 'nullable|array',
            'authorized_users.*' => 'exists:users,id',
        ]);

        $treasury = Treasury::create([
            'name' => $request->name,
            'opening_balance' => $request->opening_balance,
            'current_balance' => $request->opening_balance,
            'created_by' => auth()->id(),
        ]);

        if ($request->has('authorized_users')) {
            $treasury->authorizedUsers()->sync($request->authorized_users);
        }

        ActivityLog::log("إنشاء خزينة جديدة: {$treasury->name}", $treasury, 'create');

        return redirect()->route('accountant.finance.treasuries.index')
            ->with('success', 'تم إنشاء الخزينة بنجاح');
    }

    public function treasuriesEdit(Treasury $treasury)
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        $treasury->load('authorizedUsers');
        return view('accountant.finance.treasuries.edit', compact('treasury', 'users'));
    }

    public function treasuriesUpdate(Request $request, Treasury $treasury)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'authorized_users' => 'nullable|array',
            'authorized_users.*' => 'exists:users,id',
        ]);

        $treasury->update([
            'name' => $request->name,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $treasury->authorizedUsers()->sync($request->input('authorized_users', []));

        ActivityLog::log("تحديث الخزينة: {$treasury->name}", $treasury, 'update');

        return redirect()->route('accountant.finance.treasuries.index')
            ->with('success', 'تم تحديث الخزينة بنجاح');
    }

    public function treasuriesDestroy(Treasury $treasury)
    {
        $name = $treasury->name;

        if ($treasury->transactions()->count() > 0) {
            return redirect()->route('accountant.finance.treasuries.index')
                ->with('error', 'لا يمكن حذف الخزينة لوجود حركات مالية مرتبطة بها');
        }

        $treasury->delete();

        ActivityLog::log("حذف الخزينة: {$name}", null, 'delete');

        return redirect()->route('accountant.finance.treasuries.index')
            ->with('success', 'تم حذف الخزينة بنجاح');
    }

    /*
    |--------------------------------------------------------------------------
    | تصنيفات الحركات
    |--------------------------------------------------------------------------
    */

    public function categoriesIndex(Request $request)
    {
        $query = TransactionCategory::withCount('transactions');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $categories = $query->latest()->paginate(12)->withQueryString();

        return view('accountant.finance.categories.index', compact('categories'));
    }

    public function categoriesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
        ]);

        $category = TransactionCategory::create([
            'name' => $request->name,
            'type' => $request->type,
            'for_system' => false,
        ]);

        ActivityLog::log("إضافة تصنيف جديد: {$category->name}", $category, 'create');

        return redirect()->route('accountant.finance.categories.index')
            ->with('success', 'تم إضافة التصنيف بنجاح');
    }

    public function categoriesUpdate(Request $request, TransactionCategory $category)
    {
        if ($category->for_system) {
            return redirect()->route('accountant.finance.categories.index')
                ->with('error', 'لا يمكن تعديل التصنيفات النظامية');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
        ]);

        $category->update([
            'name' => $request->name,
            'type' => $request->type,
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::log("تحديث التصنيف: {$category->name}", $category, 'update');

        return redirect()->route('accountant.finance.categories.index')
            ->with('success', 'تم تحديث التصنيف بنجاح');
    }

    public function categoriesDestroy(TransactionCategory $category)
    {
        if ($category->for_system) {
            return redirect()->route('accountant.finance.categories.index')
                ->with('error', 'لا يمكن حذف التصنيفات النظامية');
        }

        if ($category->transactions()->count() > 0) {
            return redirect()->route('accountant.finance.categories.index')
                ->with('error', 'لا يمكن حذف التصنيف لوجود حركات مالية مرتبطة به');
        }

        $name = $category->name;
        $category->delete();

        ActivityLog::log("حذف التصنيف: {$name}", null, 'delete');

        return redirect()->route('accountant.finance.categories.index')
            ->with('success', 'تم حذف التصنيف بنجاح');
    }

    /**
     * API: جلب التصنيفات حسب نوع الحركة
     */
    public function getCategoriesByType(Request $request)
    {
        $type = $request->type;

        $categories = TransactionCategory::active()
            ->forHumans()
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->get(['id', 'name', 'type']);

        return response()->json($categories);
    }

    /**
     * API: إنشاء تصنيف جديد (AJAX)
     */
    public function categoriesStoreAjax(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
        ]);

        $category = TransactionCategory::create([
            'name' => $request->name,
            'type' => $request->type,
            'for_system' => false,
        ]);

        ActivityLog::log("إضافة تصنيف جديد: {$category->name}", $category, 'create');

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->type,
                'type_text' => $category->type_text,
            ],
            'message' => 'تم إضافة التصنيف بنجاح'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | الحركات المالية
    |--------------------------------------------------------------------------
    */

    public function transactionsIndex(Request $request)
    {
        $query = FinancialTransaction::with(['treasury', 'category', 'creator']);

        if ($request->filled('treasury_id')) {
            $query->where('treasury_id', $request->treasury_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                    ->orWhere('document_number', 'like', '%' . $request->search . '%')
                    ->orWhere('recipient_name', 'like', '%' . $request->search . '%');
            });
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();
        $treasuries = Treasury::active()->get();
        $categories = TransactionCategory::active()->get();

        $totals = [
            'income' => (clone $query)->where('type', 'income')->sum('amount'),
            'expense' => (clone $query)->where('type', 'expense')->sum('amount'),
        ];

        return view('accountant.finance.transactions.index', compact('transactions', 'treasuries', 'categories', 'totals'));
    }

    public function transactionsCreate(Request $request)
    {
        $treasuries = Treasury::active()->get();
        $categories = TransactionCategory::active()->forHumans()->get();
        $selectedTreasury = $request->treasury_id;

        return view('accountant.finance.transactions.create', compact('treasuries', 'categories', 'selectedTreasury'));
    }

    public function transactionsStore(Request $request)
    {
        $request->validate([
            'treasury_id' => 'required|exists:treasuries,id',
            'category_id' => 'required|exists:transaction_categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cash,bank_transfer',
            'bank_name' => 'nullable|required_if:payment_method,bank_transfer|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'document_number' => 'nullable|string|max:255',
            'recipient_name' => 'nullable|string|max:255',
        ]);

        $treasury = Treasury::findOrFail($request->treasury_id);

        $balanceAfter = $request->type === 'income'
            ? $treasury->current_balance + $request->amount
            : $treasury->current_balance - $request->amount;

        $transaction = FinancialTransaction::create([
            'treasury_id' => $request->treasury_id,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'payment_method' => $request->payment_method,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'document_number' => $request->document_number,
            'recipient_name' => $request->recipient_name,
            'balance_after' => $balanceAfter,
            'created_by' => auth()->id(),
        ]);

        $typeText = $request->type === 'income' ? 'إيراد' : 'مصروف';
        ActivityLog::log("إضافة حركة مالية ({$typeText}): {$request->amount} د.ل", $transaction, 'create');

        return redirect()->route('accountant.finance.transactions.index')
            ->with('success', 'تم إضافة الحركة المالية بنجاح')
            ->with('print_transaction_id', $transaction->id);
    }

    public function transactionsShow(FinancialTransaction $transaction)
    {
        $transaction->load(['treasury', 'category', 'creator']);
        return view('accountant.finance.transactions.show', compact('transaction'));
    }

    public function transactionsPrint(FinancialTransaction $transaction)
    {
        $transaction->load(['treasury', 'category', 'creator']);
        return view('accountant.finance.transactions.print', compact('transaction'));
    }

    public function transactionsPrintReport(Request $request)
    {
        $query = FinancialTransaction::with(['treasury', 'category', 'creator']);

        if ($request->filled('treasury_id')) {
            $query->where('treasury_id', $request->treasury_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                    ->orWhere('document_number', 'like', '%' . $request->search . '%')
                    ->orWhere('recipient_name', 'like', '%' . $request->search . '%');
            });
        }

        $transactions = $query->latest()->get();

        $totals = [
            'income' => $transactions->where('type', 'income')->sum('amount'),
            'expense' => $transactions->where('type', 'expense')->sum('amount'),
        ];

        return view('accountant.finance.transactions.print-report', compact('transactions', 'totals'));
    }
}
