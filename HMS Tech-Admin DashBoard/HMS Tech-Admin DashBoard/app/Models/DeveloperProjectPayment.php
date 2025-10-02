<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


    class DeveloperProjectPayment extends Model
{
    protected $fillable = [
        'developer_id',
        'project_id',
        'payment_type',
        'amount',
        'status',
        'notes',
    ];

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}

