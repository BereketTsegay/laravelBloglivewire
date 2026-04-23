<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //

    protected $fillable = ['name', 'slug', 'description', 'color'];

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    protected static function boot() : void
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = \Str::slug($category->name);
            }
        });
    }
}
