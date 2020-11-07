<?php

namespace App\Http\Resources\Common\Countries;

use Illuminate\Http\Resources\Json\JsonResource;

class countriesListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
			'id' => $this->id,
			'short_name' => $this->short_name,
			'country_name' => $this->country_name,
			'status' => $this->status,
			'inserted_by' => $this->inserted_by,
			'updated_by' => $this->updated_by,
			'updated_at' => date(config('custom.datetime'), strtotime($this->updated_at))
		];
    }
}
