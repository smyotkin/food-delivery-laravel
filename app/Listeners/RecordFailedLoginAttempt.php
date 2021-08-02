<?php

namespace App\Listeners;

use App\Services\SystemService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Failed;

class RecordFailedLoginAttempt
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Failed  $event
     * @return void
     */
    public function handle(Failed $event)
    {
        SystemService::createEvent('auth_failed', [
            'phone' => $event->user->phone,
            'ip' => request()->ip(),
        ]);
    }
}
