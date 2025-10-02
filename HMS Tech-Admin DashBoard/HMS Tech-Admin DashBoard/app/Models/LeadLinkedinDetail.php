<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadLinkedinDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'lead_id',
        'company_name',
        'profile_link',
        'job_post_url',
        'message_sent',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
