<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuscriptionType extends Model
{
    use HasFactory;

    protected $table = "suscription_types";

    protected $fillable = ['name'];

    public function suscription()
    {
        return $this->belongsTo(suscription::class, 'id', 'type_id');
    }
}
