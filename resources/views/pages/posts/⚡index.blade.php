<?php

use Livewire\Component;
use App\Models\Post;
use Livewire\WithPagination;
use Flux\Flux;

new class extends Component
{
    use WithPagination;

    //local variables

    public string $search = '';
    public string $status = 'all';
    public string $sortBy = '';
    public string $sortDirection = 'asc';


    //returns posts that are filtered

    public function with() : array {
        $query = Post::with('user')->latest();

        //filter the search query
        if($this->search){
            $query->where('title','like','%'.$this->search.'%')->orWhere('content','like','%'.$this->search.'%');
        }

        //filter the statues query
        
         if($this->status && $this->status!=='all'){
            $query->where('status','like','%'.$this->status.'%');
        }

        //authorization : authors can see only their own posts
        if(auth()->user()->hasRole('author')){
            $query->where('user_id',auth()->user()->id);
        }
        return [
            'posts' => $query->paginate(10),
        ];
    }

    public function updatingSearch() {
        $this->resetPage();
    }
    public function updatingStatus() {
        $this->resetPage();
    }

    public function deletePost(Post $post){
        //authorize
        if(auth()->user()->can('delete all posts') || (auth()->user()->can('delete own posts') && auth()->id === $post->user_id)){
            $post->delete();

            //session()->flash('sucess','Post has been deleted successfully');
            Flux::toast('Your changes have been saved.');
        }
    }
    public function sort($column) {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }
};
?>

<div>
    <!-- header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Posts</h1>
        <p class="mt-1 text-sm text-grey-600">Manage your blog</p>
    </div>
    <!-- end of header -->

    {{-- filters --}}
    <div class="mb-6 rounded-lg   p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <flux:input kbd="⌘K" 
                icon="magnifying-glass" 
                placeholder="Search..." 
                wire:model.live.debounce.300ms="search"/>
            </div>
            <div class="sm:w-48">
                <flux:select wire:model.live="status" placeholder="Choose industry...">
                    <flux:select.option value="all">All posts</flux:select.option>
                    <flux:select.option value="draft">Draft</flux:select.option>
                    <flux:select.option value="published">Published</flux:select.option>
                    <flux:select.option value="archived">Archived</flux:select.option>
                </flux:select>
            </div>
            @can('create posts')
                <div>
                    <flux:button
                        href="{{ route('posts.create') }}"
                        icon="plus"
                    >
                        Create Post
                    </flux:button>
                </div>        
            @endcan
        </div>
    </div>
    

    

    {{-- @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p4" wire:transition>
            <p class="text-sm"></p>
        </div>
        
    @endif --}}

    <div class="rounded-lg   overflow-hidden">
        <div class="overflow-x-auto">
            <flux:table :paginate="$posts">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'title'" :direction="$sortDirection" wire:click="sort('title')">Title</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'author'" :direction="$sortDirection" wire:click="sort('author')">Author</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">Status</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Created</flux:table.column>
                    <flux:table.column >Actions</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ( $posts as $post )
                        <flux:table.row :key="$post->id" wire:transition >
                            <flux:table.cell class="px-6 py-4 whitespace-nowrap">
                               <div class="text-sm font-medium text-gray-900"> {{ $post->title }}</div>
                               <div class="text-sm text-gray-500"> {{ Str::limit($post->excerpt,50) }}</div>
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">
                               <div class="text-sm text-gray-900">{{ $post->user->name }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                  {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : '' }}
                                  {{ $post->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                  {{ $post->status === 'archived' ? 'bg-gray-100 text-gray-800' : '' }}
                                ">
                                  {{ ucfirst($post->status) }}
                                </span>
                            </flux:table.cell>
                            <flux:table.cell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $post->created_at->format('M d,Y') }}
                            </flux:table.cell>
                            <flux:table.cell class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-start gap-2">
                                    @if (auth()->user()->can('edit all posts') || (auth()->user()->can('edit own posts') && auth()->id() === $post->user_id))
                                        <flux:button
                                            href="{{ route('posts.edit',$post) }}"
                                            icon="pencil-square"
                                            variant="primary"
                                            size="sm"
                                            
                                        >
                                            Edit
                                        </flux:button>
                                    @endif
                                    @if (auth()->user()->can('delete all posts') || (auth()->user()->can('delete own posts') && auth()->id() === $post->user_id))
                                        <flux:button
                                            wire:click="deletePost({{ $post->id }})"
                                            icon="trash"
                                            wire:confirm="Are you sure you want to delete this?"
                                            variant="danger"
                                            size="sm"
                                        >
                                            Delete
                                        </flux:button>
                                    @endif
                                </div>
                            </flux:table.cell>

                        </flux:table.row>
                      @empty
                        <flux:table.row>
                            <flux:table.cell class="px-6 py-12 text-center text-gray-400" colspan="6">
                                    No Post found.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

        </div>
    </div>
</div>