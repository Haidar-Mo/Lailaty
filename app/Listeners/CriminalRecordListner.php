<?php

namespace App\Listeners;

use App\Events\CriminalRecordEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\CriminalRecordNotification;
use Illuminate\Support\Facades\Notification;
class CriminalRecordListner
{

    public function handle(CriminalRecordEvent $event): void
    {

        $event->user->notify(new CriminalRecordNotification($event->message));
    }
}
