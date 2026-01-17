<?php

use App\Http\Controllers\AjaxSearchController;
use App\Http\Controllers\BasicController;
use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BasicController::class, 'index']);

Route::get('/pdfs/{number}', [PdfController::class, 'show'])
    ->name('pdf.show');

// AJAX search with validation and rate limiting
Route::get('/qarorlar/ajax-search', [AjaxSearchController::class, 'search'])
    ->middleware('throttle:60,1') // 60 requests per minute
    ->name('qarorlar.search');

