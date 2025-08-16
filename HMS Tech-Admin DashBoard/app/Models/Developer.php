<?php

namespace App\Models;

use App\Models\AddUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Developer extends Model
{
    use HasFactory;

 protected $fillable = [
    'add_user_id',
    'skill',
    'experience',
    'part_time',
    'full_time',
    'internship',
    'job',
    'salary',
    'profile_image',
    'cnic_front',
    'cnic_back',
    'contract_file',
];

     protected $table = 'developers';

     protected $guarded = [];

  public function user()
{
    return $this->belongsTo(AddUser::class, 'add_user_id');
}

public function teams() {
    return $this->belongsToMany(Team::class, 'team_user', 'user_id', 'team_id');
}


    public function developer()
{
    return $this->belongsTo(AddUser::class, 'developer_id');
}
}