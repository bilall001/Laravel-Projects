<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;
    protected $fillable = [
        'business_developer_id',
        'project_id',
        'client_id',
        'lead_title',
        'lead_description',
        'status',
        'lead_get_by',
        'expected_budget',
        'expected_start_date',
        'contact_person',
        'contact_email',
        'contact_phone',
        'next_follow_up',
        'notes',
    ];

    // Relationships
    public function businessDeveloper()
    {
        return $this->belongsTo(BusinessDeveloper::class, 'business_developer_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Platform-specific relations
    public function upwork()
    {
        return $this->hasOne(LeadUpworkDetail::class);
    }

    public function linkedin()
    {
        return $this->hasOne(LeadLinkedinDetail::class);
    }

    public function facebook()
    {
        return $this->hasOne(LeadFacebookDetail::class);
    }

    public function fiverr()
    {
        return $this->hasOne(LeadFiverrDetail::class);
    }

    public function other()
    {
        return $this->hasOne(LeadOtherDetail::class);
    }
}
