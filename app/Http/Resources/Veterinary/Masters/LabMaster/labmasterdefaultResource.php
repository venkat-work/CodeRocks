<?php

namespace App\Http\Resources\Veterinary\Masters\LabMaster;

use Illuminate\Http\Resources\Json\JsonResource;

class labmasterdefaultResource extends JsonResource
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
