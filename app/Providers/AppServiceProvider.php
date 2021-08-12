<?php

namespace App\Providers;

use App\Models\Settings;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        config([
            'global' => Settings::all()
                ->keyBy('key')
                ->transform(function ($setting) {
                    return $setting->value;
                })->toArray(),
        ]);

        config()->set('services.smscru.login', Settings::get('smscru_login') ?? env('SMSCRU_LOGIN'));
        config()->set('services.smscru.secret', Settings::getDecrypted('smscru_secret') ?? env('SMSCRU_SECRET'));
        config()->set('services.telegram-bot-api.token', Settings::getDecrypted('telegram_token') ?? env('TELEGRAM_BOT_TOKEN'));
    }
}
