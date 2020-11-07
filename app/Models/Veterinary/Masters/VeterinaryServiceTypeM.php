<?php

namespace App\Models\Veterinary\Masters;

use Illuminate\Database\Eloquent\Model;

class VeterinaryServiceTypeM extends Model
{
    protected $table = 'veterinary_service_type_m';

    public function service_charges(){
    	return $this->hasOne('App\Models\Veterinary\Transactions\VeterinaryServiceCharges', "service_id");
    } 
}
