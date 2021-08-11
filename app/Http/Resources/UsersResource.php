<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $currentPermissions = $this->is_custom_permissions ? $this->permissions : ($this->role_permissions ?? []);

        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'city_id' => $this->city_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'active' => $this->is_active,
            'last_seen' => $this->last_seen,
            'last_page' => $this->last_page,
            'timezone' => $this->timezone,
            'role' => RolesResource::collection($this->roles),
            'custom_permissions' => $this->is_custom_permissions,
            'permissions' => PermissionsResource::collection($currentPermissions),
        ];
    }
}
