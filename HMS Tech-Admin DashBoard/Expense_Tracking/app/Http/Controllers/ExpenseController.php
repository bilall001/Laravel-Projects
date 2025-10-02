<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use App\Services\ExpenseServices;
use Carbon\Carbon;
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
        $expenses = $this->expenseService->getAllExpense();
        return view('Expenses.index',compact('expenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
 $userId = Auth::id();

    // 1. Fetch categories belonging to the user
    $categories = Category::where('user_id', $userId)->get();

    // 2. Get the current month budget for this user
    //    (or you could use request('month') if you want a different month)
      $currentMonth = now()->format('Y-m'); // e.g., 2025-08
      $budget = Budget::where('user_id', $userId)
        ->whereRaw("DATE_FORMAT(month, '%Y-%m') = ?", [$currentMonth])
        ->orderBy('created_at', 'desc') // latest entry first
        ->first();

    // dd($budget);
    // Pass to the view
    return view('expenses.create', compact('categories', 'budget'));
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
        $this->expenseService->create($validation);
      return redirect()
        ->route('expenses.index')
        ->with('success', 'Expense created successfully!');

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
    public function edit($id)
    {   
    $userId = Auth::id();

    // 1. Fetch categories belonging to the user
    $categories = Category::where('user_id', $userId)->get();

    // 2. Get the current month budget for this user
    //    (or you could use request('month') if you want a different month)
      $currentMonth = now()->format('Y-m'); // e.g., 2025-08
      $budget = Budget::where('user_id', $userId)
        ->whereRaw("DATE_FORMAT(month, '%Y-%m') = ?", [$currentMonth])
        ->orderBy('created_at', 'desc') // latest entry first
        ->first();
        $expense = $this->expenseService->expenseId($id);
        return view('Expenses.edit',compact('expense','categories','budget'
    ));
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
        return redirect()
        ->route('expenses.index')
        ->with('success', 'Expense updated successfully!');
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
        return redirect()
        ->route('expenses.index')
        ->with('success', 'Expense deleted successfully!');
    }
}
