<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Post extends Model
{
 protected $fillable = ['user_id','category_id','sub_category_id','title','slug','content','featured_image','status','published_at'];

  protected static function booted()
    {
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }
     protected $casts = [
        'published_at' => 'datetime',
    ];
    // Belongs to author
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Belongs to subcategory
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class,'sub_category_id');
    }

    // One post can have many images
    public function images()
    {
        return $this->hasMany(PostImages::class);
    }

    // Featured image
    public function featuredImage()
    {
        return $this->hasOne(PostImages::class)->where('is_featured', true);
    }

    // Many-to-many with tags
    public function tags()
    {
        return $this->belongsToMany(Tag::class,'post_tags', 'post_id', 'tag_id');
    }
    // public function tags(){
    //     return $this->belongsToMany(PostTag::class, 'post_tag', 'post_id', 'tag_id'     );
    // }

    // One post has many comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
