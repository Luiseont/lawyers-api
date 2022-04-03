<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests\API\StoreSuscriptionRequest;
use App\Http\Requests\API\UpdateSuscriptionRequest;

use App\Models\Suscription;
use App\Jobs\ProcessPayments;

class LawyersController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSuscriptionRequest $request)
    {
        //capturo el id del usuario de la sesion
        $user = Auth()->user();

        //verificamos que no exista una suscripcion activa del usuario en cuestion.
        $activeSus = Suscription::where('user_id', $user->id)->where('active', 1)->first();

        if(!$activeSus)
        {
            //creo la nueva suscripcion
            $suscription = Suscription::create([
                'user_id' => $user->id,
                'type_id' => $request->input('type'),
                'amount' => $request->input('amount')
            ]);

            if($suscription)
            {

                //agregamos esta suscripcion a la cola para intentar el pago en 30minutos

                ProcessPayments::dispatch($suscription->id)->onQueue('process')->delay(now()->addMinutes(1));

                return response()->Json(['status' => 'ok', 'message' => 'Suscription created successfuly'], 200);
            }else{
                return response()->Json(['status' => 'ko', 'message' => 'Error creating a suscription'], 400);
            }

        }else{
            return response()->Json(['status' => 'ko', 'message' => 'Cliente have a active suscription'], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    
        //capturo el id del usuario de la sesion
        $user = Auth()->user();

        //busco la suscripcion que pertenezca al usuario que realiza la peticion
        //ademas de contar con el id.

        $suscription = Suscription::where([
            'user_id' => $user->id,
            'id' => $id 
        ])->get(['id', 'active', 'type_id', 'amount'])->first();

        if($suscription)
        {
            return response()->Json(['status' => 'ok', 'suscription' => $suscription], 200);
        }else{
            return response()->Json(['status' => 'ko', 'message' => 'Suscription not exist.'], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSuscriptionRequest $request)
    {
         //capturo el id del usuario de la sesion
         $user = Auth()->user();

        //verificamos que no exista una suscripcion activa del usuario en cuestion.
        $suscription = Suscription::where('user_id', $user->id)->where('id', $request->input('suscription'))->first();

        if($suscription)
        {
            $suscription->type_id = $request->input('type');
            $suscription->amount = ($request->input('amount') !== NULL)?$request->input('amount'):$suscription->amount;
            $suscription->save();
            
            return response()->Json(['status' => 'ok', 'message' => 'update successfuly'], 200);
        }else{
            return response()->Json(['status' => 'ko', 'message' => 'Suscription not exist.'], 400);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //capturo el id del usuario de la sesion
         $user = Auth()->user();

        //verificamos que no exista una suscripcion activa del usuario en cuestion.
        $suscription = Suscription::where('user_id', $user->id)->where('id', $id)->first();

        if($suscription)
        {
            $suscription->delete();
            
            return response()->Json(['status' => 'ok', 'message' => 'deleted successfuly'], 200);
        }else{
            return response()->Json(['status' => 'ko', 'message' => 'Suscription not exist.'], 400);
        }
    }
}
