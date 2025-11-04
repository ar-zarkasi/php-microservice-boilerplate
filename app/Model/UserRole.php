<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property string $user_id 
 * @property string $role_id 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property-read null|User $user 
 * @property-read null|Role $role 
 */
class UserRole extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_roles';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'user_id',
        'role_id',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
