<?php

namespace App\Observers;

use App\Models\CompanyExpense;
use App\Models\Salary;

class SalaryObserver
{
    /**
     * Handle the Salary "created" event.
     */
    public function created(Salary $salary): void
    {
        if ($salary->is_paid) {
            CompanyExpense::create([
                'salary_id'   => $salary->id,
                'title'       => 'Salary Payment - ' . ($salary->addUser->name ?? 'Developer'),
                'amount'      => $salary->amount,
                'currency'    => $salary->currency ?? 'PKR',
                'category'    => 'Salary',
                'date'        => $salary->salary_date,
                'description' => 'Salary automatically logged as expense',
            ]);
        }
    }

    /**
     * Handle the Salary "updated" event.
     */
    public function updated(Salary $salary): void
    {
         $expense = CompanyExpense::where('salary_id', $salary->id)->first();
        if ($salary->is_paid && !$expense) {
            CompanyExpense::create([
                'salary_id'   => $salary->id,
                'title'       => 'Salary Payment - ' . ($salary->addUser->name ?? 'Developer'),
                'amount'      => $salary->amount,
                'currency'    => $salary->currency ?? 'PKR',
                'category'    => 'Salary',
                'date'        => $salary->salary_date,
                'description' => 'Salary automatically logged as expense (paid later)',
            ]);
        }
          // Case 2: is paid & expense exists => update expense
        if ($salary->is_paid && $expense) {
            $expense->update([
                'title'       => 'Salary Payment - ' . ($salary->addUser->name ?? 'Developer'),
                'amount'      => $salary->amount,
                'currency'    => $salary->currency ?? 'PKR',
                'category'    => 'Salary',
                'date'        => $salary->salary_date,
                'description' => 'Salary automatically logged as expense (updated)',
            ]);
        }

        // Case 3: was paid → now unpaid => delete expense
        if (!$salary->is_paid && $expense) {
            $expense->delete();
        }
    }

    /**
     * Handle the Salary "deleted" event.
     */
    public function deleted(Salary $salary): void
    {
        CompanyExpense::where('salary_id', $salary->id)->delete();
    }

    /**
     * Handle the Salary "restored" event.
     */
    public function restored(Salary $salary): void
    {
        //
    }

    /**
     * Handle the Salary "force deleted" event.
     */
    public function forceDeleted(Salary $salary): void
    {
        //
    }
}
