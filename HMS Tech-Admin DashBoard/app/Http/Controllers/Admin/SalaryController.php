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
        $month = $request->get('month', now()->format('Y-m'));

        $salaries = Salary::with('developer')
                    ->whereRaw("DATE_FORMAT(salary_date, '%Y-%m') = ?", [$month])
                    ->get();

        $developers = AddUser::where('role', 'developer')->get();

        return view('admin.pages.salary', compact('salaries', 'developers', 'month'));
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

        // ✅ Map correct FK field
        $data['developer_id'] = $data['add_user_id'];
        unset($data['add_user_id']);

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
        $data['developer_id'] = $data['add_user_id'];
        unset($data['add_user_id']);

        $salary->update($data);

        return redirect()->route('admin.salaries.index')->with('success', 'Salary updated successfully.');
    }

    public function destroy($id)
    {
        Salary::destroy($id);
        return back()->with('success', 'Salary deleted.');
    }
}