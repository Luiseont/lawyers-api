<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "suscriptions";
    protected $fillable = ['user_id','type_id','amount'];
    protected $appends = ['status', 'type_name'];
    

    public function client()
    {
        return $this->BelongsTo(User::class, 'user_id', 'id');
    }

    public function payments()
    {
        return $this->HasMany(SuscriptionPayment::class, 'id', 'suscription_id');
    }

    public function type()
    {
        return $this->HasOne(SuscriptionType::class, 'id', 'type_id');
    }


    public function getStatusAttribute()
    {
        return $this->attributes['status'] = ($this->active == 1)?'Active':'Inactive';
    }

    public function getTypeNameAttribute()
    {
        return $this->attributes['type_name'] = ($this->type != null)?$this->type->name:'NULL';
    }
}
