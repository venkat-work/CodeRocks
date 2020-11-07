<?php

namespace App\Http\Resources\Personal\Masters;

use Illuminate\Http\Resources\Json\JsonResource;

class RolePermissions extends JsonResource
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
            "permission_id" => $this->permission_id,
            "permissions"  => $this->permissions
        ];
    }
}
