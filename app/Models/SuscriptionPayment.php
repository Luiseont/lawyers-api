<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuscriptionPayment extends Model
{
    use HasFactory;

    protected $table = "suscription_payments";
    protected $fillable = ['suscription_id', 'amount', 'response'];


    public function suscription()
    {
        return $this->belongsTo(suscription::class, 'id', 'suscription_id');
    }
}
