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
    ];
    public $casts = [ 'created_date' => 'date'];

}
