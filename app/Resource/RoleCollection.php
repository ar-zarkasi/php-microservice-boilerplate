<?php

namespace App\Resource;

use Hyperf\Resource\Json\ResourceCollection;

class RoleCollection extends ResourceCollection
{
    /**
     * The resource that this collection collects.
     *
     * @var string|null
     */
    public ?string $collects = RoleResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->collection->map(function ($role) {
            return (new RoleResource($role))->toArray();
        })->toArray();
    }
}
