<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use App\Models\Post;
use Flux\Flux;

new class extends Component
{
    use WithFileUploads;

    #[Validate('required|string|min:3|max:255')]
    public string $title = '';
    #[Validate('nullable|string|max:500')]
    public string $excerpt = '';
    #[Validate('required|string|min:10')]
    public string $content = '';
    #[Validate('required|in:draft,published')]
    public string $status = 'draft';
    #[Validate('required|image|max:2048')]
    public $featured_image = '';

    public function save(){
        $this->validate();

        $post = new Post();

        $post->user_id= auth()->id();
        $post->title = $this->title;
        $post->slug = Str::slug($this->title);
        $post->excerpt = $this->excerpt;
        $post->content = $this->content;
        $post->status = $this->status;

        if($this->featured_image){
            $path = $this->featured_image->store('posts','public');
            $post->featured_image = $path;
        }
        

        if($this->status === 'published'){
            $post->published_at = now();
        }


        $post->save();

         Flux::toast('Your changes have been saved.');

         $this->redirect('/posts', navigate:true);


    }
};
?>

<div>
     <!-- header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Create New Post</h1>
        <p class="mt-1 text-sm text-grey-600">Write and publish your blog post</p>
    </div>

    {{-- form --}}
    <form wire:submit="save" class="space-y-6">
        <flux:field>
            <flux:input placeholder="Enter your post title" type="text" label="title" wire:model.live.debounce="title"/>
            <flux:error name="title" />
        </flux:field>
        <flux:field>
            <flux:textarea placeholder="short summary of your post" rows="2" type="text" label="excerpt" wire:model.live.debounce="excerpt"/>
            <flux:error name="excerpt" />
            <flux:description>This will appear in post previews and search results</flux:description>
        </flux:field>
        <flux:field>
            
            <flux:textarea rows="12" placeholder="Write you post content here . . . ." type="text" label="content" wire:model.live.debounce="content"/>
            <flux:error name="content" />
        </flux:field>
        <flux:field>
          
            <flux:input type="file" wire:model="featured_image" accept="image/*" placeholder="Select image" label=""/>
            @if ($featured_image)
                <div class="mt-4 flex flex-col gap-2">
                    <img src="{{ $featured_image->temporaryUrl()}}"
                        class="h-32 w-auto rounded border"                    
                     alt="" srcset="">
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
        <flux:button type="submit" variant="primary">Create Post</flux:button>
        <flux:button 
         href="{{ route('posts.index') }}"
        variant="danger">Cancel</flux:button>
    </div>
    </form>
    {{-- end of form --}}
</div>