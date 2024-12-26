<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function removeSelectedItems(Request $request)
    {
        // Ensure the user is logged in
        if (!Auth::check()) {
            return response()->json([
                'message' => 'You must be logged in to add to the cart.'
            ], 401);
        }

        // Get the user's cart
        $cart = $request->user()->cart;

        // If the cart doesn't exist
        if (!$cart) {
            return response()->json([
                'message' => 'You do not have a cart.'
            ], 400);
        }

        // Validate the cart_item_ids
        $request->validate([
            'cart_item_ids' => 'required|array',
            'cart_item_ids.*' => 'exists:cart_items,id',
        ]);

        // Update selected status to false for the specified cart items
        CartItem::where('cart_id', $cart->id)
            ->whereIn('id', $request->cart_item_ids)
            ->update(['selected' => false]);

        // Return a success response
        return response()->json([
            'message' => 'Selected items have been removed from checkout successfully.'
        ], 200);
    }
}
