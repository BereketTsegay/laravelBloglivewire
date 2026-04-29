<?php

namespace App\Livewire\Blog;

use App\Models\Comment;
use App\Models\Post;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Livewire\Component;

class Comments extends Component
{
    public Post $post;
    #[Validate('nullable|string|min:3|max:1000')]
    public string $newComment = '';

    public ?int $replayingTo = null;
    #[Validate('nullable|string|min:3|max:1000')]
    public string $replyContent = '';

    public function mount(Post $post)
    {
        $this->post = $post;
    }

    public function postComment()
    {
        if(!auth()->check()){
            Flux::toast('You must be logged in to comment.', 'error');
            return redirect()->route('login');
        }
        $this->validateOnly('newComment');

       $comment = $this->post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $this->newComment,
            'status' => 'approved',
        ]);

        $this->newComment = '';

        //notify post author
        if($comment->post->user_id !== auth()->id()){
            $comment->post->user->notify(new \App\Notifications\NewCommentNotification($comment));
        }



        $this->dispatch('comment-posted');
        Flux::toast('Your comment has been Approved and is now visible.');
    }

    public function startReplay($commentId){
        if(!auth()->check()) return redirect()->route('login');

        $this->replayingTo = $commentId;
        $this->replyContent = '';
    }
    public function cancelReply() : void {
        $this->replayingTo = null;
        $this->replyContent = '';
    }
    public function postReply(){
        if(!auth()->check()){
            Flux::toast('You must be logged in to comment.', 'error');
            return redirect()->route('login');
        }
        $this->validateOnly('replyContent', [
            'replyContent' => 'required|string|min:3|max:1000',
        ]);

        $comment = $this->post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $this->replyContent,
            'status' => 'approved',
            'parent_id' => $this->replayingTo,
        ]);

        //notify post author
        if($comment->post->user_id !== auth()->id()){
            $comment->post->user->notify(new \App\Notifications\NewCommentNotification($comment));
        }


        $this->cancelReply();

        $this->dispatch('comment-posted');
        Flux::toast('Your reply has beenposted.');
    }
    #[On('comment-posted')]
    public function render()
    {
        $comments = Comment::where('post_id', $this->post->id)->approved()->topLevel()->with(['user', 'replies.user'])->latest()->get();
        return view('livewire.blog.comments',[
            'comments' => $comments,
            ]);
    }
}
