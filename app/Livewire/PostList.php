<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class PostList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search='';
    #[Url(as: 'category')]
     public string $selectedCategory = '';
    #[Url(as: 'tag')]
     public string $selectedTag = '';

   

    #[Layout('layouts.public')]
    #[Title('Blog')]
    public function render()
    {
        $posts = Post::with(['user','tags','categories'])
        ->where('status','published')
        ->when($this->search, function($q){
            $q->where('title','like','%'.$this->search.'%')
            ->OrWhere('excerpt','like','%'.$this->search.'%')
            ->OrWhere('content','like','%'.$this->search.'%');
        })
        ->when($this->selectedCategory, function($q){
            $q->whereHas('categories',function($query){
                $query->where('slug',$this->selectedCategory);
            });
        })
        ->when($this->selectedTag, function($q){
            $q->whereHas('tags',function($query){
                $query->where('slug',$this->selectedTag);
            });
        })
        ->latest('published_at')->paginate(9);

        return view('livewire.post-list',[
            'posts'=>$posts,
            'categories'=>Category::withCount('posts')->get(),
            'tags'=>Tag::withCount('posts')->get()
        ]);
    }
    public function updatingSearch() : void {
        $this->resetPage();
    }
    public function updatingSelectedCategory() : void {
        $this->resetPage();
    }
    public function updatingSelectedTag() : void {
        $this->resetPage();
    }
    public function clearFilters() : void {
        $this->search='';
        $this->selectedCategory='';
        $this->selectedTag='';

        $this->resetPage();
    }
}
