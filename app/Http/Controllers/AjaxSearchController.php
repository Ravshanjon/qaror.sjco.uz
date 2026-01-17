<?php

namespace App\Http\Controllers;

use App\Models\Qaror;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AjaxSearchController extends Controller
{
    /**
     * Search qarorlar by title
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

        $qarorlar = Qaror::query()
            ->where('title', 'like', '%' . trim($query) . '%')
            ->orderByNumber()  // Will create this scope next
            ->limit(20)
            ->select(['id', 'title', 'number', 'created_date']) // Only needed fields
            ->get();

        return response()->json($qarorlar);
    }
}
