<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property string $title 
 * @property string $slug 
 * @property string $slug 
 * @property string $content 
 * @property string $meta_title 
 * @property string $meta_description 
 * @property string $meta_keywords 
 * @property boolean $is_published 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class Page extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'pages';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_published',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'is_published' => 'boolean', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
