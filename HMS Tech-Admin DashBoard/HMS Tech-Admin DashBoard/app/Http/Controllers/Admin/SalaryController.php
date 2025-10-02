<?php

namespace App\Http\Controllers\Admin;

use App\Models\Salary;
use App\Models\AddUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalaryController extends Controller
{
   public function index(Request $request)
{
    // Get month from request, default to current month (YYYY-MM format)
    $month = $request->get('month', now()->format('Y-m'));

    // Fetch salaries with related user (add_users relationship)
    $salaries = Salary::with('addUser')
                ->whereRaw("DATE_FORMAT(salary_date, '%Y-%m') = ?", [$month])
                ->get();

    // ✅ Fetch all users (not just developers)
    $users = AddUser::all();

    // Return to view
    return view('admin.pages.salary', compact('salaries', 'users', 'month'));
}


    public function store(Request $request)
    {
        $data = $request->validate([
            'add_user_id'     => 'required|exists:add_users,id',
            'salary_date'     => 'required|date',
            'amount'          => 'required|numeric',
            'payment_method'  => 'required|in:Cash,Account',
            'payment_receipt' => 'nullable|image',
        ]);

        // ✅ Store receipt if uploaded
        if ($request->hasFile('payment_receipt')) {
            $data['payment_receipt'] = $request->file('payment_receipt')->store('receipts', 'public');
        }

        $data['is_paid'] = $request->has('is_paid');

        Salary::create($data);

        return redirect()->route('admin.salaries.index')->with('success', 'Salary paid successfully.');
    }

    public function update(Request $request, $id)
    {
        $salary = Salary::findOrFail($id);

        $data = $request->validate([
            'add_user_id'     => 'required|exists:add_users,id',
            'salary_date'     => 'required|date',
            'amount'          => 'required|numeric',
            'payment_method'  => 'required|in:Cash,Account',
            'payment_receipt' => 'nullable|image',
        ]);

        if ($request->hasFile('payment_receipt')) {
            $data['payment_receipt'] = $request->file('payment_receipt')->store('receipts', 'public');
        }

        $data['is_paid'] = $request->has('is_paid');

        $salary->update($data);

        return redirect()->route('admin.salaries.index')->with('success', 'Salary updated successfully.');
    }

    public function destroy($id)
    {
        Salary::destroy($id);
        return back()->with('success', 'Salary deleted.');
    }
}
