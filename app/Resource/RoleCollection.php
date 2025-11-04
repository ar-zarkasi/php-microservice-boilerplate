<?php

namespace App\Resource;

use Hyperf\Resource\Json\ResourceCollection;

class RoleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
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
