<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamManager extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'profile_image',
        'experience',
        'skill1',
        'contract_file',
    ];

    public function user()
    {
        return $this->belongsTo(AddUser::class, 'user_id');
    }

   public function teams()
{
     return $this->belongsToMany(Team::class, 'team_manager_teams', 'team_manager_id', 'team_id');
}
}
