<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'project_id', 'user_id', 'team_id', 'description', 'end_date', 'is_team_project'
    ];

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function user() {
        return $this->belongsTo(AddUser::class,'user_id');
    }

    public function team() {
        return $this->belongsTo(Team::class);
    }
}