<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','slug','description'];

    // One category has many subcategories
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    // One category has many tags
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }
      public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
