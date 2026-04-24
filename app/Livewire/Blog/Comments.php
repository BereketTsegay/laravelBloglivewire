<?php

namespace App\Livewire\Blog;

use App\Models\Post;
use Flux\Flux;
use Livewire\Attributes\Validate;
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

    public function addComment()
    {
        if(!auth()->check()){
            Flux::toast('You must be logged in to comment.', 'error');
            return redirect()->route('login');
        }
        $this->validateOnly('newComment');

        $this->post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $this->newComment,
            'status' => 'approved',
        ]);

        $this->newComment = '';

        Flux::toast('Your comment has been Approved and is now visible.');
    }

    public function render()
    {
        return view('livewire.blog.comments');
    }
}
