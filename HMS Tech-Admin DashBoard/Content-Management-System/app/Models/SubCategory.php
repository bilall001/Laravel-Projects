<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = 'sub_categories';
    protected $fillable = ['category_id', 'name', 'slug', 'description'];

    // Belongs to category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // One subcategory has many posts
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
