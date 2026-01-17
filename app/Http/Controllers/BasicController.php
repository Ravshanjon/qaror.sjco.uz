<?php

namespace App\Http\Controllers;

use App\Models\Qaror;
use Illuminate\Http\Request;

class BasicController extends Controller
{
    public function index()
    {
        $qarorlar = Qaror::query()
            ->orderByNumber() // Use scope instead of orderByRaw
            ->paginate(25); // Changed from get() to paginate

        return view('welcome', compact('qarorlar'));
    }
}
