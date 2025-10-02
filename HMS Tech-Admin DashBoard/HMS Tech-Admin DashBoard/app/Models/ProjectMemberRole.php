<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMemberRole extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'developer_id',
        'role_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
