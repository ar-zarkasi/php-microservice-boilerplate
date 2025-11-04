<?php

declare(strict_types=1);

namespace App\Model;

use App\Traits\UuidModel;
use Hyperf\Database\Model\SoftDeletes;

/**
 */
class Role extends Model
{
    use SoftDeletes, UuidModel;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'roles';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id',
        'name',
        'description',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];

    public function users()
    {
        return $this->belongsToMany(User::class, 'User_Roles', 'role_id', 'user_id');
    }
    
}
