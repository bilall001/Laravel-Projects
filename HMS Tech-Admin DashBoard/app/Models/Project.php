<?php

namespace App\Models;

use App\Http\Controllers\Admin\BusinessDeveloperController;
use App\Models\Team;
use App\Models\AddUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
 protected $fillable = [
    'title', 'price', 'duration', 'start_date', 'end_date', 'developer_end_date', 'type', 'team_id', 'user_id', 'file'
];

protected $casts = [
    'price' => 'float',
];

public function client() {
    return $this->belongsTo(Client::class);
}

 public function user()
{
    return $this->belongsTo(AddUser::class, 'user_id');
}

public function team()
{
    return $this->belongsTo(Team::class, 'team_id');
}
public function teams()
{
    return $this->belongsToMany(Team::class, 'team_user', 'project_id', 'team_id');
}

public function businessDeveloper()
{
    return $this->belongsTo(AddUser::class, 'business_developer_id');
}
public function schedules()
{
    return $this->hasMany(ProjectSchedule::class, 'project_id');
}
}