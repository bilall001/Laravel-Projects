<?php
namespace App\Observers;

use App\Models\CompanyExpense;
use App\Models\DeveloperProjectPayment;
use Illuminate\Support\Facades\Auth;

class DeveloperProjectPaymentObserver
{
    public function created(DeveloperProjectPayment $payment): void
    {
        if ($this->isPaid($payment)) {
            CompanyExpense::create([
                'developer_payment_id' => $payment->id,
                'title'       => 'Developer Payment - ' . ($payment->developer->user->name ?? 'Developer1'),
                'description' => $payment->notes ?: 'Developer project payment automatically logged as expense',
                'amount'      => $payment->amount,
                'currency'    => 'PKR',
                'category'    => 'Developer Payment',
                'date'        => now(),
                'created_by'  => optional(Auth::user())->id,
            ]);
        }
    }

    public function updated(DeveloperProjectPayment $payment): void
    {
        $expense = CompanyExpense::where('developer_payment_id', $payment->id)->first();

        // Case 1: status = paid & no expense => create
        if ($this->isPaid($payment) && !$expense) {
            CompanyExpense::create([
                'developer_payment_id' => $payment->id,
                'title'       => 'Developer Payment - ' . ($payment->developer->name ?? 'Developer'),
                'description' => $payment->notes ?: 'Developer project payment logged as expense (paid later)',
                'amount'      => $payment->amount,
                'currency'    => 'PKR',
                'category'    => 'Developer Payment',
                'date'        => now(),
                'created_by'  => optional(Auth::user())->id,
            ]);
        }

        // Case 2: status = paid & expense exists => update
        if ($this->isPaid($payment) && $expense) {
            $expense->update([
                'title'       => 'Developer Payment - ' . ($payment->developer->name ?? 'Developer'),
                'description' => $payment->notes ?: $expense->description,
                'amount'      => $payment->amount,
            ]);
        }

        // Case 3: status = pending & expense exists => delete
        if (!$this->isPaid($payment) && $expense) {
            $expense->delete();
        }
    }

    public function deleted(DeveloperProjectPayment $payment): void
    {
        CompanyExpense::where('developer_payment_id', $payment->id)->delete();
    }

    private function isPaid(DeveloperProjectPayment $payment): bool
    {
        return strtolower((string) $payment->status) === 'paid';
    }
}
