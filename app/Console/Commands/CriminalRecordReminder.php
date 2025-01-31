<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use App\Events\CriminalRecordEvent;
class CriminalRecordReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Criminal-Record-Reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description ;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::role(['freeDriver', 'employeeDriver'])->get();

        foreach ($users as $user) {
            if ($user->registrationDocument) {
                if (trim($user->registrationDocument->criminal_record) === '') {
                    $Date = Carbon::parse($user->captain_registration_time)->addMonths(3)->format('Y-m-d');
                    event(new CriminalRecordEvent("يرجى ادخال الفيش الجنائي قبل تاريخ $Date",$user));
                }
            }
        }
    }

}
