<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Suscription;
use App\Jobs\ProcessPayments;
use App\Http\Requests\API\SuscriptionRequest;

class AdminController extends Controller
{
    /**
     * Busca las suscripciones basado en un criterio.
     * Si el criterio no es correcto, retorna error.
     */

    public function getSuscriptions(String $flag)
    {   
       $criteria = ['active' => 1, 'inactive' => 0];
       if(!array_key_exists($flag, $criteria))
       {
            return response()->Json(['status' => 'ko', 'message' => 'Criteria error'], 400);
       }

       $suscriptions = Suscription::where('active', $criteria[$flag])->get();
       return ($suscriptions)? response()->Json(['status' => 'ok', 'suscriptions' => $suscriptions->toArray()], 200):response()->Json(['status' => 'ok', 'suscriptions' => []], 200);
    }

    /**
     *  Retorna la informacion de una suscripcion.
     */

    public function getSuscription(int $id)
    {

        //busco la suscripcion
        $suscription = Suscription::where([
            'id' => $id 
        ])->first();

        if($suscription)
        {
            return response()->Json(['status' => 'ok', 'suscription' => $suscription], 200);
        }else{
            return response()->Json(['status' => 'ko', 'message' => 'Suscription not exist.'], 400);
        }

    }
    /**
     *  Marca una suscripcion como inactiva.
     */

    public function cancelSuscription(SuscriptionRequest $request)
    {
        $suscription = Suscription::where([
            'id' => $request->input('suscription')
        ])->first();

        if($suscription)
        {
            $suscription->active = 0;
            $suscription->save();

            return response()->Json(['status' => 'ok', 'message' => "suscription unsubscribe: ".$suscription->id], 200);
        }else{
            return response()->Json(['status' => 'ko', 'message' => 'Suscription not exist.'], 400);
        }

    }

    /**
    *  Recuperacion manual de suscripciones. 
    *  Solo se ejecuta si la suscripcion no fue eliminada y esta como inactiva (fallo en proceso de pago.)
    */
    public function retryPaymentManual(SuscriptionRequest $request)
    {
        $suscription = Suscription::where([
            'id' => $request->input('suscription'),
            'active' => 0
        ])->first();

        if($suscription)
        {
            //reintenta el pago en 5 segundos en el proceso normal. 
            //si falla, no se reintenta.

            ProcessPayments::dispatch($suscription->id, true)->onQueue('process')->delay(now()->addSeconds(5));
            return response()->Json(['status' => 'ok', 'message' => "Payment process in Queue"], 200);
        }else{
            return response()->Json(['status' => 'ko', 'message' => 'Suscription not exist or is active'], 400);
        }

    }
    
}
