<?php

namespace App\Listeners;

use App\Events\UnsubscribeEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\UnsubscribeMail;
use Illuminate\Support\Facades\Mail;

class SendUnsubscribeMail
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
     * @param  \App\Events\UnsubscribeEvent  $event
     * @return void
     */
    public function handle(UnsubscribeEvent $event)
    {
        Mail::to($event->suscription->client->email)->send(new UnsubscribeMail());
    }
}
