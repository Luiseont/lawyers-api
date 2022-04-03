<?php

namespace App\Listeners;

use App\Events\PaymentSuccessEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\SuccessMail;
use App\Http\Models\Suscription;
use Illuminate\Support\Facades\Mail;

class SendMailSuccess
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
     * @param  \App\Events\PaymentSuccessEvent  $event
     * @return void
     */
    public function handle(PaymentSuccessEvent $event)
    {
        Mail::to($event->suscription->client->email)->send(new SuccessMail());
    }
}
