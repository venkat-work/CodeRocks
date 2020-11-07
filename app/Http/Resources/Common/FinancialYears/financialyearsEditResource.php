<?php

namespace App\Http\Resources\Common\FinancialYears;

use Illuminate\Http\Resources\Json\JsonResource;

class financialyearsEditResource extends JsonResource
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
			'financial_year' => $this->financial_year,
			'is_current' => $this->is_current,
			'inserted_by' => $this->inserted_by,
			'updated_by' => $this->updated_by,
			'updated_at' => date(config('custom.datetime'), strtotime($this->updated_at))
		];
    }
}
