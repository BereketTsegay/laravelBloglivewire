<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    //

    protected $fillable = ['name', 'slug'];
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    protected static function boot() : void
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = \Str::slug($tag->name);
            }
        });
    }
}
