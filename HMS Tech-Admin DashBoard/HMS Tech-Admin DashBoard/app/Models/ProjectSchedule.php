<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'date', 'status'];

    // Define relationship to Project
    public function project()
    {
        return $this->belongsTo(Project::class,'project_id');
    }
}
