<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'add_users'; // ✅ custom table name

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


        public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_user', 'user_id', 'team_id');
    }

    // Add your developer() if needed:
    public function developer()
    {
        return $this->hasOne(Developer::class, 'add_user_id');
    }
     public function points()
    {
        return $this->hasMany(Point::class, 'developer_id');
    }
    public function projects()
{
    return $this->hasMany(Project::class, 'client_id');
}
public function teamManager()
{
    return $this->hasMany(TeamManager::class, 'user_id');
}

}