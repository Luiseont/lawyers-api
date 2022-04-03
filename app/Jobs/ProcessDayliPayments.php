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

/**
 * Proceso de pago diario.
 * aca se envian a cola las suscripciones con pago fallido para reintentarlo.
 * tiene 2 intentos para un total de 3
 */

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
                $suscription->active = 1;
                $suscription->payment_attemps = 0;
                $suscription->save();

                //ejecuta el evento de envio de email.
                PaymentSuccessEvent::dispatch($suscription);

            }else{

                $payment = SuscriptionPayment::create([
                    'suscription_id' => $suscription->id,
                    'amount' => $suscription->amount,
                    'response' => 0
                ]);

                /**
                 *  se aumenta los intemtos de cobro 
                 *  y agrega la fecha del nuevo intento.
                 */
                $suscription->payment_attemps = $suscription->payment_attemps + 1;
                $suscription->attemp_date = Carbon::now();

                //ejecuta el evento de fallo.
                /**
                 * verifiamos que los intentos no excedan la cantidad definida.
                 */
                if($this->attempts() == 2){

                    /**
                     * Si es igual a la cantidad maxima
                     * Se marca como inactiva la suscripcion y se envia el email correspondiente.
                     */
                    $suscription->active = 0;
                    $suscription->save();
                    UnsubscribeEvent::dispatch($suscription);

                    /**
                     * Para evitar que se ejecute nuevamente el job y aumente los attempts se envia como fallido a la tabla failed_jobs.
                     */
                    $this->fail();
                }else{
                    /**
                     * En caso de que el numero de intentos no haya alcanzado el maximo
                     * se guarda la informacion
                     * se dispara el evento de mail para el apgo fallido.
                     */
                    $suscription->save();
                    PaymentFailEvent::dispatch($suscription);
                    
                    //envia el job de nuevo a cola por 24h
                    $this->release(60*60*24);
                }
                
            }

    }

}
