<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyProfit extends Model
{
     protected $fillable = ['month','total_revenue','total_expenses','net_profit'];

    public function partnerProfits() {
        return $this->hasMany(PartnerProfit::class);
    }
}
