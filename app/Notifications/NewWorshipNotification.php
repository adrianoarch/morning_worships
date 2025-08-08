<?php

namespace App\Notifications;

use App\Models\MorningWorship;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewWorshipNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public MorningWorship $worship)
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
        return (new MailMessage)
                    ->subject('Nova Adoração Matinal Disponível')
                    ->greeting('Olá!')
                    ->line('Uma nova Adoração Matinal foi publicada em nosso site.')
                    ->line('Título: ' . $this->worship->title)
                    ->action('Assista Agora', route('worship.show', $this->worship))
                    ->line('Esperamos que goste!');
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
