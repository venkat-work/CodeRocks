<?php

namespace App\Http\Resources\Veterinary\Masters\ParameterType;

use Illuminate\Http\Resources\Json\JsonResource;

class parametertypedefaultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
