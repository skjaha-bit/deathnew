<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The admin panel is built on Bootstrap 5, not Tailwind.
        Paginator::useBootstrapFive();

        // The app has no customer-facing web password reset flow (mobile
        // users authenticate via OTP), so the single reset-link destination
        // is the admin panel's own reset-password page.
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            return route('admin.password.reset', ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()]);
        });
    }
}
