<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostImages extends Model
{
     use HasFactory;
    protected $table = 'post_images';
    protected $fillable = ['post_id','image_path','alt_text',];

    // Belongs to post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
