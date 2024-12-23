<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CriminalRecordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $message)
    {

    }

    public function databaseType(object $notifiable): string
    {
        return 'criminal-record';
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */


    public function toArray($notifiable)
    {
        return [
            'message'=>$this->message
        ];
    }
}
