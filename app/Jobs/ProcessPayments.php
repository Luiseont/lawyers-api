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
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $susId)
    {
        $this->suscription = $susId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $suscription = Suscription::with('client')->where('id', $this->suscription)->first();

        if(random_int(0, 100) > 100)
        {
            //registro el pago.
            $payment = SuscriptionPayment::create([
                'suscription_id' => $suscription->id,
                'amount' => $suscription->amount,
                'response' =>1
            ]);

            $suscription->last_payment_id = $payment->id;
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
            $suscription->save();

            //ejecuta el evento de fallo.
            PaymentFailEvent::dispatch($suscription);
            ProcessDayliPayments::dispatch($suscription->id)->onQueue('daily')->delay(now()->addMinutes(1));
        }

    }
}
