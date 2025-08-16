<?php

namespace App\Http\Controllers\Admin;

use App\Models\Developer;
use App\Models\CompanyExpense;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyExpenseController extends Controller
{
    /**
     * Display all expenses (with create/edit modals).
     */
    public function index()
    {
        $developers = Developer::with('user')->get();
        $expenses = CompanyExpense::latest()->get();

        return view('admin.pages.companyExpense.add_expense', compact('developers', 'expenses'));
    }

    /**
     * Store a new expense.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'amount' => 'required|numeric',
            'currency' => 'nullable',
            'category' => 'nullable',
            'date' => 'required|date',
            'receipt_file' => 'nullable|file',
        ]);

        $data = $request->only([
            'title', 'description', 'amount', 'currency', 'category', 'date',
        ]);

        if ($request->hasFile('receipt_file')) {
            $data['receipt_file'] = $this->uploadFile($request->file('receipt_file'));
        }

        CompanyExpense::create($data);

        return redirect()->route('companyExpense.index')->with('success', 'Expense created successfully!');
    }

    /**
     * Update an existing expense.
     */
    public function update(Request $request, CompanyExpense $companyExpense)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'amount' => 'required|numeric',
            'currency' => 'nullable',
            'category' => 'nullable',
            'date' => 'required|date',
            'receipt_file' => 'nullable|file',
        ]);

        $data = $request->only([
            'title', 'description', 'amount', 'currency', 'category', 'date',
        ]);

        if ($request->hasFile('receipt_file')) {
            // Delete old
            if ($companyExpense->receipt_file && file_exists(public_path($companyExpense->receipt_file))) {
                unlink(public_path($companyExpense->receipt_file));
            }

            $data['receipt_file'] = $this->uploadFile($request->file('receipt_file'));
        }

        $companyExpense->update($data);

        return redirect()->route('companyExpense.index')->with('success', 'Expense updated successfully!');
    }

    /**
     * Delete an expense.
     */
    public function destroy(CompanyExpense $companyExpense)
    {
        if ($companyExpense->receipt_file && file_exists(public_path($companyExpense->receipt_file))) {
            unlink(public_path($companyExpense->receipt_file));
        }

        $companyExpense->delete();

        return redirect()->route('companyExpense.index')->with('success', 'Expense deleted successfully!');
    }

    /**
     * Handle file upload.
     */
    protected function uploadFile($file)
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = public_path('uploads/expenses/receipts');

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $file->move($path, $filename);

        return 'uploads/expenses/receipts/' . $filename;
    }
}
