<?php

namespace App\Http\Controllers;

use App\Models\Qaror;
use Illuminate\Support\Facades\Session;

class PdfController extends Controller
{
    public function show($number)
    {
        $qaror = Qaror::where('number', $number)->firstOrFail();
        return view('pdf-viewer', compact('qaror'));
    }

}

