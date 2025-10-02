<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Developer;
use App\Models\Project;
use App\Models\DeveloperProjectPayment;
use Illuminate\Http\Request;

class DeveloperProjectPaymentController extends Controller
{
    /**
     * Display all payments with developers and projects.
     */
    public function index()
    {
        $payments = DeveloperProjectPayment::with(['developer.user', 'project'])->latest()->paginate(10);
        $developers = Developer::with('user')->get();
        $projects = Project::all();

        return view('admin.pages.developer_project_payments.index', compact('payments', 'developers', 'projects'));
    }

    /**
     * Store a new payment from modal form.
     */ 
    public function store(Request $request)
    {
        $data = $request->validate([
            'developer_id' => 'required|exists:developers,id',
            'project_id'   => 'required|exists:projects,id',
            'payment_type' => 'required|in:fixed,percentage',
            'amount'       => 'required|numeric|min:0',
            'status'       => 'required|in:pending,paid',
            'notes'        => 'nullable|string',
        ]);

        DeveloperProjectPayment::create($data);

        return redirect()->route('developer_project_payments.index')->with('success', 'Payment record created successfully!');
    }

    /**
     * Update an existing payment from modal form.
     */
    public function update(Request $request, DeveloperProjectPayment $developerProjectPayment)
    {
        $data = $request->validate([
            'developer_id' => 'required|exists:developers,id',
            'project_id'   => 'required|exists:projects,id',
            'payment_type' => 'required|in:fixed,percentage',
            'amount'       => 'required|numeric|min:0',
            'status'       => 'required|in:pending,paid',
            'notes'        => 'nullable|string',
        ]);

        $developerProjectPayment->update($data);

        return redirect()->route('developer_project_payments.index')->with('success', 'Payment record updated successfully!');
    }

    /**
     * Delete a payment record.
     */
    public function destroy(DeveloperProjectPayment $developerProjectPayment)
    {
        $developerProjectPayment->delete();

        return redirect()->route('developer_project_payments.index')->with('success', 'Payment record deleted successfully!');
    }
}
