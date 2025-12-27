<?php

namespace App\Http\Controllers\Accountant;

use App\Models\User;
use App\Models\Payroll;
use App\Models\Treasury;
use App\Models\ActivityLog;
use App\Models\PayrollItem;
use Illuminate\Http\Request;
use App\Models\EmployeeAttendance;
use App\Http\Controllers\Controller;
use App\Models\EmployeeAccountTransaction;

class PayrollController extends Controller
{
    /**
     * قائمة كشوفات المرتبات
     */
    public function index(Request $request)
    {
        $query = Payroll::with(['creator', 'executor'])
            ->withCount('items');

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->latest()->paginate(12)->withQueryString();

        $years = Payroll::distinct()->pluck('year')->sort()->reverse();

        return view('accountant.payroll.index', compact('payrolls', 'years'));
    }

    /**
     * إنشاء كشف مرتبات جديد
     */
    public function create()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // السنوات المتاحة
        $years = range($currentYear - 2, $currentYear + 1);

        // الأشهر
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return view('accountant.payroll.create', compact('years', 'months', 'currentYear', 'currentMonth'));
    }

    /**
     * عرض/تحرير كشف المرتبات
     */
    public function show(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        // البحث عن كشف موجود أو إنشاء جديد
        $payroll = Payroll::firstOrCreate(
            ['year' => $year, 'month' => $month],
            ['created_by' => auth()->id(), 'status' => 'draft']
        );

        // إذا كان مسودة، قم بتحديث/إنشاء العناصر
        if ($payroll->status === 'draft') {
            $this->syncPayrollItems($payroll);
        }

        $payroll->load(['items.user']);

        // الأشهر
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        $years = range(now()->year - 2, now()->year + 1);

        return view('accountant.payroll.show', compact('payroll', 'months', 'years', 'year', 'month'));
    }

    /**
     * مزامنة عناصر الكشف مع الموظفين
     */
    private function syncPayrollItems(Payroll $payroll)
    {
        $employees = User::where('is_active', true)->get();

        foreach ($employees as $employee) {
            // حساب أيام وساعات العمل من الحضور
            $startDate = "{$payroll->year}-{$payroll->month}-01";
            $endDate = date('Y-m-t', strtotime($startDate));

            $attendances = EmployeeAttendance::where('user_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereIn('status', ['present', 'late'])
                ->get();

            $workDays = $attendances->count();
            $workHours = $attendances->sum(function ($att) {
                return $att->work_hours ?? 0;
            });

            PayrollItem::updateOrCreate(
                ['payroll_id' => $payroll->id, 'user_id' => $employee->id],
                [
                    'base_salary' => $employee->salary ?? 0,
                    'work_days' => $workDays,
                    'work_hours' => round($workHours, 2),
                ]
            );
        }
    }

    /**
     * تحديث عنصر في الكشف
     */
    public function updateItem(Request $request, PayrollItem $item)
    {
        if ($item->payroll->status === 'executed') {
            return response()->json(['error' => 'لا يمكن التعديل على كشف تم تنفيذه'], 422);
        }

        $request->validate([
            'base_salary' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $data = [];
        if ($request->has('base_salary')) $data['base_salary'] = $request->base_salary;
        if ($request->has('bonus')) $data['bonus'] = $request->bonus;
        if ($request->has('deduction')) $data['deduction'] = $request->deduction;
        if ($request->has('notes')) $data['notes'] = $request->notes;

        $item->update($data);

        return response()->json([
            'success' => true,
            'net_salary' => number_format($item->fresh()->net_salary, 2),
        ]);
    }

    /**
     * تنفيذ كشف المرتبات
     * يسجل حركات محاسبية في حسابات الموظفين فقط (بدون تأثير على الخزينة)
     */
    public function execute(Request $request, Payroll $payroll)
    {
        if ($payroll->status === 'executed') {
            return redirect()->back()->with('error', 'تم تنفيذ هذا الكشف مسبقاً');
        }

        $payroll->load('items.user');

        foreach ($payroll->items as $item) {
            if ($item->is_processed) continue;

            $user = $item->user;
            $netSalary = $item->net_salary;

            // تسجيل صافي الراتب كحركة في حساب الموظف (له)
            // هذا تسجيل محاسبي فقط - الصرف الفعلي يكون لاحقاً
            if ($netSalary > 0) {
                $balanceAfter = $user->account_balance + $netSalary;

                EmployeeAccountTransaction::create([
                    'user_id' => $user->id,
                    'treasury_id' => null, // لا يوجد تأثير على الخزينة
                    'financial_transaction_id' => null,
                    'type' => 'credit', // له
                    'transaction_type' => 'salary',
                    'amount' => $netSalary,
                    'balance_after' => $balanceAfter,
                    'description' => "راتب شهر {$payroll->period_text}" .
                        ($item->bonus > 0 ? " (يشمل مكافأة " . number_format($item->bonus, 2) . ")" : '') .
                        ($item->deduction > 0 ? " (بعد خصم " . number_format($item->deduction, 2) . ")" : ''),
                    'created_by' => auth()->id(),
                ]);
            }

            $item->update(['is_processed' => true]);
        }

        $payroll->update([
            'status' => 'executed',
            'executed_by' => auth()->id(),
            'executed_at' => now(),
        ]);

        ActivityLog::log(
            "تنفيذ كشف مرتبات شهر {$payroll->period_text} - إجمالي: " . number_format($payroll->total_net, 2) . " د.ل",
            $payroll,
            'update'
        );

        return redirect()->route('accountant.payroll.show', ['year' => $payroll->year, 'month' => $payroll->month])
            ->with('success', 'تم تنفيذ كشف المرتبات بنجاح. الرواتب مسجلة في حسابات الموظفين.');
    }

    /**
     * طباعة كشف المرتبات
     */
    public function print(Payroll $payroll)
    {
        $payroll->load(['items.user', 'creator', 'executor']);
        return view('accountant.payroll.print', compact('payroll'));
    }
}
