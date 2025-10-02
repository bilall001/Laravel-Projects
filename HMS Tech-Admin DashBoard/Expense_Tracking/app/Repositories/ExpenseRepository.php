<?php

namespace App\Repositories;

use App\Models\Expense;

class ExpenseRepository
{
  public function getAll($userid){
    return Expense::where('user_id',$userid)->latest()->get();
  }
  public function findUser($id){
    return Expense::find($id);
  }
   public function create(array $data){
    $expense = Expense::create($data);
    return $expense;
   }
   public function update(Expense $expense,array $data){
    $expense->update($data);
    return $expense;
   }
   public function delete(Expense $expense){
   return $expense->delete();
   }
}
