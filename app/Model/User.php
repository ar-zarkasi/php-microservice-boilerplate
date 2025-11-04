<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\SoftDeletes;

/**
 * @property string $id 
 * @property string $name 
 * @property string $email 
 * @property string $phone 
 * @property string $password 
 * @property string $email_verified_at 
 * @property string $avatar 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 * @property-read null|\Hyperf\Database\Model\Collection|Role[] $roles 
 */
class User extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'Users';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'name',
        'email',
        'phone',
        'email_verified_at',
        'avatar',
    ];

    protected array $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'User_Roles', 'user_id', 'role_id');
    }
}
