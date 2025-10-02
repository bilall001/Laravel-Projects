<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadUpworkDetail extends Model
{
    use HasFactory;
     protected $fillable = [
        'lead_id',
        'project_title',
        'proposal_cover_letter',
        'connect_bids',
        'job_url',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
