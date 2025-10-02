<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
     protected $fillable = [
        'cryptocurrency_id',
        'price_usd',
        'market_cap',
        'change_24h',
        'fetched_at',
    ];
      protected $casts = [
        'fetched_at' => 'datetime',
    ];
    public function cryptocurrency()
{
    return $this->belongsToMany(Cryptocurrency::class);
}
}
