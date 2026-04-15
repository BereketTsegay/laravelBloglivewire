<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use App\Models\Post;
use Flux\Flux;

new class extends Component
{
   use WithFileUploads;

    public Post $post;

    #[Validate('required|string|min:3|max:255')]
    public string $title = '';
    #[Validate('nullable|string|max:500')]
    public string $excerpt = '';
    #[Validate('required|string|min:10')]
    public string $content = '';
    #[Validate('required|in:draft,published')]
    public string $status = 'draft';
    #[Validate('nullable|image|max:2048')]
    public $featured_image = '';

    public $existing_image = '';


    public function mount(Post $post){

        //authorization
        if(!auth()->user()->can('edit all posts') && !(auth()->user()->can('edit own posts') && $post->user_id===auth()->id()))
        abort(403);

        $this->post = $post;
        $this->title = $post->title;
        $this->excerpt = $post->excerpt ?? '';
        $this->content = $post->content;
        $this->status = $post->status;
        $this->existing_image = $post->featured_image ?? '';
    }

    public function update(){
        $this->validate();

        $this->post->user_id= auth()->id();
        $this->post->title = $this->title;
        $this->post->slug = Str::slug($this->title);
        $this->post->excerpt = $this->excerpt;
        $this->post->content = $this->content;
        $this->post->status = $this->status;

        if($this->featured_image){
            //delete exixting image
            if($this->existing_image){
                \Storage::disk('public')->delete($this->existing_image);
            }
            $path = $this->featured_image->store('posts','public');
            $this->post->featured_image = $path;
            $this->existing_image = $path;
        }
        

        if($this->status === 'published' && !$this->post->published_at){
            $this->post->published_at = now();
        }


        $this->post->save();

         Flux::toast('Your changes have been saved.');

         $this->redirect('/posts', navigate:true);


    }
};
?>

<div>
     <!-- header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit post Post</h1>
        <p class="mt-1 text-sm text-grey-600">update your blog post</p>
    </div>

    {{-- form --}}
    <form wire:submit="update" class="space-y-6">
        <flux:field>
            <flux:input placeholder="Enter your post title" type="text" lable="title" wire:model.live.debounce="title"/>
            <flux:error name="title" />
        </flux:field>
        <flux:field>
            <flux:textarea placeholder="short summary of your post" rows="2" type="text" lable="excerpt" wire:model.live.debounce="excerpt"/>
            <flux:error name="excerpt" />
            <flux:description>This will appear in post previews and search results</flux:description>
        </flux:field>
        <flux:field>
            
            <flux:textarea rows="12" placeholder="Write you post content here . . . ." type="text" lable="content" wire:model.live.debounce="content"/>
            <flux:error name="content" />
        </flux:field>
        <flux:field>
            <flux:input type="file" wire:model="featured_image" accept="image/*" placeholder="Select image" label=""/>
            @if ($existing_image && !$featured_image)
                <div class="mt-4 flex flex-col gap-2">
                    <img src="{{ Storage::Url($existing_image)}}"
                        class="h-12 w-12 rounded border"                    
                    >
                </div>
            @endif
            <flux:error name="featured_image" />
        </flux:field>
        <flux:fieldset>
    <flux:legend>Status</flux:legend>
    <flux:radio.group wire:model.live="status">
            <flux:radio
                value="draft"
                label="Draft"
                description="Save as draft,not vissible to readers"
                checked
            />
            @can('publish posts')
               <flux:radio
                value="published"
                label="Publish"
                description="Publish imedeatly, vissible to all readers."
            /> 
            @endcan
        </flux:radio.group>
        <flux:error name="status" />
    </flux:fieldset>

    <div class="flex gap-3">
        <flux:button type="submit" variant="primary">Update Post</flux:button>
        <flux:button 
         href="{{ route('posts.index') }}"
        variant="danger">Cancel</flux:button>
    </div>
    </form>
    {{-- end of form --}}
</div>