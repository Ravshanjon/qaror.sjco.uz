<?php

namespace App\Http\Controllers;

use App\Models\Qaror;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Display the main page with paginated qarorlar
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $page = request('page', 1);
        $perPage = config('qaror.items_per_page', 25);

        $cacheKey = "home_qarorlar_page_{$page}_per_{$perPage}";

        $qarorlar = Cache::remember($cacheKey, 3600, function () use ($perPage) {
            return Qaror::query()
                ->orderByNumber()
                ->paginate($perPage);
        });

        return view('welcome', compact('qarorlar'));
    }
}
