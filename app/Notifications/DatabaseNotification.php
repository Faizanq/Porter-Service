<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DatabaseNotification extends Notification
{
    use Queueable;
    
    private $subscription;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($letter)
    {
        $this->subscription = $letter;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->greeting('Hello,')
                    ->from('noreply.atithiinfosoft@gmail.com','Portal')
                    ->subject('New Job')
                    ->line('New job has been added in your area, please check your app')
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'letter'=>$this->subscription
        ];
    }
}
