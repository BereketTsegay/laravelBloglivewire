<?php

namespace App\Observers;

use App\Models\Post;
use Flux\Flux;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        //send if the post is published and the published_at is set was not beforee than now
        // if ($post->status === 'published' && $post->published_at <= now()) {
        //     //send notification to all subscribers
        //     $users = \App\Models\Subscriber::where('is_verified', true)->get();
        //     foreach ($users as $user) {
        //         $user->notify(new \App\Notifications\NewPostPublished($post));
        //     }
        // }

        // Flux::toast('Email sent to subscribers', 'success');

    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        //check if the post is published and the published_at is set was not beforee than now
        if ($post->isDirty('status')
            && $post->status === 'published'
            && $post->getOriginal('status') != 'published'
            && $post->published_at <= now()) {
            //send notification to all subscribers
            $users = \App\Models\Subscriber::where('is_verified', true)->get();
            foreach ($users as $user) {
            $user->notify(new \App\Notifications\NewPostPublished($post));
            }
        }
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        //
    }
}
