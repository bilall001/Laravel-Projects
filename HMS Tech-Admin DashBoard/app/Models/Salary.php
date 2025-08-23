<?php

// app/Models/Salary.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'add_user_id', 'salary_date', 'amount', 'payment_method',
        'payment_receipt', 'is_paid',
    ];

  public function addUser()
    {
        return $this->belongsTo(AddUser::class, 'add_user_id');
    }
}