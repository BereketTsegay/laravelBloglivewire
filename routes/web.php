<?php

use App\Livewire\PostList;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect('/blog');
})->name('home');

Route::livewire('dashboard', 'pages::dashborad')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/blog',PostList::class)->name('blog.index');
Route::livewire('/blog/{slug}','pages::posts.show')->name('blog.show');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware('auth')->group(function(){
    Route::livewire('/users','pages::users.index')->middleware('can:manage users')->name('users.index');
    Route::livewire('/users/{user}/edit','pages::users.edit')->middleware('can:manage users')->name('users.edit');
    Route::livewire('/users/create','pages::users.create')->middleware('can:manage users')->name('users.create');

    //categories routes
    Route::livewire('/categories','pages::categories.index')->middleware('can:manage roles')->name('categories.index');
    Route::livewire('/categories/{user}/edit','pages::categories.edit')->middleware('can:manage roles')->name('categories.edit');
    Route::livewire('/categories/create','pages::categories.create')->middleware('can:manage roles')->name('categories.create');

    //comments routes
    Route::livewire('/comments','pages::comments.index')->middleware('can:manage roles')->name('comments.index');
    //posts routes
    Route::livewire('/posts','pages::posts.index')->middleware('can:create posts')->name('posts.index');
    Route::livewire('/posts/{post}/edit','pages::posts.edit')->name('posts.edit');
    Route::livewire('/posts/create','pages::posts.create')->middleware('can:create posts')->name('posts.create');
});

require __DIR__.'/auth.php';
