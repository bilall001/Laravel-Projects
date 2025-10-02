<?php

namespace App\Models;

use App\Http\Controllers\Admin\BusinessDeveloperController;
use App\Models\Team;
use App\Models\AddUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;

/**
 * @property string|null $assignment_type
 */
class Project extends Model
{
    use HasFactory;
    protected $appends = ['assignment_type'];

    // default null if not set
    public function getAssignmentTypeAttribute()
    {
        return $this->attributes['assignment_type'] ?? null;
    }
    protected $fillable = [
        'client_id',
        'title',
        'price',
        'duration',
        'start_date',
        'end_date',
        'developer_end_date',
        'type',
        'business_developer_id',
        'paid_price',
        'remaining_price',
        'file',
        'body_html',    
        'body_json',
    ];

    protected $casts = [
        'price' => 'float',
        'paid_price' => 'float',
        'remaining_price' => 'float',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function developers()
    {
        // Many developers can be assigned to a project
        return $this->belongsToMany(Developer::class, 'project_developer', 'project_id', 'developer_id');
    }
    public function team()
    {
        return $this->belongsTo(Team::class, 'project_team');
    }
    public function teams()
    {
        // Many teams can be assigned to a project
        return $this->belongsToMany(Team::class, 'project_team', 'project_id', 'team_id');
    }

    public function businessDeveloper()
    {
        return $this->belongsTo(AddUser::class, 'business_developer_id');
    }
    public function schedules()
    {
        return $this->hasMany(ProjectSchedule::class, 'project_id');
    }
    public function payments()
    {
        return $this->hasMany(DeveloperProjectPayment::class);
    }
    public function tasks()
    {
        return $this->hasMany(Task::class,'project_id');
    }
    public function images()
    {
        return $this->hasMany(\App\Models\ProjectAsset::class);
    }
    public function memberRoles()
    {
        return $this->hasMany(ProjectMemberRole::class);
    }
    public function developersWithRoles()
    {
        return $this->belongsToMany(Developer::class, 'project_member_roles')
            ->withPivot('role_id');
    }
     public function points()
    {
        return $this->hasMany(Point::class, 'project_id');
    }
    public function eligibleDevelopers(): Collection
    {
        if ($this->type === 'individual') {
            return $this->developers()->with('user')->get();
        }

        // type === 'team' -> from all attached teams
        $teamMembers = Developer::query()
            ->whereHas('teams', function ($q) {
                $q->whereIn('teams.id', $this->teams()->pluck('teams.id'));
            })
            ->with('user')
            ->get();

        // distinct by developer id
        return $teamMembers->unique('id')->values();
    }
}
