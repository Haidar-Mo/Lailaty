<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use App\Events\CriminalRecordEvent;
use App\Notifications\CriminalRecordNotification;
class CriminalRecord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Criminal-Record';

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

                if (Carbon::parse($user->captain_registration_time)->addMonths(3)->isPast()) {
                    if (trim($user->registrationDocument->criminal_record) === '') {
                        $user->full_registered=false;
                        $user->save();
                        event(new CriminalRecordEvent("! عذرا تم ايقاف نشاط حسابك بسبب مضي ثلاث اشهر من تسجيلك في التطبيق و عدم ادخالك للفيش الجنائي",$user));
                      // $user->notify(new CriminalRecordNotification("! عذرا تم ايقاف نشاط حسابك بسبب مضي ثلاث اشهر من تسجيلك في التطبيق و عدم ادخالك للفيش الجنائي"));
                    }
                }
            }
        }
    }
}
