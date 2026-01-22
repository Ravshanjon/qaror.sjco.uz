<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qaror extends Model
{
    use HasFactory;

    protected $fillable = [
        'published_id',
        'title',
        'pdf_path',
        'created_date',
        'number',
        'file',
        'text',
        'views',
    ];

    protected $casts = [
        'created_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($qaror) {
            if (empty($qaror->published_id)) {
                $qaror->published_id = self::generateUniquePublishedId();
            }
        });
    }

    /**
     * Generate a unique 5-digit published_id
     */
    public static function generateUniquePublishedId(): int
    {
        do {
            $id = random_int(10000, 99999);
        } while (self::where('published_id', $id)->exists());

        return $id;
    }

    /**
     * Scope to order qarorlar by number (numeric sorting)
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByNumber($query)
    {
        return $query->orderByRaw('CAST(number AS UNSIGNED) DESC');
    }
}
