<?php

namespace App\Http\Resources\Veterinary\Masters\LabMaster;

use Illuminate\Http\Resources\Json\JsonResource;

class labmasterListResource extends JsonResource
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
			'lab_name' => $this->lab_name,
			'description' => $this->description,
			'inserted_by' => $this->inserted_by,
			'updated_by' => $this->updated_by,
			'updated_at' => date(config('custom.datetime'), strtotime($this->updated_at))
		];
    }
}
