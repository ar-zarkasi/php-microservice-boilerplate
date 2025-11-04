<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'role_id' => $this->id,
            'role_name' => $this->name,
            'role_description' => $this->description,
        ];
    }
}
