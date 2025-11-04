<?php

namespace App\Resource;

use Carbon\Carbon;
use Hyperf\Resource\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
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
            'email_verified_at' => Carbon::parse($this->email_verified_at)->toDateTimeString(),
            $this->mergeWhen($this->relationLoaded('roles'), function(){
                return [
                    'roles' => (new RoleCollection($this->roles))->jsonSerialize()
                ];
            })
        ];
    }
}
