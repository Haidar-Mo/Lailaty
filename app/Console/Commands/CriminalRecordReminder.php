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
        $users = User::get();

        foreach ($users as $user) {
            if ($user->registrationDocument) {
                if (empty($user->registrationDocument->criminal_record) &&
                    in_array($user->getRoleNames()[0], ['freeDriver', 'employeeDriver'])) {
                    $Date = Carbon::parse($user->registrationDocument->created_at)->addMonths(3)->format('Y-m-d');
                    event(new CriminalRecordEvent("يرجى ادخال الفيش الجنائي قبل تاريخ $Date",$user));
                }
            }
        }
    }

}
