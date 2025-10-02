<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'email', 'phone', 'gender'];

    // Relation to User model
    public function user()
    {
        return $this->belongsTo(AddUser::class,'user_id');
    }

    // Relation to Projects
    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id');
    }
}
