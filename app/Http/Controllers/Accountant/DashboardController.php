<?php

namespace App\Http\Controllers\Accountant;

use App\Models\Treasury;
use App\Models\TransactionCategory;
use App\Models\FinancialTransaction;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'treasuries_count' => Treasury::active()->count(),
            'total_balance' => Treasury::active()->sum('current_balance'),
            'today_income' => FinancialTransaction::whereDate('created_at', today())->where('type', 'income')->sum('amount'),
            'today_expense' => FinancialTransaction::whereDate('created_at', today())->where('type', 'expense')->sum('amount'),
            'month_income' => FinancialTransaction::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->where('type', 'income')->sum('amount'),
            'month_expense' => FinancialTransaction::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->where('type', 'expense')->sum('amount'),
        ];

        $treasuries = Treasury::active()->get();
        $recentTransactions = FinancialTransaction::with(['treasury', 'category', 'creator'])
            ->latest()
            ->take(10)
            ->get();

        return view('accountant.dashboard', compact('stats', 'treasuries', 'recentTransactions'));
    }
}
