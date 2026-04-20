<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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

    Route::livewire('/posts','pages::posts.index')->middleware('can:create posts')->name('posts.index');
    Route::livewire('/posts/{post}/edit','pages::posts.edit')->name('posts.edit');
    Route::livewire('/posts/create','pages::posts.create')->middleware('can:create posts')->name('posts.create');
});

require __DIR__.'/auth.php';
