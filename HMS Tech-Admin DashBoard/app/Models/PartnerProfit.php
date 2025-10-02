<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerProfit extends Model
{
 protected $fillable = [
        'monthly_profit_id','partner_id','percentage','profit_amount','is_received','reinvested'
    ];

    public function monthlyProfit() {
        return $this->belongsTo(MonthlyProfit::class);
    }

    public function partner() {
        return $this->belongsTo(Partner::class);
    }
}
