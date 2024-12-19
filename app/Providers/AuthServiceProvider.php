<?php

namespace App\Providers;

use App\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
        BaseResetPassword::createUrlUsing(function ($notifiable, $token) {
            return (new ResetPassword($token))->resetUrl($notifiable);
        });
    }
}
