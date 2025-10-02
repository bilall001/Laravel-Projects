<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'contribution',
        'payment_method',
        'payment_receipt',
        'contribution_date',
        'is_received'
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}