<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddUser extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'username',
    'email',
    'password',
    'role',
];


    protected $hidden = [
        'password',
    ];

public function developer()
{
    return $this->hasOne(Developer::class, 'add_user_id');
}
public function clients()
{
    return $this->hasMany(Client::class, 'user_id');
}
public function teams()
{
    return $this->belongsToMany(Team::class, 'team_user', 'user_id', 'team_id');
}
public function teamManager()
{
    return $this->hasMany(TeamManager::class, 'user_id');
}

    
}