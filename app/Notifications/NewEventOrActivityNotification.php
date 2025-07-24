<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewEventOrActivityNotification extends Notification
{
    use Queueable;

    protected $type;
    protected $title;
    protected $description;
    protected $url;
    protected $date;

    /**
     * Create a new notification instance.
     */
    public function __construct($type, $title, $description, $url, $date)
    {
        $this->type = $type; // 'event' or 'activity'
        $this->title = $title;
        $this->description = $description;
        $this->url = $url;
        $this->date = $date;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'date' => $this->date,
        ];
    }
}
