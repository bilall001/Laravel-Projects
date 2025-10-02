<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $guarded = [];
    protected $casts = [
    'expense_date' => 'date',
];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }
}
