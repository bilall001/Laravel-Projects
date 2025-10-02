<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;

    protected $guarded = [];

      protected $fillable = [
        'developer_id',
        'team_id',
        'project_id',
        'video_link',
        'video_file',
        'points',
         'github_url', 
        'uploaded_at',
    ];

    public function developer() {
        return $this->belongsTo(Developer::class, 'developer_id');
    }

    public function team() {
        return $this->belongsTo(Team::class,'team_id');
    }

    public function project() {
        return $this->belongsTo(Project::class,'project_id');
    }
}