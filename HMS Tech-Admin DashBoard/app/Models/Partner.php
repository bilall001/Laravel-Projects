<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Partner extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image',
    ];

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }
    public function user()
    {
        return $this->belongsTo(AddUser::class, 'user_id');
    }
}
