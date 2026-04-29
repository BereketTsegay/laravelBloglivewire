<?php

namespace App\Livewire\Blog;

use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Subcribe extends Component
{
    #[Validate('email|required|unique:subscribers,email')]
    public string $email = '';

    public function subscribe() : void {
        $this->validate();

        $subscriber = \App\Models\Subscriber::create([
            'email'=>$this->email,
            'verified_at'=>now(),
            'is_verified'=>true
        ]);

        $subscriber->save();

        $this->email = '';

        // \Flux\Flux::toast('You have successfully subscribed to our blog updates. Please check your email to confirm your subscription.');
        Flux::toast('You have successfully subscribed to our blog updates. thanks!!', 'success');
    }
    public function render()
    {
        return view('livewire.blog.subcribe');
    }
}
