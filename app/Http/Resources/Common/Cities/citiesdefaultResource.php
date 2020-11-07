<?php

namespace App\Http\Resources\Common\Cities;

use Illuminate\Http\Resources\Json\JsonResource;

class citiesdefaultResource extends JsonResource
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
