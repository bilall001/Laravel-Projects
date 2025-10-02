<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostTag extends Model
{
     protected $fillable = ['post_id','user_id','comment','status'];

    // Belongs to post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Belongs to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
