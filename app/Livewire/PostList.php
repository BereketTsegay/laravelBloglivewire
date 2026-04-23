<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class PostList extends Component
{
    use WithPagination;

    public string $search = '';

    #[Layout('layouts.public')]
    #[Title('Blog')]
    public function render()
    {
        $posts = Post::with('user')
        ->where('status','published')
        ->when($this->search, function($q){
            $q->where('title','like','%'.$this->search.'%')
            ->OrWhere('excerpt','like','%'.$this->search.'%');
        })->latest('published_at')->paginate(9);

        return view('livewire.post-list',[
            'posts'=>$posts
        ]);
    }
    public function updatingSearch() : void {
        $this->resetPage();
    }
}
