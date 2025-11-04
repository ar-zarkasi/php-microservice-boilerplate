<?php

namespace App\Resource;

use Hyperf\Resource\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    /**
     * The resource that this collection collects.
     *
     * @var string|null
     */
    public ?string $collects = UserResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->collection->map(function ($user) {
            return (new UserResource($user))->toArray();
        })->toArray();
    }
}
