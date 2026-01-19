<?php

namespace App\Http\Controllers;

use App\Models\Qaror;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AjaxSearchController extends Controller
{
    /**
     * Search qarorlar by title (cached for popular queries)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:255',
        ]);

        $query = $validated['q'] ?? '';

        // Return empty array if query is empty or too short
        if (strlen(trim($query)) < 1) {
            return response()->json([]);
        }

        $normalizedQuery = trim(strtolower($query));
        $cacheKey = "ajax_search_" . md5($normalizedQuery);

        $qarorlar = Cache::remember($cacheKey, 1800, function () use ($query) {
            return Qaror::query()
                ->where('title', 'like', '%' . trim($query) . '%')
                ->orderByNumber()
                ->limit(20)
                ->select(['id', 'title', 'number', 'created_date'])
                ->get();
        });

        return response()->json($qarorlar);
    }
}
