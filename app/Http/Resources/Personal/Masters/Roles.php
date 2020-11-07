<?php

namespace App\Http\Resources\Personal\Masters;

use Illuminate\Http\Resources\Json\JsonResource;

class Roles extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $perm = [];
        foreach($this->permissions as $permission){
            $perm[] = $permission->permission_id;
        }
        return [
            "id" => $this->id,
            "name" => $this->name,
            "permissions" => [
                "permission_id" => $perm
            ]
        ];
    }
}
