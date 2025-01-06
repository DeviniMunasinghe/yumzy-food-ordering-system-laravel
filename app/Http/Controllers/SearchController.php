<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        // Validate the search query parameter
        $validated = $request->validate([
            'query' => 'required|string|max:255',
        ]);

        $query = $validated['query'];

        // Search in items table by name or description
        $results = Item::where('item_name', 'LIKE', "%{$query}%")
            ->orWhere('item_description', 'LIKE', "%{$query}%")
            ->get();

        // Return the search results
        return response()->json([
            'message' => 'Search results retrieved successfully.',
            'data' => $results,
        ], 200);
    }
}
