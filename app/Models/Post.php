<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Post extends Model
{
    //

    protected $guarded =[];

    protected $casts =[
        'published_at' => 'datetime',
    ];

    /**
     * Get the user that owns the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot(){

        parent::boot();

        static::creating(function ($post) {
            if(empty($post->slug)){
                $post->slug = Str::slug($post->title);
            }
        });
    } 
}
