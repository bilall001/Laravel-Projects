<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Services\ExpenseServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $expenseService;
    public function __construct(ExpenseServices $expenseService){
        // $this->middleware('auth:sanctum');
        $this->expenseService = $expenseService;
    }
    public function index()
    {
        $expense = $this->expenseService->getAllExpense();
        return response()->json($expense);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = $request->validate([
             'budget_id' => 'required|exists:budgets,id',
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'description'   => 'nullable|string',
            'expense_date'   => 'required|date',
        ]);
        $validation['user_id'] = Auth::id();
        $expense = $this->expenseService->create($validation);
        return response()->json([
            'message' => 'Expense created successfully',
            'expense' => $expense,
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
          $validation = $request->validate([
             'budget_id' => 'required|exists:budgets,id',
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'description'   => 'nullable|string',
            'expense_date'   => 'required|date',
        ]);
          $expense = $this->expenseService->update($validation, $id);

        if (!$expense) {
            return response()->json(['message' => 'Unauthorized or not found'], 403);
        }
        return response()->json([
            'message' => 'Expense updated successfully',
            'expense' => $expense,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
     $deleted = $this->expenseService->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Unauthorized or not found'], 403);
        }
        return response()->json([
            'message' => "Expense deleted successfully",
        ]);
    }
}
