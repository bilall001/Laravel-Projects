<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadFacebookDetail extends Model
{
    use HasFactory;
     protected $fillable = [
        'lead_id',
        'page_name',
        'ad_campaign_name',
        'post_url',
        'inquiry_message',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
