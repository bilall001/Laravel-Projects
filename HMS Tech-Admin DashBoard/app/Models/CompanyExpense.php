<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyExpense extends Model
{
    use HasFactory;


      protected $fillable = [
        'title',
        'salary_id',
        'developer_payment_id',
        'description',
        'amount',
        'currency',
        'category',
        'date',
        'receipt_file',
        'created_by',
    ];
}