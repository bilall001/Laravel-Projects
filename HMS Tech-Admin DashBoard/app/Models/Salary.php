<?php

// app/Models/Salary.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'developer_id', 'salary_date', 'amount', 'payment_method',
        'payment_receipt', 'is_paid',
    ];

   public function developer()
    {
        return $this->belongsTo(AddUser::class, 'developer_id');
    }
}