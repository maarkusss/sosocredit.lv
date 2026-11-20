<?php

namespace App\Models;

use Database\Factories\ReviewFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property $id
 * @property $lang
 * @property $image
 * @property $name
 * @property $text
 * @property $section_id
 * @property $created_at
 * @property $updated_at
 */
class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'lang',
        'image',
        'name',
        'text',
        'section_id',
    ];

    public $timestamps = true;

    protected static function newFactory(): Factory
    {
        return ReviewFactory::new();
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}
