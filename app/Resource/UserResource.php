<?php

namespace App\Resource;

use Carbon\Carbon;
use Hyperf\Resource\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar_path' => $this->avatar,
            'email_verified_at' => $this->email_verified_at ? Carbon::parse($this->email_verified_at)->toDateTimeString() : null,
            'roles' => $this->when($this->relationLoaded('roles'), function () {
                return RoleResource::collection($this->roles)->toArray();
            }),
        ];
    }
}
