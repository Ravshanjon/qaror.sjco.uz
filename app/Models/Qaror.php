<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qaror extends Model
{
    protected $fillable = [
        'published_id',
        'title',
        'pdf_path',
        'created_date',
        'number',
        'file',
        'text',
        'views', // Added: for view counter
    ];

    public $casts = ['created_date' => 'date'];

    /**
     * Scope to order qarorlar by number (numeric sorting)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByNumber($query)
    {
        return $query->orderByRaw('CAST(number AS UNSIGNED) DESC');
    }
}
