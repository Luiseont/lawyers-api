<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Suscription;
use App\Models\SuscriptionPayment;

use App\Events\PaymentSuccessEvent;
use App\Events\PaymentFailEvent;
use App\Events\UnsubscribeEvent;

use Carbon\Carbon;

class ProcessDayliPayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    private $suscription;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $sus)
    {
        $this->suscription = $sus;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $suscription = Suscription::with('client')->where('id', $this->suscription)->first();

            if(random_int(0, 100) > 50)
            {
                //registro el pago.
                $payment = SuscriptionPayment::create([
                    'suscription_id' => $suscription->id,
                    'amount' => $suscription->amount,
                    'response' =>1
                ]);

                $suscription->last_payment_id = $payment->id;
                $suscription->payment_attemps = 0;
                $suscription->attemp_date = NULL;
                $suscription->save();

                //ejecuta el evento de envio de email.
                PaymentSuccessEvent::dispatch($suscription);

            }else{

                $payment = SuscriptionPayment::create([
                    'suscription_id' => $suscription->id,
                    'amount' => $suscription->amount,
                    'response' => 0
                ]);

                $suscription->payment_attemps = $suscription->payment_attemps + 1;
                $suscription->attemp_date = Carbon::now();

                //ejecuta el evento de fallo.
                if($this->attempts() == 2){
                    $suscription->active = 0;
                    $suscription->save();
                    UnsubscribeEvent::dispatch($suscription);
                    //excepcion 
                    $this->fail();
                }else{
                    $suscription->save();
                    PaymentFailEvent::dispatch($suscription);
                    //envia el job de nuevo a cola por 24h
                    $this->release(60*60*24);
                }
                
            }

    }

}
