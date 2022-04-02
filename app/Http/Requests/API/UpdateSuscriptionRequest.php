<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSuscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'suscription' => 'required|numeric|exists:suscriptions,id|min:1',
            'type' => 'required|numeric|exists:suscription_types,id',
            'amount'=> 'numeric'
        ];
    }

    public function attributes()
    {
        return [
            'suscription' => 'ID suscripcion',
            'types' => 'Tipo de suscripcion',
            'amount'=> 'Monto'
        ];
    }
}
