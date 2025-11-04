<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property string $key 
 * @property string $key 
 * @property string $value 
 * @property string $type 
 * @property string $group 
 */
class Setting extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'setting';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer'];

    public function getValueAttribute($value): mixed
    {
        switch ($this->type) {
            case 'json':
                return json_decode($value, true);
            case 'int':
                return (int)$value;
            case 'float':
                return (float)$value;
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            default:
                return $value; // Return as is for string or other types
        }
    }
}
