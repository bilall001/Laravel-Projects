<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadOtherDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'lead_id',
        'platform_name',
        'platform_url',
        'campaign_name',
        'inquiry_message',
        'estimated_budget',
        'contact_method',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
