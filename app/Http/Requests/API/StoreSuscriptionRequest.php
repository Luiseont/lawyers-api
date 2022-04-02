<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuscriptionRequest extends FormRequest
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
            'type' => 'required|numeric|exists:suscription_types,id',
            'amount'=> 'required|numeric'
        ];
    }

    public function attributes()
    {
        return [
            'types' => 'Tipo de suscripcion',
            'amount'=> 'Monto'
        ];
    }
}
