<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadFiverrDetail extends Model
{
    use HasFactory;
     protected $fillable = [
        'lead_id',
        'gig_title',
        'buyer_request_message',
        'offer_amount',
        'buyer_username',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
