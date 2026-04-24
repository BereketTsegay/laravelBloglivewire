<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use League\Uri\Builder;

class Comment extends Model
{
    protected $fillable = ['post_id', 'user_id', 'parent_id', 'content', 'status'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user', 'replies');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function topLevel($query)
    {
       $query->whereNull('parent_id');
    }


}
