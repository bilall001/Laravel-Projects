<?php

namespace App\Repositories;

use App\Models\Budget;

class BudgetRepository
{
    /**
     * Create a new class instance.
     */
     public function getAllForUser($userId)
    {
        return Budget::where('user_id', $userId)->get();
    }

    public function getById($id)
    {
        return Budget::find($id);
    }

    public function create(array $data)
    {
        return Budget::create($data);
    }

    public function update(Budget $budget, array $data)
    {
        $budget->update($data);
        return $budget;
    }

    public function delete(Budget $budget)
    {
        return $budget->delete();
    }    }
