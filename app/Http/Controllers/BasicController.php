<?php

namespace App\Http\Controllers;

use App\Models\Qaror;
use Illuminate\Http\Request;

class BasicController extends Controller
{
    public function index()
    {
        $qarorlar = Qaror::orderByRaw('CAST(number AS UNSIGNED) DESC')->get();

        return view('welcome', compact('qarorlar'));
    }
}
