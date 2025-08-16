<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessDeveloper extends Model
{
    use HasFactory;

    protected $fillable = [
        'add_user_id',
        'phone',
        'gender',
        'percentage',
        'image',
        'cnic_front',
        'cnic_back',
    ];

    public function addUser()
    {
        return $this->belongsTo(AddUser::class, 'add_user_id');
    }
}
