<?php

namespace App\Services;

use App\Repositories\ExpenseRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class ExpenseServices
{
    /**
     * Create a new class instance.
     */
    protected $expenseRepo;
    public function __construct(ExpenseRepository $expenseRepo)
    {
        $this->expenseRepo = $expenseRepo;
    }
    public function getAllExpense(){
        return $this->expenseRepo->getAll(Auth::id());
    }
    public function expenseId($id){
        return $this->expenseRepo->findUser($id);
    }
    public function create(array $data){
        $data['user_id'] = Auth::id();
        return $this->expenseRepo->create($data);
    }
    public function update(array $data, $id){
       $expense = $this->expenseRepo->findUser($id);
        if (!$expense) {
    throw new ModelNotFoundException("Expense not found.");
}

if ($expense->user_id !== Auth::id()) {
    throw new AuthorizationException("Unauthorized access.");
}
        return $this->expenseRepo->update($expense, $data);
    }
    public function delete($id){
        $expense = $this->expenseRepo->findUser($id);
        if (!$expense || $expense->user_id !== Auth::id()) {
            throw new ModelNotFoundException("Unauthorized or not found.");
            }
            return $this->expenseRepo->delete($expense);
    }
}
