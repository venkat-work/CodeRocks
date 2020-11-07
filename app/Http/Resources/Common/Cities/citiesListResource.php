<?php

namespace App\Http\Resources\Common\Cities;

use Illuminate\Http\Resources\Json\JsonResource;

class citiesListResource extends JsonResource
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
			'city_name' => $this->city_name,
			'state_id' => $this->state_id,
			'status' => $this->status,
			'inserted_by' => $this->inserted_by,
			'updated_by' => $this->updated_by,
			'updated_at' => date(config('custom.datetime'), strtotime($this->updated_at))
		];
    }
}
