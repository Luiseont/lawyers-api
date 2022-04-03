<?php

namespace App\Listeners;

use App\Events\PaymentFailEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\FailedMail;
use Illuminate\Support\Facades\Mail;

class SendFailSuccess
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
     * @param  \App\Events\PaymentFailEvent  $event
     * @return void
     */
    public function handle(PaymentFailEvent $event)
    {
        Mail::to($event->suscription->client->email)->send(new FailedMail());
    }
}
