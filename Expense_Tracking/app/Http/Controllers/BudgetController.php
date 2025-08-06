<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BudgetService;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    protected $budgetService;

    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function index()
    {
        $budgets = $this->budgetService->getAll(Auth::id());
        return response()->json($budgets);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'month' => 'required'
        ]);

        $validated['user_id'] = Auth::id();
        $budget = $this->budgetService->create($validated);

        return response()->json([
            'budget' => $budget,
            'message' => 'Budget created successfully'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $budget = $this->budgetService->getById($id);

        if (!$budget || $budget->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized or not found'], 403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric',
            'month' => 'required'
        ]);

        $updated = $this->budgetService->update($budget, $validated);

        return response()->json([
            'budget' => $updated,
            'message' => 'Budget updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $budget = $this->budgetService->getById($id);

        if (!$budget || $budget->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized or not found'], 403);
        }

        $this->budgetService->delete($budget);

        return response()->json(['message' => 'Budget deleted successfully']);
    }
}
