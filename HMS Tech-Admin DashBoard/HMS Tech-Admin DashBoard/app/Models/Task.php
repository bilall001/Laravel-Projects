<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
       'project_id',
        'title',
        'body_html',
        'body_json',
        'due_date',
        'status',   // 
        'priority', // low | normal | high | urgent
        'created_by'
    ];
     protected $casts = [
        'due_date' => 'date',
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // people assigned to the task
    public function assignees()
    {
        return $this->belongsToMany(Developer::class, 'task_assignees', 'task_id', 'developer_id')
                    ;
    }

    // optional team-level assignment/labeling
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'task_teams', 'task_id', 'team_id')
                   ;
    }

    public function images()
    {
        return $this->hasMany(TaskImage::class);
    }

    public function creator()
    {
        return $this->belongsTo(AddUser::class, 'created_by');
    }
    public function user()
    {
        return $this->belongsTo(AddUser::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function assignedDeveloper() {
    return $this->belongsTo(Developer::class, 'developer_id');
}

// Helper to get role within a project
public function getRolesForAssignees()
{
    if (!$this->project || $this->assignees->isEmpty()) {
        return collect();
    }

    $developerIds = $this->assignees->pluck('id');

    return $this->project
        ->memberRoles()
        ->whereIn('developer_id', $developerIds)
        ->with('developer.user', 'role')
        ->get();
}
}
