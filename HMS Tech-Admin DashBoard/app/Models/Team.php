<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name','team_lead_id'];

    // Relate team to add_users instead of default users table
    public function users()
    {
        return $this->belongsToMany(AddUser::class, 'team_user', 'team_id', 'user_id');
    }
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function teamManager()
    {
        return $this->belongsToMany(TeamManager::class, 'team_manager_teams', 'team_id', 'team_manager_id');
    }
    public function members()
{
    return $this->belongsToMany(Developer::class, 'team_users', 'team_id', 'user_id');
}
public function teamLead()
{
    return $this->belongsTo(Developer::class, 'team_lead_id')->with('user');
}
}
