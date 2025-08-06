<?php

namespace App\Services;

// use Illuminate\Container\Attributes\Auth;

use App\Models\Budget;
use App\Repositories\BudgetRepository;
use Illuminate\Support\Facades\Auth;
class BudgetService
{
    /**
     * Create a new class instance.
     */
   protected $budgetRepository;

    public function __construct(BudgetRepository $budgetRepository)
    {
        $this->budgetRepository = $budgetRepository;
    }

    public function getAll($userId)
    {
        return $this->budgetRepository->getAllForUser($userId);
    }

    public function create(array $data)
    {
        return $this->budgetRepository->create($data);
    }

    public function getById($id)
    {
        return $this->budgetRepository->getById($id);
    }

    public function update(Budget $budget, array $data)
    {
        return $this->budgetRepository->update($budget, $data);
    }

    public function delete(Budget $budget)
    {
        return $this->budgetRepository->delete($budget);
    }
}
