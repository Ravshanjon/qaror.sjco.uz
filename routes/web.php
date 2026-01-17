<?php

use App\Http\Controllers\BasicController;
use App\Http\Controllers\PdfController;
use App\Models\Qaror;
use Illuminate\Support\Facades\Route;

Route::get('/', [BasicController::class, 'index']);

Route::get('/pdfs/{number}', [PdfController::class, 'show'])
    ->name('pdf.show');

Route::get('/qarorlar/ajax-search', function () {
    $q = $_GET['q'] ?? ''; // 🔥 oddiy PHP

    return Qaror::where('title', 'like', "%{$q}%")
        ->orderByRaw('CAST(number AS UNSIGNED) DESC')
        ->limit(20)
        ->get();

});

