<?php

use App\Http\Controllers\AjaxSearchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PdfController;
use App\Exports\QarorlarExport;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', [HomeController::class, 'index']);

Route::get('/pdfs/{number}', [PdfController::class, 'show'])
    ->name('pdf.show');

Route::get('/admin/qarorlar/export-csv', function () {
    return Excel::download(new QarorlarExport, 'qarorlar-'.now()->format('Y-m-d').'.csv', \Maatwebsite\Excel\Excel::CSV);
})->middleware(['auth'])->name('qarorlar.export-csv');

// AJAX search with validation and rate limiting
Route::get('/qarorlar/ajax-search', [AjaxSearchController::class, 'search'])
    ->middleware('throttle:60,1') // 60 requests per minute
    ->name('qarorlar.search');

