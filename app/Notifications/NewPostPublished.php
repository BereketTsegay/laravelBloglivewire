<?php

namespace App\Notifications;

use App\Models\Post;
use Flux\Flux;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPostPublished extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct( public Post $post)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Flux::toast('Email sent to subscribers', 'success');
        return (new MailMessage)
            ->subject('New Post Published: ' . $this->post->title)
            ->greeting('Hello!'. $notifiable->name . '!')
            ->line('A new post has been published on our blog. ')
            ->line('**' . $this->post->title . '**')
            ->line($this->post->excerpt ?? 'Click the button below to read the full post.')
            ->action('Read Post', route('blog.show', $this->post->slug))
            ->line('Thank you for being a valued reader!')
            ->line('[Unsubscribe]( ' . route('unsubscribe', $notifiable->token) . ' )');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
