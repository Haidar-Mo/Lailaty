<?php

namespace App\Listeners;

use App\Events\CriminalRecordEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\CriminalRecordNotification;
use Illuminate\Support\Facades\Notification;
class CriminalRecordListner
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(CriminalRecordEvent $event): void
    {
        $message = $event->message;
        $user = $event->user; 

        Notification::send($user, new CriminalRecordNotification($message));
    }
}
