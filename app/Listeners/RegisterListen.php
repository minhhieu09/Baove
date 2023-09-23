<?php

namespace App\Listeners;

use App\Events\RegisterEvent;
use App\Mail\VerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class RegisterListen
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(RegisterEvent $event)
    {
        return Mail::to($event->email)->send(new VerifyEmail($event->email, $event->token));
    }
}
