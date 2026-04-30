<?php

use Livewire\Component;

new class extends Component
{
    //
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
    <div class="mb-6 rounded-lg  bg-zinc-50 border-zinc-200 border-1  p-8">
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
</div>