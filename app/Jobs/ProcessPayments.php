<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\PaymentSuccessEvent;
use App\Events\PaymentFailEvent;
use App\Models\Suscription;
use App\Models\SuscriptionPayment;

use App\Jobs\ProcessDayliPayments;

use Carbon\Carbon;

class ProcessPayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 1;

    private $suscription;
    private $now;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $susId, bool $manual = false)
    {
        $this->suscription = $susId;
        //esta variable determina si el job, encaso de fallo, debe o no enviarse a la cola diaria.
        $this->now = $manual;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $suscription = Suscription::with('client')->where('id', $this->suscription)->first();

        /**
         * Forma de randomizar los resultados. 
         * numero aleatorio entre 0 y 100
         * Si es menor que 50. el pago es exitoso, sino. es fallido.
         */
        if(random_int(0, 100) > 50)
        {
            //registro el pago.
            $payment = SuscriptionPayment::create([
                'suscription_id' => $suscription->id,
                'amount' => $suscription->amount,
                'response' =>1
            ]);

            /**
             * Actualiza los detalles de la suscripcion.
             * y el id del ultimo pago valido.
             * a partir dl created_at de ese pago, se puede determinar el siguiente sobro.
             */
            $suscription->last_payment_id = $payment->id;
            $suscription->active = 1;
            $suscription->payment_attemps = 0;
            $suscription->save();

            //ejecuta el evento de envio de email.
            // Utilizando un event-listener que envia el correo con la informacion de la suscripcion.
            PaymentSuccessEvent::dispatch($suscription);

        }else{

            /**
             * Si el pago es fallido, se crea una entrada para tener registro de los intentos de cobro.
             */
            $payment = SuscriptionPayment::create([
                'suscription_id' => $suscription->id,
                'amount' => $suscription->amount,
                'response' => 0
            ]);

            /**
             * Aumenta los intentos en 1
             * Guarda la fecha del ultimo intento. para futuras implementaciones.
             */
            $suscription->payment_attemps = $suscription->payment_attemps + 1;
            $suscription->attemp_date = Carbon::now();
            $suscription->save();

            //ejecuta el evento de fallo.
            //si es falso, la suscripcion se enviara a la cola dialy para procesarlo en 24h/
            // ademas de enviar el correo correspondiente de fallo.
            if(!$this->now)
            {            
                PaymentFailEvent::dispatch($suscription);
                //manda a la cola daily para que se procese de nuevo en 24h
                ProcessDayliPayments::dispatch($suscription->id)->onQueue('daily')->delay(now()->addHours(24));

            }

        }

    }
}
