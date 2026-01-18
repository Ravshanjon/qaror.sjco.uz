<?php

namespace App\Http\Controllers;

use App\Models\Qaror;

class HomeController extends Controller
{
    /**
     * Display the main page with paginated qarorlar
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $qarorlar = Qaror::query()
            ->orderByNumber()
            ->paginate(config('qaror.items_per_page', 25));

        return view('welcome', compact('qarorlar'));
    }
}
