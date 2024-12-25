<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use App\Events\CriminalRecordEvent;

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

        $users = User::get();

        foreach ($users as $user) {

            if ($user->registrationDocument) {

                if (Carbon::parse($user->created_at)->addMonths(3)->isPast()) {
                    if (empty($user->registrationDocument->criminal_record)&&
                    in_array($user->getRoleNames()[0], ['freeDriver', 'employeeDriver'])) {
                        $user->full_registered=false;
                        $user->save();
                        
                        event(new CriminalRecordEvent("! عذرا تم ايقاف نشاط حسابك بسبب مضي ثلاث اشهر من تسجيلك في التطبيق و عدم ادخالك للفيش الجنائي",$user));
                    }
                }
            }
        }
    }
}
