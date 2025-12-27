<?php

namespace App\Traits;

use App\Models\Setting;
use App\Models\User;
use App\Models\EmployeeAccountTransaction;

trait AddsIncentive
{
    /**
     * التحقق من أن الوقت الحالي بعد انتهاء الدوام
     */
    protected function isAfterWorkHours(): bool
    {
        $now = now();
        $workEndTime = Setting::getWorkEndTime(); // مثل "12:30"

        $endTime = \Carbon\Carbon::createFromFormat('H:i', $workEndTime);

        return $now->format('H:i') >= $endTime->format('H:i');
    }

    /**
     * إضافة حافز للموظف
     */
    protected function addIncentiveToEmployee(User $employee, float $amount, string $type, string $description): ?EmployeeAccountTransaction
    {
        // التحقق من تفعيل نظام الحوافز
        if (!Setting::isIncentivesEnabled()) {
            return null;
        }

        // الحوافز تُضاف فقط بعد انتهاء وقت الدوام الرسمي
        if (!$this->isAfterWorkHours()) {
            return null;
        }

        if ($amount <= 0) {
            return null;
        }

        $balanceAfter = $employee->account_balance + $amount;

        $transaction = EmployeeAccountTransaction::create([
            'user_id' => $employee->id,
            'treasury_id' => null,
            'financial_transaction_id' => null,
            'type' => 'credit',
            'transaction_type' => $type, // session_incentive, daycare_incentive
            'amount' => $amount,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'payment_method' => null,
            'created_by' => auth()->id(),
        ]);

        return $transaction;
    }

    /**
     * إضافة حافز جلسة علاجية
     */
    protected function addSessionIncentive(User $employee, string $sessionInfo = ''): ?EmployeeAccountTransaction
    {
        $amount = Setting::getSessionIncentive();
        $description = 'حافز جلسة ' . ($sessionInfo ? " - {$sessionInfo}" : '');

        return $this->addIncentiveToEmployee($employee, $amount, 'session_incentive', $description);
    }

    /**
     * إضافة حافز رعاية نهارية
     */
    protected function addDaycareIncentive(User $employee, string $daycareInfo = ''): ?EmployeeAccountTransaction
    {
        $amount = Setting::getDaycareIncentive();
        $description = 'حافز رعاية نهارية' . ($daycareInfo ? " - {$daycareInfo}" : '');

        return $this->addIncentiveToEmployee($employee, $amount, 'daycare_incentive', $description);
    }
}
