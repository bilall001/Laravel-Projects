<?php

namespace App\Models;

use App\Models\AddUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Developer extends Model
{
    use HasFactory;

    protected $fillable = [
        'add_user_id',
        'skill',
        'experience',
        'part_time',
        'full_time',
        'internship',
        'job',
        'salary',
        'profile_image',
        'salary_type',
        'cnic_front',
        'cnic_back',
        'contract_file',
    ];

    protected $table = 'developers';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(AddUser::class, 'add_user_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_user', 'developer_id', 'team_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_developer', 'developer_id', 'project_id');
    }

    public function developer()
    {
        return $this->belongsTo(AddUser::class, );
    }
    public function payments()
    {
        return $this->hasMany(DeveloperProjectPayment::class);
    }

    public function tasks()
    {
       return $this->belongsToMany(Task::class, 'task_assignees', 'developer_id', 'task_id')
                    ;
    }
    public function projectRoles()
    {
        return $this->hasMany(ProjectMemberRole::class);
    }
     public function points()
    {
        return $this->hasMany(Point::class, 'developer_id');
    }
}
