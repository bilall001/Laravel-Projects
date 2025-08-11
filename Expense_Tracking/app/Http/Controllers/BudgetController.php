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
        return view('Budgets.index',compact('budgets'));
    }

    public function create(){
        return view('Budgets.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0|max:99999999.99',
            'month' => 'required|date_format:Y-m'
        ]);
        $validated['month'] = $validated['month'] . '-01';
        $validated['user_id'] = Auth::id();
         $month = \Carbon\Carbon::parse($validated['month'])->startOfMonth();
         $userId = Auth::id();
         $existingBudget = Budget::where('user_id', $userId)
        ->whereRaw("DATE_FORMAT(month, '%Y-%m') = ?", [$month->format('Y-m')])
        ->first();

    if ($existingBudget) {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors(['month' => 'You have already created a budget for ' . $month->format('F Y') . '. You can update or delete it.']);
    }

        $this->budgetService->create($validated);

         return redirect()
        ->route('budgets.index')
        ->with('success', 'Budget created successfully!');
    }

    public function edit($id){
        $budget = $this->budgetService->getById($id);
        return view('Budgets.edit',compact('budget'));
    }
    public function update(Request $request, $id)
    {
        $budget = $this->budgetService->getById($id);

        if (!$budget || $budget->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized or not found'], 403);
        }

        $validated = $request->validate([
           'amount' => 'required|numeric|min:0|max:99999999.99',
            'month' => 'required|date_format:Y-m'
        ]);
         $validated['month'] = $validated['month'] . '-01';
         $this->budgetService->update($budget, $validated);

        return redirect()
        ->route('budgets.index')
        ->with('success', 'Budget updated successfully!');
    }

    public function destroy($id)
    {
        $budget = $this->budgetService->getById($id);

        if (!$budget || $budget->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized or not found'], 403);
        }

        $this->budgetService->delete($budget);
        return redirect()
        ->route('budgets.index')
        ->with('success', 'Budget deleted successfully!');
}
}